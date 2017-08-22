<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Controller\Admin\Customer;

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Component;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\Master\SexRepository;
use Eccube\Service\CsvExportService;
use Eccube\Service\MailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Component
 * @Route(service=CustomerController::class)
 */
class CustomerController extends AbstractController
{
    /**
     * @Inject(CsvExportService::class)
     * @var CsvExportService
     */
    protected $csvExportService;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject(MailService::class)
     * @var MailService
     */
    protected $mailService;

    /**
     * @Inject(PrefRepository::class)
     * @var PrefRepository
     */
    protected $prefRepository;

    /**
     * @Inject(SexRepository::class)
     * @var SexRepository
     */
    protected $sexRepository;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(PageMaxRepository::class)
     * @var PageMaxRepository
     */
    protected $pageMaxRepository;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject(CustomerRepository::class)
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @Route("/{_admin}/customer", name="admin_customer")
     * @Route("/{_admin}/customer/new", name="admin_customer_new")
     * @Route("/{_admin}/customer/page/{page_no}", requirements={"page_no" = "\d+"}, name="admin_customer_page")
     * @Template("Customer/index.twig")
     */
    public function index(Application $app, Request $request, $page_no = null)
    {
        $session = $request->getSession();
        $pagination = array();
        $builder = $this->formFactory
            ->createBuilder(SearchCustomerType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_INDEX_INITIALIZE, $event);

        $searchForm = $builder->getForm();

        //アコーディオンの制御初期化( デフォルトでは閉じる )
        $active = false;

        $pageMaxis = $this->pageMaxRepository->findAll();
        $page_count = $this->appConfig['default_page_count'];

        if ('POST' === $request->getMethod()) {

            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();

                // paginator
                $qb = $this->customerRepository->getQueryBuilderBySearchData($searchData);
                $page_no = 1;

                $event = new EventArgs(
                    array(
                        'form' => $searchForm,
                        'qb' => $qb,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_INDEX_SEARCH, $event);

                $pagination = $app['paginator']()->paginate(
                    $qb,
                    $page_no,
                    $page_count
                );

                // sessionのデータ保持
                $session->set('eccube.admin.customer.search', $searchData);
                $session->set('eccube.admin.customer.search.page_no', $page_no);
            }
        } else {
            if (is_null($page_no) && $request->get('resume') != Constant::ENABLED) {
                // sessionを削除
                $session->remove('eccube.admin.customer.search');
                $session->remove('eccube.admin.customer.search.page_no');
            } else {
                // pagingなどの処理
                $searchData = $session->get('eccube.admin.customer.search');
                if (is_null($page_no)) {
                    $page_no = intval($session->get('eccube.admin.customer.search.page_no'));
                } else {
                    $session->set('eccube.admin.customer.search.page_no', $page_no);
                }
                if (!is_null($searchData)) {
                    // 表示件数
                    $pcount = $request->get('page_count');
                    $page_count = empty($pcount) ? $page_count : $pcount;

                    $qb = $this->customerRepository->getQueryBuilderBySearchData($searchData);

                    $event = new EventArgs(
                        array(
                            'form' => $searchForm,
                            'qb' => $qb,
                        ),
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_INDEX_SEARCH, $event);

                    $pagination = $app['paginator']()->paginate(
                        $qb,
                        $page_no,
                        $page_count
                    );

                    // セッションから検索条件を復元
                    if (count($searchData['sex']) > 0) {
                        $sex_ids = array();
                        foreach ($searchData['sex'] as $Sex) {
                            $sex_ids[] = $Sex->getId();
                        }
                        $searchData['sex'] = $this->sexRepository->findBy(array('id' => $sex_ids));
                    }

                    if (!is_null($searchData['pref'])) {
                        $searchData['pref'] = $this->prefRepository->find($searchData['pref']->getId());
                    }
                    $searchForm->setData($searchData);
                }
            }
        }
        return [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_count' => $page_count,
            'active' => $active,
        ];
    }

