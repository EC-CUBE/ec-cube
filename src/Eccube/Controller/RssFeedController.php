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

use Eccube\Repository\NewsRepository;
use Eccube\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RssFeedController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * RssFeedController constructor.
     *
     * @param ProductRepository $productRepository
     * @param NewsRepository $newsRepository
     */
    public function __construct(ProductRepository $productRepository, NewsRepository $newsRepository)
    {
        $this->productRepository = $productRepository;
        $this->newsRepository = $newsRepository;
    }

    /**
     * @Route("/products_feed", name="rss_feed_for_products")
     */
    public function products()
    {
        $products = $this->productRepository->findBy(
            ['Status' => 1],
            [
                'create_date' => 'DESC',
                'id' => 'DESC',
            ],
            20
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/xml; charset=UTF-8');

        return $this->render(
            'Feed/products.twig',
            ['products' => $products],
            $response
        );
    }

    /**
     * @Route("/news_feed", name="rss_feed_for_news")
     */
    public function news()
    {
        $builder = $this->newsRepository->createQueryBuilder('news');
        $news = $builder
            ->where('news.visible = :visible')
            ->andWhere($builder->expr()->lte('news.publish_date', ':now'))
            ->setParameters([
                'visible' => true,
                'now' => new \DateTime(),
            ])
            ->orderBy('news.publish_date', 'DESC')
            ->addOrderBy('news.id', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/xml; charset=UTF-8');

        return $this->render(
            'Feed/news.twig',
            ['news' => $news],
            $response
        );
    }
}
