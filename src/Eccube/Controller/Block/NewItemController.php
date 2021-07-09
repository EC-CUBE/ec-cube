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

class NewItemController extends AbstractController
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
     * @Route("/block/new_item", name="block_new_item")
     * @Template("Block/new_item.twig")
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $qb = $this->productRepository->getQueryBuilderBySearchData([
            'orderby' => $this->productListOrderByRepository->find($this->eccubeConfig['eccube_product_order_newer']),
        ])
        ->setMaxResults(5);

        return [
            'Products' => $qb->getQuery()->getResult(),
        ];
    }
}
