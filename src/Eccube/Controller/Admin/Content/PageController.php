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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Component;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\PageLayout;
use Eccube\Entity\PageLayoutLayout;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MainEditType;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\PageLayoutRepository;
use Eccube\Util\Str;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Component
 * @Route(service=PageController::class)
 */
class PageController extends AbstractController
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

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
     * @Inject(PageLayoutRepository::class)
     * @var PageLayoutRepository
     */
    protected $pageLayoutRepository;

    /**
     * @Inject(DeviceTypeRepository::class)
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * @Route("/{_admin}/content/page", name="admin_content_page")
     * @Template("Content/page.twig")
     */
    public function index(Application $app, Request $request)
    {
        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayouts = $this->pageLayoutRepository->getPageList($DeviceType);

        $event = new EventArgs(
            array(
                'DeviceType' => $DeviceType,
                'PageLayouts' => $PageLayouts,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_INDEX_COMPLETE, $event);

        return $app->render('Content/page.twig', array(
            'PageLayouts' => $PageLayouts,
        ));
    }

    /**
     * @Route("/{_admin}/content/page/new", name="admin_content_page_new")
     * @Route("/{_admin}/content/page/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_page_edit")
     * @Template("Content/page_edit.twig")
     */
    public function edit(Application $app, Request $request, $id = null)
    {
        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = $this->pageLayoutRepository
            ->findOrCreate($id, $DeviceType);

        $editable = true;

        $builder = $this->formFactory
            ->createBuilder(MainEditType::class, $PageLayout);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'DeviceType' => $DeviceType,
                'PageLayout' => $PageLayout,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        // 更新時
        $fileName = null;
        if ($id) {
            // 編集不可ページはURL、ページ名、ファイル名を保持
            if ($PageLayout->getEditFlg() == PageLayout::EDIT_FLG_DEFAULT) {
                $editable = false;
                $PrevPageLayout = clone $PageLayout;
            }
            // テンプレートファイルの取得
            $file = $this->pageLayoutRepository
                ->getReadTemplateFile($PageLayout->getFileName(), $editable);

            $form->get('tpl_data')->setData($file['tpl_data']);

            $fileName = $PageLayout->getFileName();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $PageLayout = $form->getData();

            if (!$editable) {
                $PageLayout
                    ->setUrl($PrevPageLayout->getUrl())
                    ->setFileName($PrevPageLayout->getFileName())
                    ->setName($PageLayout->getName());
            }
            // DB登録
            $this->entityManager->persist($PageLayout);
            $this->entityManager->flush();

            // ファイル生成・更新
            $templatePath = $this->pageLayoutRepository->getWriteTemplatePath($editable);
            $filePath = $templatePath.'/'.$PageLayout->getFileName().'.twig';

            $fs = new Filesystem();
            $pageData = $form->get('tpl_data')->getData();
            $pageData = Str::convertLineFeed($pageData);
            $fs->dumpFile($filePath, $pageData);

            // 更新でファイル名を変更した場合、以前のファイルを削除
            if ($PageLayout->getFileName() != $fileName && !is_null($fileName)) {
                $oldFilePath = $templatePath.'/'.$fileName.'.twig';
                if ($fs->exists($oldFilePath)) {
                    $fs->remove($oldFilePath);
                }
            }

            foreach ($PageLayout->getPageLayoutLayouts() as $PageLayoutLayout) {
                $PageLayout->removePageLayoutLayout($PageLayoutLayout);
                $this->entityManager->remove($PageLayoutLayout);
                $this->entityManager->flush($PageLayoutLayout);
            }

            $Layout = $form['PcLayout']->getData();
            if ($Layout) {
                $PageLayoutLayout = new PageLayoutLayout();
                $PageLayoutLayout->setLayoutId($Layout->getId());
                $PageLayoutLayout->setLayout($Layout);
                $PageLayoutLayout->setPageId($PageLayout->getId());
                $PageLayoutLayout->setPageLayout($PageLayout);

                $this->entityManager->persist($PageLayoutLayout);
                $this->entityManager->flush($PageLayoutLayout);
            }

            $Layout = $form['SpLayout']->getData();
            if ($Layout) {
                $PageLayoutLayout = new PageLayoutLayout();
                $PageLayoutLayout->setLayoutId($Layout->getId());
                $PageLayoutLayout->setLayout($Layout);
                $PageLayoutLayout->setPageId($PageLayout->getId());
                $PageLayoutLayout->setPageLayout($PageLayout);

                $this->entityManager->persist($PageLayoutLayout);
                $this->entityManager->flush($PageLayoutLayout);
            }

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'PageLayout' => $PageLayout,
                    'templatePath' => $templatePath,
                    'filePath' => $filePath,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_EDIT_COMPLETE, $event);

            $app->addSuccess('admin.register.complete', 'admin');

            // twig キャッシュの削除.
            $finder = Finder::create()->in($this->appConfig['root_dir'].'/app/cache/twig');
            $fs->remove($finder);

            return $app->redirect($app->url('admin_content_page_edit', array('id' => $PageLayout->getId())));
        }

        $templatePath = $this->pageLayoutRepository->getWriteTemplatePath($editable);

        return [
            'form' => $form->createView(),
            'page_id' => $PageLayout->getId(),
            'editable' => $editable,
            'template_path' => $templatePath,
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/{_admin}/content/page/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_page_delete")
     */
    public function delete(Application $app, Request $request, $id = null)
    {
        $this->isTokenValid($app);

        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = $this->pageLayoutRepository
            ->findOneBy(array(
                'id' => $id,
                'DeviceType' => $DeviceType
            ));

        if (!$PageLayout) {
            $app->deleteMessage();

            return $app->redirect($app->url('admin_content_page'));
        }

        // ユーザーが作ったページのみ削除する
        if ($PageLayout->getEditFlg() == PageLayout::EDIT_FLG_USER) {
            $templatePath = $this->pageLayoutRepository->getWriteTemplatePath(true);
            $file = $templatePath.'/'.$PageLayout->getFileName().'.twig';
            $fs = new Filesystem();
            if ($fs->exists($file)) {
                $fs->remove($file);
            }
            $this->entityManager->remove($PageLayout);
            $this->entityManager->flush();

            $event = new EventArgs(
                array(
                    'DeviceType' => $DeviceType,
                    'PageLayout' => $PageLayout,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_DELETE_COMPLETE, $event);

            $app->addSuccess('admin.delete.complete', 'admin');
        }

        return $app->redirect($app->url('admin_content_page'));
    }
}
