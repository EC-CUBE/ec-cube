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
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\SearchProductBlockType;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;

class SearchProductController extends AbstractController
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(RequestStack $requestStack,
    ) {
        $this->requestStack = $requestStack;
    }

    /**
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    #[Route(path: '/block/search_product', name: 'block_search_product', methods: ['GET'])]
    #[Route(path: '/block/search_product_sp', name: 'block_search_product_sp', methods: ['GET'])]
    #[Template(template: 'Block/search_product.twig')]
    public function index(Request $request): array
    {
        $builder = $this->formFactory
            ->createNamedBuilder('', SearchProductBlockType::class)
            ->setMethod('GET');

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );

        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_BLOCK_SEARCH_PRODUCT_INDEX_INITIALIZE);

        $request = $this->requestStack->getMainRequest();

        $form = $builder->getForm();
        $form->handleRequest($request);

        return [
            'form' => $form->createView(),
        ];
    }
}
