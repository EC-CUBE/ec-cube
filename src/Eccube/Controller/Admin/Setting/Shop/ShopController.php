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


namespace Eccube\Controller\Admin\Setting\Shop;

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\ShopMasterType;
use Eccube\Repository\BaseInfoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

/**
 * @Component
 * @Route(service=ShopController::class)
 */
class ShopController extends AbstractController
{
    /**
     * @Inject("twig")
     * @var Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject(BaseInfoRepository::class)
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @Route("/{_admin}/setting/shop", name="admin_setting_shop")
     * @Template("Setting/Shop/shop_master.twig")
     */
    public function index(Application $app, Request $request)
    {
        $BaseInfo = $this->baseInfoRepository->get();

        $builder = $this->formFactory
            ->createBuilder(ShopMasterType::class, $BaseInfo);

        $CloneInfo = clone $BaseInfo;
        $this->entityManager->detach($CloneInfo);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'BaseInfo' => $BaseInfo,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_SHOP_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->entityManager->persist($BaseInfo);
                $this->entityManager->flush();

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'BaseInfo' => $BaseInfo,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_SHOP_INDEX_COMPLETE, $event);

                $app->addSuccess('admin.shop.save.complete', 'admin');

                return $app->redirect($app->url('admin_setting_shop'));
            }
            $app->addError('admin.shop.save.error', 'admin');
        }

        $this->twigEnvironment->addGlobal('BaseInfo', $CloneInfo);

        return [
            'form' => $form->createView(),
        ];
    }
}
