<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SamplePayment\Controller\Admin;

use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Order;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Util\FormUtil;
use Knp\Component\Pager\PaginatorInterface;
use Plugin\SamplePayment\Form\Type\Admin\SearchPaymentType;
use Plugin\SamplePayment\Repository\PaymentStatusRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaymentController extends AbstractController
{
    /**
     * @var PaymentStatusRepository
     */
    protected $paymentStatusRepository;

    /**
     * @var PageMaxRepository
     */
    protected $pageMaxRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var array
     */
    protected $bulkActions = [
        ['id' => 1, 'name' => '一括売上'],
        ['id' => 2, 'name' => '一括取消'],
        ['id' => 3, 'name' => '一括再オーソリ'],
    ];

    /**
     * PaymentController constructor.
     *
     * @param OrderStatusRepository $orderStatusRepository
     */
    public function __construct(PaymentStatusRepository $paymentStatusRepository, PageMaxRepository $pageMaxRepository, OrderRepository $orderRepository)
    {
        $this->paymentStatusRepository = $paymentStatusRepository;
        $this->pageMaxRepository = $pageMaxRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * 決済状況一覧画面
     *
     * @Route("/%eccube_admin_route%/sample_payment/order/payment/list", name="admin_sample_payment_list")
     * @Route("/%eccube_admin_route%/sample_payment/order/payment/list/{page_no}", requirements={"page_no" = "\d+"}, name="admin_sample_payment_list_pageno")
     * @Template("@SamplePayment/admin/list.twig")
     */
    public function index(Request $request, $page_no = null, PaginatorInterface $paginator)
    {
        $searchForm = $this->createForm(SearchPaymentType::class);

        /**
         * ページの表示件数は, 以下の順に優先される.
         * - リクエストパラメータ
         * - セッション
         * - デフォルト値
         * また, セッションに保存する際は mtb_page_maxと照合し, 一致した場合のみ保存する.
         **/
        $page_count = $this->session->get('sample_payment.admin.order.search.page_count',
            $this->eccubeConfig->get('eccube_default_page_count'));

        $page_count_param = (int) $request->get('page_count');
        $pageMaxis = $this->pageMaxRepository->findAll();

        if ($page_count_param) {
            foreach ($pageMaxis as $pageMax) {
                if ($page_count_param == $pageMax->getName()) {
                    $page_count = $pageMax->getName();
                    $this->session->set('sample_payment.admin.order.search.page_count', $page_count);
                    break;
                }
            }
        }

        if ('POST' === $request->getMethod()) {
            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                /**
                 * 検索が実行された場合は, セッションに検索条件を保存する.
                 * ページ番号は最初のページ番号に初期化する.
                 */
                $page_no = 1;
                $searchData = $searchForm->getData();

                // 検索条件, ページ番号をセッションに保持.
                $this->session->set('sample_payment.admin.order.search', FormUtil::getViewData($searchForm));
                $this->session->set('sample_payment.admin.order.search.page_no', $page_no);
            } else {
                // 検索エラーの際は, 詳細検索枠を開いてエラー表示する.
                return [
                    'searchForm' => $searchForm->createView(),
                    'pagination' => [],
                    'pageMaxis' => $pageMaxis,
                    'page_no' => $page_no,
                    'page_count' => $page_count,
                    'has_errors' => true,
                ];
            }
        } else {
            if (null !== $page_no || $request->get('resume')) {
                /*
                 * ページ送りの場合または、他画面から戻ってきた場合は, セッションから検索条件を復旧する.
                 */
                if ($page_no) {
                    // ページ送りで遷移した場合.
                    $this->session->set('sample_payment.admin.order.search.page_no', (int) $page_no);
                } else {
                    // 他画面から遷移した場合.
                    $page_no = $this->session->get('sample_payment.admin.order.search.page_no', 1);
                }
                $viewData = $this->session->get('sample_payment.admin.order.search', []);
                $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
            } else {
                /**
                 * 初期表示の場合.
                 */
                $page_no = 1;
                $searchData = [];

                // セッション中の検索条件, ページ番号を初期化.
                $this->session->set('eccube.admin.order.search', $searchData);
                $this->session->set('eccube.admin.order.search.page_no', $page_no);
            }
        }

        $qb = $this->createQueryBuilder($searchData);
        $pagination = $paginator->paginate(
            $qb,
            $page_no,
            $page_count
        );

        return [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_count' => $page_count,
            'has_errors' => false,
            'bulkActions' => $this->bulkActions,
        ];
    }

    /**
     * 一括処理.
     *
     * @Method("POST")
     * @Route("/%eccube_admin_route%/sample_payment/order/payment/bulk_action/{id}", requirements={"id" = "\d+"}, name="admin_sample_payment_bulk_action")
     */
    public function bulkAction(Request $request, $id)
    {
        if (!isset($this->bulkActions[$id])) {
            throw new BadRequestHttpException();
        }

        $this->isTokenValid();

        /** @var Order[] $Orders */
        $Orders = $this->orderRepository->findBy(['id' => $request->get('ids')]);
        $count = 0;

        foreach ($Orders as $Order) {
            switch ($id) {
                // 一括売上
                case 1:
                    // 通信処理
                    // Order等の更新処理
                    break;
                // 一括取消
                case 2:
                    // 通信処理
                    // Order等の更新処理
                break;
                // 一括再オーソリ
                case 3:
                    // 通信処理
                    // Order等の更新処理
                break;
            }
            $this->entityManager->flush($Order);
            $count++;
        }

        $this->addSuccess($count.'件を処理しました', 'admin');

        return $this->redirectToRoute('admin_sample_payment_list_pageno', ['resume' => Constant::ENABLED]);
    }

    /**
     * 受注編集 > 決済のキャンセル処理
     *
     * @Method("POST")
     * @Route("/%eccube_admin_route%/sample_payment/order/cancel/{id}", requirements={"id" = "\d+"}, name="admin_sample_payment_order_cancel")
     */
    public function cancel(Request $request, Order $Order)
    {
        if ($request->isXmlHttpRequest() && $this->isTokenValid()) {
            // 通信処理

            $this->addSuccess('取消処理を行いました', 'admin');

            return $this->json([]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * 受注編集 > 決済の金額変更
     *
     * @Method("POST")
     * @Route("/%eccube_admin_route%/sample_payment/order/change_price/{id}", requirements={"id" = "\d+"}, name="admin_sample_payment_order_change_price")
     */
    public function changePrice(Request $request, Order $Order)
    {
        if ($request->isXmlHttpRequest() && $this->isTokenValid()) {
            // 通信処理

            $this->addSuccess('金額変更処理を行いました', 'admin');

            return $this->json([]);
        }

        throw new BadRequestHttpException();
    }

    private function createQueryBuilder(array $searchData)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('o')
            ->from(Order::class, 'o')
            ->orderBy('o.order_date', 'DESC')
            ->addOrderBy('o.id', 'DESC');

        if (!empty($searchData['Payments']) && count($searchData['Payments']) > 0) {
            $qb->andWhere($qb->expr()->in('o.Payment', ':Payments'))
                ->setParameter('Payments', $searchData['Payments']);
        }

        if (!empty($searchData['OrderStatuses']) && count($searchData['OrderStatuses']) > 0) {
            $qb->andWhere($qb->expr()->in('o.OrderStatus', ':OrderStatuses'))
                ->setParameter('OrderStatuses', $searchData['OrderStatuses']);
        }

        if (!empty($searchData['PaymentStatuses']) && count($searchData['PaymentStatuses']) > 0) {
            $qb->andWhere($qb->expr()->in('o.SamplePaymentPaymentStatus', ':PaymentStatuses'))
                ->setParameter('PaymentStatuses', $searchData['PaymentStatuses']);
        }

        return $qb;
    }
}
