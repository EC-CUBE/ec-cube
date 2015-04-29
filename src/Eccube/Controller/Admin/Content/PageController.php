<?php

namespace Eccube\Controller\Admin\Content;

use Eccube\Application;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PageController
{
    protected $title = 'コンテンツ管理';

    protected $subtitle = 'ページ詳細設定';

    public function index(Application $app, $page_id = null, $device_type_id = 10)
    {

        // TODO: page_idをUniqにしてpage_idだけの検索にしたい。
        $PageLayout = $app['eccube.repository.page_layout']
            ->findOrCreate($page_id, $device_type_id);

        $builder = $app['form.factory']->createBuilder('main_edit');

        $tpl_data = '';
        $editable = true;
        // 更新時
        if ( $page_id ) {
            // TODO: こういう余計な変換なくしたい
            $PageLayout->setHeaderChk($PageLayout->getHeaderChk() == 1 ? true : false);
            $PageLayout->setFooterChk($PageLayout->getFooterChk() == 1 ? true : false);
            // 編集不可ページはURL、ページ名、ファイル名を保持
            if ($PageLayout->getEditFlg() == 2 ) {
                $editable = false;
                $previous_url = $PageLayout->getUrl();
                $previous_filename = $PageLayout->getFilename();
                $previous_name = $PageLayout->getName();
            }
            // テンプレートファイルの取得
            $file = $app['eccube.repository.page_layout']
                ->getTemplateFile($PageLayout->getFilename(), $device_type_id, $editable);
            $tpl_data = $file['tpl_data'];
        }

        $form = $builder->getForm();
        $form->setData($PageLayout);
        $form->get('tpl_data')->setData($tpl_data);

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $PageLayout = $form->getData();
                $PageLayout->setUrl($form->get('filename')->getData());

                if (!$editable) {
                    $PageLayout->setUrl($previous_url);
                    $PageLayout->setFilename($previous_filename);
                    $PageLayout->setName($previous_name);
                }
                // DB登録
                $app['orm.em']->persist($PageLayout);
                $app['orm.em']->flush();
                // ファイル生成・更新
                $templatePath = $app['eccube.repository.page_layout']->getTemplatePath($device_type_id, $editable);
                $filePath = $templatePath . $PageLayout->getFilename() . '.twig';
                $fs = new Filesystem();
                $fs->dumpFile($filePath, $form->get('tpl_data')->getData());
                // TODO:ルーティングの追加
                $app['session']->getFlashBag()->add('page.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_content_page'));
            }
        }

        // 登録されているページ一覧の取得
        $PageLayouts = $app['eccube.repository.page_layout']->getPageList($device_type_id);

        return $app['view']->render('Admin/Content/page.twig', array(
            'tpl_maintitle' => $this->title,
            'tpl_subtitle' => $this->subtitle,
            'PageLayouts' => $PageLayouts,
            'page_id' => $page_id,
            'editable' => $editable,
            'form' => $form->createView(),
        ));
    }

    public function delete(Application $app, $page_id = null, $device_type_id = 10)
    {

        $PageLayout = $app['eccube.repository.page_layout']->findOrCreate($page_id, $device_type_id);

        // ユーザーが作ったページのみ削除する
        if ($PageLayout->getEditFlg() == null ) {
            $templatePath = $app['eccube.repository.page_layout']
                ->getTemplatePath($device_type_id, true);
            $file = $templatePath . $PageLayout->getFileName() . '.twig';
            $fs = new Filesystem();
            if ($fs->exists($file)) {
                $fs->remove($file);
            }
            $app['orm.em']->remove($PageLayout);
            $app['orm.em']->flush();
        }
        return $app->redirect($app['url_generator']->generate('admin_content_page'));
    }




}