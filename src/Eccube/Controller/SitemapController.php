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

use Eccube\Repository\ProductRepository;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\PageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * SitemapController constructor.
     *
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @param PageRepository $pageRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        PageRepository $pageRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository= $categoryRepository;
        $this->pageRepository = $pageRepository;
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
            ->where("(p.meta_robots not like '%noindex%' or p.meta_robots not like '%none%' or p.meta_robots IS NULL)")
            ->andWhere('p.id <> 0')
            ->andWhere('p.MasterPage is null')
            ->orderBy("p.update_date","DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        $Product = $this->productRepository->findOneBy(["Status"=>1],["update_date"=>"DESC"]);
        $Category = $this->categoryRepository->findOneBy([],["update_date"=>"DESC"]);
        return $this->outputXml(
            [
                "Category"    =>  $Category,
                "Product"    =>  $Product,
                "Page"         =>  $Page
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
        $Categories = $this->categoryRepository->getList(null,true);
        return $this->outputXml(["Categories"=>$Categories]);
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
        $Products = $this->productRepository->findBy(["Status"=>1],["update_date"=>"DESC"]);
        return $this->outputXml(["Products"=>$Products]);
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
        $Pages = $this->pageRepository->getPageList("(p.meta_robots not like '%noindex%' or p.meta_robots not like '%none%' or p.meta_robots IS NULL)");
        return $this->outputXml(["Pages"=>$Pages]);
    }

    /**
     * Output XML response by data.
     *
     * @param array $data
     * @param String $template_name
     * @return Response
     */
    private function outputXml(Array $data, $template_name = 'sitemap.xml.twig')
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
