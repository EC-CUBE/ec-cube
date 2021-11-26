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

namespace Eccube\Controller\Admin\Setting\System;

use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\AuthorityRoleType;
use Eccube\Repository\AuthorityRoleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthorityController extends AbstractController
{
    /**
     * @var AuthorityRoleRepository
     */
    protected $authorityRoleRepository;

    /**
     * AuthorityController constructor.
     *
     * @param AuthorityRoleRepository $authorityRoleRepository
     */
    public function __construct(AuthorityRoleRepository $authorityRoleRepository)
    {
        $this->authorityRoleRepository = $authorityRoleRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/authority", name="admin_setting_system_authority", methods={"GET", "POST"})
     * @Template("@admin/Setting/System/authority.twig")
     */
    public function index(Request $request)
    {
        $AuthorityRoles = $this->authorityRoleRepository->findAllSort();

        $builder = $this->formFactory->createBuilder();
        $builder
            ->add(
                'AuthorityRoles',
                CollectionType::class,
                [
                    'entry_type' => AuthorityRoleType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'data' => $AuthorityRoles,
                ]
            );

        $event = new EventArgs(
            [
                'builder' => $builder,
                'AuthorityRoles' => $AuthorityRoles,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_AUTHORITY_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        if (count($AuthorityRoles) == 0) {
            // 1件もない場合、空行を追加
            $form->get('AuthorityRoles')->add(uniqid(), AuthorityRoleType::class);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
                [
                    'form' => $form,
                    'AuthorityRoles' => $AuthorityRoles,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_AUTHORITY_INDEX_COMPLETE, $event);

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_setting_system_authority');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
