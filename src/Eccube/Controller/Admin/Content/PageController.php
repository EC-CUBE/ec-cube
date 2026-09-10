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

namespace Eccube\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Page;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MainEditType;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\PageLayoutRepository;
use Eccube\Repository\PageRepository;
use Eccube\Service\Content\PageContentService;
use Eccube\Util\CacheUtil;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class PageController extends AbstractController
{
    /**
     * PageController constructor.
     */
    public function __construct(protected PageRepository $pageRepository, protected PageLayoutRepository $pageLayoutRepository, protected DeviceTypeRepository $deviceTypeRepository, private readonly Environment $twig, private readonly CacheUtil $cacheUtil, private readonly PageContentService $pageContentService)
    {
    }

    /**
     * @return array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/content/page', name: 'admin_content_page', methods: ['GET'])]
    #[Template(template: '@admin/Content/page.twig')]
    public function index(Request $request): array
    {
        $Pages = $this->pageRepository->getPageList();

        $event = new EventArgs(
            [
                'Pages' => $Pages,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CONTENT_PAGE_INDEX_COMPLETE);

        return [
            'Pages' => $Pages,
            'router' => $this->router,
        ];
    }

    /**
     * @param string|null $id
     *
     * @return RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/content/page/new', name: 'admin_content_page_new', methods: ['GET', 'POST'])]
    #[Route(path: '/%eccube_admin_route%/content/page/{id}/edit', name: 'admin_content_page_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Content/page_edit.twig')]
    public function edit(Request $request, $id = null): RedirectResponse|array
    {
        $this->addInfoOnce('admin.common.restrict_file_upload_info', 'admin');

        if (null === $id) {
            $Page = $this->pageRepository->newPage();
        } else {
            $Page = $this->pageRepository->find($id);
        }

        $isUserDataPage = true;

        $builder = $this->formFactory
            ->createBuilder(MainEditType::class, $Page);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Page' => $Page,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CONTENT_PAGE_EDIT_INITIALIZE);

        $form = $builder->getForm();

        // 更新時
        $fileName = null;
        $namespace = '@user_data/';
        $PrevPage = clone $Page;
        if ($id) {
            // 編集不可ページはURL、ページ名、ファイル名を保持
            if ($Page->getEditType() >= Page::EDIT_TYPE_DEFAULT) {
                $isUserDataPage = false;
                $namespace = '';
            }
            // テンプレートファイルの取得
            $source = $this->twig->getLoader()
                ->getSourceContext($namespace.$Page->getFileName().'.twig')
                ->getCode();

            $form->get('tpl_data')->setData($source);

            $fileName = $Page->getFileName();
        } elseif ($request->getMethod() === 'GET' && !$form->isSubmitted()) {
            $source = $this->twig->getLoader()
                ->getSourceContext('@admin/empty_page.twig')
                ->getCode();
            $form->get('tpl_data')->setData($source);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Page = $form->getData();

            if (!$isUserDataPage) {
                $Page
                    ->setUrl($PrevPage->getUrl())
                    ->setFileName($PrevPage->getFileName())
                    ->setName($Page->getName());
            }
            // DB 登録とテンプレートファイルの生成は Service に委譲する
            $result = $this->pageContentService->save(
                $Page,
                (string) $form->get('tpl_data')->getData(),
                $form['PcLayout']->getData(),
                $form['SpLayout']->getData(),
                $fileName
            );
            $templatePath = $this->pageContentService->getTemplateDir($Page);
            $filePath = (string) $result->path();

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Page' => $Page,
                    'templatePath' => $templatePath,
                    'filePath' => $filePath,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CONTENT_PAGE_EDIT_COMPLETE);

            $this->addSuccess('admin.common.save_complete', 'admin');

            // キャッシュの削除
            $this->cacheUtil->clearTwigCache();
            $this->cacheUtil->clearDoctrineCache();

            return $this->redirectToRoute('admin_content_page_edit', ['id' => $Page->getId()]);
        }

        if ($isUserDataPage) {
            $templatePath = $this->getParameter('eccube_theme_user_data_dir');
            $url = '';
        } else {
            $templatePath = $this->getParameter('eccube_theme_front_dir');
            $url = $this->router->getRouteCollection()->get($PrevPage->getUrl())->getPath();
        }
        $projectDir = $this->getParameter('kernel.project_dir');
        $templatePath = str_replace($projectDir.'/', '', $templatePath);

        return [
            'form' => $form->createView(),
            'page_id' => $Page->getId(),
            'is_user_data_page' => $isUserDataPage,
            'is_confirm_page' => $Page->getEditType() == Page::EDIT_TYPE_DEFAULT_CONFIRM,
            'template_path' => $templatePath,
            'url' => $url,
        ];
    }

    /**
     * @param string|null $id
     */
    #[Route(path: '/%eccube_admin_route%/content/page/{id}/delete', name: 'admin_content_page_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(Request $request, $id = null): RedirectResponse
    {
        $this->isTokenValid();

        $Page = $this->pageRepository
            ->findOneBy([
                'id' => $id,
            ]);

        if (!$Page) {
            $this->deleteMessage();

            return $this->redirectToRoute('admin_content_page');
        }

        // ユーザーが作ったページのみ削除する
        if ($Page->getEditType() == Page::EDIT_TYPE_USER) {
            $this->pageContentService->remove($Page);

            $event = new EventArgs(
                [
                    'Page' => $Page,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CONTENT_PAGE_DELETE_COMPLETE);

            $this->addSuccess('admin.common.delete_complete', 'admin');

            // キャッシュの削除
            $this->cacheUtil->clearTwigCache();
            $this->cacheUtil->clearDoctrineCache();
        }

        return $this->redirectToRoute('admin_content_page');
    }
}
