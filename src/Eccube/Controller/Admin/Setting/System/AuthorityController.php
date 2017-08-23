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


namespace Eccube\Controller\Admin\Setting\System;

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\AuthorityRoleType;
use Eccube\Repository\AuthorityRoleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Component
 * @Route(service=AuthorityController::class)
 */
class AuthorityController extends AbstractController
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
     * @Inject(AuthorityRoleRepository::class)
     * @var AuthorityRoleRepository
     */
    protected $authorityRoleRepository;

    /**
     * @Route("/{_admin}/setting/system/authority", name="admin_setting_system_authority")
     * @Template("Setting/System/authority.twig")
     */
    public function index(Application $app, Request $request)
    {
        $AuthorityRoles = $this->authorityRoleRepository->findAllSort();

        $builder = $this->formFactory->createBuilder();
        $builder
            ->add(
                'AuthorityRoles',
                CollectionType::class,
                array(
                    'entry_type' => AuthorityRoleType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'data' => $AuthorityRoles,
                )
            );

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'AuthorityRoles' => $AuthorityRoles,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_AUTHORITY_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        if (count($AuthorityRoles) == 0) {
            // 1件もない場合、空行を追加
            $form->get('AuthorityRoles')->add(uniqid(), AuthorityRoleType::class);
        }


        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                foreach ($AuthorityRoles as $AuthorityRole) {
                    $this->entityManager->remove($AuthorityRole);
                }

                foreach ($data['AuthorityRoles'] as $AuthorityRole) {
                    $Authority = $AuthorityRole->getAuthority();
                    $denyUrl = $AuthorityRole->getDenyUrl();
                    if ($Authority && !empty($denyUrl)) {
                        $this->entityManager->persist($AuthorityRole);
                    } else {
                        $id = $AuthorityRole->getId();
                        if (!empty($id)) {
                            $role = $this->authorityRoleRepository->find($id);
                            if ($role) {
                                // 削除
                                $this->entityManager->remove($AuthorityRole);
                            }
                        }
                    }
                }
                $this->entityManager->flush();

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'AuthorityRoles' => $AuthorityRoles,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_AUTHORITY_INDEX_COMPLETE, $event);

                $app->addSuccess('admin.system.authority.save.complete', 'admin');

                return $app->redirect($app->url('admin_setting_system_authority'));

            }
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
