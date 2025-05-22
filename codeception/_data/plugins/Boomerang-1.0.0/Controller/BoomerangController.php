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

namespace Plugin\Boomerang\Controller;


use Eccube\Controller\AbstractController;
use Eccube\Entity\Cart;
use Eccube\Repository\CartRepository;
use Plugin\Boomerang\Entity\Bar;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BoomerangController extends AbstractController
{
    /**
     * @var CartRepository
     */
    private $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @Route("/boomerang", name="boomerang")
     * @return JsonResponse
     */
    public function index()
    {
        /** @var Cart[] $list */
        $list = $this->cartRepository->findAll();
        $ids = array_map(function (Cart $c) { return $c->getId(); }, $list);

        return $this->json($ids);
    }

    /**
     * @Route("/boomerang/new")
     */
    public function new()
    {
        $Bar = new Bar();
        $Bar->name = 'bar';
        $this->entityManager->persist($Bar);

        $Cart = new Cart();
        $Cart->setTotalPrice(0);
        $Cart->setDeliveryFeeTotal(0);
        $Cart->bar = $Bar;

        $this->cartRepository->save($Cart);
        $this->entityManager->flush();

        return $this->redirectToRoute('boomerang');
    }
}