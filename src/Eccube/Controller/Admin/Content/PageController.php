<?php

namespace Eccube\Controller\Admin\Content;

use Eccube\Application;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PageController
{
    protected $title = 'コンテンツ管理';

    protected $subtitle = 'ページ詳細設定';
    // todo
    protected $tplDir = 'app';

    public function __construct()
    {
        $this->main_title = 'コンテンツ管理';
        $this->sub_title = 'ページ詳細設定';

        $this->tpl_mainno = 'design';
        $this->tpl_subno = 'main_edit';
    }

    public function index(Application $app, $page_id = null, $device_id = 10)
    {
        $builder = $app['form.factory']->createBuilder('main_edit');

        $form = $builder->getForm();

        // ページエンティティの生成
        if ( $page_id ) {
            // 既存のインスタンスを取得
            // TODO: page_idをUniqにしてpage_idだけの検索にしたい。
            $PageLayout = $app['eccube.repository.page_layout']
                ->getPageProperties($page_id, $device_id);
            // テンプレートファイルの取得
            $PageLayout->setHeaderChk($PageLayout->getHeaderChk() == 1 ? true : false);
            $PageLayout->setFooterChk($PageLayout->getFooterChk() == 1 ? true : false);
            $form->setData($PageLayout);
            $file = $app['eccube.repository.page_layout']
                ->getTemplateFile($PageLayout->getFilename(), $device_id);
            $form->get('tpl_data')->setData($file['tpl_data']);
        }

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {

                // DB登録
                $app['orm.em']->persist($form->getData());
                $app['orm.em']->flush();
                // ファイル生成・更新
                // $this->createFile(), $this->updateFile()

                $app['session']->getFlashBag()->add('page.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_basis_tax_rule'));
            }
        }

        // 登録されているページ一覧の取得
        $PageLayouts = $app['eccube.repository.page_layout']->getPageList($device_id);

        return $app['view']->render('Admin/Content/page.twig', array(
            'tpl_maintitle' => $this->title,
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