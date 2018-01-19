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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\OrderStatus;
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
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route(service=AdminController::class)
 */
class AdminController extends AbstractController
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var AuthenticationUtils
     */
    protected $helper;

    /**
     * @var MemberRepository
     */
    protected $memberRepository;


    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;


    /**
     * AdminController constructor.
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param AuthenticationUtils $helper
     * @param MemberRepository $memberRepository
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        AuthenticationUtils $helper,
        MemberRepository $memberRepository,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->helper = $helper;
        $this->memberRepository = $memberRepository;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @Route("/%admin_route%/login", name="admin_login")
     * @Template("@admin/login.twig")
     */
    public function login(Request $request)
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_homepage');
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $builder = $this->formFactory->createNamedBuilder('', LoginType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ADMIM_LOGIN_INITIALIZE, $event);

        $form = $builder->getForm();

        return [
            'error' => $this->helper->getLastAuthenticationError(),
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/%admin_route%/", name="admin_homepage")
     * @Template("@admin/index.twig")
     */
    public function index(Request $request)
    {
        // install.phpのチェック.
        if (isset($this->eccubeConfig['eccube_install']) && $this->eccubeConfig['eccube_install'] == 1) {
            $file = $this->eccubeConfig['root_dir'] . '/html/install.php';
            if (file_exists($file)) {
                $message = $this->translator->trans('admin.install.warning', array('installphpPath' => 'html/install.php'));
                $this->addWarning($message, 'admin');
            }
            $fileOnRoot = $this->eccubeConfig['root_dir'] . '/install.php';
            if (file_exists($fileOnRoot)) {
                $message = $this->translator->trans('admin.install.warning', array('installphpPath' => 'install.php'));
                $this->addWarning($message, 'admin');
            }
        }

        // 受注マスター検索用フォーム
        $searchOrderBuilder = $this->createFormBuilder(SearchOrderType::class);
        // 商品マスター検索用フォーム
        $searchProductBuilder = $this->createFormBuilder(SearchProductType::class);
        // 会員マスター検索用フォーム
        $searchCustomerBuilder = $this->createFormBuilder(SearchCustomerType::class);

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
        $excludes[] = OrderStatus::PENDING;
        $excludes[] = OrderStatus::PROCESSING;
        $excludes[] = OrderStatus::CANCEL;
        $excludes[] = OrderStatus::DELIVERED;

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
        $excludes[] = OrderStatus::PROCESSING;
        $excludes[] = OrderStatus::CANCEL;
        $excludes[] = OrderStatus::PENDING;

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
     * @Route("/%admin_route%/change_password", name="admin_change_password")
     * @Template("@admin/change_password.twig")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function changePassword(Request $request)
    {
        $builder = $this->formFactory
            ->createBuilder(ChangePasswordType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ADMIM_CHANGE_PASSWORD_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Member = $this->getUser();
            $salt = $Member->getSalt();
            $password = $form->get('change_password')->getData();

            $encoder = $this->encoderFactory->getEncoder($Member);

            // 2系からのデータ移行でsaltがセットされていない場合はsaltを生成.
            if (empty($salt)) {
                $salt = $encoder->createSalt();
            }

            $password = $encoder->encodePassword($password, $salt);

            $Member
                ->setPassword($password)
                ->setSalt($salt);

            $this->memberRepository->save($Member);

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Member' => $Member
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ADMIN_CHANGE_PASSWORD_COMPLETE, $event);

            $this->addSuccess('admin.change_password.save.complete', 'admin');

            return $this->redirectToRoute('admin_change_password');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * 在庫なし商品の検索結果を表示する.
     *
     * @Route("/%admin_route%/nonstock", name="admin_homepage_nonstock")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchNonStockProducts(Request $request)
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

            return $this->redirectToRoute('admin_product_page', array(
                'page_no' => 1,
                'status' => $this->eccubeConfig['admin_product_stock_status']));
        }
        return $this->redirectToRoute('admin_homepage');
    }

    /**
     * @param $em
     * @param array $excludes
     * @return array
     */
    protected function findOrderStatus($em, array $excludes)
    {
        /* @var $qb QueryBuilder */
        $qb = $em
            ->getRepository('Eccube\Entity\Master\OrderStatus')
            ->createQueryBuilder('os');

        return $qb
            ->where($qb->expr()->notIn('os.id', $excludes))
            ->orderBy('os.sort_no', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $em
     * @param array $excludes
     * @return array
     */
    protected function getOrderEachStatus($em, array $excludes)
    {
        $sql = 'SELECT
                    t1.order_status_id as status,
                    COUNT(t1.id) as count
                FROM
                    dtb_order t1
                WHERE
                    t1.order_status_id NOT IN (:excludes)
                GROUP BY
                    t1.order_status_id
                ORDER BY
                    t1.order_status_id';
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

    /**
     * @param $em
     * @param $dateTime
     * @param array $excludes
     * @return array
     */
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
                    o.OrderStatus NOT IN (:excludes)
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

    /**
     * @param $em
     * @param $dateTime
     * @param array $excludes
     * @return array
     */
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
                    o.OrderStatus NOT IN (:excludes)
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

    /**
     * @param $em
     * @return mixed
     * @throws NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function countNonStockProducts($em)
    {
        /** @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->getRepository('Eccube\Entity\Product')
            ->createQueryBuilder('p')
            ->select('count(DISTINCT p.id)')
            ->innerJoin('p.ProductClasses', 'pc')
            ->where('pc.stock_unlimited = :StockUnlimited AND pc.stock = 0')
            ->setParameter('StockUnlimited', false);

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $em
     * @return mixed
     * @throws NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
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
