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
use Eccube\Service\Content\MailTemplateContentService;
use Eccube\Util\CacheUtil;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;
use Twig\Error\LoaderError;

/**
 * Class MailController
 */
class MailController extends AbstractController
{
    /**
     * MailController constructor.
     */
    public function __construct(protected MailTemplateRepository $mailTemplateRepository, private readonly Environment $twig, private readonly CacheUtil $cacheUtil, private readonly MailTemplateContentService $mailTemplateContentService)
    {
    }

    /**
     * @return RedirectResponse|array<string, mixed>
     *
     * @throws LoaderError
     */
    #[Route(path: '/%eccube_admin_route%/setting/shop/mail', name: 'admin_setting_shop_mail', methods: ['GET', 'POST'])]
    #[Route(path: '/%eccube_admin_route%/setting/shop/mail/{id}', name: 'admin_setting_shop_mail_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Setting/Shop/mail.twig')]
    public function index(Request $request, ?MailTemplate $Mail = null): RedirectResponse|array
    {
        $Mail ??= new MailTemplate();
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
            $source = $this->twig->getLoader()
                ->getSourceContext($Mail->getFileName())
                ->getCode();

            $form->get('tpl_data')->setData($source);

            $htmlFileName = $this->getHtmlFileName($Mail->getFileName());

            if ($this->twig->getLoader()->exists($htmlFileName)) {
                $source = $this->twig->getLoader()
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

                // DB 登録とテンプレートファイルの生成は Service に委譲する.
                // HTML 本文が null の場合は HTML パートのファイルを削除する.
                $htmlMailData = $form->get('html_tpl_data')->getData();
                $result = $this->mailTemplateContentService->save(
                    $Mail,
                    (string) $form->get('tpl_data')->getData(),
                    null === $htmlMailData ? null : (string) $htmlMailData
                );
                $templatePath = $this->mailTemplateContentService->getTemplateDir();
                $filePath = (string) $result->path();

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
                $this->cacheUtil->clearTwigCache();

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
     * @return array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/setting/shop/mail/preview', name: 'admin_setting_shop_mail_preview', methods: ['POST'])]
    #[Template(template: '@admin/Setting/Shop/mail_view.twig')]
    public function preview(Request $request): array
    {
        if (!$request->isXmlHttpRequest() || !$this->isTokenValid()) {
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

    #[Route(path: '/%eccube_admin_route%/setting/shop/mail/{id}/delete', name: 'admin_setting_shop_mail_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(MailTemplate $Mail): RedirectResponse
    {
        $this->isTokenValid();

        if (!$Mail->isDeletable()) {
            return $this->redirectToRoute('admin_setting_shop_mail');
        }

        log_info('メールテンプレート削除開始', [$Mail->getId()]);

        $this->mailTemplateContentService->remove($Mail);

        $this->addSuccess('admin.common.delete_complete', 'admin');

        log_info('メールテンプレート削除完了', [$Mail->getId()]);

        return $this->redirectToRoute('admin_setting_shop_mail');
    }

    /**
     * HTML用テンプレート名を取得する
     */
    protected function getHtmlFileName(string $fileName): string
    {
        return $this->mailTemplateContentService->getHtmlFileName($fileName);
    }

    /**
     * テンプレートディレクトリ配下のパスかどうかを検証する
     */
    protected function validateFilePath(string $path): bool
    {
        return $this->mailTemplateContentService->isInsideTemplateDir($path);
    }
}