    /**
     * @Route("/{_admin}/customer/{id}/resend", requirements={"id" = "\d+"}, name="admin_customer_resend")
     */
    public function resend(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $Customer = $this->customerRepository
            ->find($id);

        if (is_null($Customer)) {
            throw new NotFoundHttpException();
        }

        $activateUrl = $app->url('entry_activate', array('secret_key' => $Customer->getSecretKey()));

        // メール送信
        $this->mailService->sendAdminCustomerConfirmMail($Customer, $activateUrl);

        $event = new EventArgs(
            array(
                'Customer' => $Customer,
                'activateUrl' => $activateUrl,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_RESEND_COMPLETE, $event);

        $app->addSuccess('admin.customer.resend.complete', 'admin');

        return $app->redirect($app->url('admin_customer'));
    }

    /**
     * @Method("DELETE")
     * @Route("/{_admin}/customer/{id}/delete", requirements={"id" = "\d+"}, name="admin_customer_delete")
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        log_info('会員削除開始', array($id));

        $session = $request->getSession();
        $page_no = intval($session->get('eccube.admin.customer.search.page_no'));
        $page_no = $page_no ? $page_no : Constant::ENABLED;

        $Customer = $this->customerRepository
            ->find($id);

        if (!$Customer) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_customer_page', array('page_no' => $page_no)).'?resume='.Constant::ENABLED);
        }

        $Customer->setDelFlg(Constant::ENABLED);
        $this->entityManager->persist($Customer);
        $this->entityManager->flush();

        log_info('会員削除完了', array($id));

        $event = new EventArgs(
            array(
                'Customer' => $Customer,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_DELETE_COMPLETE, $event);

        $app->addSuccess('admin.customer.delete.complete', 'admin');

        return $app->redirect($app->url('admin_customer_page', array('page_no' => $page_no)).'?resume='.Constant::ENABLED);
    }

    /**
     * 会員CSVの出力.
     *
     * @Route("/{_admin}/customer/export", name="admin_customer_export")
     *
     * @param Application $app
     * @param Request $request
     * @return StreamedResponse
     */
    public function export(Application $app, Request $request)
    {
        // タイムアウトを無効にする.
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $this->entityManager;
        $em->getConfiguration()->setSQLLogger(null);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($app, $request) {

            // CSV種別を元に初期化.
            $this->csvExportService->initCsvType(CsvType::CSV_TYPE_CUSTOMER);

            // ヘッダ行の出力.
            $this->csvExportService->exportHeader();

            // 会員データ検索用のクエリビルダを取得.
            $qb = $this->csvExportService
                ->getCustomerQueryBuilder($request);

            // データ行の出力.
            $this->csvExportService->setExportQueryBuilder($qb);
            $this->csvExportService->exportData(function ($entity, $csvService) use ($app, $request) {

                $Csvs = $csvService->getCsvs();

                /** @var $Customer \Eccube\Entity\Customer */
                $Customer = $entity;

                $ExportCsvRow = new \Eccube\Entity\ExportCsvRow();

                // CSV出力項目と合致するデータを取得.
                foreach ($Csvs as $Csv) {
                    // 会員データを検索.
                    $ExportCsvRow->setData($csvService->getData($Csv, $Customer));

                    $event = new EventArgs(
                        array(
                            'csvService' => $csvService,
                            'Csv' => $Csv,
                            'Customer' => $Customer,
                            'ExportCsvRow' => $ExportCsvRow,
                        ),
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_CSV_EXPORT, $event);

                    $ExportCsvRow->pushData();
                }

                //$row[] = number_format(memory_get_usage(true));
                // 出力.
                $csvService->fputcsv($ExportCsvRow->getRow());
            });
        });

        $now = new \DateTime();
        $filename = 'customer_' . $now->format('YmdHis') . '.csv';
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);

        $response->send();

        log_info("会員CSVファイル名", array($filename));

        return $response;
    }
}
