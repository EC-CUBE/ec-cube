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


namespace Eccube\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Page;
use Eccube\Entity\PageLayout;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MainEditType;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\PageRepository;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class PageController extends AbstractController
{
    /**
     * @var PageRepository
     */
    protected $pageRepository;

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
        DeviceTypeRepository $deviceTypeRepository
    ) {
        $this->pageRepository = $pageRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
    }

    /**
     * @Route("/%admin_route%/content/page", name="admin_content_page")
     * @Template("@admin/Content/page.twig")
     */
    public function index(Request $request)
    {
        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Pages = $this->pageRepository->getPageList($DeviceType);

        $event = new EventArgs(
            array(
                'DeviceType' => $DeviceType,
                'Pages' => $Pages,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_INDEX_COMPLETE, $event);

        return [
            'Pages' => $Pages,
        ];
    }

    /**
     * @Route("/%admin_route%/content/page/new", name="admin_content_page_new")
     * @Route("/%admin_route%/content/page/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_page_edit")
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
            array(
                'builder' => $builder,
                'DeviceType' => $DeviceType,
                'Page' => $Page,
            ),
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
                $templatePath = $this->getParameter('eccube.theme.user_data_dir');
            } else {
                $templatePath = $this->getParameter('eccube.theme.front_dir');
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
            if ($Layout) {
                $PageLayout = new PageLayout();
                $PageLayout->setLayoutId($Layout->getId());
                $PageLayout->setLayout($Layout);
                $PageLayout->setPageId($Page->getId());
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
                $PageLayout->setPage($Page);

                $this->entityManager->persist($PageLayout);
                $this->entityManager->flush($PageLayout);
            }

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Page' => $Page,
                    'templatePath' => $templatePath,
                    'filePath' => $filePath,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_EDIT_COMPLETE, $event);

            $this->addSuccess('admin.register.complete', 'admin');

            // twig キャッシュの削除.
            $cacheDir = $this->getParameter('kernel.cache_dir').'/twig';
            $fs->remove($cacheDir);

            return $this->redirectToRoute('admin_content_page_edit', array('id' => $Page->getId()));
        }

        if ($isUserDataPage) {
            $templatePath = $this->getParameter('eccube.theme.user_data_dir');
            $url = '';
        } else {
            $templatePath = $this->getParameter('eccube.theme.front_dir');
            $url = $router->getRouteCollection()->get($PrevPage->getUrl())->getPath();
        }

        return [
            'form' => $form->createView(),
            'page_id' => $Page->getId(),
            'is_user_data_page' => $isUserDataPage,
            'template_path' => $templatePath,
            'user_data_route' => $this->getParameter('user_data_route'),
            'url' => $url,
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/%admin_route%/content/page/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_page_delete")
     */
    public function delete(Request $request, $id = null)
    {
        $this->isTokenValid();

        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Page = $this->pageRepository
            ->findOneBy(array(
                'id' => $id,
                'DeviceType' => $DeviceType,
            ));

        if (!$Page) {
            $this->deleteMessage();

            return $this->redirectToRoute('admin_content_page');
        }

        // ユーザーが作ったページのみ削除する
        if ($Page->getEditType() == Page::EDIT_TYPE_USER) {
            $templatePath = $this->getParameter('eccube.theme.user_data_dir');
            $file = $templatePath.'/'.$Page->getFileName().'.twig';
            $fs = new Filesystem();
            if ($fs->exists($file)) {
                $fs->remove($file);
            }
            $this->entityManager->remove($Page);
            $this->entityManager->flush();

            $event = new EventArgs(
                array(
                    'DeviceType' => $DeviceType,
                    'Page' => $Page,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_DELETE_COMPLETE, $event);

            $this->addSuccess('admin.delete.complete', 'admin');
        }

        return $this->redirectToRoute('admin_content_page');
    }
}
