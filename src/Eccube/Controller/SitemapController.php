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

use Eccube\Entity\Page;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\PageRepository;
use Eccube\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class SitemapController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * SitemapController constructor.
     */
    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        PageRepository $pageRepository,
        RouterInterface $router
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->pageRepository = $pageRepository;
        $this->router = $router;
    }

    /**
     * Output sitemap index
     *
     * @Route("/sitemap.xml", name="sitemap_xml")
     */
    public function index()
    {
        $qb = $this->pageRepository->createQueryBuilder('p');
        $Page = $qb->select('p')
            ->where("((p.meta_robots not like '%noindex%' and p.meta_robots not like '%none%') or p.meta_robots IS NULL)")
            ->andWhere('p.id <> 0')
            ->andWhere('p.MasterPage is null')
            ->orderBy('p.update_date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        $Product = $this->productRepository->findOneBy(['Status' => 1], ['update_date' => 'DESC']);
        $Category = $this->categoryRepository->findOneBy([], ['update_date' => 'DESC']);

        return $this->outputXml(
            [
                'Category' => $Category,
                'Product' => $Product,
                'Page' => $Page,
            ],
            'sitemap_index.xml.twig'
        );
    }

    /**
     * Output sitemap of product categories
     *
     * @Route("/sitemap_category.xml", name="sitemap_category_xml")
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
     * @Route("/sitemap_product.xml", name="sitemap_product_xml")
     */
    public function product()
    {
        $Products = $this->productRepository->findBy(['Status' => 1], ['update_date' => 'DESC']);

        return $this->outputXml(['Products' => $Products]);
    }

    /**
     * Output sitemap of pages
     *
     * Output sitemap of pages without 'noindex' in meta robots.
     *
     * @Route("/sitemap_page.xml", name="sitemap_page_xml")
     */
    public function page()
    {
        $Pages = $this->pageRepository->getPageList("((p.meta_robots not like '%noindex%' and p.meta_robots not like '%none%') or p.meta_robots IS NULL)");

        // URL に変数が含まれる場合は URL の生成ができないためここで除外する
        $Pages = array_filter($Pages, function (Page $Page) {
            $route = $this->router->getRouteCollection()->get($Page->getUrl());
            if (is_null($route)) {
                return false;
            }
            return count($route->compile()->getPathVariables()) < 1;
        });

        return $this->outputXml(['Pages' => $Pages]);
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
        $response->headers->set('Content-Type', 'application/xml'); //Content-Typeを設定

        return $this->render(
            $template_name,
            $data,
            $response
        );
    }
}
