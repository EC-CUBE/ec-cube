<?php

namespace Eccube\Controller\Admin\Content;

use Eccube\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PageController
{
    private $app;

    private $form;

    public function __construct()
    {
        $this->main_title = 'コンテンツ管理';
        $this->sub_title = 'ページ詳細設定';

        $this->tpl_mainno = 'design';
        $this->tpl_subno = 'main_edit';
    }

    public function index(Application $app, $page_id = null, $device_id = 10)
    {
        // ページエンティティの生成
        if ( $page_id == null) {
            $PageLayout = $app['eccube.repository.page_layout']->newPageLayout($device_id);

        } else {
            // 既存のインスタンスを取得
//            $PageLayout = $app['eccube.repository.page_layout']->getByPageId($device_id, $page_id); // TODO: $page_idだけの検索したい
        }

        $builder = $app['form.factory']->createBuilder('main_edit', $PageLayout);

        $form = $builder->getForm();

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {

                // DB登録
                $app['orm.em']->persist($PageLayout);
                $app['orm.em']->flush();
                // ファイル生成・更新
                // $this->createFile(), $this->updateFile()

                $app['session']->getFlashBag()->add('page.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_basis_tax_rule'));
            }

        }

        // 登録されているページ一覧の取得
        $PageLayouts = $app['eccube.repository.page_layout']->getPageProperties($device_id, $page_id);

        return $app['view']->render('Admin/Content/page.twig', array(
            'tpl_maintitle' => $this->main_title,
            'tpl_subtitle' => $this->sub_title,
            'PageLayouts' => $PageLayouts,
            'page_id' => $page_id,
            'form' => $form->createView(),
        ));
    }

    public function delete(Application $app, $page_id = null, $device_id = 10)
    {
        return $app->redirect($app['url_generator']->generate('admin_content_page'));
    }

}