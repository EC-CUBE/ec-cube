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

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends AbstractController
{

    public function index(Application $app, Request $request, $page_no = null)
    {

        $session = $request->getSession();

        $builder = $app['form.factory']
            ->createBuilder('admin_search_order');

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_INITIALIZE, $event);

        $searchForm = $builder->getForm();

        $pagination = array();

        $disps = $app['eccube.repository.master.disp']->findAll();
        $pageMaxis = $app['eccube.repository.master.page_max']->findAll();
        $page_count = $app['config']['default_page_count'];
        $page_status = null;
        $active = false;

        if ('POST' === $request->getMethod()) {

            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();

                // paginator
                $qb = $app['eccube.repository.order']->getQueryBuilderBySearchDataForAdmin($searchData);

                $event = new EventArgs(
                    array(
                        'form' => $searchForm,
                        'qb' => $qb,
                    ),
                    $request
                );
                $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_SEARCH, $event);

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
                        if ($status != $app['config']['admin_product_stock_status']) {
                            $searchData['status']->clear();
                            $searchData['status']->add($status);
                        } else {
                            $searchData['stock_status'] = $app['config']['disabled'];
                        }
                        $page_status = $status;
                    }
                    // 表示件数
                    $pcount = $request->get('page_count');

                    $page_count = empty($pcount) ? $page_count : $pcount;

                    $qb = $app['eccube.repository.order']->getQueryBuilderBySearchDataForAdmin($searchData);

                    $event = new EventArgs(
                        array(
                            'form' => $searchForm,
                            'qb' => $qb,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_SEARCH, $event);

                    $pagination = $app['paginator']()->paginate(
                        $qb,
                        $page_no,
                        $page_count
                    );

                    // セッションから検索条件を復元
                    if (!empty($searchData['status'])) {
                        $searchData['status'] = $app['eccube.repository.master.order_status']->find($searchData['status']);
                    }
                    if (count($searchData['multi_status']) > 0) {
                        $statusIds = array();
                        foreach ($searchData['multi_status'] as $Status) {
                            $statusIds[] = $Status->getId();
                        }
                        $searchData['multi_status'] = $app['eccube.repository.master.order_status']->findBy(array('id' => $statusIds));
                    }
                    if (count($searchData['sex']) > 0) {
                        $sex_ids = array();
                        foreach ($searchData['sex'] as $Sex) {
                            $sex_ids[] = $Sex->getId();
                        }
                        $searchData['sex'] = $app['eccube.repository.master.sex']->findBy(array('id' => $sex_ids));
                    }
                    if (count($searchData['payment']) > 0) {
                        $payment_ids = array();
                        foreach ($searchData['payment'] as $Payment) {
                            $payment_ids[] = $Payment->getId();
                        }
                        $searchData['payment'] = $app['eccube.repository.payment']->findBy(array('id' => $payment_ids));
                    }
                    $searchForm->setData($searchData);
                }
            }
        }

        return $app->render('Order/index.twig', array(
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'disps' => $disps,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_status' => $page_status,
            'page_count' => $page_count,
            'active' => $active,
        ));

    }

    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);
        $session = $request->getSession();
        $page_no = intval($session->get('eccube.admin.order.search.page_no'));
        $page_no = $page_no ? $page_no : Constant::ENABLED;

        $Order = $app['orm.em']->getRepository('Eccube\Entity\Order')
            ->find($id);

        if (!$Order) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_order_page', array('page_no' => $page_no)).'?resume='.Constant::ENABLED);
        }

        log_info('受注削除開始', array($Order->getId()));

        $Order->setDelFlg(Constant::ENABLED);

        $app['orm.em']->persist($Order);
        $app['orm.em']->flush();

        $Customer = $Order->getCustomer();
        if ($Customer) {
            // 会員の場合、購入回数、購入金額などを更新
            $app['eccube.repository.customer']->updateBuyData($app, $Customer, $Order->getOrderStatus()->getId());
        }

        $event = new EventArgs(
            array(
                'Order' => $Order,
                'Customer' => $Customer,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_DELETE_COMPLETE, $event);

        $app->addSuccess('admin.order.delete.complete', 'admin');

        log_info('受注削除完了', array($Order->getId()));

        return $app->redirect($app->url('admin_order_page', array('page_no' => $page_no)).'?resume='.Constant::ENABLED);
    }


    /**
     * 受注CSVの出力.
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
        $em = $app['orm.em'];
        $em->getConfiguration()->setSQLLogger(null);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($app, $request) {

            // CSV種別を元に初期化.
            $app['eccube.service.csv.export']->initCsvType(CsvType::CSV_TYPE_ORDER);

            // ヘッダ行の出力.
            $app['eccube.service.csv.export']->exportHeader();

            // 受注データ検索用のクエリビルダを取得.
            $qb = $app['eccube.service.csv.export']
                ->getOrderQueryBuilder($request);

            // データ行の出力.
            $app['eccube.service.csv.export']->setExportQueryBuilder($qb);
            $app['eccube.service.csv.export']->exportData(function ($entity, $csvService) {

                $Csvs = $csvService->getCsvs();

                $Order = $entity;
                $OrderDetails = $Order->getOrderDetails();

                foreach ($OrderDetails as $OrderDetail) {
                    $row = array();

                    // CSV出力項目と合致するデータを取得.
                    foreach ($Csvs as $Csv) {
                        // 受注データを検索.
                        $data = $csvService->getData($Csv, $Order);
                        if (is_null($data)) {
                            // 受注データにない場合は, 受注明細を検索.
                            $data = $csvService->getData($Csv, $OrderDetail);
                        }
                        $row[] = $data;

                    }

                    //$row[] = number_format(memory_get_usage(true));
                    // 出力.
                    $csvService->fputcsv($row);
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
     * @param Application $app
     * @param Request $request
     * @return StreamedResponse
     */
    public function exportShipping(Application $app, Request $request)
    {
        // タイムアウトを無効にする.
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $app['orm.em'];
        $em->getConfiguration()->setSQLLogger(null);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($app, $request) {

            // CSV種別を元に初期化.
            $app['eccube.service.csv.export']->initCsvType(CsvType::CSV_TYPE_SHIPPING);

            // ヘッダ行の出力.
            $app['eccube.service.csv.export']->exportHeader();

            // 受注データ検索用のクエリビルダを取得.
            $qb = $app['eccube.service.csv.export']
                ->getOrderQueryBuilder($request);

            // データ行の出力.
            $app['eccube.service.csv.export']->setExportQueryBuilder($qb);
            $app['eccube.service.csv.export']->exportData(function ($entity, $csvService) {

                $Csvs = $csvService->getCsvs();

                /** @var $Order \Eccube\Entity\Order */
                $Order = $entity;
                /** @var $Shippings \Eccube\Entity\Shipping[] */
                $Shippings = $Order->getShippings();

                foreach ($Shippings as $Shipping) {
                    /** @var $ShipmentItems \Eccube\Entity\ShipmentItem */
                    $ShipmentItems = $Shipping->getShipmentItems();
                    foreach ($ShipmentItems as $ShipmentItem) {
                        $row = array();

                        // CSV出力項目と合致するデータを取得.
                        foreach ($Csvs as $Csv) {
                            // 受注データを検索.
                            $data = $csvService->getData($Csv, $Order);
                            if (is_null($data)) {
                                // 配送情報を検索.
                                $data = $csvService->getData($Csv, $Shipping);
                            }
                            if (is_null($data)) {
                                // 配送商品を検索.
                                $data = $csvService->getData($Csv, $ShipmentItem);
                            }
                            $row[] = $data;
                        }
                        //$row[] = number_format(memory_get_usage(true));
                        // 出力.
                        $csvService->fputcsv($row);
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
