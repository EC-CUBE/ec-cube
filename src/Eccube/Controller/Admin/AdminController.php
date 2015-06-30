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


namespace Eccube\Controller\Admin;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    public function login(Application $app, Request $request)
    {
        if ($app['security']->isGranted('ROLE_ADMIN')) {
            return $app->redirect($app['url_generator']->generate('admin_login'));
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $app['form.factory']
            ->createNamedBuilder('', 'admin_login')
            ->getForm();

        return $app['view']->render('login.twig', array(
            'maintitle' => '',
            'error' => $app['security.last_error']($request),
            'form' => $form->createView(),
        ));
    }

    public function index(Application $app, Request $request)
    {
        // install.phpのチェック.
        if (isset($app['config']['eccube_install']) && $app['config']['eccube_install'] == 1) {
            $file = $app['config']['root_dir'] . '/html/install.php';
            if (file_exists($file)) {
                $app->addWarning('admin.install.warning', 'admin');
            }
        }

        // 受注マスター検索用フォーム
        $searchOrderForm = $app['form.factory']
            ->createBuilder('admin_search_order')
            ->getForm();
        // 商品マスター検索用フォーム
        $searchProductForm = $app['form.factory']
            ->createBuilder('admin_search_product')
            ->getForm();
        // 会員マスター検索用フォーム
        $searchCustomerForm = $app['form.factory']
            ->createBuilder('admin_search_customer')
            ->getForm();

        /**
         * 受注状況.
         */
        $excludes = array();
        $excludes[] = $app['config']['order_pending'];
        $excludes[] = $app['config']['order_processing'];
        $excludes[] = $app['config']['order_cancel'];
        $excludes[] = $app['config']['order_deliv'];

        // 受注ステータスごとの受注件数.
        $Orders = $this->getOrderEachStatus($app['orm.em'], $excludes);
        // 受注ステータスの一覧.
        $OrderStatuses = $this->findOrderStatus($app['orm.em'], $excludes);

        /**
         * 売り上げ状況
         */
        $excludes = array();
        $excludes[] = $app['config']['order_processing'];
        $excludes[] = $app['config']['order_cancel'];
        $excludes[] = $app['config']['order_pending'];

        // 今日の売上/件数
        $salesToday = $this->getSalesByDay($app['orm.em'], new \DateTime(), $excludes);
        // 昨日の売上/件数
        $salesYesterday = $this->getSalesByDay($app['orm.em'], new \DateTime('-1 day'), $excludes);
        // 今月の売上/件数
        $salesThisMonth = $this->getSalesByMonth($app['orm.em'], new \DateTime(), $excludes);

        /**
         * ショップ状況
         */
        // 在庫切れ商品数
        $countNonStockProducts = $this->countNonStockProducts($app['orm.em']);
        // 本会員数
        $countCustomers = $this->countCustomers($app['orm.em']);

        return $app->render('index.twig', array(
            'searchOrderForm' => $searchOrderForm->createView(),
            'searchProductForm' => $searchProductForm->createView(),
            'searchCustomerForm' => $searchCustomerForm->createView(),
            'Orders' => $Orders,
            'OrderStatuses' => $OrderStatuses,
            'salesThisMonth' => $salesThisMonth,
            'salesToday' => $salesToday,
            'salesYesterday' => $salesYesterday,
            'countNonStockProducts' => $countNonStockProducts,
            'countCustomers' => $countCustomers,
        ));
    }

    protected function findOrderStatus($em, array $excludes)
    {
        $qb = $em
            ->getRepository('Eccube\Entity\Master\OrderStatus')
            ->createQueryBuilder('os');

        return $qb
            ->where($qb->expr()->notIn('os.id', $excludes))
            ->getQuery()
            ->getResult();
    }

    protected function getOrderEachStatus($em, array $excludes)
    {
        $sql = 'SELECT
                    t1.status as status,
                    COUNT(t1.order_id) as count
                FROM
                    dtb_order t1
                WHERE
                    t1.del_flg = 0
                    AND t1.status NOT IN (:excludes)
                GROUP BY
                    t1.status
                ORDER BY
                    t1.status';
        $rsm = new ResultSetMapping();;
        $rsm->addScalarResult('status', 'status');
        $rsm->addScalarResult('count', 'count');
        $query = $em->createNativeQuery($sql, $rsm);
        $query->setParameters(array(':excludes' => $excludes));
        $result = $query->getResult();
        $orderArray = array();
        foreach ($result as $row) {
            $orderArray[$row['status']] = $row['count'];
        }

        return $orderArray;
    }

    protected function getSalesByMonth($em, $dateTime, array $excludes)
    {
        // concat... for pgsql
        // http://stackoverflow.com/questions/1091924/substr-does-not-work-with-datatype-timestamp-in-postgres-8-3
        $dql = 'SELECT
                  SUBSTRING(CONCAT(o.order_date, \'\'), 1, 7) AS order_month,
                  SUM(o.payment_total) AS order_amount,
                  COUNT(o) AS order_count
                FROM
                  Eccube\Entity\Order o
                WHERE
                    o.del_flg = 0
                    AND o.OrderStatus NOT IN (:excludes)
                    AND SUBSTRING(CONCAT(o.order_date, \'\'), 1, 7) = SUBSTRING(:targetDate, 1, 7)
                GROUP BY
                  order_month';

        $q = $em
            ->createQuery($dql)
            ->setParameter(':excludes', $excludes)
            ->setParameter(':targetDate', $dateTime);

        $result = array();
        try {
            $result = $q->getSingleResult();
        } catch (NoResultException $e) {
            // 結果がない場合は空の配列を返す.
        }
        return $result;
    }

    protected function getSalesByDay($em, $dateTime, array $excludes)
    {
        // concat... for pgsql
        // http://stackoverflow.com/questions/1091924/substr-does-not-work-with-datatype-timestamp-in-postgres-8-3
        $dql = 'SELECT
                  SUBSTRING(CONCAT(o.order_date, \'\'), 1, 10) AS order_day,
                  SUM(o.payment_total) AS order_amount,
                  COUNT(o) AS order_count
                FROM
                  Eccube\Entity\Order o
                WHERE
                    o.del_flg = 0
                    AND o.OrderStatus NOT IN (:excludes)
                    AND SUBSTRING(CONCAT(o.order_date, \'\'), 1, 10) = SUBSTRING(:targetDate, 1, 10)
                GROUP BY
                  order_day';

        $q = $em
            ->createQuery($dql)
            ->setParameter(':excludes', $excludes)
            ->setParameter(':targetDate', $dateTime);

        $result = array();
        try {
            $result = $q->getSingleResult();
        } catch (NoResultException $e) {
            // 結果がない場合は空の配列を返す.
        }
        return $result;
    }

    protected function countNonStockProducts($em)
    {
        /** @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->getRepository('Eccube\Entity\Product')
            ->createQueryBuilder('p')
            ->select('count(p.id)')
            ->innerJoin('p.ProductClasses', 'pc')
            ->where('pc.stock_unlimited = :StockUnlimited AND pc.stock = 0')
            ->setParameter('StockUnlimited', Constant::DISABLED);

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    protected function countCustomers($em)
    {
        $Status = $em
            ->getRepository('Eccube\Entity\Master\CustomerStatus')
            ->find(2);

        /** @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->getRepository('Eccube\Entity\Customer')
            ->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.Status = :Status')
            ->setParameter('Status', $Status);

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }
}