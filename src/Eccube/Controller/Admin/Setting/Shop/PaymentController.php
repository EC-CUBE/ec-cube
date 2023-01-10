<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Setting\Shop;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Payment;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\PaymentRegisterType;
use Eccube\Repository\PaymentRepository;
use Eccube\Service\Payment\Method\Cash;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PaymentController
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
     * @Route("/%eccube_admin_route%/setting/shop/payment", name="admin_setting_shop_payment", methods={"GET"})
     * @Template("@admin/Setting/Shop/payment.twig")
     */
    public function index(Request $request)
    {
        $Payments = $this->paymentRepository
            ->findBy(
                [],
                ['sort_no' => 'DESC']
            );

        $event = new EventArgs(
            [
                'Payments' => $Payments,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_INDEX_COMPLETE);

        return [
            'Payments' => $Payments,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/payment/new", name="admin_setting_shop_payment_new", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/setting/shop/payment/{id}/edit", requirements={"id" = "\d+"}, name="admin_setting_shop_payment_edit", methods={"GET", "POST"})
     * @Template("@admin/Setting/Shop/payment_edit.twig")
     */
    public function edit(Request $request, Payment $Payment = null)
    {
        if (is_null($Payment)) {
            $Payment = $this->paymentRepository->findOneBy([], ['sort_no' => 'DESC']);
            $sortNo = 1;
            if ($Payment) {
                $sortNo = $Payment->getSortNo() + 1;
            }

            $Payment = new \Eccube\Entity\Payment();
            $Payment
                ->setSortNo($sortNo)
                ->setFixed(true)
                ->setVisible(true);
        }

        $builder = $this->formFactory
            ->createBuilder(PaymentRegisterType::class, $Payment);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Payment' => $Payment,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_EDIT_INITIALIZE);

        $form = $builder->getForm();

        // 既に画像保存されてる場合は取得する
        $oldPaymentImage = $Payment->getPaymentImage();

        $form->setData($Payment);
        $form->handleRequest($request);

        // 登録ボタン押下
        if ($form->isSubmitted() && $form->isValid()) {
            $Payment = $form->getData();

            // ファイルアップロード
            $file = $form['payment_image']->getData();
            $fs = new Filesystem();
            if ($file && strpos($file, '..') === false && $fs->exists($this->getParameter('eccube_temp_image_dir').'/'.$file)) {
                $fs->rename(
                    $this->getParameter('eccube_temp_image_dir').'/'.$file,
                    $this->getParameter('eccube_save_image_dir').'/'.$file
                );
            }

            // Payment method class of Cash to default.
            if (!$Payment->getMethodClass()) {
                $Payment->setMethodClass(Cash::class);
            }
            $this->entityManager->persist($Payment);
            $this->entityManager->flush();

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Payment' => $Payment,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_EDIT_COMPLETE);

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_setting_shop_payment_edit', ['id' => $Payment->getId()]);
        }

        return [
            'form' => $form->createView(),
            'payment_id' => $Payment->getId(),
            'Payment' => $Payment,
            'oldPaymentImage' => $oldPaymentImage,
        ];
    }

    /**
     * 画像アップロード時にリクエストされるメソッド.
     *
     * @see https://pqina.nl/filepond/docs/api/server/#process
     * @Route("/%eccube_admin_route%/setting/shop/payment/image/process", name="admin_payment_image_process", methods={"POST"})
     */
    public function imageProcess(Request $request)
    {
        if (!$request->isXmlHttpRequest() && $this->isTokenValid()) {
            throw new BadRequestHttpException();
        }

        $images = $request->files->get('payment_register');

        $allowExtensions = ['gif', 'jpg', 'jpeg', 'png'];

        $filename = null;
        if (isset($images['payment_image_file'])) {
            $image = $images['payment_image_file'];

            // ファイルフォーマット検証
            $mimeType = $image->getMimeType();
            if (0 !== strpos($mimeType, 'image')) {
                throw new UnsupportedMediaTypeHttpException();
            }

            // 拡張子
            $extension = $image->getClientOriginalExtension();
            if (!in_array(strtolower($extension), $allowExtensions)) {
                throw new UnsupportedMediaTypeHttpException();
            }

            $filename = date('mdHis').uniqid('_').'.'.$extension;
            $image->move($this->getParameter('eccube_temp_image_dir'), $filename);
        }
        $event = new EventArgs(
            [
                'images' => $images,
                'filename' => $filename,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_IMAGE_ADD_COMPLETE);
        $filename = $event->getArgument('filename');

        return new Response($filename);
    }

    /**
     * アップロード画像を取得する際にコールされるメソッド.
     *
     * @see https://pqina.nl/filepond/docs/api/server/#load
     * @Route("/%eccube_admin_route%/setting/shop/payment/image/load", name="admin_payment_image_load", methods={"GET"})
     */
    public function imageLoad(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $dirs = [
            $this->eccubeConfig['eccube_save_image_dir'],
            $this->eccubeConfig['eccube_temp_image_dir'],
        ];

        foreach ($dirs as $dir) {
            $image = \realpath($dir.'/'.$request->query->get('source'));
            $dir = \realpath($dir);

            if (\is_file($image) && \str_starts_with($image, $dir)) {
                $file = new \SplFileObject($image);

                return $this->file($file, $file->getBasename());
            }
        }

        throw new NotFoundHttpException();
    }

    /**
     * アップロード画像をすぐ削除する際にコールされるメソッド.
     *
     * @see https://pqina.nl/filepond/docs/api/server/#revert
     * @Route("/%eccube_admin_route%/setting/shop/payment/image/revert", name="admin_payment_image_revert", methods={"DELETE"})
     */
    public function imageRevert(Request $request)
    {
        if (!$request->isXmlHttpRequest() && $this->isTokenValid()) {
            throw new BadRequestHttpException();
        }

        $tempFile = $this->eccubeConfig['eccube_temp_image_dir'].'/'.$request->getContent();
        if (is_file($tempFile) && stripos(realpath($tempFile), $this->eccubeConfig['eccube_temp_image_dir']) === 0) {
            $fs = new Filesystem();
            $fs->remove($tempFile);

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        throw new NotFoundHttpException();
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/payment/{id}/delete", requirements={"id" = "\d+"}, name="admin_setting_shop_payment_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param Payment $TargetPayment
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Payment $TargetPayment)
    {
        $this->isTokenValid();

        $sortNo = 1;
        $Payments = $this->paymentRepository->findBy([], ['sort_no' => 'ASC']);
        foreach ($Payments as $Payment) {
            $Payment->setSortNo($sortNo++);
        }

        try {
            $this->paymentRepository->delete($TargetPayment);
            $this->entityManager->flush();

            $event = new EventArgs(
                [
                    'Payment' => $TargetPayment,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_PAYMENT_DELETE_COMPLETE);

            $this->addSuccess('admin.common.delete_complete', 'admin');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->entityManager->rollback();

            $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $TargetPayment->getMethod()]);
            $this->addError($message, 'admin');
        }

        return $this->redirectToRoute('admin_setting_shop_payment');
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/payment/{id}/visible", requirements={"id" = "\d+"}, name="admin_setting_shop_payment_visible", methods={"PUT"})
     */
    public function visible(Payment $Payment)
    {
        $this->isTokenValid();

        $Payment->setVisible(!$Payment->isVisible());

        $this->entityManager->flush();

        if ($Payment->isVisible()) {
            $this->addSuccess(trans('admin.common.to_show_complete', ['%name%' => $Payment->getMethod()]), 'admin');
        } else {
            $this->addSuccess(trans('admin.common.to_hide_complete', ['%name%' => $Payment->getMethod()]), 'admin');
        }

        return $this->redirectToRoute('admin_setting_shop_payment');
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/payment/sort_no/move", name="admin_setting_shop_payment_sort_no_move", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function moveSortNo(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        if ($this->isTokenValid()) {
            $sortNos = $request->request->all();
            foreach ($sortNos as $paymentId => $sortNo) {
                /** @var Payment $Payment */
                $Payment = $this->paymentRepository
                    ->find($paymentId);
                $Payment->setSortNo($sortNo);
                $this->entityManager->persist($Payment);
            }
            $this->entityManager->flush();

            return new Response();
        }

        throw new BadRequestHttpException();
    }
}
