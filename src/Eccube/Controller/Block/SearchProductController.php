<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Controller\Block;

use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\SearchProductBlockType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @Component
 * @Route(service=SearchProductController::class)
 */
class SearchProductController
{
    /**
     * @Inject("request_stack")
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var
     */
    protected $eventDispatcher;

    /**
     * @Route("/block/search_product", name="block_search_product")
     * @Template("Block/search_product.twig")
     */
    public function index(Application $app, Request $request)
    {
        $builder = $this->formFactory
            ->createNamedBuilder('', SearchProductBlockType::class)
            ->setMethod('GET');

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );

        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_BLOCK_SEARCH_PRODUCT_INDEX_INITIALIZE, $event);

        $request = $this->requestStack->getMasterRequest();

        $form = $builder->getForm();
        $form->handleRequest($request);

        return [
            'form' => $form->createView(),
        ];
    }
}
