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

namespace Eccube\Controller\Block;

use Eccube\Controller\AbstractController;
use Eccube\Repository\Master\ProductListOrderByRepository;
use Eccube\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AutoNewItemController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductListOrderByRepository
     */
    private $productListOrderByRepository;

    public function __construct(
        ProductRepository $productRepository,
        ProductListOrderByRepository $productListOrderByRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productListOrderByRepository = $productListOrderByRepository;
    }

    /**
     * @Route("/block/auto_new_item", name="block_auto_new_item", methods={"GET"})
     * @Template("Block/auto_new_item.twig")
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $qb = $this->productRepository->getQueryBuilderBySearchData([
            'orderby' => $this->productListOrderByRepository->find($this->eccubeConfig['eccube_product_order_newer']),
        ])
            ->setMaxResults($this->eccubeConfig['eccube_max_number_new_items_get']);

        return [
            'Products' => $qb->getQuery()->getResult(),
        ];
    }
}