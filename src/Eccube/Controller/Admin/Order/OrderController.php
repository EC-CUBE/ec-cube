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
use Symfony\Component\HttpFoundation\Request;

class OrderController
{

    public function index(Application $app, Request $request, $page_no = null)
    {

        $session = $request->getSession();

        $searchForm = $app['form.factory']
            ->createBuilder('admin_search_order')
            ->getForm();

        $pagination = array();

        $em = $app['orm.em'];
        $disps = $em->getRepository('Eccube\Entity\Master\Disp')->findAll();
        $pageMaxis = $em->getRepository('Eccube\Entity\Master\PageMax')->findAll();
        $page_count = $app['config']['default_page_count'];
        $page_status = null;


        if ('POST' === $request->getMethod()) {

            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();

                // paginator
                $qb = $app['eccube.repository.order']->getQueryBuilderBySearchDataForAdmin($searchData);
                $page_no = 1;
                $pagination = $app['paginator']()->paginate(
                    $qb,
                    $page_no,
                    $page_count
                );

                // sessionのデータ保持
                $session->set('eccube.admin.order.search', $searchData);
            }
        } else {
            if (is_null($page_no)) {
                // sessionを削除
                $session->remove('eccube.admin.order.search');
            } else {
                // pagingなどの処理
                $searchData = $session->get('eccube.admin.order.search');
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
            'page_status' => $page_status,
            'page_count' => $page_count,
        ));

    }

    public function delete(Application $app, $id)
    {
        $Order = $app['orm.em']->getRepository('Eccube\Entity\Order')
            ->find($id);

        if ($Order) {
            $Order->setDelFlg(1);
            $app['orm.em']->persist($Order);
            $app['orm.em']->flush();

            $app['session']->getFlashBag()->add('admin.order.complete', 'admin.order.delete.complete');
        }

        return $this->index($app);
    }
}
