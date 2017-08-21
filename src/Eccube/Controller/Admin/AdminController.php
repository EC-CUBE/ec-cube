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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Component;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\ChangePasswordType;
use Eccube\Form\Type\Admin\LoginType;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Form\Type\Admin\SearchOrderType;
use Eccube\Form\Type\Admin\SearchProductType;
use Eccube\Repository\MemberRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Component
 * @Route(service=AdminController::class)
 */
class AdminController extends AbstractController
{
    /**
     * @Inject(MemberRepository::class)
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

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
     * @Route("/{_admin}/login", name="admin_login")
     * @Template("login.twig")
     */
    public function login(Application $app, Request $request)
    {
        if ($app->isGranted('ROLE_ADMIN')) {
            return $app->redirect($app->url('admin_homepage'));
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $builder = $this->formFactory
            ->createNamedBuilder('', LoginType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ADMIM_LOGIN_INITIALIZE, $event);

        $form = $builder->getForm();

        return [
            'error' => $app['security.last_error']($request),
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/{_admin}", name="admin_homepage")
     * @Template("index.twig")
     */
    public function index(Application $app, Request $request)
    {
        // install.phpのチェック.
        if (isset($this->appConfig['eccube_install']) && $this->appConfig['eccube_install'] == 1) {
            $file = $this->appConfig['root_dir'] . '/html/install.php';
            if (file_exists($file)) {
                $message = $app->trans('admin.install.warning', array('installphpPath' => 'html/install.php'));
                $app->addWarning($message, 'admin');
            }
            $fileOnRoot = $this->appConfig['root_dir'] . '/install.php';
            if (file_exists($fileOnRoot)) {
                $message = $app->trans('admin.install.warning', array('installphpPath' => 'install.php'));
                $app->addWarning($message, 'admin');
            }
        }

        // 受注マスター検索用フォーム
        $searchOrderBuilder = $this->formFactory
            ->createBuilder(SearchOrderType::class);
        // 商品マスター検索用フォーム
        $searchProductBuilder = $this->formFactory
            ->createBuilder(SearchProductType::class);
        // 会員マスター検索用フォーム
        $searchCustomerBuilder = $this->formFactory
            ->createBuilder(SearchCustomerType::class);

        $event = new EventArgs(
            array(
                'searchOrderBuilder' => $searchOrderBuilder,
                'searchProductBuilder' => $searchProductBuilder,
                'searchCustomerBuilder' => $searchCustomerBuilder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ADMIM_INDEX_INITIALIZE, $event);

        // 受注マスター検索用フォーム
        $searchOrderForm = $searchOrderBuilder->getForm();

        // 商品マスター検索用フォーム
        $searchProductForm = $searchProductBuilder->getForm();

        // 会員マスター検索用フォーム
        $searchCustomerForm = $searchCustomerBuilder->getForm();

        /**
         * 受注状況.
         */
        $excludes = array();
        $excludes[] = $this->appConfig['order_pending'];
        $excludes[] = $this->appConfig['order_processing'];
        $excludes[] = $this->appConfig['order_cancel'];
        $excludes[] = $this->appConfig['order_deliv'];

        $event = new EventArgs(
            array(
                'excludes' => $excludes,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ADMIM_INDEX_ORDER, $event);
        $excludes = $event->getArgument('excludes');

        // 受注ステータスごとの受注件数.
        $Orders = $this->getOrderEachStatus($this->entityManager, $excludes);
        // 受注ステータスの一覧.
        $OrderStatuses = $this->findOrderStatus($this->entityManager, $excludes);

        /**
         * 売り上げ状況
         */
        $excludes = array();
        $excludes[] = $this->appConfig['order_processing'];
        $excludes[] = $this->appConfig['order_cancel'];
        $excludes[] = $this->appConfig['order_pending'];

        $event = new EventArgs(
            array(
                'excludes' => $excludes,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ADMIM_INDEX_SALES, $event);
        $excludes = $event->getArgument('excludes');

        // 今日の売上/件数
        $salesToday = $this->getSalesByDay($this->entityManager, new \DateTime(), $excludes);
        // 昨日の売上/件数
        $salesYesterday = $this->getSalesByDay($this->entityManager, new \DateTime('-1 day'), $excludes);
        // 今月の売上/件数
        $salesThisMonth = $this->getSalesByMonth($this->entityManager, new \DateTime(), $excludes);

        /**
         * ショップ状況
         */
        // 在庫切れ商品数
        $countNonStockProducts = $this->countNonStockProducts($this->entityManager);
        // 本会員数
        $countCustomers = $this->countCustomers($this->entityManager);

        $event = new EventArgs(
            array(
                'Orders' => $Orders,
                'OrderStatuses' => $OrderStatuses,
                'salesThisMonth' => $salesThisMonth,
                'salesToday' => $salesToday,
                'salesYesterday' => $salesYesterday,
                'countNonStockProducts' => $countNonStockProducts,
                'countCustomers' => $countCustomers,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ADMIM_INDEX_COMPLETE, $event);

        return [
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
        ];
    }

    /**
     * パスワード変更画面
     *
     * @Route("/{_admin}/login", name="admin_change_password")
     * @Template("change_password.twig")
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function changePassword(Application $app, Request $request)
    {
        $builder = $this->formFactory
            ->createBuilder(ChangePasswordType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
p            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ADMIM_CHANGE_PASSWORD_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('change_password')->getData();

            $Member = $app->user();

            $dummyMember = clone $Member;
            $dummyMember->setPassword($password);
            $salt = $dummyMember->getSalt();
            if (!isset($salt)) {
                $salt = $this->memberRepository->createSalt(5);
                $dummyMember->setSalt($salt);
            }

            $encryptPassword = $this->memberRepository->encryptPassword($dummyMember);

            $Member
                ->setPassword($encryptPassword)
                ->setSalt($salt);

            $status = $this->memberRepository->save($Member);
            if ($status) {
                $event = new EventArgs(
                    array(
                        'form' => $form,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ADMIN_CHANGE_PASSWORD_COMPLETE, $event);

                $app->addSuccess('admin.change_password.save.complete', 'admin');

                return $app->redirect($app->url('admin_change_password'));
            }

            $app->addError('admin.change_password.save.error', 'admin');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * 在庫なし商品の検索結果を表示する.
     *
     * @Route("/{_admin}/login", name="admin_homepage_nonstock")
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchNonStockProducts(Application $app, Request $request)
    {
        // 商品マスター検索用フォーム
        /* @var Form $form */
        $form = $this->formFactory
            ->createBuilder(SearchProductType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 在庫なし商品の検索条件をセッションに付与し, 商品マスタへリダイレクトする.
            $searchData = array();
            $searchData['stock_status'] = Constant::DISABLED;
            $session = $request->getSession();
            $session->set('eccube.admin.product.search', $searchData);

            return $app->redirect($app->url('admin_product_page', array(
                'page_no' => 1,
                'status' => $this->appConfig['admin_product_stock_status'])));
        }

        return $app->redirect($app->url('admin_homepage'));
    }

    protected function findOrderStatus($em, array $excludes)
    {
        /* @var $qb QueryBuilder */
        $qb = $em
            ->getRepository('Eccube\Entity\Master\OrderStatus')
            ->createQueryBuilder('os');

        return $qb
            ->where($qb->expr()->notIn('os.id', $excludes))
            ->orderBy('os.rank', 'ASC')
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
            ->select('count(DISTINCT p.id)')
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
