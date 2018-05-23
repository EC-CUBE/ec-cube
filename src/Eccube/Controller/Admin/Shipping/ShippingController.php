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

namespace Eccube\Controller\Admin\Shipping;

use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\ShippingStatus;
use Eccube\Entity\Shipping;
use Eccube\Entity\OrderItem;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\SearchShippingType;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Repository\Master\ShippingStatusRepository;
use Eccube\Repository\ShippingRepository;
use Eccube\Service\MailService;
use Eccube\Util\FormUtil;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingController extends AbstractController
{
    /**
     * @var ShippingRepository
     */
    protected $shippingRepository;

    /**
     * @var PageMaxRepository
     */
    protected $pageMaxRepository;

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var ShippingStatusRepository
     */
    protected $shippingStatusRepository;

    /**
     * ShippingController constructor.
     *
     * @param ShippingRepository $shippingRepository
     * @param PageMaxRepository $pageMaxRepository
     * @param ShippingStatusRepository $shippingStatusRepository
     * @param MailService $mailService
     */
    public function __construct(
        ShippingRepository $shippingRepository,
        PageMaxRepository $pageMaxRepository,
        ShippingStatusRepository $shippingStatusRepository,
        MailService $mailService
    ) {
        $this->shippingRepository = $shippingRepository;
        $this->pageMaxRepository = $pageMaxRepository;
        $this->shippingStatusRepository = $shippingStatusRepository;
        $this->mailService = $mailService;
    }

    /**
     * @Route("/%eccube_admin_route%/shipping", name="admin_shipping")
     * @Route("/%eccube_admin_route%/shipping/page/{page_no}", name="admin_shipping_page")
     * @Template("@admin/Shipping/index.twig")
     */
    public function index(Request $request, $page_no = null, PaginatorInterface $paginator)
    {
        $builder = $this->formFactory
            ->createBuilder(SearchShippingType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SHIPPING_INDEX_INITIALIZE, $event);

        $searchForm = $builder->getForm();

        /**
         * ページの表示件数は, 以下の順に優先される.
         * - リクエストパラメータ
         * - セッション
         * - デフォルト値
         * また, セッションに保存する際は mtb_page_maxと照合し, 一致した場合のみ保存する.
         **/
        $page_count = $this->session->get('eccube.admin.shipping.search.page_count',
            $this->eccubeConfig->get('eccube_default_page_count'));

        $page_count_param = (int) $request->get('page_count');
        $pageMaxis = $this->pageMaxRepository->findAll();

        if ($page_count_param) {
            foreach ($pageMaxis as $pageMax) {
                if ($page_count_param == $pageMax->getName()) {
                    $page_count = $pageMax->getName();
                    $this->session->set('eccube.admin.shipping.search.page_count', $page_count);
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
                $this->session->set('eccube.admin.shipping.search', FormUtil::getViewData($searchForm));
                $this->session->set('eccube.admin.shipping.search.page_no', $page_no);
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
                /*
                 * ページ送りの場合または、他画面から戻ってきた場合は, セッションから検索条件を復旧する.
                 */
                if ($page_no) {
                    // ページ送りで遷移した場合.
                    $this->session->set('eccube.admin.shipping.search.page_no', (int) $page_no);
                } else {
                    // 他画面から遷移した場合.
                    $page_no = $this->session->get('eccube.admin.shipping.search.page_no', 1);
                }
                $viewData = $this->session->get('eccube.admin.shipping.search', []);
                $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
            } else {
                /**
                 * 初期表示の場合.
                 */
                $page_no = 1;
                $searchData = [];

                // セッション中の検索条件, ページ番号を初期化.
                $this->session->set('eccube.admin.shipping.search', $searchData);
                $this->session->set('eccube.admin.shipping.search.page_no', $page_no);
            }
        }

        $qb = $this->shippingRepository->getQueryBuilderBySearchDataForAdmin($searchData);

        $event = new EventArgs(
            [
                'qb' => $qb,
                'searchData' => $searchData,
            ],
            $request
        );

        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SHIPPING_INDEX_SEARCH, $event);

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
        ];
    }

    /**
     * @Method("PUT")
     * @Route("/%eccube_admin_route%/shipping/mark_as_shipped/{id}", requirements={"id" = "\d+"}, name="admin_shipping_mark_as_shipped")
     *
     * @param Request $request
     * @param Shipping $Shipping
     *
     * @return JsonResponse
     *
     * @throws \Twig_Error
     */
    public function markAsShipped(Request $request, Shipping $Shipping)
    {
        $this->isTokenValid();

        $result = [];
        if ($Shipping->getShippingStatus()->getId() !== ShippingStatus::SHIPPED) {
            /** @var ShippingStatus $StatusShipped */
            $StatusShipped = $this->shippingStatusRepository->find(ShippingStatus::SHIPPED);
            $Shipping->setShippingStatus($StatusShipped);
            $Shipping->setShippingDate(new \DateTime());
            $this->shippingRepository->save($Shipping);

            if ($request->get('notificationMail')) {
                $this->mailService->sendShippingNotifyMail($Shipping);
                $result['mail'] = true;
            } else {
                $result['mail'] = false;
            }

            $this->entityManager->flush();
            $result['shipped'] = true;

            return new JsonResponse($result);
        }

        return new JsonResponse([
            'shipped' => false,
            'mail' => false,
        ]);
    }

    /**
     * @Route("/%eccube_admin_route%/shipping/preview_notify_mail/{id}", requirements={"id" = "\d+"}, name="admin_shipping_preview_notify_mail")
     *
     * @param Shipping $Shipping
     *
     * @return Response
     *
     * @throws \Twig_Error
     */
    public function previewShippingNotifyMail(Shipping $Shipping)
    {
        return new Response($this->mailService->getShippingNotifyMailBody($Shipping, $Shipping->getOrders()->first()));
    }

    /**
     * @Method("PUT")
     * @Route("/%eccube_admin_route%/shipping/notify_mail/{id}", requirements={"id" = "\d+"}, name="admin_shipping_notify_mail")
     *
     * @param Request $request
     * @param Shipping $Shipping
     *
     * @return JsonResponse
     */
    public function notifyMail(Shipping $Shipping)
    {
        $this->isTokenValid();

        if ($Shipping->getShippingStatus()->getId() === ShippingStatus::SHIPPED) {
            $this->mailService->sendShippingNotifyMail($Shipping);

            return new JsonResponse([
                'mail' => true,
                'shipped' => false,
            ]);
        }

        return new JsonResponse([
            'mail' => false,
            'shipped' => false,
        ]);
    }

    /**
     * @Method("POST")
     * @Route("/%eccube_admin_route%/shipping/bulk_delete", name="admin_shipping_bulk_delete")
     */
    public function bulkDelete(Request $request)
    {
        $this->isTokenValid();
        $ids = $request->get('ids');

        foreach ($ids as $shipping_id) {
            /** @var Shipping $Shipping */
            $Shipping = $this->shippingRepository->find($shipping_id);
            if ($Shipping) {
                $OrderItems = $Shipping->getOrderItems();
                /** @var OrderItem $OrderItem */
                foreach ($OrderItems as $OrderItem) {
                    $OrderItem->setShipping(null);
                }
                $this->entityManager->remove($Shipping);
                log_info('出荷削除', [$Shipping->getId()]);
            }
        }
        $this->entityManager->flush();

        $this->addSuccess('admin.shipping.delete.complete', 'admin');

        return $this->redirect($this->generateUrl('admin_shipping', ['resume' => Constant::ENABLED]));
    }
}
