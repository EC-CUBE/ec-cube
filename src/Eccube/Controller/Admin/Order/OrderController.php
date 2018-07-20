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

namespace Eccube\Controller\Admin\Order;

use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Csv;
use Eccube\Entity\ExportCsvRow;
use Eccube\Entity\Master\CsvType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\SearchOrderType;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\Master\SexRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Service\CsvExportService;
use Eccube\Service\OrderStateMachine;
use Eccube\Util\FormUtil;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderController extends AbstractController
{
    /**
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    /**
     * @var CsvExportService
     */
    protected $csvExportService;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var SexRepository
     */
    protected $sexRepository;

    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @var PageMaxRepository
     */
    protected $pageMaxRepository;

    /**
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var OrderStateMachine
     */
    protected $orderStateMachine;

    /**
     * OrderController constructor.
     *
     * @param PurchaseFlow $orderPurchaseFlow
     * @param CsvExportService $csvExportService
     * @param CustomerRepository $customerRepository
     * @param PaymentRepository $paymentRepository
     * @param SexRepository $sexRepository
     * @param OrderStatusRepository $orderStatusRepository
     * @param PageMaxRepository $pageMaxRepository
     * @param ProductStatusRepository $productStatusRepository
     * @param OrderRepository $orderRepository
     * @param OrderStateMachine $orderStateMachine;
     */
    public function __construct(
        PurchaseFlow $orderPurchaseFlow,
        CsvExportService $csvExportService,
        CustomerRepository $customerRepository,
        PaymentRepository $paymentRepository,
        SexRepository $sexRepository,
        OrderStatusRepository $orderStatusRepository,
        PageMaxRepository $pageMaxRepository,
        ProductStatusRepository $productStatusRepository,
        OrderRepository $orderRepository,
        ValidatorInterface $validator,
        OrderStateMachine $orderStateMachine
    ) {
        $this->purchaseFlow = $orderPurchaseFlow;
        $this->csvExportService = $csvExportService;
        $this->customerRepository = $customerRepository;
        $this->paymentRepository = $paymentRepository;
        $this->sexRepository = $sexRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->pageMaxRepository = $pageMaxRepository;
        $this->productStatusRepository = $productStatusRepository;
        $this->orderRepository = $orderRepository;
        $this->validator = $validator;
        $this->orderStateMachine = $orderStateMachine;
    }

    /**
     * 受注一覧画面.
     *
     * - 検索条件, ページ番号, 表示件数はセッションに保持されます.
     * - クエリパラメータでresume=1が指定された場合、検索条件, ページ番号, 表示件数をセッションから復旧します.
     * - 各データの, セッションに保持するアクションは以下の通りです.
     *   - 検索ボタン押下時
     *      - 検索条件をセッションに保存します
     *      - ページ番号は1で初期化し、セッションに保存します。
     *   - 表示件数変更時
     *      - クエリパラメータpage_countをセッションに保存します。
     *      - ただし, mtb_page_maxと一致しない場合, eccube_default_page_countが保存されます.
     *   - ページング時
     *      - URLパラメータpage_noをセッションに保存します.
     *   - 初期表示
     *      - 検索条件は空配列, ページ番号は1で初期化し, セッションに保存します.
     *
     * @Route("/%eccube_admin_route%/order", name="admin_order")
     * @Route("/%eccube_admin_route%/order/page/{page_no}", requirements={"page_no" = "\d+"}, name="admin_order_page")
     * @Template("@admin/Order/index.twig")
     */
    public function index(Request $request, $page_no = null, PaginatorInterface $paginator)
    {
        $builder = $this->formFactory
            ->createBuilder(SearchOrderType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_INITIALIZE, $event);

        $searchForm = $builder->getForm();

        /**
         * ページの表示件数は, 以下の順に優先される.
         * - リクエストパラメータ
         * - セッション
         * - デフォルト値
         * また, セッションに保存する際は mtb_page_maxと照合し, 一致した場合のみ保存する.
         **/
        $page_count = $this->session->get('eccube.admin.order.search.page_count',
                $this->eccubeConfig->get('eccube_default_page_count'));

        $page_count_param = (int) $request->get('page_count');
        $pageMaxis = $this->pageMaxRepository->findAll();

        if ($page_count_param) {
            foreach ($pageMaxis as $pageMax) {
                if ($page_count_param == $pageMax->getName()) {
                    $page_count = $pageMax->getName();
                    $this->session->set('eccube.admin.order.search.page_count', $page_count);
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
                $this->session->set('eccube.admin.order.search', FormUtil::getViewData($searchForm));
                $this->session->set('eccube.admin.order.search.page_no', $page_no);
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
                    $this->session->set('eccube.admin.order.search.page_no', (int) $page_no);
                } else {
                    // 他画面から遷移した場合.
                    $page_no = $this->session->get('eccube.admin.order.search.page_no', 1);
                }
                $viewData = $this->session->get('eccube.admin.order.search', []);
                $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
            } else {
                /**
                 * 初期表示の場合.
                 */
                $page_no = 1;
                $viewData = [];

                if ($statusId = (int) $request->get('order_status_id')) {
                    $viewData = ['status' => $statusId];
                }

                $searchData = FormUtil::submitAndGetData($searchForm, $viewData);

                // セッション中の検索条件, ページ番号を初期化.
                $this->session->set('eccube.admin.order.search', $viewData);
                $this->session->set('eccube.admin.order.search.page_no', $page_no);
            }
        }

        $qb = $this->orderRepository->getQueryBuilderBySearchDataForAdmin($searchData);

        $event = new EventArgs(
            [
                'qb' => $qb,
                'searchData' => $searchData,
            ],
            $request
        );

        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_SEARCH, $event);

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
            'OrderStatuses' => $this->orderStatusRepository->findBy([], ['sort_no' => 'ASC']),
        ];
    }

    /**
     * @Method("POST")
     * @Route("/%eccube_admin_route%/order/bulk_delete", name="admin_order_bulk_delete")
     */
    public function bulkDelete(Request $request)
    {
        $this->isTokenValid();
        $ids = $request->get('ids');
        foreach ($ids as $order_id) {
            $Order = $this->orderRepository
                ->find($order_id);
            if ($Order) {
                $this->entityManager->remove($Order);
                log_info('受注削除', [$Order->getId()]);
            }
        }

        $this->entityManager->flush();

        $this->addSuccess('admin.order.delete.complete', 'admin');

        return $this->redirect($this->generateUrl('admin_order', ['resume' => Constant::ENABLED]));
    }

    /**
     * 受注CSVの出力.
     *
     * @Route("/%eccube_admin_route%/order/export/order", name="admin_order_export_order")
     *
     * @param Request $request
     *
     * @return StreamedResponse
     */
    public function exportOrder(Request $request)
    {
        $filename = 'order_'.(new \DateTime())->format('YmdHis').'.csv';
        $response = $this->exportCsv($request, CsvType::CSV_TYPE_ORDER, $filename);
        log_info('受注CSV出力ファイル名', [$filename]);

        return $response;
    }

    /**
     * 配送CSVの出力.
     *
     * @Route("/%eccube_admin_route%/order/export/shipping", name="admin_order_export_shipping")
     *
     * @param Request $request
     *
     * @return StreamedResponse
     */
    public function exportShipping(Request $request)
    {
        $filename = 'shipping_'.(new \DateTime())->format('YmdHis').'.csv';
        $response = $this->exportCsv($request, CsvType::CSV_TYPE_SHIPPING, $filename);
        log_info('配送CSV出力ファイル名', [$filename]);

        return $response;
    }

    /**
     * @param Request $request
     * @param $csvTypeId
     * @param $fileName
     *
     * @return StreamedResponse
     */
    private function exportCsv(Request $request, $csvTypeId, $fileName)
    {
        // タイムアウトを無効にする.
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $this->entityManager;
        $em->getConfiguration()->setSQLLogger(null);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($request, $csvTypeId) {
            // CSV種別を元に初期化.
            $this->csvExportService->initCsvType($csvTypeId);

            // ヘッダ行の出力.
            $this->csvExportService->exportHeader();

            // 受注データ検索用のクエリビルダを取得.
            $qb = $this->csvExportService
                ->getOrderQueryBuilder($request);

            // データ行の出力.
            $this->csvExportService->setExportQueryBuilder($qb);
            $this->csvExportService->exportData(function ($entity, $csvService) use ($request) {
                $Csvs = $csvService->getCsvs();

                $Order = $entity;
                $OrderItems = $Order->getOrderItems();

                foreach ($OrderItems as $OrderItem) {
                    $ExportCsvRow = new ExportCsvRow();

                    // CSV出力項目と合致するデータを取得.
                    foreach ($Csvs as $Csv) {
                        // 受注データを検索.
                        $ExportCsvRow->setData($csvService->getData($Csv, $Order));
                        if ($ExportCsvRow->isDataNull()) {
                            // 受注データにない場合は, 受注明細を検索.
                            $ExportCsvRow->setData($csvService->getData($Csv, $OrderItem));
                        }
                        if ($ExportCsvRow->isDataNull() && $Shipping = $OrderItem->getShipping()) {
                            // 受注明細データにない場合は, 出荷を検索.
                            $ExportCsvRow->setData($csvService->getData($Csv, $Shipping));
                        }

                        $event = new EventArgs(
                            [
                                'csvService' => $csvService,
                                'Csv' => $Csv,
                                'OrderItem' => $OrderItem,
                                'ExportCsvRow' => $ExportCsvRow,
                            ],
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

        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);
        $response->send();

        return $response;
    }

    /**
     * Update to order status
     *
     * @Method("PUT")
     * @Route("/%eccube_admin_route%/shipping/{id}/order_status", requirements={"id" = "\d+"}, name="admin_shipping_update_order_status")
     *
     * @param Request $request
     * @param Shipping $shipping
     *
     * @return RedirectResponse
     */
    public function updateOrderStatus(Request $request, Shipping $Shipping)
    {
        if (!($request->isXmlHttpRequest() && $this->isTokenValid())) {
            return $this->json(['status' => 'NG'], 400);
        }

        $Order = $Shipping->getOrder();
        $OrderStatus = $this->entityManager->find(OrderStatus::class, $request->get('order_status'));

        $result = [];
        try {
            // 発送済みに変更された場合は、関連する出荷がすべて出荷済みになったら OrderStatus を変更する
            if (OrderStatus::DELIVERED == $OrderStatus->getId()) {
                if (!$Shipping->getShippingDate()) {
                    $Shipping->setShippingDate(new \DateTime());
                    $this->entityManager->flush($Shipping);
                }
                $RelateShippings = $Order->getShippings();
                $allShipped = true;
                foreach ($RelateShippings as $RelateShipping) {
                    if (!$RelateShipping->getShippingDate()) {
                        $allShipped = false;
                        break;
                    }
                }
                if ($allShipped) {
                    if ($this->orderStateMachine->can($Order, $OrderStatus)) {
                        $this->orderStateMachine->apply($Order, $OrderStatus);
                    } else {
                        $from = $Order->getOrderStatus()->getName();
                        $to = $OrderStatus->getName();
                        $result = ['message' => sprintf('%s: %s から %s へのステータス変更はできません', $Shipping->getId(), $from, $to)];
                    }
                }
            } else {
                if ($this->orderStateMachine->can($Order, $OrderStatus)) {
                    $this->orderStateMachine->apply($Order, $OrderStatus);
                } else {
                    $from = $Order->getOrderStatus()->getName();
                    $to = $OrderStatus->getName();
                    $result = ['message' => sprintf('%s: %s から %s へのステータス変更はできません', $Shipping->getId(), $from, $to)];
                }
            }
            $this->entityManager->flush($Order);

            // 会員の場合、購入回数、購入金額などを更新
            if ($Customer = $Order->getCustomer()) {
                $this->orderRepository->updateOrderSummary($Customer);
                $this->entityManager->flush($Customer);
            }
            log_info('対応状況一括変更処理完了', [$Order->getId()]);
        } catch (\Exception $e) {
            log_error('予期しないエラーです', [$e->getMessage()]);

            return $this->json(['status' => 'NG'], 500);
        }

        return $this->json(array_merge(['status' => 'OK'], $result));
    }

    /**
     * Bulk action to order status
     *
     * @Method("POST")
     * @Route("/%eccube_admin_route%/order/bulk/order-status/{id}", requirements={"id" = "\d+"}, name="admin_order_bulk_order_status")
     *
     * @param Request $request
     * @param OrderStatus $OrderStatus
     *
     * @return RedirectResponse
     *
     * @deprecated 使用していない
     */
    public function bulkOrderStatus(Request $request, OrderStatus $OrderStatus)
    {
        $this->isTokenValid();

        /** @var Order[] $Orders */
        $Orders = $this->orderRepository->findBy(['id' => $request->get('ids')]);

        $count = 0;
        foreach ($Orders as $Order) {
            try {
                // TODO: should support event for plugin customize
                // 編集前の受注情報を保持
                $OriginOrder = clone $Order;

                $Order->setOrderStatus($OrderStatus);

                $purchaseContext = new PurchaseContext($OriginOrder, $OriginOrder->getCustomer());

                $flowResult = $this->purchaseFlow->validate($Order, $purchaseContext);
                if ($flowResult->hasWarning()) {
                    foreach ($flowResult->getWarning() as $warning) {
                        $msg = $this->translator->trans('admin.order.index.bulk_warning', [
                            '%orderId%' => $Order->getId(),
                            '%message%' => $warning->getMessage(),
                        ]);
                        $this->addWarning($msg, 'admin');
                    }
                }

                if ($flowResult->hasError()) {
                    foreach ($flowResult->getErrors() as $error) {
                        $msg = $this->translator->trans('admin.order.index.bulk_error', [
                            '%orderId%' => $Order->getId(),
                            '%message%' => $error->getMessage(),
                        ]);
                        $this->addError($msg, 'admin');
                    }
                    continue;
                }

                try {
                    $this->purchaseFlow->commit($Order, $purchaseContext);
                } catch (PurchaseException $e) {
                    $msg = $this->translator->trans('admin.order.index.bulk_error', [
                        '%orderId%' => $Order->getId(),
                        '%message%' => $e->getMessage(),
                    ]);
                    $this->addError($msg, 'admin');
                    continue;
                }

                $this->orderRepository->save($Order);

                $count++;
            } catch (\Exception $e) {
                $this->addError('#'.$Order->getId().': '.$e->getMessage(), 'admin');
            }
        }
        try {
            if ($count) {
                $this->entityManager->flush();
                $msg = $this->translator->trans('admin.order.index.bulk_order_status_success_count', [
                    '%count%' => $count,
                    '%status%' => $OrderStatus->getName(),
                ]);
                $this->addSuccess($msg, 'admin');
            }
        } catch (\Exception $e) {
            log_error('Bulk order status error', [$e]);
            $this->addError('admin.flash.register_failed', 'admin');
        }

        return $this->redirectToRoute('admin_order', ['resume' => Constant::ENABLED]);
    }

    /**
     * Update to Tracking number.
     *
     * @Method("PUT")
     * @Route("/%eccube_admin_route%/shipping/{id}/tracking_number", requirements={"id" = "\d+"}, name="admin_shipping_update_tracking_number")
     *
     * @param Request $request
     * @param Shipping $shipping
     *
     * @return Response
     */
    public function updateTrackingNumber(Request $request, Shipping $shipping)
    {
        if (!($request->isXmlHttpRequest() && $this->isTokenValid())) {
            return $this->json(['status' => 'NG'], 400);
        }

        $trackingNumber = mb_convert_kana($request->get('tracking_number'), 'a', 'utf-8');
        /** @var \Symfony\Component\Validator\ConstraintViolationListInterface $errors */
        $errors = $this->validator->validate(
            $trackingNumber,
            [
                new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                new Assert\Regex(
                    ['pattern' => '/^[0-9a-zA-Z-]+$/u', 'message' => trans('form.type.admin.nottrackingnumberstyle')]
                ),
            ]
        );

        if ($errors->count() != 0) {
            log_info('送り状番号入力チェックエラー');
            $messages = [];
            /** @var \Symfony\Component\Validator\ConstraintViolationInterface $error */
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }

            return $this->json(['status' => 'NG', 'messages' => $messages], 400);
        }

        try {
            $shipping->setTrackingNumber($trackingNumber);
            $this->entityManager->flush($shipping);
            log_info('送り状番号変更処理完了', [$shipping->getId()]);
            $message = ['status' => 'OK', 'shipping_id' => $shipping->getId(), 'tracking_number' => $trackingNumber];

            return $this->json($message);
        } catch (\Exception $e) {
            log_error('予期しないエラー', [$e->getMessage()]);

            return $this->json(['status' => 'NG'], 500);
        }
    }
}
