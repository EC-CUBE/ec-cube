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

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MemberController extends AbstractController
{
    public function __construct()
    {
    }

    public function index(Application $app, Request $request)
    {
        $Members = $app['eccube.repository.member']->findBy(array(), array('rank' => 'DESC'));

        $builder = $app['form.factory']->createBuilder();

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Members' => $Members,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_INDEX_INITIALIZE, $event);

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
            $Member = $app['eccube.repository.member']->find($id);
            if (!$Member) {
                throw new NotFoundHttpException();
            }
            $previous_password = $Member->getPassword();
            $Member->setPassword($app['config']['default_password']);
        } else {
            $Member = new \Eccube\Entity\Member();
        }

        $builder = $app['form.factory']
            ->createBuilder('admin_member', $Member);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Member' => $Member
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if (!is_null($previous_password)
                    && $Member->getpassword() === $app['config']['default_password']) {
                    // 編集時にPWを変更していなければ
                    // 変更前のパスワード(暗号化済み)をセット
                    $Member->setPassword($previous_password);
                } else {
                    $salt = $Member->getSalt();
                    if (!isset($salt)) {
                        $salt = $app['eccube.repository.member']->createSalt(5);
                        $Member->setSalt($salt);
                    }

                    // 入力されたPWを暗号化してセット
                    $password = $app['eccube.repository.member']->encryptPassword($Member);
                    $Member->setPassword($password);
                }
                $status = $app['eccube.repository.member']->save($Member);

                if ($status) {
                    $event = new EventArgs(array(
                            'form' => $form,
                            'Member' => $Member
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_EDIT_COMPLETE, $event);

                    $app->addSuccess('admin.member.save.complete', 'admin');

                    return $app->redirect($app->url('admin_setting_system_member'));
                } else {
                    $app->addError('admin.member.save.error', 'admin');
                }
            }
        }

        return $app->render('Setting/System/member_edit.twig', array(
            'form' => $form->createView(),
            'Member' => $Member,
        ));

    }

    public function up(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetMember = $app['eccube.repository.member']->find($id);

        if (!$TargetMember) {
            throw new NotFoundHttpException();
        }

        $status = false;
        if ('PUT' === $request->getMethod()) {
            $status = $app['eccube.repository.member']->up($TargetMember);
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

        $TargetMember = $app['eccube.repository.member']->find($id);

        if (!$TargetMember) {
            throw new NotFoundHttpException();
        }

        $status = false;
        if ('PUT' === $request->getMethod()) {
            $status = $app['eccube.repository.member']->down($TargetMember);
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

        $TargetMember = $app['eccube.repository.member']->find($id);
        if (!$TargetMember) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_setting_system_member'));
        }

        $event = new EventArgs(
            array(
                'TargetMember' => $TargetMember
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_DELETE_INITIALIZE, $event);

        $status = $app['eccube.repository.member']->delete($TargetMember);

        if ($status) {
            $app->addSuccess('admin.member.delete.complete', 'admin');
            $event = new EventArgs(
                array(),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_DELETE_COMPLETE, $event);
        } else {
            $app->addError('admin.member.delete.error', 'admin');
        }

        return $app->redirect($app->url('admin_setting_system_member'));
    }
}