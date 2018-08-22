<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Page;
use Eccube\Entity\PageLayout;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MainEditType;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\PageLayoutRepository;
use Eccube\Repository\PageRepository;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class PageController extends AbstractController
{
    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var PageLayoutRepository
     */
    protected $pageLayoutRepository;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * PageController constructor.
     *
     * @param PageRepository $pageRepository
     * @param DeviceTypeRepository $deviceTypeRepository
     */
    public function __construct(
        PageRepository $pageRepository,
        PageLayoutRepository $pageLayoutRepository,
        DeviceTypeRepository $deviceTypeRepository
    ) {
        $this->pageRepository = $pageRepository;
        $this->pageLayoutRepository = $pageLayoutRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/content/page", name="admin_content_page")
     * @Template("@admin/Content/page.twig")
     */
    public function index(Request $request)
    {
        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Pages = $this->pageRepository->getPageList($DeviceType);

        $event = new EventArgs(
            [
                'DeviceType' => $DeviceType,
                'Pages' => $Pages,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_INDEX_COMPLETE, $event);

        return [
            'Pages' => $Pages,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/content/page/new", name="admin_content_page_new")
     * @Route("/%eccube_admin_route%/content/page/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_page_edit")
     * @Template("@admin/Content/page_edit.twig")
     */
    public function edit(Request $request, $id = null, Environment $twig, Router $router)
    {
        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Page = $this->pageRepository
            ->findOrCreate($id, $DeviceType);

        $isUserDataPage = true;

        $builder = $this->formFactory
            ->createBuilder(MainEditType::class, $Page);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'DeviceType' => $DeviceType,
                'Page' => $Page,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        // 更新時
        $fileName = null;
        $namespace = '@user_data/';
        if ($id) {
            // 編集不可ページはURL、ページ名、ファイル名を保持
            if ($Page->getEditType() == Page::EDIT_TYPE_DEFAULT) {
                $isUserDataPage = false;
                $namespace = '';
                $PrevPage = clone $Page;
            }
            // テンプレートファイルの取得
            $source = $twig->getLoader()
                ->getSourceContext($namespace.$Page->getFileName().'.twig')
                ->getCode();

            $form->get('tpl_data')->setData($source);

            $fileName = $Page->getFileName();
        } elseif ($request->getMethod() === 'GET' && !$form->isSubmitted()) {
            $source = $twig->getLoader()
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
            // DB登録
            $this->entityManager->persist($Page);
            $this->entityManager->flush();

            // ファイル生成・更新
            if ($isUserDataPage) {
                $templatePath = $this->getParameter('eccube_theme_user_data_dir');
            } else {
                $templatePath = $this->getParameter('eccube_theme_front_dir');
            }
            $filePath = $templatePath.'/'.$Page->getFileName().'.twig';

            $fs = new Filesystem();
            $pageData = $form->get('tpl_data')->getData();
            $pageData = StringUtil::convertLineFeed($pageData);
            $fs->dumpFile($filePath, $pageData);

            // 更新でファイル名を変更した場合、以前のファイルを削除
            if ($Page->getFileName() != $fileName && !is_null($fileName)) {
                $oldFilePath = $templatePath.'/'.$fileName.'.twig';
                if ($fs->exists($oldFilePath)) {
                    $fs->remove($oldFilePath);
                }
            }

            foreach ($Page->getPageLayouts() as $PageLayout) {
                $Page->removePageLayout($PageLayout);
                $this->entityManager->remove($PageLayout);
                $this->entityManager->flush($PageLayout);
            }

            $Layout = $form['PcLayout']->getData();
            $LastPageLayout = $this->pageLayoutRepository->findOneBy([], ['sort_no' => 'DESC']);
            $sortNo = $LastPageLayout->getSortNo();

            if ($Layout) {
                $PageLayout = new PageLayout();
                $PageLayout->setLayoutId($Layout->getId());
                $PageLayout->setLayout($Layout);
                $PageLayout->setPageId($Page->getId());
                $PageLayout->setSortNo($sortNo++);
                $PageLayout->setPage($Page);

                $this->entityManager->persist($PageLayout);
                $this->entityManager->flush($PageLayout);
            }

            $Layout = $form['SpLayout']->getData();
            if ($Layout) {
                $PageLayout = new PageLayout();
                $PageLayout->setLayoutId($Layout->getId());
                $PageLayout->setLayout($Layout);
                $PageLayout->setPageId($Page->getId());
                $PageLayout->setSortNo($sortNo++);
                $PageLayout->setPage($Page);

                $this->entityManager->persist($PageLayout);
                $this->entityManager->flush($PageLayout);
            }

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Page' => $Page,
                    'templatePath' => $templatePath,
                    'filePath' => $filePath,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_EDIT_COMPLETE, $event);

            $this->addSuccess('admin.register.complete', 'admin');

            // twig キャッシュの削除.
            $cacheDir = $this->getParameter('kernel.cache_dir').'/twig';
            $fs->remove($cacheDir);

            return $this->redirectToRoute('admin_content_page_edit', ['id' => $Page->getId()]);
        }

        if ($isUserDataPage) {
            $templatePath = $this->getParameter('eccube_theme_user_data_dir');
            $url = '';
        } else {
            $templatePath = $this->getParameter('eccube_theme_front_dir');
            $url = $router->getRouteCollection()->get($PrevPage->getUrl())->getPath();
        }
        $projectDir = $this->getParameter('kernel.project_dir');
        $templatePath = str_replace($projectDir.'/', '', $templatePath);

        return [
            'form' => $form->createView(),
            'page_id' => $Page->getId(),
            'is_user_data_page' => $isUserDataPage,
            'template_path' => $templatePath,
            'url' => $url,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/content/page/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_page_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $id = null)
    {
        $this->isTokenValid();

        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Page = $this->pageRepository
            ->findOneBy([
                'id' => $id,
                'DeviceType' => $DeviceType,
            ]);

        if (!$Page) {
            $this->deleteMessage();

            return $this->redirectToRoute('admin_content_page');
        }

        // ユーザーが作ったページのみ削除する
        if ($Page->getEditType() == Page::EDIT_TYPE_USER) {
            $templatePath = $this->getParameter('eccube_theme_user_data_dir');
            $file = $templatePath.'/'.$Page->getFileName().'.twig';
            $fs = new Filesystem();
            if ($fs->exists($file)) {
                $fs->remove($file);
            }
            $this->entityManager->remove($Page);
            $this->entityManager->flush();

            $event = new EventArgs(
                [
                    'DeviceType' => $DeviceType,
                    'Page' => $Page,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_DELETE_COMPLETE, $event);

            $this->addSuccess('admin.delete.complete', 'admin');
        }

        return $this->redirectToRoute('admin_content_page');
    }
}
