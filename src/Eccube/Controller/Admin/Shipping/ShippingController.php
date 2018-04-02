<?php

namespace Eccube\Controller\Admin\Shipping;

use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\SearchShippingType;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\Master\ShippingStatusRepository;
use Eccube\Repository\ShippingRepository;
use Eccube\Util\FormUtil;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class ShippingController extends AbstractController
{
    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @var ShippingStatusRepository
     */
    protected $shippingStatusRepository;

    /**
     * @var ShippingRepository
     */
    protected $shippingRepository;

    /**
     * @var PageMaxRepository
     */
    protected $pageMaxRepository;

    /**
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    public function __construct(
        OrderStatusRepository $orderStatusRepository,
        ShippingRepository $shippingRepository,
        PageMaxRepository $pageMaxRepository,
        ProductStatusRepository $productStatusRepository,
        ShippingStatusRepository $shippingStatusRepository
    )
    {
        $this->orderStatusRepository = $orderStatusRepository;
        $this->shippingRepository = $shippingRepository;
        $this->pageMaxRepository = $pageMaxRepository;
        $this->productStatusRepository = $productStatusRepository;
        $this->shippingStatusRepository = $shippingStatusRepository;
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
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_INITIALIZE, $event);

        $searchForm = $builder->getForm();

        $page_count = $this->session->get('eccube.admin.shipping.search.page_count',
            $this->eccubeConfig->get('eccube_default_page_count'));

        $page_count_param = (int)$request->get('page_count');
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

        $ProductStatuses = $this->productStatusRepository->findAll();

        if ('POST' === $request->getMethod()) {

            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $page_no = 1;
                $searchData = $searchForm->getData();

                // 検索条件, ページ番号をセッションに保持.
                $this->session->set('eccube.admin.shipping.search', FormUtil::getViewData($searchForm));
                $this->session->set('eccube.admin.shipping.search.page_no', $page_no);
            }
        } else {
            if ($page_no || $request->get('resume') == Constant::ENABLED) {
                if ($page_no) {
                    // ページ送りで遷移した場合.
                    $this->session->set('eccube.admin.shipping.search.page_no', (int)$page_no);
                } else {
                    // 他画面から遷移した場合.
                    $page_no = $this->session->get('eccube.admin.shipping.search.page_no', 1);
                }
                $viewData = $this->session->get('eccube.admin.shipping.search', []);
                $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
            } else {
                // 初期表示
                $page_no = 1;
                $searchData = [];
                $page_count = $this->eccubeConfig->get('eccube_default_page_count');
                // セッション中の検索条件, ページ番号を初期化.
                $this->session->set('eccube.admin.shipping.search', $searchData);
                $this->session->set('eccube.admin.shipping.search.page_no', $page_no);
                $this->session->set('eccube.admin.shipping.search.page_count', $page_count);
            }
        }

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

        $pagination = $paginator->paginate(
            $qb,
            $page_no,
            $page_count
        );

        return [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'productStatuses' => $ProductStatuses,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_count' => $page_count,
        ];
    }
}
