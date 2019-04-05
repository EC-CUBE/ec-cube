<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

        // 表示件数は順番で取得する、1.SESSION 2.設定ファイル
        $page_count = $session->get('eccube.admin.order.search.page_count', $app['config']['default_page_count']);

        $page_count_param = $request->get('page_count');
        // 表示件数はURLパラメターから取得する
        if($page_count_param && is_numeric($page_count_param)){
            foreach($pageMaxis as $pageMax){
                if($page_count_param == $pageMax->getName()){
                    $page_count = $pageMax->getName();
                    // 表示件数入力値正し場合はSESSIONに保存する
                    $session->set('eccube.admin.order.search.page_count', $page_count);
                    break;
                }
            }
        }

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

                // sessionに検索条件を保持.
                $viewData = \Eccube\Util\FormUtil::getViewData($searchForm);
                $session->set('eccube.admin.order.search', $viewData);
                $session->set('eccube.admin.order.search.page_no', $page_no);
            }
        } else {
            if (is_null($page_no) && $request->get('resume') != Constant::ENABLED) {
                // sessionを削除
                $session->remove('eccube.admin.order.search');
                $session->remove('eccube.admin.order.search.page_no');
                $session->remove('eccube.admin.order.search.page_count');
            } else {
                // pagingなどの処理
                if (is_null($page_no)) {
                    $page_no = intval($session->get('eccube.admin.order.search.page_no'));
                } else {
                    $session->set('eccube.admin.order.search.page_no', $page_no);
                }
                $viewData = $session->get('eccube.admin.order.search');
                if (!is_null($viewData)) {
                    // sessionに保持されている検索条件を復元.
                    $searchData = \Eccube\Util\FormUtil::submitAndGetData($searchForm, $viewData);
                }
                if (!is_null($searchData)) {
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
                        }
                    }
                        }

        return $app->render('Order/index.twig', array(
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'disps' => $disps,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
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
            $app['eccube.service.csv.export']->exportData(function ($entity, $csvService) use ($app, $request) {

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
                        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_CSV_EXPORT_ORDER, $event);

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
            $app['eccube.service.csv.export']->exportData(function ($entity, $csvService) use ($app, $request) {

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
                            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_CSV_EXPORT_SHIPPING, $event);

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
