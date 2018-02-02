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

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Payment;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\PaymentRegisterType;
use Eccube\Repository\PaymentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * Class PaymentController
 *
 * @package Eccube\Controller\Admin\Setting\Shop
 */
class PaymentController extends AbstractController
{
    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * PaymentController constructor.
     *
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }


    /**
     * @Route("/%eccube_admin_route%/setting/shop/payment", name="admin_setting_shop_payment")
     * @Template("@admin/Setting/Shop/payment.twig")
     */
    public function index(Request $request)
    {
        $Payments = $this->paymentRepository
            ->findBy(
                array(),
                array('sort_no' => 'DESC')
            );

        $event = new EventArgs(
            array(
                'Payments' => $Payments,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_INDEX_COMPLETE, $event);

        return [
            'Payments' => $Payments,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/payment/new", name="admin_setting_shop_payment_new")
     * @Route("/%eccube_admin_route%/setting/shop/payment/{id}/edit", requirements={"id" = "\d+"}, name="admin_setting_shop_payment_edit")
     * @Template("@admin/Setting/Shop/payment_edit.twig")
     */
    public function edit(Request $request, Payment $Payment = null)
    {
        if (is_null($Payment)) {
            // FIXME
            $Payment = $this->paymentRepository
                ->findOrCreate(0);
        }

        $builder = $this->formFactory
            ->createBuilder(PaymentRegisterType::class, $Payment);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Payment' => $Payment,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        // 登録ボタン押下
        if ($form->isSubmitted() && $form->isValid()) {
            $Payment = $form->getData();

            // ファイルアップロード
            $file = $form['payment_image']->getData();
            $fs = new Filesystem();
            if ($file && $fs->exists($this->getParameter('eccube.temp_image_dir').'/'.$file)) {
                $fs->rename(
                    $this->getParameter('eccube.temp_image_dir').'/'.$file,
                    $this->getParameter('eccube.save_image_dir').'/'.$file
                );
            }

            $Payment->setVisible(true);
            $this->entityManager->persist($Payment);
            $this->entityManager->flush();

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Payment' => $Payment,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_EDIT_COMPLETE, $event);

            $this->addSuccess('admin.register.complete', 'admin');

            return $this->redirectToRoute('admin_setting_shop_payment');
        }

        return [
            'form' => $form->createView(),
            'payment_id' => $Payment->getId(),
            'Payment' => $Payment,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/payment/image/add", name="admin_payment_image_add")
     */
    public function imageAdd(Request $request)
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
            $filename = date('mdHis').uniqid('_').'.'.$extension;
            $image->move($this->getParameter('eccube.temp_image_dir'), $filename);
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

        return $this->json(array('filename' => $filename), 200);
    }

    /**
     * @Method("DELETE")
     * @Route("/%eccube_admin_route%/setting/shop/payment/{id}/delete", requirements={"id" = "\d+"}, name="admin_setting_shop_payment_delete")
     *
     * @param Request $request
     * @param Payment $TargetPayment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Payment $TargetPayment)
    {
        $this->isTokenValid();

        $sortNo = 1;
        $Payments = $this->paymentRepository->findBy(array(), array('sort_no' => 'ASC'));
        foreach ($Payments as $Payment) {
            $Payment->setSortNo($sortNo++);
        }

        try {
            $this->paymentRepository->delete($TargetPayment);
            $this->entityManager->flush();

            $event = new EventArgs(
                array(
                    'Payment' => $TargetPayment,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_DELETE_COMPLETE, $event);

            $this->addSuccess('admin.delete.complete', 'admin');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->entityManager->rollback();

            $message = $this->translator->trans('admin.delete.failed.foreign_key', ['%name%' => '支払方法']);
            $this->addError($message, 'admin');
        }

        return $this->redirectToRoute('admin_setting_shop_payment');
    }

    /**
     * @Method("PUT")
     * @Route("/%eccube_admin_route%/setting/shop/payment/{id}/up", requirements={"id" = "\d+"}, name="admin_setting_shop_payment_up")
     */
    public function up(Payment $current)
    {
        $this->isTokenValid();

        $currentSortNo = $current->getSortNo();
        $targetSortNo = $currentSortNo + 1;

        $target = $this->paymentRepository->findOneBy(array('sort_no' => $targetSortNo));

        $target->setSortNo($currentSortNo);
        $current->setSortNo($targetSortNo);

        $this->entityManager->flush();

        $this->addSuccess('admin.sort_no.move.complete', 'admin');

        return $this->redirectToRoute('admin_setting_shop_payment');
    }

    /**
     * @Method("PUT")
     * @Route("/%eccube_admin_route%/setting/shop/payment/{id}/down", requirements={"id" = "\d+"}, name="admin_setting_shop_payment_down")
     */
    public function down(Payment $current)
    {
        $this->isTokenValid();

        $currentSortNo = $current->getSortNo();
        $targetSortNo = $currentSortNo - 1;

        $target = $this->paymentRepository->findOneBy(array('sort_no' => $targetSortNo));

        $target->setSortNo($currentSortNo);
        $current->setSortNo($targetSortNo);

        $this->entityManager->flush();

        $this->addSuccess('admin.sort_no.move.complete', 'admin');

        return $this->redirectToRoute('admin_setting_shop_payment');
    }

    /**
     * @Method("PUT")
     * @Route("/%eccube_admin_route%/setting/shop/payment/{id}/visible", requirements={"id" = "\d+"}, name="admin_setting_shop_payment_visible")
     */
    public function visible(Payment $Payment)
    {
        $this->isTokenValid();

        $Payment->setVisible(!$Payment->isVisible());

        $this->entityManager->flush();

        if ($Payment->isVisible()) {
            $this->addSuccess('admin.payment.visible.complete', 'admin');
        } else {
            $this->addSuccess('admin.payment.invisible.complete', 'admin');
        }

        return $this->redirectToRoute('admin_setting_shop_payment');
    }
}
