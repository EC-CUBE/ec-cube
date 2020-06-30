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

use Eccube\Controller\AbstractController;
use Eccube\Entity\MailTemplate;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MailType;
use Eccube\Repository\MailTemplateRepository;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * Class MailController
 */
class MailController extends AbstractController
{
    /**
     * @var MailTemplateRepository
     */
    protected $mailTemplateRepository;

    /**
     * MailController constructor.
     *
     * @param MailTemplateRepository $mailTemplateRepository
     */
    public function __construct(MailTemplateRepository $mailTemplateRepository)
    {
        $this->mailTemplateRepository = $mailTemplateRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/mail", name="admin_setting_shop_mail")
     * @Template("@admin/Setting/Shop/mail.twig")
     */
    public function index()
    {
        $MailTemplates = $this->mailTemplateRepository->getList();

        return ['MailTemplates' => $MailTemplates];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/mail/{id}/delete", requirements={"id" = "\d+"}, name="admin_setting_shop_mail_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $id = null, CacheUtil $cacheUtil)
    {
        $this->isTokenValid();

        /** @var MailTemplate $Mail */
        $Mail = $this->mailTemplateRepository
            ->findOneBy([
                'id' => $id,
            ]);

        if (!$Mail) {
            throw new NotFoundHttpException();
        }

        // ユーザーが作ったページのみ削除する
        if ($Mail->getEditType() == MailTemplate::EDIT_TYPE_USER) {

            $templatePath = $this->getParameter('eccube_theme_front_dir');
            $file = $templatePath.'/'.$Mail->getFileName();
            $htmlFile = $this->getHtmlFileName($file);

            $fs = new Filesystem();
            if ($fs->exists($file)) {
                $fs->remove($file);
            }
            if ($fs->exists($htmlFile)) {
                $fs->remove($htmlFile);
            }
            $this->entityManager->remove($Mail);
            $this->entityManager->flush();

            $event = new EventArgs(
                [
                    'Mail' => $Mail,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_MAIL_DELETE_COMPLETE, $event);

            $this->addSuccess('admin.common.delete_complete', 'admin');

            // キャッシュの削除
            $cacheUtil->clearTwigCache();
            $cacheUtil->clearDoctrineCache();
        }

        return $this->redirectToRoute('admin_setting_shop_mail');
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/mail/edit/new", name="admin_setting_shop_mail_new")
     * @Route("/%eccube_admin_route%/setting/shop/mail/edit/{id}", requirements={"id" = "\d+"}, name="admin_setting_shop_mail_edit")
     * @Template("@admin/Setting/Shop/mail_edit.twig")
     */
    public function edit(Request $request, $id = null, Environment $twig)
    {

        $Mail = null;
        if ($id) {
            $Mail = $this->mailTemplateRepository->find($id);
            if (is_null($Mail)) {
                throw new NotFoundHttpException();
            }
        }

        $builder = $this->formFactory
            ->createBuilder(MailType::class, $Mail);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Mail' => $Mail,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_MAIL_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        $isUserType = false;

        // 更新時
        if (!is_null($Mail)) {

            $htmlFileName = $this->getHtmlFileName($Mail->getFileName());

            // テンプレートファイルの取得
            $source = $twig->getLoader()
                ->getSourceContext($Mail->getFileName())
                ->getCode();

            $form->get('tpl_data')->setData($source);
            if ($twig->getLoader()->exists($htmlFileName)) {
                $source = $twig->getLoader()
                    ->getSourceContext($htmlFileName)
                    ->getCode();

                $form->get('html_tpl_data')->setData($source);
            }
        } else {
            $isUserType = true;
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (is_null($Mail)) {
                $fileName = sprintf('Mail/%s.twig', $form->get('file_name')->getData());
            } else {
                $fileName = $Mail->getFileName();
            }


            // ファイル生成・更新
            $templatePath = $this->getParameter('eccube_theme_front_dir');
            $filePath = $templatePath.'/'.$fileName;

            $fs = new Filesystem();
            $mailData = $form->get('tpl_data')->getData();
            $mailData = StringUtil::convertLineFeed($mailData);
            $fs->dumpFile($filePath, $mailData);

            // HTMLファイル用
            $htmlMailData = $form->get('html_tpl_data')->getData();
            $htmlFile = $this->getHtmlFileName($filePath);
            if (!is_null($htmlMailData)) {
                $htmlMailData = StringUtil::convertLineFeed($htmlMailData);
                $fs->dumpFile($htmlFile, $htmlMailData);
            } else {
                $fs = new Filesystem();
                if ($fs->exists($htmlFile)) {
                    $fs->remove($htmlFile);
                }
            }

            /** @var MailTemplate $Mail */
            $Mail = $form->getData();
            if ($isUserType) {
                $Mail->setEditType(MailTemplate::EDIT_TYPE_USER);
            }
            $Mail->setFileName($fileName);
            $this->entityManager->persist($Mail);
            $this->entityManager->flush();


            $event = new EventArgs(
                [
                    'form' => $form,
                    'Mail' => $Mail,
                    'templatePath' => $templatePath,
                    'filePath' => $filePath,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_MAIL_INDEX_COMPLETE, $event);

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_setting_shop_mail_edit', ['id' => $Mail->getId()]);
        }


        return [
            'form' => $form->createView(),
            'Mail' => $Mail,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/mail/preview", name="admin_setting_shop_mail_preview")
     * @Template("@admin/Setting/Shop/mail_view.twig")
     */
    public function preview(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $html_body = $request->get('html_body');

        $event = new EventArgs(
            [
                'html_body' => $html_body,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_MAIL_PREVIEW_COMPLETE, $event);

        return [
            'html_body' => $html_body,
        ];
    }

    /**
     * HTML用テンプレート名を取得する
     *
     * @param  string $fileName
     *
     * @return string
     */
    protected function getHtmlFileName($fileName)
    {
        // HTMLテンプレートファイルの取得
        $targetTemplate = explode('.', $fileName);
        $suffix = '.html';

        return $targetTemplate[0].$suffix.'.'.$targetTemplate[1];
    }
}
