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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\PaymentRegisterType;
use Eccube\Repository\PaymentRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class PaymentController extends AbstractController
{
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
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject(PaymentRepository::class)
     * @var PaymentRepository
     */
    protected $paymentRepository;

    public function index(Application $app, Request $request)
    {
        $Payments = $this->paymentRepository
            ->findBy(
                array('del_flg' => 0),
                array('rank' => 'DESC')
            );

        $event = new EventArgs(
            array(
                'Payments' => $Payments,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_INDEX_COMPLETE, $event);

        return $app->render('Setting/Shop/payment.twig', array(
            'Payments' => $Payments,
        ));
    }

    public function edit(Application $app, Request $request, $id = null)
    {
        $Payment = $this->paymentRepository
            ->findOrCreate($id);

        $builder = $this->formFactory
            ->createBuilder(PaymentRegisterType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Payment' => $Payment,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->setData($Payment);

        // 登録ボタン押下
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $PaymentData = $form->getData();

                // 手数料を設定できない場合には、手数料を0にする
                if ($PaymentData->getChargeFlg() == 2) {
                    $PaymentData->setCharge(0);
                }

                // ファイルアップロード
                $file = $form['payment_image']->getData();
                $fs = new Filesystem();
                if ($file && $fs->exists($this->appConfig['image_temp_realdir'] . '/' . $file)) {
                    $fs->rename(
                        $this->appConfig['image_temp_realdir'] . '/' . $file,
                        $this->appConfig['image_save_realdir'] . '/' . $file
                    );
                }

                $this->entityManager->persist($PaymentData);

                $this->entityManager->flush();

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'Payment' => $Payment,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_EDIT_COMPLETE, $event);

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
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $images = $request->files->get('payment_register');
        $filename = null;
        if (isset($images['payment_image_file'])) {
            $image = $images['payment_image_file'];

            //ファイルフォーマット検証
            $mimeType = $image->getMimeType();
            if (0 !== strpos($mimeType, 'image')) {
                throw new UnsupportedMediaTypeHttpException();
            }

            $extension = $image->guessExtension();
            $filename = date('mdHis') . uniqid('_') . '.' . $extension;
            $image->move($this->appConfig['image_temp_realdir'], $filename);
        }
        $event = new EventArgs(
            array(
                'images' => $images,
                'filename' => $filename,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_IMAGE_ADD_COMPLETE, $event);
        $filename = $event->getArgument('filename');

        return $app->json(array('filename' => $filename), 200);
    }

    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $Payment = $this->paymentRepository->find($id);
        if (!$Payment) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_setting_shop_payment'));
        }

        $Payment
            ->setDelFlg(Constant::ENABLED)
            ->setRank(0);
        $this->entityManager->persist($Payment);

        $rank = 1;
        $Payments = $this->paymentRepository->findBy(array('del_flg' => Constant::DISABLED), array('rank' => 'ASC'));
        foreach ($Payments as $Payment) {
            if ($Payment->getId() != $id) {
                $Payment->setRank($rank);
                $rank ++;
            }
        }

        $this->entityManager->flush();

        $event = new EventArgs(
            array(
                'Payment' => $Payment,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_DELETE_COMPLETE, $event);

        $app->addSuccess('admin.delete.complete', 'admin') ;

        return $app->redirect($app->url('admin_setting_shop_payment'));
    }

    public function up(Application $app, $id)
    {
        $this->isTokenValid($app);

        $repo = $this->paymentRepository;

        $current = $repo->find($id);
        $currentRank = $current->getRank();

        $targetRank = $currentRank + 1;
        $target = $repo->findOneBy(array('rank' => $targetRank));

        $this->entityManager->persist($target->setRank($currentRank));
        $this->entityManager->persist($current->setRank($targetRank));
        $this->entityManager->flush();

        $app->addSuccess('admin.rank.move.complete', 'admin');

        return $app->redirect($app->url('admin_setting_shop_payment'));
    }

    public function down(Application $app, $id)
    {
        $this->isTokenValid($app);

        $repo = $this->paymentRepository;

        $current = $repo->find($id);
        $currentRank = $current->getRank();

        $targetRank = $currentRank - 1;
        $target = $repo->findOneBy(array('rank' => $targetRank));

        $this->entityManager->persist($target->setRank($currentRank));
        $this->entityManager->persist($current->setRank($targetRank));
        $this->entityManager->flush();

        $app->addSuccess('admin.rank.move.complete', 'admin');

        return $app->redirect($app->url('admin_setting_shop_payment'));
    }
}
