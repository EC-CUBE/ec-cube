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
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
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
     * @Inject("security.encoder_factory")
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @Route("/%admin_route%/setting/system/member", name="admin_setting_system_member")
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
     * @Route("/%admin_route%/setting/system/member/new", name="admin_setting_system_member_new")
     * @Template("Setting/System/member_edit.twig")
     */
    public function create(Application $app, Request $request)
    {
        $LoginMember = clone $app->user();
        $this->entityManager->detach($LoginMember);

        $Member = new Member();
        $builder = $this->formFactory
            ->createBuilder(MemberType::class, $Member);

        $event = new EventArgs([
            'builder' => $builder,
            'Member' => $Member,
        ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encoder = $this->encoderFactory->getEncoder($Member);
            $salt = $encoder->createSalt();
            $rawPassword = $Member->getPassword();
            $encodedPassword = $encoder->encodePassword($rawPassword, $salt);
            $Member
                ->setSalt($salt)
                ->setPassword($encodedPassword);

            $this->memberRepository->save($Member);

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
        }

        $this->tokenStorage->getToken()->setUser($LoginMember);

        return [
            'form' => $form->createView(),
            'Member' => $Member,
        ];
    }

    /**
     * @Route("/%admin_route%/setting/system/member/{id}/edit", requirements={"id" = "\d+"}, name="admin_setting_system_member_edit")
     * @Template("Setting/System/member_edit.twig")
     */
    public function edit(Application $app, Request $request, Member $Member)
    {
        $LoginMember = clone $app->user();
        $this->entityManager->detach($LoginMember);

        $previousPassword = $Member->getPassword();
        $Member->setPassword($this->appConfig['default_password']);

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
            if ($Member->getpassword() === $this->appConfig['default_password']) {
                // 編集時にパスワードを変更していなければ
                // 変更前のパスワード(暗号化済み)をセット
                $Member->setPassword($previousPassword);
            } else {
                $salt = $Member->getSalt();
                // 2系からのデータ移行でsaltがセットされていない場合はsaltを生成.
                if (empty($salt)) {
                    $salt = bin2hex(openssl_random_pseudo_bytes(5));
                    $Member->setSalt($salt);
                }

                $rawPassword = $Member->getPassword();
                $encoder = $this->encoderFactory->getEncoder($Member);
                $encodedPassword = $encoder->encodePassword($rawPassword, $salt);
                $Member->setPassword($encodedPassword);
            }

            $this->memberRepository->save($Member);

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
        }

        $this->tokenStorage->getToken()->setUser($LoginMember);

        return [
            'form' => $form->createView(),
            'Member' => $Member,
        ];
    }

    /**
     * @Method("PUT")
     * @Route("/%admin_route%/setting/system/member/{id}/up", requirements={"id" = "\d+"}, name="admin_setting_system_member_up")
     */
    public function up(Application $app, Request $request, Member $Member)
    {
        $this->isTokenValid($app);

        try {
            $this->memberRepository->up($Member);

            $app->addSuccess('admin.member.up.complete', 'admin');

        } catch (\Exception $e) {
            log_error('メンバー表示順更新エラー', [$Member->getId(), $e]);

            $app->addError('admin.member.up.error', 'admin');
        }

        return $app->redirect($app->url('admin_setting_system_member'));
    }

    /**
     * @Method("PUT")
     * @Route("/%admin_route%/setting/system/member/{id}/down", requirements={"id" = "\d+"}, name="admin_setting_system_member_down")
     */
    public function down(Application $app, Request $request, Member $Member)
    {
        $this->isTokenValid($app);

        try {
            $this->memberRepository->down($Member);

            $app->addSuccess('admin.member.down.complete', 'admin');
        } catch (\Exception $e) {
            log_error('メンバー表示順更新エラー', [$Member->getId(), $e]);

            $app->addError('admin.member.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_setting_system_member'));
    }

    /**
     * @Method("DELETE")
     * @Route("/%admin_route%/setting/system/member/{id}/delete", requirements={"id" = "\d+"}, name="admin_setting_system_member_delete")
     */
    public function delete(Application $app, Request $request, Member $Member)
    {
        $this->isTokenValid($app);

        log_info('メンバー削除開始', [$Member->getId()]);

        try {
            $this->memberRepository->delete($Member);

            $event = new EventArgs(
                [
                    'Member' => $Member,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_DELETE_COMPLETE, $event);

            $app->addSuccess('admin.member.delete.complete', 'admin');

            log_info('メンバー削除完了', [$Member->getId()]);

        } catch (\Exception $e) {
            log_info('メンバー削除エラー', [$Member->getId(), $e]);

            $message = $app->trans('admin.delete.failed.foreign_key', ['%name%' => 'メンバー']);
            $app->addError($message, 'admin');
        }

        return $app->redirect($app->url('admin_setting_system_member'));
    }
}
