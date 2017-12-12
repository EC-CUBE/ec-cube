<?php

namespace Eccube\Controller\Admin\Shipping;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\AddCartType;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Form\Type\Admin\SearchProductType;
use Eccube\Form\Type\Admin\SearchShippingType;
use Eccube\Form\Type\Admin\OrderItemType;
use Eccube\Form\Type\Admin\ShippingType;

use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Repository\ShippingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Route(service=ShippingController::class)
 */
class ShippingController
{
    /**
     * @Inject(OrderStatusRepository::class)
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @Inject(ShippingRepository::class)
     * @var ShippingRepository
     */
    protected $shippingRepository;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(PageMaxRepository::class)
     * @var PageMaxRepository
     */
    protected $pageMaxRepository;

    /**
     * @Inject(ProductStatusRepository::class)
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

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
     * @Route("/{_admin}/shipping", name="admin_shipping")
     * @Route("/{_admin}/shipping/page/{page_no}", name="admin_shipping_page")
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("Shipping/index.twig")
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request, $page_no = null)
    {
        $session = $request->getSession();

        $builder = $this->formFactory
            ->createBuilder(SearchShippingType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_INITIALIZE, $event);

        $searchForm = $builder->getForm();

        $pagination = array();

        $ProductStatuses = $this->productStatusRepository->findAll();
        $pageMaxis = $this->pageMaxRepository->findAll();
        $page_count = $this->appConfig['default_page_count'];
        $page_status = null;
        $active = false;

        if ('POST' === $request->getMethod()) {

            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();

                // paginator
                $qb = $this->shippingRepository->getQueryBuilderBySearchDataForAdmin($searchData);

                $event = new EventArgs(
                    array(
                        'form' => $searchForm,
                        'qb' => $qb,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_SEARCH, $event);

                $page_no = 1;
                $pagination = $app['paginator']()->paginate(
                    $qb,
                    $page_no,
                    $page_count
                );

                // sessionのデータ保持
                $session->set('eccube.admin.shipping.search', $searchData);
                $session->set('eccube.admin.shipping.search.page_no', $page_no);
            }
        } else {
            if (is_null($page_no) && $request->get('resume') != Constant::ENABLED) {
                // sessionを削除
                $session->remove('eccube.admin.shipping.search');
                $session->remove('eccube.admin.shipping.search.page_no');
            } else {
                // pagingなどの処理
                $searchData = $session->get('eccube.admin.shipping.search');
                if (is_null($page_no)) {
                    $page_no = intval($session->get('eccube.admin.shipping.search.page_no'));
                } else {
                    $session->set('eccube.admin.shipping.search.page_no', $page_no);
                }

                if (!is_null($searchData)) {

                    // 公開ステータス
                    $status = $request->get('status');
                    if (!empty($status)) {
                        if ($status != $this->appConfig['admin_product_stock_status']) {
                            $searchData['status']->clear();
                            $searchData['status']->add($status);
                        } else {
                            $searchData['stock_status'] = $this->appConfig['disabled'];
                        }
                        $page_status = $status;
                    }
                    // 表示件数
                    $pcount = $request->get('page_count');

                    $page_count = empty($pcount) ? $page_count : $pcount;

                    $qb = $this->shippingRepository->getQueryBuilderBySearchDataForAdmin($searchData);

                    $event = new EventArgs(
                        array(
                            'form' => $searchForm,
                            'qb' => $qb,
                        ),
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_SEARCH, $event);

                    $pagination = $app['paginator']()->paginate(
                        $qb,
                        $page_no,
                        $page_count
                    );

                    // セッションから検索条件を復元
                    if (!empty($searchData['status'])) {
                        $searchData['status'] = $this->orderStatusRepository->find($searchData['status']);
                    }
                    if (count($searchData['multi_status']) > 0) {
                        $statusIds = array();
                        foreach ($searchData['multi_status'] as $Status) {
                            $statusIds[] = $Status->getId();
                        }
                        $searchData['multi_status'] = $this->orderStatusRepository->findBy(array('id' => $statusIds));
                    }
                    $searchForm->setData($searchData);
                }
            }
        }

        return [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'productStatuses' => $ProductStatuses,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_status' => $page_status,
            'page_count' => $page_count,
            'active' => $active,
        ];
    }
}
