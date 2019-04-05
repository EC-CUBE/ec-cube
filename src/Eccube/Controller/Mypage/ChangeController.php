<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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


namespace Eccube\Controller\Mypage;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;

class ChangeController extends AbstractController
{
    /**
     * 会員情報編集画面.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $Customer = $app->user();
        $LoginCustomer = clone $Customer;
        $app['orm.em']->detach($LoginCustomer);

        $previous_password = $Customer->getPassword();
        $Customer->setPassword($app['config']['default_password']);

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('entry', $Customer);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Customer' => $Customer,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_INITIALIZE, $event);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            log_info('会員編集開始');

            if ($Customer->getPassword() === $app['config']['default_password']) {
                $Customer->setPassword($previous_password);
            } else {
                if ($Customer->getSalt() === null) {
                    $Customer->setSalt($app['eccube.repository.customer']->createSalt(5));
                }
                $Customer->setPassword(
                    $app['eccube.repository.customer']->encryptPassword($app, $Customer)
                );
            }
            $app['orm.em']->flush();

            log_info('会員編集完了');

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Customer' => $Customer,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_COMPLETE, $event);

            return $app->redirect($app->url('mypage_change_complete'));
        }

        $app['security']->getToken()->setUser($LoginCustomer);

        return $app->render('Mypage/change.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * 会員情報編集完了画面.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function complete(Application $app, Request $request)
    {
        return $app->render('Mypage/change_complete.twig');
    }
}
