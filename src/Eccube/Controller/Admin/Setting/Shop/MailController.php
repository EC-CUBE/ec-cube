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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
     * @Route("/%eccube_admin_route%/setting/shop/mail", name="admin_setting_shop_mail", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/setting/shop/mail/{id}", requirements={"id" = "\d+"}, name="admin_setting_shop_mail_edit", methods={"GET", "POST"})
     * @Template("@admin/Setting/Shop/mail.twig")
     */
    public function index(Request $request, Environment $twig, CacheUtil $cacheUtil, MailTemplate $Mail = null)
    {
        $Mail = $Mail ?? new MailTemplate();
        $builder = $this->formFactory
            ->createBuilder(MailType::class, $Mail);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Mail' => $Mail,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_MAIL_INDEX_INITIALIZE);

        $form = $builder->getForm();

        // 更新時
        if (null !== $Mail->getId()) {
            $form['template']->setData($Mail);

            // テンプレートファイルの取得
            $source = $twig->getLoader()
                ->getSourceContext($Mail->getFileName())
                ->getCode();

            $form->get('tpl_data')->setData($source);

            $htmlFileName = $this->getHtmlFileName($Mail->getFileName());

            if ($twig->getLoader()->exists($htmlFileName)) {
                $source = $twig->getLoader()
                    ->getSourceContext($htmlFileName)
                    ->getCode();

                $form->get('html_tpl_data')->setData($source);
            }
        }

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $Mail = $form->getData();
                $Mail->setDeletable(true);
                $this->entityManager->persist($Mail);
                $this->entityManager->flush();

                // ファイル生成・更新
                $templatePath = $this->getParameter('eccube_theme_front_dir');
                $filePath = $templatePath.'/'.$Mail->getFileName();

                $fs = new Filesystem();
                $mailData = $form->get('tpl_data')->getData();
                $mailData = StringUtil::convertLineFeed($mailData);
                $fs->dumpFile($filePath, $mailData);

                // HTMLファイル用
                $htmlMailData = $form->get('html_tpl_data')->getData();
                $htmlFileName = $this->getHtmlFileName($Mail->getFileName());

                if (!is_null($htmlMailData)) {
                    $htmlMailData = StringUtil::convertLineFeed($htmlMailData);
                    $fs->dumpFile($templatePath.'/'.$htmlFileName, $htmlMailData);
                } else {
                    // 空登録の場合は削除
                    $htmlFilePath = $templatePath.'/'.$htmlFileName;
                    if ($this->validateFilePath($htmlFilePath) && is_file($htmlFilePath) ) {
                        $fs->remove($htmlFilePath);
                    }
                }

                $event = new EventArgs(
                    [
                        'form' => $form,
                        'Mail' => $Mail,
                        'templatePath' => $templatePath,
                        'filePath' => $filePath,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_MAIL_INDEX_COMPLETE);

                $this->addSuccess('admin.common.save_complete', 'admin');

                // キャッシュの削除
                $cacheUtil->clearTwigCache();

                return $this->redirectToRoute('admin_setting_shop_mail_edit', ['id' => $Mail->getId()]);
            }
        }

        return [
            'form' => $form->createView(),
            'id' => $Mail->getId(),
            'Mail' => $Mail,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/mail/preview", name="admin_setting_shop_mail_preview", methods={"POST"})
     * @Template("@admin/Setting/Shop/mail_view.twig")
     */
    public function preview(Request $request)
    {
        if (!$request->isXmlHttpRequest() && $this->isTokenValid()) {
            throw new BadRequestHttpException();
        }

        $html_body = $request->get('html_body');

        $event = new EventArgs(
            [
                'html_body' => $html_body,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_MAIL_PREVIEW_COMPLETE);

        return [
            'html_body' => $html_body,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/mail/{id}/delete", requirements={"id" = "\d+"}, name="admin_setting_shop_mail_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MailTemplate $Mail)
    {
        $this->isTokenValid();

        if (!$Mail->isDeletable()) {
            return $this->redirectToRoute('admin_setting_shop_mail');
        }

        log_info('メールテンプレート削除開始', [$Mail->getId()]);

        $this->entityManager->remove($Mail);
        $this->entityManager->flush();

        $fs = new Filesystem();
        $templatePath = $this->getParameter('eccube_theme_front_dir');
        $filePath = $templatePath.'/'.$Mail->getFileName();
        if ($this->validateFilePath($filePath) && is_file($filePath)) {
            $fs->remove($filePath);
        }
        $htmlFilePath = $templatePath.'/'.$this->getHtmlFileName($Mail->getFileName());
        if ($this->validateFilePath($htmlFilePath) && is_file($htmlFilePath)) {
            $fs->remove($htmlFilePath);
        }

        $this->addSuccess('admin.common.delete_complete', 'admin');

        log_info('メールテンプレート削除完了', [$Mail->getId()]);

        return $this->redirectToRoute('admin_setting_shop_mail');
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
        $targetTemplate = pathinfo($fileName);
        $suffix = '.html';

        return $targetTemplate['dirname'].DIRECTORY_SEPARATOR.$targetTemplate['filename'].$suffix.'.'.$targetTemplate['extension'];
    }

    /**
     * テンプレートディレクトリ配下のパスかどうかを検証する
     *
     * @param $path
     * @return bool
     */
    protected function validateFilePath($path)
    {
        $templatePath = realpath($this->getParameter('eccube_theme_front_dir'));
        $path = realpath($path);

        return \str_starts_with($path, $templatePath);
    }
}
