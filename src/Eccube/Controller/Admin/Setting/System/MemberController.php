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
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MemberType;
use Eccube\Repository\MemberRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

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

    public function __construct()
    {
    }

    public function index(Application $app, Request $request)
    {
        $Members = $this->memberRepository->findBy(array(), array('rank' => 'DESC'));

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

        return $app->render('Setting/System/member.twig', array(
            'form' => $form->createView(),
            'Members' => $Members,
        ));
    }

    public function edit(Application $app, Request $request, $id = null)
    {
        $previous_password = null;
        if ($id) {
            $Member = $this->memberRepository->find($id);
            if (!$Member) {
                throw new NotFoundHttpException();
            }
            $previous_password = $Member->getPassword();
            $Member->setPassword($this->appConfig['default_password']);
        } else {
            $Member = new \Eccube\Entity\Member();
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

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if (!is_null($previous_password)
                    && $Member->getpassword() === $this->appConfig['default_password']) {
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
        }

        $this->tokenStorage->getToken()->setUser($LoginMember);

        return $app->render('Setting/System/member_edit.twig', array(
            'form' => $form->createView(),
            'Member' => $Member,
        ));

    }

    public function up(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetMember = $this->memberRepository->find($id);

        if (!$TargetMember) {
            throw new NotFoundHttpException();
        }

        $status = false;
        if ('PUT' === $request->getMethod()) {
            $status = $this->memberRepository->up($TargetMember);
        }

        if ($status) {
            $app->addSuccess('admin.member.up.complete', 'admin');
        } else {
            $app->addError('admin.member.up.error', 'admin');
        }

        return $app->redirect($app->url('admin_setting_system_member'));
    }

    public function down(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetMember = $this->memberRepository->find($id);

        if (!$TargetMember) {
            throw new NotFoundHttpException();
        }

        $status = false;
        if ('PUT' === $request->getMethod()) {
            $status = $this->memberRepository->down($TargetMember);
        }

        if ($status) {
            $app->addSuccess('admin.member.down.complete', 'admin');
        } else {
            $app->addError('admin.member.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_setting_system_member'));
    }

    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetMember = $this->memberRepository->find($id);
        if (!$TargetMember) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_setting_system_member'));
        }

        $event = new EventArgs(
            array(
                'TargetMember' => $TargetMember,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_DELETE_INITIALIZE, $event);

        $status = $this->memberRepository->delete($TargetMember);

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