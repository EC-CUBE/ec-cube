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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class RefundRequestController extends AbstractController
{
    /** 入力 → 確認画面遷移時にセッションに格納するキー. */
    private const SESSION_KEY = 'eccube.front.refund_request.data';

    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly RefundRequestRepository $refundRequestRepository,
        private readonly RefundRequestService $refundRequestService,
    ) {
    }

    /**
     * 返品申請入力画面.
     *
     * 入力 OK の場合はエビデンスファイルを一時領域に保存し、参照キーをセッションに格納したうえで
     * 確認画面（{@link confirm}）へリダイレクトする。
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
            $quantity = (string) $form->get('quantity')->getData();
            $reason = (string) $form->get('reason')->getData();

            $sessionId = $request->getSession()->getId();

            // 既存の一時データがあれば前回分の一時ファイルを掃除（複数回 confirm に進むケース）
            $previous = $this->session->get(self::SESSION_KEY);
            if (is_array($previous) && isset($previous['temp_files']) && is_array($previous['temp_files'])) {
                foreach ($previous['temp_files'] as $info) {
                    if (isset($info['key'])) {
                        $this->refundRequestService->removeTempFile($sessionId, (string) $info['key']);
                    }
                }
            }

            $uploadedFiles = $form->get('files')->getData() ?? [];
            $tempFiles = [];
            foreach ($uploadedFiles as $uploadedFile) {
                if ($uploadedFile instanceof UploadedFile) {
                    $tempFiles[] = $this->refundRequestService->saveTempFile($uploadedFile, $sessionId);
                }
            }

            $this->session->set(self::SESSION_KEY, [
                'order_no' => $order_no,
                'order_item_id' => $order_item_id,
                'quantity' => $quantity,
                'reason' => $reason,
                'temp_files' => $tempFiles,
            ]);

            log_info('返品申請確認画面へ遷移', ['order_no' => $order_no, 'order_item_id' => $order_item_id, 'files' => count($tempFiles)]);

            return $this->redirectToRoute('mypage_refund_request_confirm', [
                'order_no' => $order_no,
                'order_item_id' => $order_item_id,
            ]);
        }

        return [
            'form' => $form->createView(),
            'Order' => $Order,
            'OrderItem' => $OrderItem,
            'max_quantity' => $maxQuantity,
        ];
    }

    /**
     * 返品申請確認画面.
     *
     * セッションに保持した入力値・一時ファイルを表示する。POST で確定処理を行う。
     *
     * @return Response|RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/mypage/refund_request/{order_no}/{order_item_id}/confirm', name: 'mypage_refund_request_confirm', requirements: ['order_item_id' => '\d+'], methods: ['GET', 'POST'])]
    #[Template(template: 'Mypage/refund_request_confirm.twig')]
    public function confirm(Request $request, string $order_no, int $order_item_id): Response|RedirectResponse|array
    {
        $data = $this->session->get(self::SESSION_KEY);
        if (!is_array($data) || ($data['order_no'] ?? null) !== $order_no || (int) ($data['order_item_id'] ?? 0) !== $order_item_id) {
            return $this->redirectToRoute('mypage_refund_request', [
                'order_no' => $order_no,
                'order_item_id' => $order_item_id,
            ]);
        }

        $Order = $this->getValidOrder($order_no);
        $OrderItem = $this->getValidOrderItem($Order, $order_item_id);

        /** @var Customer $Customer */
        $Customer = $this->getUser();

        $event = new EventArgs(
            [
                'Order' => $Order,
                'OrderItem' => $OrderItem,
                'Customer' => $Customer,
                'data' => $data,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_REFUND_REQUEST_CONFIRM_INITIALIZE);

        if ($request->isMethod('POST')) {
            $this->isTokenValid();

            $sessionId = $request->getSession()->getId();
            $RefundRequest = new RefundRequest();
            $RefundRequest->setOrder($Order);
            $RefundRequest->setOrderItem($OrderItem);
            $RefundRequest->setCustomer($Customer);
            $RefundRequest->setQuantity((string) $data['quantity']);
            $RefundRequest->setReason((string) $data['reason']);

            $tempFiles = is_array($data['temp_files'] ?? null) ? $data['temp_files'] : [];

            log_info('返品申請処理開始', ['order_no' => $order_no, 'order_item_id' => $order_item_id]);

            try {
                $this->refundRequestService->createRefundRequest($RefundRequest, $tempFiles, $sessionId);
            } catch (\RuntimeException $e) {
                log_error('返品申請処理失敗', ['error' => $e->getMessage()]);
                $this->addError('front.mypage.refund_request.error');

                return $this->redirectToRoute('mypage_refund_request', [
                    'order_no' => $order_no,
                    'order_item_id' => $order_item_id,
                ]);
            }

            $this->session->remove(self::SESSION_KEY);

            log_info('返品申請処理完了', ['id' => $RefundRequest->getId()]);

            $event = new EventArgs(
                [
                    'Order' => $Order,
                    'OrderItem' => $OrderItem,
                    'RefundRequest' => $RefundRequest,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_REFUND_REQUEST_CONFIRM_COMPLETE);

            return $this->redirectToRoute('mypage_refund_request_complete', [
                'order_no' => $order_no,
                'order_item_id' => $order_item_id,
            ]);
        }

        return [
            'Order' => $Order,
            'OrderItem' => $OrderItem,
            'data' => $data,
            'temp_files' => is_array($data['temp_files'] ?? null) ? $data['temp_files'] : [],
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

        $realPath = realpath($filePath);
        $realTopDir = realpath($topDir);

        if ($realPath === false || $realTopDir === false || !str_starts_with($realPath, $realTopDir.DIRECTORY_SEPARATOR)) {
            throw new NotFoundHttpException();
        }

        $response = new BinaryFileResponse($realPath);
        $response->headers->set('Content-Type', (string) $RefundRequestFile->getMimeType());
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }

    /**
     * 確認画面プレビュー用に、一時保存中のエビデンスファイルを配信する.
     * 自セッションに紐づく一時ファイルのみ参照可。
     */
    #[Route(path: '/mypage/refund_request/temp_file/{key}', name: 'mypage_refund_request_temp_file', requirements: ['key' => '[A-Za-z0-9._-]+'], methods: ['GET'])]
    public function downloadTempFile(Request $request, string $key): BinaryFileResponse
    {
        $data = $this->session->get(self::SESSION_KEY);
        if (!is_array($data) || !isset($data['temp_files']) || !is_array($data['temp_files'])) {
            throw new NotFoundHttpException();
        }

        $matched = null;
        foreach ($data['temp_files'] as $info) {
            if (isset($info['key']) && $info['key'] === $key) {
                $matched = $info;
                break;
            }
        }
        if ($matched === null) {
            throw new NotFoundHttpException();
        }

        $sessionId = $request->getSession()->getId();
        $real = $this->refundRequestService->getTempFilePath($sessionId, $key);
        if ($real === null) {
            throw new NotFoundHttpException();
        }

        $response = new BinaryFileResponse($real);
        $response->headers->set('Content-Type', (string) ($matched['mime_type'] ?? 'application/octet-stream'));
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
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
