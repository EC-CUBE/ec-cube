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
use Eccube\Form\Type\Admin\CustomerAgreementType;
use Eccube\Repository\HelpRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Component
 * @Route(service=CustomerAgreementController::class)
 */
class CustomerAgreementController extends AbstractController
{
    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject(HelpRepository::class)
     * @var HelpRepository
     */
    protected $helpRepository;

    public $form;

    /**
     * @Route("/{_admin}/setting/shop/customer_agreement", name="admin_setting_shop_customer_agreement")
     * @Template("Setting/Shop/customer_agreement.twig")
     */
    public function index(Application $app, Request $request)
    {
        $Help = $this->helpRepository->get();

        $builder = $this->formFactory
            ->createBuilder(CustomerAgreementType::class, $Help);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Help' => $Help,
            ),
            $request
        );

        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_CUSTOMER_AGREEMENT_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $Help = $form->getData();
                $this->entityManager->persist($Help);
                $this->entityManager->flush();

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'Help' => $Help,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(
                    EccubeEvents::ADMIN_SETTING_SHOP_CUSTOMER_AGREEMENT_INDEX_COMPLETE,
                    $event
                );

                $app->addSuccess('admin.register.complete', 'admin');

                return $app->redirect($app->url('admin_setting_shop_customer_agreement'));

            } else {
                $app->addError('admin.register.failed', 'admin');
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
