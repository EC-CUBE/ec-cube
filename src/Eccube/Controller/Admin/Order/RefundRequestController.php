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

namespace Eccube\Controller\Admin\Order;

use Eccube\Controller\AbstractController;
use Eccube\Entity\RefundRequest;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\RefundRequestEditType;
use Eccube\Form\Type\Admin\SearchRefundRequestType;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Repository\RefundRequestRepository;
use Eccube\Service\RefundRequestService;
use Eccube\Util\FormUtil;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class RefundRequestController extends AbstractController
{
    public function __construct(
        private readonly RefundRequestRepository $refundRequestRepository,
        private readonly RefundRequestService $refundRequestService,
        private readonly PageMaxRepository $pageMaxRepository,
        private readonly PaginatorInterface $paginator,
    ) {
    }

    /**
     * 返品申請一覧画面.
     *
     * @return array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/order/refund_request', name: 'admin_refund_request', methods: ['GET', 'POST'])]
    #[Route(path: '/%eccube_admin_route%/order/refund_request/page/{page_no}', name: 'admin_refund_request_page', requirements: ['page_no' => '\d+'], methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Order/refund_request.twig')]
    public function index(Request $request, ?int $page_no = null): array
    {
        $builder = $this->formFactory->createBuilder(SearchRefundRequestType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_REFUND_REQUEST_INDEX_INITIALIZE);

        $searchForm = $builder->getForm();

        $page_count = $this->session->get('eccube.admin.refund_request.search.page_count',
            $this->eccubeConfig->get('eccube_default_page_count'));

        $page_count_param = (int) $request->get('page_count');
        $pageMaxis = $this->pageMaxRepository->findAll();

        if ($page_count_param) {
            foreach ($pageMaxis as $pageMax) {
                if ($page_count_param == $pageMax->getName()) {
                    $page_count = $pageMax->getName();
                    $this->session->set('eccube.admin.refund_request.search.page_count', $page_count);
                    break;
                }
            }
        }

        if ('POST' === $request->getMethod()) {
            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $page_no = 1;
                $searchData = $searchForm->getData();

                $this->session->set('eccube.admin.refund_request.search', FormUtil::getViewData($searchForm));
                $this->session->set('eccube.admin.refund_request.search.page_no', $page_no);
            } else {
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
                if ($page_no) {
                    $this->session->set('eccube.admin.refund_request.search.page_no', (int) $page_no);
                } else {
                    $page_no = $this->session->get('eccube.admin.refund_request.search.page_no', 1);
                }
                $viewData = $this->session->get('eccube.admin.refund_request.search', []);
                $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
            } else {
                $page_no = 1;
                $viewData = [];
                $searchData = FormUtil::submitAndGetData($searchForm, $viewData);

                $this->session->set('eccube.admin.refund_request.search', $viewData);
                $this->session->set('eccube.admin.refund_request.search.page_no', $page_no);
            }
        }

        $qb = $this->refundRequestRepository->getQueryBuilderBySearchData($searchData);

        $event = new EventArgs(
            [
                'qb' => $qb,
                'searchData' => $searchData,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_REFUND_REQUEST_INDEX_SEARCH);

        $pagination = $this->paginator->paginate(
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
        ];
    }

    /**
     * 返品申請詳細画面.
     *
     * @return Response|array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/order/refund_request/{id}/edit', name: 'admin_refund_request_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Order/refund_request_edit.twig')]
    public function edit(Request $request, RefundRequest $RefundRequest): Response|array
    {
        $form = $this->createForm(RefundRequestEditType::class, null, [
            'refund_request' => $RefundRequest,
        ]);
        $form->get('admin_note')->setData($RefundRequest->getAdminNote());

        $event = new EventArgs(
            [
                'form' => $form,
                'RefundRequest' => $RefundRequest,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_REFUND_REQUEST_EDIT_INITIALIZE);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminNote = $form->get('admin_note')->getData();
            $RefundRequest->setAdminNote($adminNote);
            $this->entityManager->flush();

            $transition = $form->get('transition')->getData();
            if ($transition) {
                try {
                    $this->refundRequestService->changeStatus($RefundRequest, $transition);
                    $this->addSuccess('admin.common.save_complete', 'admin');
                } catch (\InvalidArgumentException $e) {
                    $this->addError('admin.order.refund_request.transition_error', 'admin');
                }
            } else {
                $this->addSuccess('admin.common.save_complete', 'admin');
            }

            $event = new EventArgs(
                [
                    'form' => $form,
                    'RefundRequest' => $RefundRequest,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_REFUND_REQUEST_EDIT_COMPLETE);

            return $this->redirectToRoute('admin_refund_request_edit', ['id' => $RefundRequest->getId()]);
        }

        $availableTransitions = $this->refundRequestService->getAvailableTransitions($RefundRequest);

        return [
            'form' => $form->createView(),
            'RefundRequest' => $RefundRequest,
            'availableTransitions' => $availableTransitions,
        ];
    }

    /**
     * ステータス変更 API.
     */
    #[Route(path: '/%eccube_admin_route%/order/refund_request/{id}/update_status', name: 'admin_refund_request_update_status', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function updateStatus(Request $request, RefundRequest $RefundRequest): JsonResponse
    {
        $this->isTokenValid();

        $transition = $request->get('transition');
        if (!$transition) {
            throw new BadRequestHttpException();
        }

        try {
            $this->refundRequestService->changeStatus($RefundRequest, $transition);

            return $this->json([
                'success' => true,
                'status' => (string) $RefundRequest->getRefundRequestStatus(),
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => trans('admin.order.refund_request.transition_error'),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * CSVエクスポート.
     */
    #[Route(path: '/%eccube_admin_route%/order/refund_request/export', name: 'admin_refund_request_export', methods: ['GET'])]
    public function export(Request $request): StreamedResponse
    {
        set_time_limit(0);

        $this->entityManager->getConfiguration()->setSQLLogger();

        $viewData = $this->session->get('eccube.admin.refund_request.search', []);
        $searchForm = $this->formFactory->createBuilder(SearchRefundRequestType::class)->getForm();
        $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
        $qb = $this->refundRequestRepository->getQueryBuilderBySearchData($searchData);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($qb): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            $header = [
                trans('admin.order.refund_request.id'),
                trans('admin.order.refund_request.order_no'),
                trans('admin.order.refund_request.customer'),
                trans('admin.order.refund_request.product'),
                trans('admin.order.refund_request.status'),
                trans('admin.order.refund_request.quantity'),
                trans('admin.order.refund_request.reason'),
                trans('admin.order.refund_request.create_date'),
                trans('admin.order.refund_request.update_date'),
            ];
            fputcsv($out, $header);

            foreach ($qb->getQuery()->toIterable() as $RefundRequest) {
                $row = [
                    $RefundRequest->getId(),
                    $RefundRequest->getOrder()?->getOrderNo(),
                    $RefundRequest->getCustomer() ? $RefundRequest->getCustomer()->getName01().' '.$RefundRequest->getCustomer()->getName02() : '',
                    $RefundRequest->getOrderItem()?->getProductName(),
                    (string) $RefundRequest->getRefundRequestStatus(),
                    $RefundRequest->getQuantity(),
                    $RefundRequest->getReason(),
                    $RefundRequest->getCreateDate()?->format('Y-m-d H:i:s'),
                    $RefundRequest->getUpdateDate()?->format('Y-m-d H:i:s'),
                ];
                fputcsv($out, $row);
                $this->entityManager->detach($RefundRequest);
            }

            fclose($out);
        });

        $filename = 'refund_request_'.(new \DateTime())->format('YmdHis').'.csv';
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);

        log_info('返品申請CSV出力', [$filename]);

        return $response;
    }

    /**
     * エビデンスファイル配信（管理画面）.
     */
    #[Route(path: '/%eccube_admin_route%/order/refund_request/{id}/file/{file_id}', name: 'admin_refund_request_file', requirements: ['id' => '\d+', 'file_id' => '\d+'], methods: ['GET'])]
    public function downloadFile(RefundRequest $RefundRequest, int $file_id): BinaryFileResponse
    {
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
}
