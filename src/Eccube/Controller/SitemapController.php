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

namespace Eccube\Controller;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Page;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\Master\ProductListOrderByRepository;
use Eccube\Repository\PageRepository;
use Eccube\Repository\ProductRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class SitemapController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var ProductListOrderByRepository
     */
    private $productListOrderByRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * SitemapController constructor.
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        PageRepository $pageRepository,
        ProductListOrderByRepository $productListOrderByRepository,
        ProductRepository $productRepository,
        RouterInterface $router,
        BaseInfoRepository $baseInfoRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->pageRepository = $pageRepository;
        $this->productListOrderByRepository = $productListOrderByRepository;
        $this->productRepository = $productRepository;
        $this->router = $router;
        $this->BaseInfo = $baseInfoRepository->get();
    }

    /**
     * Output sitemap index
     *
     * @Route("/sitemap.xml", name="sitemap_xml", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator)
    {
        $pageQueryBuilder = $this->pageRepository->createQueryBuilder('p');
        $Page = $pageQueryBuilder->select('p')
            ->where("((p.meta_robots not like '%noindex%' and p.meta_robots not like '%none%') or p.meta_robots IS NULL)")
            ->andWhere('p.id <> 0')
            ->andWhere('p.MasterPage is null')
            ->orderBy('p.update_date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        $Product = $this->productRepository->findOneBy(['Status' => 1], ['update_date' => 'DESC']);

        // フロントの商品一覧の条件で商品情報を取得
        $ProductListOrder = $this->productListOrderByRepository->find($this->eccubeConfig['eccube_product_order_newer']);
        $productQueryBuilder = $this->productRepository->getQueryBuilderBySearchData(['orderby' => $ProductListOrder]);
        /** @var SlidingPagination $pagination */
        $pagination = $paginator->paginate(
            $productQueryBuilder,
            1,
            $this->eccubeConfig['eccube_sitemap_products_per_page']
        );
        $paginationData = $pagination->getPaginationData();

        $Category = $this->categoryRepository->findOneBy([], ['update_date' => 'DESC']);

        return $this->outputXml(
            [
                'Category' => $Category,
                'Product' => $Product,
                'productPageCount' => $paginationData['pageCount'],
                'Page' => $Page,
            ],
            'sitemap_index.xml.twig'
        );
    }

    /**
     * Output sitemap of product categories
     *
     * @Route("/sitemap_category.xml", name="sitemap_category_xml", methods={"GET"})
     */
    public function category()
    {
        $Categories = $this->categoryRepository->getList(null, true);

        return $this->outputXml(['Categories' => $Categories]);
    }

    /**
     * Output sitemap of products
     *
     * Output sitemap of products as status is 1
     *
     * @Route("/sitemap_product_{page}.xml", name="sitemap_product_xml", requirements={"page" = "\d+"}, methods={"GET"})
     *
     * @return Response
     */
    public function product(Request $request, PaginatorInterface $paginator)
    {
        // Doctrine SQLFilter
        if ($this->BaseInfo->isOptionNostockHidden()) {
            $this->entityManager->getFilters()->enable('option_nostock_hidden');
        }
        // フロントの商品一覧の条件で商品情報を取得
        $ProductListOrder = $this->productListOrderByRepository->find($this->eccubeConfig['eccube_product_order_newer']);
        $productQueryBuilder = $this->productRepository->getQueryBuilderBySearchData(['orderby' => $ProductListOrder]);
        /** @var SlidingPagination $pagination */
        $pagination = $paginator->paginate(
            $productQueryBuilder,
            $request->get('page'),
            $this->eccubeConfig['eccube_sitemap_products_per_page']
        );
        $paginationData = $pagination->getPaginationData();

        if ($paginationData['currentItemCount'] === 0) {
            throw new NotFoundHttpException();
        }

        return $this->outputXml(['Products' => $pagination]);
    }

    /**
     * Output sitemap of pages
     *
     * Output sitemap of pages without 'noindex' in meta robots.
     *
     * @Route("/sitemap_page.xml", name="sitemap_page_xml", methods={"GET"})
     */
    public function page()
    {
        $Pages = $this->pageRepository->getPageList("((p.meta_robots not like '%noindex%' and p.meta_robots not like '%none%') or p.meta_robots IS NULL)");

        // URL に変数が含まれる場合は URL の生成ができないためここで除外する
        $DefaultPages = array_filter($Pages, function (Page $Page) {
            // 管理画面から作成されたページは除外
            if ($Page->getEditType() === Page::EDIT_TYPE_USER) {
                return false;
            }

            $route = $this->router->getRouteCollection()->get($Page->getUrl());
            if (is_null($route)) {
                return false;
            }

            return count($route->compile()->getPathVariables()) < 1;
        });

        // 管理画面から作成されたページ
        $UserPages = array_filter($Pages, function (Page $Page) {
            return $Page->getEditType() === Page::EDIT_TYPE_USER;
        });

        return $this->outputXml([
            'DefaultPages' => $DefaultPages,
            'UserPages' => $UserPages,
        ]);
    }

    /**
     * Output XML response by data.
     *
     * @param array $data
     * @param string $template_name
     *
     * @return Response
     */
    private function outputXml(array $data, $template_name = 'sitemap.xml.twig')
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/xml'); // Content-Typeを設定

        return $this->render(
            $template_name,
            $data,
            $response
        );
    }
}
