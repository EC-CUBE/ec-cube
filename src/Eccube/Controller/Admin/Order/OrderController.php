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


namespace Eccube\Controller\Admin\Order;

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Component;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\SearchOrderType;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\DispRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Repository\Master\SexRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Service\CsvExportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Component
 * @Route(service=OrderController::class)
 */
class OrderController extends AbstractController
{
    /**
     * @Inject(CsvExportService::class)
     * @var CsvExportService
     */
    protected $csvExportService;

    /**
     * @Inject(CustomerRepository::class)
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject(PaymentRepository::class)
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @Inject(SexRepository::class)
     * @var SexRepository
     */
    protected $sexRepository;

    /**
     * @Inject(OrderStatusRepository::class)
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

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
     * @Inject(DispRepository::class)
     * @var DispRepository
     */
    protected $dispRepository;

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
     * @Inject(OrderRepository::class)
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @Route("/{_admin}/order", name="admin_order")
     * @Route("/{_admin}/order/page/{page_no}", requirements={"page_no" = "\d+"}, name="admin_order_page")
     * @Template("Order/index.twig")
     */
    public function index(Application $app, Request $request, $page_no = null)
    {

        $session = $request->getSession();

        $builder = $this->formFactory
            ->createBuilder(SearchOrderType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_INITIALIZE, $event);

        $searchForm = $builder->getForm();

        $pagination = array();

        $disps = $this->dispRepository->findAll();
        $pageMaxis = $this->pageMaxRepository->findAll();
        $page_count = $this->appConfig['default_page_count'];
        $page_status = null;
        $active = false;

        if ('POST' === $request->getMethod()) {

            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();

                // paginator
                $qb = $this->orderRepository->getQueryBuilderBySearchDataForAdmin($searchData);

                $event = new EventArgs(
                    array(
                        'form' => $searchForm,
                        'qb' => $qb,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_SEARCH, $event);

                $page_no = 1;
                $pagination = $app['paginator']()->paginate(
                    $qb,
                    $page_no,
                    $page_count
                );

                // sessionのデータ保持
                $session->set('eccube.admin.order.search', $searchData);
                $session->set('eccube.admin.order.search.page_no', $page_no);
            }
        } else {
            if (is_null($page_no) && $request->get('resume') != Constant::ENABLED) {
                // sessionを削除
                $session->remove('eccube.admin.order.search');
                $session->remove('eccube.admin.order.search.page_no');
            } else {
                // pagingなどの処理
                $searchData = $session->get('eccube.admin.order.search');
                if (is_null($page_no)) {
                    $page_no = intval($session->get('eccube.admin.order.search.page_no'));
                } else {
                    $session->set('eccube.admin.order.search.page_no', $page_no);
                }

                if (!is_null($searchData)) {

                    // 公開ステータス
                    $status = $request->get('status');
                    if (!empty($status)) {
                        if ($status != $this->appConfig['admin_product_stock_status']) {
                            $searchData['status']->clear();
                            $searchData['status']->add($status);
                        } else {
                            $searchData['stock_status'] = $this->appConfig['disabled'];
                        }
                        $page_status = $status;
                    }
                    // 表示件数
                    $pcount = $request->get('page_count');

                    $page_count = empty($pcount) ? $page_count : $pcount;

                    $qb = $this->orderRepository->getQueryBuilderBySearchDataForAdmin($searchData);

                    $event = new EventArgs(
                        array(
                            'form' => $searchForm,
                            'qb' => $qb,
                        ),
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_SEARCH, $event);

                    $pagination = $app['paginator']()->paginate(
                        $qb,
                        $page_no,
                        $page_count
                    );

                    // セッションから検索条件を復元
                    if (!empty($searchData['status'])) {
                        $searchData['status'] = $this->orderStatusRepository->find($searchData['status']);
                    }
                    if (count($searchData['multi_status']) > 0) {
                        $statusIds = array();
                        foreach ($searchData['multi_status'] as $Status) {
                            $statusIds[] = $Status->getId();
                        }
                        $searchData['multi_status'] = $this->orderStatusRepository->findBy(array('id' => $statusIds));
                    }
                    if (count($searchData['sex']) > 0) {
                        $sex_ids = array();
                        foreach ($searchData['sex'] as $Sex) {
                            $sex_ids[] = $Sex->getId();
                        }
                        $searchData['sex'] = $this->sexRepository->findBy(array('id' => $sex_ids));
                    }
                    if (count($searchData['payment']) > 0) {
                        $payment_ids = array();
                        foreach ($searchData['payment'] as $Payment) {
                            $payment_ids[] = $Payment->getId();
                        }
                        $searchData['payment'] = $this->paymentRepository->findBy(array('id' => $payment_ids));
                    }
                    $searchForm->setData($searchData);
                }
            }
        }

        return [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'disps' => $disps,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_status' => $page_status,
            'page_count' => $page_count,
            'active' => $active,
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/{_admin}/order/{id}/delete", requirements={"id" = "\d+"}, name="admin_order_delete")
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);
        $session = $request->getSession();
        $page_no = intval($session->get('eccube.admin.order.search.page_no'));
        $page_no = $page_no ? $page_no : Constant::ENABLED;

        $Order = $this->orderRepository
            ->find($id);

        if (!$Order) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_order_page', array('page_no' => $page_no)).'?resume='.Constant::ENABLED);
        }

        log_info('受注削除開始', array($Order->getId()));

        $Order->setDelFlg(Constant::ENABLED);

        $this->entityManager->persist($Order);
        $this->entityManager->flush();

        $Customer = $Order->getCustomer();
        if ($Customer) {
            // 会員の場合、購入回数、購入金額などを更新
            $this->customerRepository->updateBuyData($app, $Customer, $Order->getOrderStatus()->getId());
        }

