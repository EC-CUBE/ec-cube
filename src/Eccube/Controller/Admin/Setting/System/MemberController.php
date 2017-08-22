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
use Eccube\Entity\Member;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MemberType;
use Eccube\Repository\MemberRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * @Component
 * @Route(service=MemberController::class)
 */
class MemberController extends AbstractController
{
    /**
     * @Inject("security.token_storage")
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

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
     * @Inject(MemberRepository::class)
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     * @Route("/{_admin}/setting/system/member", name="admin_setting_system_member")
     * @Template("Setting/System/member.twig")
     */
    public function index(Application $app, Request $request)
    {
        $Members = $this->memberRepository->findBy([], ['rank' => 'DESC']);

        $builder = $this->formFactory->createBuilder();

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Members' => $Members,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        return [
            'form' => $form->createView(),
            'Members' => $Members,
        ];
    }

    /**
     * @Route("/{_admin}/setting/system/member/new", name="admin_setting_system_member_new")
     * @Route("/{_admin}/setting/system/member/{id}/edit", requirements={"id":"\d+"}, name="admin_setting_system_member_edit")
     * @Template("Setting/System/member_edit.twig")
     */
    public function edit(Application $app, Request $request, Member $Member = null)
    {
        $previous_password = null;
        if (is_null($Member)) {
            $Member = new Member();
        } else {
            $previous_password = $Member->getPassword();
            $Member->setPassword($this->appConfig['default_password']);
        }

        $LoginMember = clone $app->user();
        $this->entityManager->detach($LoginMember);

        $builder = $this->formFactory
            ->createBuilder(MemberType::class, $Member);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Member' => $Member,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!is_null($previous_password)
                && $Member->getpassword() === $this->appConfig['default_password']
            ) {
                // 編集時にPWを変更していなければ
                // 変更前のパスワード(暗号化済み)をセット
                $Member->setPassword($previous_password);
            } else {
                $salt = $Member->getSalt();
                if (!isset($salt)) {
                    $salt = $this->memberRepository->createSalt(5);
                    $Member->setSalt($salt);
                }

                // 入力されたPWを暗号化してセット
                $password = $this->memberRepository->encryptPassword($Member);
                $Member->setPassword($password);
            }
            $status = $this->memberRepository->save($Member);

            if ($status) {
                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'Member' => $Member,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_EDIT_COMPLETE, $event);

                $app->addSuccess('admin.member.save.complete', 'admin');

                return $app->redirect($app->url('admin_setting_system_member'));
            } else {
                $app->addError('admin.member.save.error', 'admin');
            }
        }

        $this->tokenStorage->getToken()->setUser($LoginMember);

        return [
            'form' => $form->createView(),
            'Member' => $Member,
        ];
    }

    /**
     * @Method("PUT")
     * @Route("/{_admin}/setting/system/member/{id}/up", requirements={"id":"\d+"}, name="admin_setting_system_member_up")
     */
    public function up(Application $app, Request $request, Member $Member)
    {
        $this->isTokenValid($app);

        $status = $this->memberRepository->up($Member);

        if ($status) {
            $app->addSuccess('admin.member.up.complete', 'admin');
        } else {
            $app->addError('admin.member.up.error', 'admin');
        }

        return $app->redirect($app->url('admin_setting_system_member'));
    }

    /**
     * @Method("PUT")
     * @Route("/{_admin}/setting/system/member/{id}/down", requirements={"id":"\d+"}, name="admin_setting_system_member_down")
     */
    public function down(Application $app, Request $request, Member $Member)
    {
        $this->isTokenValid($app);

        $status = $this->memberRepository->down($Member);

        if ($status) {
            $app->addSuccess('admin.member.down.complete', 'admin');
        } else {
            $app->addError('admin.member.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_setting_system_member'));
    }

    /**
     * @Method("DELETE")
     * @Route("/{_admin}/setting/system/member/{id}/delete", requirements={"id":"\d+"}, name="admin_setting_system_member_delete")
     */
    public function delete(Application $app, Request $request, Member $Member)
    {
        $this->isTokenValid($app);

        $event = new EventArgs(
            array(
                'TargetMember' => $Member,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_DELETE_INITIALIZE, $event);

        $status = $this->memberRepository->delete($Member);

        if ($status) {
            $app->addSuccess('admin.member.delete.complete', 'admin');
            $event = new EventArgs(
                array(),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_DELETE_COMPLETE, $event);
        } else {
            $app->addError('admin.member.delete.error', 'admin');
        }

        return $app->redirect($app->url('admin_setting_system_member'));
    }
}