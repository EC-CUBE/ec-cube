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
use Eccube\Controller\AbstractController;

class PaymentController extends AbstractController
{
    public $form;

    public function __construct()
    {
    }

    public function index(Application $app)
    {
        $payments = $app['orm.em']->getRepository('Eccube\Entity\Payment')
            ->findBy(array('del_flg' => 0), array('rank' => 'DESC'));

        return $app['view']->render('Admin/Basis/payment.twig', array(
            'Payments' => $payments,
        ));
    }

    public function edit(Application $app, $id = 0, $delete_image = false)
    {
        $Payment = $app['orm.em']->getRepository('\Eccube\Entity\Payment')
            ->findOrCreate($id);

        $form = $app['form.factory']
            ->createBuilder('payment_register')
            ->getForm();
        $form->setData($Payment);

        $image = null;
        $filename = $Payment->getPaymentImage();
        if (!$delete_image && $filename !== null) {
            $image = $app['config']['image_save_urlpath'] . $filename;
        }

        // 登録ボタン押下
        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);

            if ($form->isValid()) {
                $PaymentData = $form->getData();

                // 手数料を設定できない場合には、手数料を0にする
                if ($PaymentData->getChargeFlg() == 2) {
                    $PaymentData->setCharge(0);
                }

                // ファイルアップロード
                $file = $form['payment_image_file']->getData();
                if (!$delete_image && $file !== null) {
                    $extension = $file->guessExtension();
                    if (!$extension) {
                        // 拡張子が推測できなかった場合
                        $extension = 'jpg';
                    }
                    $filename = date('mdHi') . '_' . uniqid('') . '.' . $extension;
                    $file->move($app['config']['image_save_realdir'], $filename);
                    $PaymentData->setPaymentImage($filename);
                }
                if ($delete_image) {
                    $PaymentData->setPaymentImage(null);
                }

                $app['orm.em']->persist($PaymentData);
                $app['orm.em']->flush();

                $app['session']->getFlashBag()->add('payment.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_setting_shop_payment'));
            }
        }

        return $app['view']->render('Admin/Setting/Shop/payment_edit.twig', array(
            'form' => $form->createView(),
            'payment_id' => $id,
            'Payment' => $Payment,
            'image' => $image,
        ));
    }

    public function deleteImage(Application $app, $id)
    {
        return $this->edit($app, $id, true);
    }

    public function delete(Application $app, $id)
    {
        $repo = $app['orm.em']->getRepository('Eccube\Entity\Payment');
        $Payment = $repo->find($id);

        $Payment
            ->setDelFlg(1)
            ->setRank(0);
        $app['orm.em']->persist($Payment);

        $rank = 1;
        $Payments = $repo->findBy(array('del_flg' => 0), array('rank' => 'ASC'));
        foreach ($Payments as $Payment) {
            if ($Payment->getId() != $id) {
                $Payment->setRank($rank);
                $rank ++;
            }
        }
        $app['orm.em']->flush();

        $app['session']->getFlashBag()->add('payment.complete', 'admin.delete.complete') ;

        return $app->redirect($app['url_generator']->generate('admin_setting_shop_payment'));
    }

    public function up(Application $app, $id)
    {
        $repo = $app['orm.em']->getRepository('Eccube\Entity\Payment');

        $current = $repo->find($id);
        $currentRank = $current->getRank();

        $targetRank = $currentRank + 1;
        $target = $repo->findOneBy(array('rank' => $targetRank));

        $app['orm.em']->persist($target->setRank($currentRank));
        $app['orm.em']->persist($current->setRank($targetRank));
        $app['orm.em']->flush();

        $app['session']->getFlashBag()->add('payment.complete', 'admin.rank.move.complete');

        return $app->redirect($app['url_generator']->generate('admin_setting_shop_payment'));
    }

    public function down(Application $app, $id)
    {
        $repo = $app['orm.em']->getRepository('Eccube\Entity\Payment');

        $current = $repo->find($id);
        $currentRank = $current->getRank();

        $targetRank = $currentRank - 1;
        $target = $repo->findOneBy(array('rank' => $targetRank));

        $app['orm.em']->persist($target->setRank($currentRank));
        $app['orm.em']->persist($current->setRank($targetRank));
        $app['orm.em']->flush();

        $app['session']->getFlashBag()->add('payment.complete', 'admin.rank.move.complete');

        return $app->redirect($app['url_generator']->generate('admin_setting_shop_payment'));
    }
}