        $event = new EventArgs(
            array(
                'Order' => $Order,
                'Customer' => $Customer,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_DELETE_COMPLETE, $event);

        $app->addSuccess('admin.order.delete.complete', 'admin');

        log_info('受注削除完了', array($Order->getId()));

        return $app->redirect($app->url('admin_order_page', array('page_no' => $page_no)).'?resume='.Constant::ENABLED);
    }


    /**
     * 受注CSVの出力.
     *
     * @Route("/order/export/order", name="admin_order_export_order")
     *
     * @param Application $app
     * @param Request $request
     * @return StreamedResponse
     */
    public function exportOrder(Application $app, Request $request)
    {

        // タイムアウトを無効にする.
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $this->entityManager;
        $em->getConfiguration()->setSQLLogger(null);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($app, $request) {

            // CSV種別を元に初期化.
            $this->csvExportService->initCsvType(CsvType::CSV_TYPE_ORDER);

            // ヘッダ行の出力.
            $this->csvExportService->exportHeader();

            // 受注データ検索用のクエリビルダを取得.
            $qb = $this->csvExportService
                ->getOrderQueryBuilder($request);

            // データ行の出力.
            $this->csvExportService->setExportQueryBuilder($qb);
            $this->csvExportService->exportData(function ($entity, $csvService) use ($app, $request) {

                $Csvs = $csvService->getCsvs();

                $Order = $entity;
                $OrderDetails = $Order->getOrderDetails();

                foreach ($OrderDetails as $OrderDetail) {
                    $ExportCsvRow = new \Eccube\Entity\ExportCsvRow();

                    // CSV出力項目と合致するデータを取得.
                    foreach ($Csvs as $Csv) {
                        // 受注データを検索.
                        $ExportCsvRow->setData($csvService->getData($Csv, $Order));
                        if ($ExportCsvRow->isDataNull()) {
                            // 受注データにない場合は, 受注明細を検索.
                            $ExportCsvRow->setData($csvService->getData($Csv, $OrderDetail));
                        }

                        $event = new EventArgs(
                            array(
                                'csvService' => $csvService,
                                'Csv' => $Csv,
                                'OrderDetail' => $OrderDetail,
                                'ExportCsvRow' => $ExportCsvRow,
                            ),
                            $request
                        );
                        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_CSV_EXPORT_ORDER, $event);

                        $ExportCsvRow->pushData();
                    }

                    //$row[] = number_format(memory_get_usage(true));
                    // 出力.
                    $csvService->fputcsv($ExportCsvRow->getRow());
                }
            });
        });

        $now = new \DateTime();
        $filename = 'order_' . $now->format('YmdHis') . '.csv';
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $response->send();

        log_info('受注CSV出力ファイル名', array($filename));

        return $response;
    }

    /**
     * 配送CSVの出力.
     *
     * @Route("/order/export/shipping", name="admin_order_export_shipping")
     *
     * @param Application $app
     * @param Request $request
     * @return StreamedResponse
     */
    public function exportShipping(Application $app, Request $request)
    {
        // タイムアウトを無効にする.
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $this->entityManager;
        $em->getConfiguration()->setSQLLogger(null);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($app, $request) {

            // CSV種別を元に初期化.
            $this->csvExportService->initCsvType(CsvType::CSV_TYPE_SHIPPING);

            // ヘッダ行の出力.
            $this->csvExportService->exportHeader();

            // 受注データ検索用のクエリビルダを取得.
            $qb = $this->csvExportService
                ->getOrderQueryBuilder($request);

            // データ行の出力.
            $this->csvExportService->setExportQueryBuilder($qb);
            $this->csvExportService->exportData(function ($entity, $csvService) use ($app, $request) {

                $Csvs = $csvService->getCsvs();

                /** @var $Order \Eccube\Entity\Order */
                $Order = $entity;
                /** @var $Shippings \Eccube\Entity\Shipping[] */
                $Shippings = $Order->getShippings();

                foreach ($Shippings as $Shipping) {
                    /** @var $ShipmentItems \Eccube\Entity\ShipmentItem */
                    $ShipmentItems = $Shipping->getShipmentItems();
                    foreach ($ShipmentItems as $ShipmentItem) {
                        $ExportCsvRow = new \Eccube\Entity\ExportCsvRow();

                        // CSV出力項目と合致するデータを取得.
                        foreach ($Csvs as $Csv) {
                            // 受注データを検索.
                            $ExportCsvRow->setData($csvService->getData($Csv, $Order));
                            if ($ExportCsvRow->isDataNull()) {
                                // 配送情報を検索.
                                $ExportCsvRow->setData($csvService->getData($Csv, $Shipping));
                            }
                            if ($ExportCsvRow->isDataNull()) {
                                // 配送商品を検索.
                                $ExportCsvRow->setData($csvService->getData($Csv, $ShipmentItem));
                            }

                            $event = new EventArgs(
                                array(
                                    'csvService' => $csvService,
                                    'Csv' => $Csv,
                                    'ShipmentItem' => $ShipmentItem,
                                    'ExportCsvRow' => $ExportCsvRow,
                                ),
                                $request
                            );
                            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_CSV_EXPORT_SHIPPING, $event);

                            $ExportCsvRow->pushData();
                        }
                        //$row[] = number_format(memory_get_usage(true));
                        // 出力.
                        $csvService->fputcsv($ExportCsvRow->getRow());
                    }
                }
            });
        });

        $now = new \DateTime();
        $filename = 'shipping_' . $now->format('YmdHis') . '.csv';
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $response->send();

        log_info('配送CSV出力ファイル名', array($filename));

        return $response;
    }
}
