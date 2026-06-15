<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Mypage;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\RefundRequest;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\RefundRequestType;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\RefundRequestRepository;
use Eccube\Service\RefundRequestService;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class RefundRequestController extends AbstractController
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly RefundRequestRepository $refundRequestRepository,
        private readonly RefundRequestService $refundRequestService,
    ) {
    }

    /**
     * 返品申請入力画面.
     *
     * @return Response|RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/mypage/refund_request/{order_no}/{order_item_id}', name: 'mypage_refund_request', requirements: ['order_item_id' => '\d+'], methods: ['GET', 'POST'])]
    #[Template(template: 'Mypage/refund_request.twig')]
    public function index(Request $request, string $order_no, int $order_item_id): Response|RedirectResponse|array
    {
        $Order = $this->getValidOrder($order_no);
        $OrderItem = $this->getValidOrderItem($Order, $order_item_id);

        /** @var Customer $Customer */
        $Customer = $this->getUser();

        $RefundRequest = new RefundRequest();
        $RefundRequest->setOrder($Order);
        $RefundRequest->setOrderItem($OrderItem);
        $RefundRequest->setCustomer($Customer);

        $maxQuantity = (int) $OrderItem->getQuantity();
        $form = $this->createForm(RefundRequestType::class, $RefundRequest, [
            'max_quantity' => $maxQuantity,
        ]);

        $event = new EventArgs(
            [
                'form' => $form,
                'Order' => $Order,
                'OrderItem' => $OrderItem,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_REFUND_REQUEST_INDEX_INITIALIZE);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantity = $form->get('quantity')->getData();
            $RefundRequest->setQuantity((string) $quantity);

            switch ($request->get('mode')) {
                case 'confirm':
                    log_info('返品申請確認画面表示', ['order_no' => $order_no, 'order_item_id' => $order_item_id]);

                    return $this->render(
                        'Mypage/refund_request_confirm.twig',
                        [
                            'form' => $form->createView(),
                            'Order' => $Order,
                            'OrderItem' => $OrderItem,
                            'RefundRequest' => $RefundRequest,
                        ]
                    );

                case 'complete':
                    log_info('返品申請処理開始', ['order_no' => $order_no, 'order_item_id' => $order_item_id]);

                    $uploadedFiles = $form->get('files')->getData() ?? [];
                    $this->refundRequestService->createRefundRequest($RefundRequest, $uploadedFiles);

                    log_info('返品申請処理完了', ['id' => $RefundRequest->getId()]);

                    $event = new EventArgs(
                        [
                            'form' => $form,
                            'Order' => $Order,
                            'OrderItem' => $OrderItem,
                            'RefundRequest' => $RefundRequest,
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_REFUND_REQUEST_INDEX_COMPLETE);

                    return $this->redirectToRoute('mypage_refund_request_complete', [
                        'order_no' => $order_no,
                        'order_item_id' => $order_item_id,
                    ]);
            }
        }

        return [
            'form' => $form->createView(),
            'Order' => $Order,
            'OrderItem' => $OrderItem,
            'max_quantity' => $maxQuantity,
        ];
    }

    /**
     * 返品申請完了画面.
     *
     * @return array<string, mixed>
     */
    #[Route(path: '/mypage/refund_request/{order_no}/{order_item_id}/complete', name: 'mypage_refund_request_complete', requirements: ['order_item_id' => '\d+'], methods: ['GET'])]
    #[Template(template: 'Mypage/refund_request_complete.twig')]
    public function complete(string $order_no, int $order_item_id): array
    {
        return [
            'order_no' => $order_no,
            'order_item_id' => $order_item_id,
        ];
    }

    /**
     * 商品別返品申請履歴画面.
     *
     * @return array<string, mixed>
     */
    #[Route(path: '/mypage/refund_request/{order_no}/{order_item_id}/history', name: 'mypage_refund_request_item_history', requirements: ['order_item_id' => '\d+'], methods: ['GET'])]
    #[Template(template: 'Mypage/refund_request_item_history.twig')]
    public function itemHistory(Request $request, string $order_no, int $order_item_id): array
    {
        $Order = $this->getValidOrder($order_no);
        $OrderItem = $this->getValidOrderItem($Order, $order_item_id);

        /** @var Customer $Customer */
        $Customer = $this->getUser();
        $RefundRequests = $this->refundRequestRepository->findByOrderItemAndCustomer($OrderItem, $Customer);

        $event = new EventArgs(
            [
                'Order' => $Order,
                'OrderItem' => $OrderItem,
                'RefundRequests' => $RefundRequests,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_REFUND_REQUEST_ITEM_HISTORY_INITIALIZE);

        return [
            'Order' => $Order,
            'OrderItem' => $OrderItem,
            'RefundRequests' => $RefundRequests,
        ];
    }

    /**
     * エビデンスファイル配信（会員所有チェック付き）.
     */
    #[Route(path: '/mypage/refund_request/file/{refund_request_id}/{file_id}', name: 'mypage_refund_request_file', requirements: ['refund_request_id' => '\d+', 'file_id' => '\d+'], methods: ['GET'])]
    public function downloadFile(int $refund_request_id, int $file_id): BinaryFileResponse
    {
        /** @var Customer $Customer */
        $Customer = $this->getUser();

        $RefundRequest = $this->refundRequestRepository->find($refund_request_id);
        if (!$RefundRequest || $RefundRequest->getCustomer()?->getId() !== $Customer->getId()) {
            throw new NotFoundHttpException();
        }

        $RefundRequestFile = null;
        foreach ($RefundRequest->getRefundRequestFiles() as $File) {
            if ($File->getId() === $file_id) {
                $RefundRequestFile = $File;
                break;
            }
        }
        if (!$RefundRequestFile) {
            throw new NotFoundHttpException();
        }

        $filePath = $this->eccubeConfig['eccube_save_refund_request_file_dir'].'/'.$RefundRequestFile->getFileName();
        $topDir = $this->eccubeConfig['eccube_save_refund_request_file_dir'];

        if (str_contains($filePath, '..')) {
            throw new NotFoundHttpException();
        }

        $realPath = realpath($filePath);
        $realTopDir = realpath($topDir);

        if ($realPath === false || $realTopDir === false || !str_starts_with($realPath, $realTopDir)) {
            throw new NotFoundHttpException();
        }

        return new BinaryFileResponse($realPath);
    }

    /**
     * 注文の所有チェック + 発送済みステータス検証.
     */
    private function getValidOrder(string $order_no): Order
    {
        $this->entityManager->getFilters()->enable('incomplete_order_status_hidden');

        /** @var Order|null $Order */
        $Order = $this->orderRepository->findOneBy([
            'order_no' => $order_no,
            'Customer' => $this->getUser(),
        ]);

        if (!$Order) {
            throw new NotFoundHttpException();
        }

        if ($Order->getOrderStatus()->getId() !== OrderStatus::DELIVERED) {
            throw new AccessDeniedHttpException();
        }

        return $Order;
    }

    /**
     * 注文明細の所有チェック + 商品明細かつ返品許可チェック.
     */
    private function getValidOrderItem(Order $Order, int $order_item_id): OrderItem
    {
        $OrderItem = null;
        foreach ($Order->getOrderItems() as $item) {
            if ($item->getId() === $order_item_id && $item->isProduct()) {
                $OrderItem = $item;
                break;
            }
        }

        if (!$OrderItem) {
            throw new NotFoundHttpException();
        }

        if ($OrderItem->getProduct() && !$OrderItem->getProduct()->isRefundAllowed()) {
            throw new AccessDeniedHttpException();
        }

        return $OrderItem;
    }
}
