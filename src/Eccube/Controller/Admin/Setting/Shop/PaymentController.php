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

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{
    public function index(Application $app)
    {
        $Payments = $app['eccube.repository.payment']
            ->findBy(
                array('del_flg' => 0),
                array('rank' => 'DESC')
            );
        $event = new EventArgs(
            array(
                'payments' => $Payments
            )
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PAYMENT_INDEX_COMPLETE, $event);

        return $app->render('Setting/Shop/payment.twig', array(
            'Payments' => $Payments,
        ));
    }

    public function edit(Application $app, $id = null)
    {
        $Payment = $app['eccube.repository.payment']
            ->findOrCreate($id);

        $form = $app['form.factory']
            ->createBuilder('payment_register')
            ->getForm();
        $event = new EventArgs(
            array(
                'form' => $form,
                'payment' => $Payment
            )
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PAYMENT_EDIT_INITIALIZE, $event);

        $form->setData($Payment);

        // 登録ボタン押下
        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);

            if ($form->isValid()) {
                $PaymentData = $form->getData();

                // 手数料を設定できない場合には、手数料を0にする
                if ($PaymentData->getChargeFlg() == 2) {
                    $PaymentData->setCharge(0);
                }

                // ファイルアップロード
                $file = $form['payment_image']->getData();
                $fs = new Filesystem();
                if ($file && $fs->exists($app['config']['image_temp_realdir'] . '/' . $file)) {
                    $fs->rename(
                        $app['config']['image_temp_realdir'] . '/' . $file,
                        $app['config']['image_save_realdir'] . '/' . $file
                    );
                }

                $app['orm.em']->persist($PaymentData);

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'payment' => $Payment
                    )
                );
                $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PAYMENT_EDIT_COMPLETE, $event);

                $app['orm.em']->flush();

                $app->addSuccess('admin.register.complete', 'admin');

                return $app->redirect($app->url('admin_setting_shop_payment'));
            }
        }

        return $app->render('Setting/Shop/payment_edit.twig', array(
            'form' => $form->createView(),
            'payment_id' => $id,
            'Payment' => $Payment,
        ));
    }

    public function imageAdd(Application $app, Request $request)
    {
        $images = $request->files->get('payment_register');
        $filename = null;
        if (isset($images['payment_image_file'])) {
            $image = $images['payment_image_file'];
            $extension = $image->guessExtension();
            $filename = date('mdHis') . uniqid('_') . '.' . $extension;
            $image->move($app['config']['image_temp_realdir'], $filename);
        }
        $event = new EventArgs(
            array(
                'images' => $images,
                'filename' => $filename
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PAYMENT_IMAGE_ADD_COMPLETE, $event);

        return $app->json(array('filename' => $filename), 200);
    }

    public function delete(Application $app, $id)
    {
        $this->isTokenValid($app);

        $Payment = $app['eccube.repository.payment']->find($id);
        if (!$Payment) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_setting_shop_payment'));
        }

        $Payment
            ->setDelFlg(Constant::ENABLED)
            ->setRank(0);
        $app['orm.em']->persist($Payment);

        $rank = 1;
        $Payments = $app['eccube.repository.payment']->findBy(array('del_flg' => Constant::DISABLED), array('rank' => 'ASC'));
        foreach ($Payments as $Payment) {
            if ($Payment->getId() != $id) {
                $Payment->setRank($rank);
                $rank ++;
            }
        }
        $event = new EventArgs(
            array(
                'payment' => $Payment,
            )
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PAYMENT_DELETE_COMPLETE, $event);

        $app['orm.em']->flush();

        $app->addSuccess('admin.delete.complete', 'admin') ;

        return $app->redirect($app->url('admin_setting_shop_payment'));
    }

    public function up(Application $app, $id)
    {
        $this->isTokenValid($app);

        $repo = $app['orm.em']->getRepository('Eccube\Entity\Payment');

        $current = $repo->find($id);
        $currentRank = $current->getRank();

        $targetRank = $currentRank + 1;
        $target = $repo->findOneBy(array('rank' => $targetRank));

        $app['orm.em']->persist($target->setRank($currentRank));
        $app['orm.em']->persist($current->setRank($targetRank));
        $app['orm.em']->flush();

        $app->addSuccess('admin.rank.move.complete', 'admin');

        return $app->redirect($app->url('admin_setting_shop_payment'));
    }

    public function down(Application $app, $id)
    {
        $this->isTokenValid($app);

        $repo = $app['orm.em']->getRepository('Eccube\Entity\Payment');

        $current = $repo->find($id);
        $currentRank = $current->getRank();

        $targetRank = $currentRank - 1;
        $target = $repo->findOneBy(array('rank' => $targetRank));

        $app['orm.em']->persist($target->setRank($currentRank));
        $app['orm.em']->persist($current->setRank($targetRank));
        $app['orm.em']->flush();

        $app->addSuccess('admin.rank.move.complete', 'admin');

        return $app->redirect($app->url('admin_setting_shop_payment'));
    }
}
