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

use Eccube\Controller\AbstractController;
use Eccube\Entity\BaseInfo;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\ShopMasterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

/**
 * Class ShopController
 *
 * @package Eccube\Controller\Admin\Setting\Shop
 */
class ShopController extends AbstractController
{

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * ShopController constructor.
     *
     * @param BaseInfo $BaseInfo
     */
    public function __construct(Twig_Environment $twig, BaseInfo $BaseInfo)
    {
        $this->BaseInfo = $BaseInfo;
        $this->twig = $twig;
    }


    /**
     * @Route("/%admin_route%/setting/shop", name="admin_setting_shop")
     * @Template("@admin/Setting/Shop/shop_master.twig")
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Request $request)
    {
        $builder = $this->formFactory
            ->createBuilder(ShopMasterType::class, $this->BaseInfo);

        $CloneInfo = clone $this->BaseInfo;
        $this->entityManager->detach($CloneInfo);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'BaseInfo' => $this->BaseInfo,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_SHOP_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->entityManager->persist($this->BaseInfo);
                $this->entityManager->flush();

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'BaseInfo' => $this->BaseInfo,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_SHOP_INDEX_COMPLETE, $event);

                $this->addSuccess('admin.shop.save.complete', 'admin');

                return $this->redirectToRoute('admin_setting_shop');
            }
            $this->addError('admin.shop.save.error', 'admin');
        }

        $this->twig->addGlobal('BaseInfo', $CloneInfo);

        return [
            'form' => $form->createView(),
        ];
    }
}
