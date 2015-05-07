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


namespace Eccube\Controller\Admin\Basis;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class PaymentController extends AbstractController
{
    private $main_title;
    private $sub_title;

    public $form;

    public function __construct()
    {
        $this->main_title = '基本情報管理';
        $this->sub_title = '支払方法設定';
    }

    public function index(Application $app)
    {
        $payments = $app['orm.em']->getRepository('Eccube\Entity\Payment')
            ->findBy(array('del_flg' => 0), array('rank' => 'DESC'));

        return $app['view']->render('Admin/Basis/payment.twig', array(
            'Payments' => $payments,
            'tpl_maintitle' => $this->main_title,
            'tpl_subtitle' => $this->sub_title,
        ));
    }

    public function edit(Application $app, $paymentId = 0, $deleteImage = false)
    {
        $Payment = $app['orm.em']->getRepository('\Eccube\Entity\Payment')
            ->findOrCreate($paymentId);

        $form = $app['form.factory']
            ->createBuilder('payment_register')
            ->getForm();
        $form->setData($Payment);

        $image = null;
        $filename = $Payment->getPaymentImage();
        if (!$deleteImage && $filename !== null) {
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
                if (!$deleteImage && $file !== null) {
                    $extension = $file->guessExtension();
                    if (!$extension) {
                        // 拡張子が推測できなかった場合
                        $extension = 'jpg';
                    }
                    $filename = date('mdHi') . '_' . uniqid('') . '.' . $extension;
                    $file->move($app['config']['image_save_realdir'], $filename);
                    $PaymentData->setPaymentImage($filename);
                }
                if ($deleteImage) {
                    $PaymentData->setPaymentImage(null);
                }

                $app['orm.em']->persist($PaymentData);
                $app['orm.em']->flush();

                $app['session']->getFlashBag()->add('payment.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_basis_payment'));
            }
        }

        return $app['view']->render('Admin/Basis/payment_edit.twig', array(
            'tpl_maintitle' => $this->main_title,
            'tplsubtitle' => $this->sub_title,
            'form' => $form->createView(),
            'payment_id' => $paymentId,
            'Payment' => $Payment,
            'image' => $image,
        ));
    }

    public function deleteImage(Application $app, $paymentId)
    {
        return $this->edit($app, $paymentId, true);
    }

    public function delete(Application $app, $paymentId)
    {
        $repo = $app['orm.em']->getRepository('Eccube\Entity\Payment');
        $Payment = $repo->find($paymentId);

        $Payment
            ->setDelFlg(1)
            ->setRank(0);
        $app['orm.em']->persist($Payment);

        $rank = 1;
        $Payments = $repo->findBy(array('del_flg' => 0), array('rank' => 'ASC'));
        foreach ($Payments as $Payment) {
            if ($Payment->getId() != $paymentId) {
                $Payment->setRank($rank);
                $rank ++;
            }
        }
        $app['orm.em']->flush();

        $app['session']->getFlashBag()->add('payment.complete', 'admin.delete.complete') ;

        return $app->redirect($app['url_generator']->generate('admin_basis_payment'));
    }

    public function up(Application $app, $paymentId)
    {
        $repo = $app['orm.em']->getRepository('Eccube\Entity\Payment');

        $current = $repo->find($paymentId);
        $currentRank = $current->getRank();

        $targetRank = $currentRank + 1;
        $target = $repo->findOneBy(array('rank' => $targetRank));

        $app['orm.em']->persist($target->setRank($currentRank));
        $app['orm.em']->persist($current->setRank($targetRank));
        $app['orm.em']->flush();

        $app['session']->getFlashBag()->add('payment.complete', 'admin.rank.move.complete');

        return $app->redirect($app['url_generator']->generate('admin_basis_payment'));
    }

    public function down(Application $app, $paymentId)
    {
        $repo = $app['orm.em']->getRepository('Eccube\Entity\Payment');

        $current = $repo->find($paymentId);
        $currentRank = $current->getRank();

        $targetRank = $currentRank - 1;
        $target = $repo->findOneBy(array('rank' => $targetRank));

        $app['orm.em']->persist($target->setRank($currentRank));
        $app['orm.em']->persist($current->setRank($targetRank));
        $app['orm.em']->flush();

        $app['session']->getFlashBag()->add('payment.complete', 'admin.rank.move.complete');

        return $app->redirect($app['url_generator']->generate('admin_basis_payment'));
    }
}
