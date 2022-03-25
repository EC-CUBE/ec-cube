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

use Codeception\Util\Fixtures;
use Page\Admin\BlockEditPage;
use Page\Admin\BlockManagePage;
use Page\Admin\CssManagePage;
use Page\Admin\FileManagePage;
use Page\Admin\JavaScriptManagePage;
use Page\Admin\LayoutEditPage;
use Page\Admin\LayoutManagePage;
use Page\Admin\MaintenanceManagePage;
use Page\Admin\NewsEditPage;
use Page\Admin\NewsManagePage;
use Page\Admin\PageEditPage;
use Page\Admin\PageManagePage;
use Page\Front\TopPage;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @group admin
 * @group admin02
 * @group contentsmanagement
 * @group ea6
 */
class EA06ContentsManagementCest
{
    public function _before(AcceptanceTester $I)
    {
        // すべてのテストケース実施前にログインしておく
        // ログイン後は管理アプリのトップページに遷移している
        $I->loginAsAdmin();
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function contentsmanagement_新着情報管理(AcceptanceTester $I)
    {
        $I->wantTo('EA0601-UC01-T01(& UC02-T01/UC03-T01) 新着情報管理（作成・編集・削除）');

        // EA0601-UC01-T01_新着情報管理（新規作成）
        NewsManagePage::go($I)->新規登録();

        NewsEditPage::of($I)
            ->入力_日付(date('Y-m-d'))
            ->入力_タイトル('news_title1')
            ->入力_本文('newsnewsnewsnewsnews')
            ->登録();

        $I->see('保存しました', NewsManagePage::$登録完了メッセージ);

        // EA0601-UC02-T01_新着情報管理（編集）
        NewsManagePage::go($I)->一覧_編集(2);
        $new_title = 'news_title '.uniqid();

        NewsEditPage::of($I)
            ->入力_タイトル($new_title)
            ->登録();

        $I->see('保存しました', NewsManagePage::$登録完了メッセージ);

        $NewsListPage = NewsManagePage::go($I);
        $I->assertEquals($new_title, $NewsListPage->一覧_タイトル(2));

        // EA0601-UC03-T01_新着情報管理（削除）
        $NewsListPage->一覧_削除(2);
        $NewsListPage->ポップアップを受け入れます(2);

        $I->assertNotEquals($new_title, $NewsListPage->一覧_タイトル(2));
    }

    /**
     * @env firefox
     * @env chrome
     * @group vaddy
     */
    public function contentsmanagement_ファイル管理(AcceptanceTester $I)
    {
        $I->wantTo('EA0602-UC01-T01(& UC01-T02/UC01-T03/UC01-T04/UC01-T05/UC01-T06/UC01-T07/UC01-T08) ファイル管理');

        $backupDir = sys_get_temp_dir().'/'.random_int(0, 1000);
        $user_data = __DIR__.'/../../html/user_data';
        $fs = new Filesystem();
        $fs->mkdir($backupDir);
        $fs->mirror($user_data, $backupDir);
        try {
            $files = Finder::create()
                ->ignoreDotFiles(false)
                ->in($user_data);
            $fs->remove($files);

            /** @var FileManagePage $FileManagePage */
            $FileManagePage = FileManagePage::go($I)
                ->入力_ファイル('upload.txt')
                ->アップロード();

            $I->see('upload.txt', $FileManagePage->ファイル名(1));

            $FileManagePage->一覧_ダウンロード(1);
            $UploadedFile = $I->getLastDownloadFile('/^upload\.txt$/');
            $I->assertEquals('This is uploaded file.', file_get_contents($UploadedFile));

            $FileManagePage->一覧_パスをコピー(1);
            $I->wait(5);
            $returnText = $I->grabValueFrom('#fileList table > tbody > tr:nth-child(1) > td:nth-child(4) span.copy-file-path input.form-control');
            $I->assertEquals('/html/user_data/upload.txt', $returnText);

            $FileManagePage->一覧_表示(1);
            $I->switchToNewWindow();
            $I->see('This is uploaded file.');

            FileManagePage::go($I)
                ->一覧_削除(1)
                ->一覧_削除_accept(1);
            $I->dontSee('upload.txt', $FileManagePage->ファイル名(1));

            $FileManagePage = FileManagePage::go($I)
                ->入力_フォルダ名('folder1')
                ->フォルダ作成();

            $I->see('folder1', $FileManagePage->ファイル名(1));

            $FileManagePage->一覧_ファイル名_クリック(1);
            $I->see('folder1', $FileManagePage->パンくず(2));

            $config = Fixtures::get('config');
            $I->amOnPage('/'.$config['eccube_admin_route'].'/content/file_manager');
            $I->see('ファイル管理コンテンツ管理', '.c-pageTitle');

            FileManagePage::go($I)
                ->一覧_削除(1)
                ->一覧_削除_accept(1);
            $I->dontSee('folder1', $FileManagePage->ファイル名(1));
        } finally {
            $fs->mirror($backupDir, $user_data);
        }
    }

    public function contentsmanagement_ファイル管理_php(AcceptanceTester $I)
    {
        $I->wantTo('EA0602-UC01-T09_ファイル管理（phpファイルのアップロード）');
        FileManagePage::go($I)
            ->入力_ファイル('upload.php')
            ->アップロード();

        $I->see('phpファイルはアップロードできません', '#form1 .errormsg');
    }

    /**
     * @group vaddy
     */
    public function contentsmanagement_ページ管理(AcceptanceTester $I)
    {
        $I->wantTo('EA0603-UC01-T01(& UC01-T02/UC01-T03) ページ管理');
        $faker = Fixtures::get('faker');
        $page = 'page_'.$faker->word;
        PageManagePage::go($I)->新規入力();

        /* 新規作成時の初期タグ */
        $I->assertEquals(PageEditPage::at($I)->出力_内容(), "{% extends 'default_frame.twig' %}\n\n{% block main %}\n\n{% endblock %}");

        /* 作成 */
        PageEditPage::at($I)
            ->入力_名称($page)
            ->入力_ファイル名($page)
            ->入力_URL($page)
            ->入力_内容($page)
            ->入力_PC用レイアウト('下層ページ用レイアウト')
            ->登録();
        $I->see('保存しました', PageEditPage::$登録完了メッセージ);

        $I->amOnPage('/user_data/'.$page);
        $I->see($page, 'body');

        /* 編集 */
        PageManagePage::go($I)->ページ編集($page);
        PageEditPage::at($I)
            ->入力_内容("{% extends 'default_frame.twig' %}")
            ->登録();
        $I->see('保存しました', PageEditPage::$登録完了メッセージ);

        $I->amOnPage('/user_data/'.$page);
        $config = Fixtures::get('config');
        $I->seeElement('div.ec-layoutRole__footer');

        /* レイアウト編集 */
        LayoutManagePage::go($I)->レイアウト編集('下層ページ用レイアウト');
        LayoutEditPage::at($I)
            ->ブロックを移動('新着情報', '#position_4')
            ->登録();

        $I->see('保存しました', LayoutEditPage::$登録完了メッセージ);
        $I->amOnPage('/user_data/'.$page);
        $I->see('新着情報', '.ec-newsRole');

        LayoutManagePage::go($I)->レイアウト編集('下層ページ用レイアウト');
        LayoutEditPage::at($I)
            ->ブロックを移動('カート', '#position_2')
            ->登録();
        LayoutEditPage::at($I)
            ->ブロックを移動('ログインナビ(共通)', '#position_2')
            ->登録();
        LayoutEditPage::at($I)
            ->ブロックを移動('商品検索', '#position_2')
            ->コンテキストメニューで上に移動('商品検索')
            ->登録();
        LayoutEditPage::at($I)
            ->コンテキストメニューで下に移動('商品検索')
            ->登録();
        LayoutEditPage::at($I)
            ->コンテキストメニューでセクションに移動('商品検索')
            ->登録();
        LayoutEditPage::at($I)
            ->コンテキストメニューでコードプレビュー(
                '商品検索',
                ['xpath' => "//*[@id='block-source-code']//div[contains(text(), 'file that was distributed with this source code.')]"]
            );

        LayoutManagePage::go($I)->レイアウト編集('下層ページ用レイアウト');
        LayoutEditPage::at($I)
            ->ブロックを移動('カート', '#position_0')
            ->選択_プレビューページ('商品一覧ページ')
            ->プレビュー();

        $I->switchToNewWindow();

        /* 削除 */
        PageManagePage::go($I)->削除($page);
        $I->see('削除しました', PageEditPage::$登録完了メッセージ);
        $I->amOnPage('/user_data/'.$page);
        $I->seeInTitle('ページがみつかりません');
    }

    public function contentsmanagement_レイアウト管理(AcceptanceTester $I)
    {
        $I->wantTo('EA0605-UC01-T01 (& UC01-T02 / UC01-T03) レイアウト管理（新規作成・編集・削除）');

        $layoutName = 'layout'.uniqid();
        $pageName = 'page'.uniqid();

        // レイアウト名を未入力で登録
        LayoutManagePage::go($I)->新規登録();
        LayoutEditPage::at($I)
            ->登録();

        // html5のバリデーションエラーで画面遷移しないはず
        $I->seeInCurrentUrl('/admin/content/layout/new');
        $I->cantSee('入力されていません。');

        // レイアウト名を入力して登録
        LayoutEditPage::at($I)
            ->レイアウト名($layoutName)
            ->端末種別('PC')
            ->ブロックを移動('新着情報', '#position_3')
            ->登録();
        $I->see('保存しました');

        // レイアウトを適用した新規ページを作成
        PageManagePage::go($I)->新規入力();
        PageEditPage::at($I)
            ->入力_名称($pageName)
            ->入力_ファイル名($pageName)
            ->入力_URL($pageName)
            ->入力_PC用レイアウト($layoutName)
            ->登録();
        $I->see('保存しました', PageEditPage::$登録完了メッセージ);

        // 作成したページの表示確認 (新着情報がヘッダエリアに表示されていることを確認)
        $I->amOnPage('/user_data/'.$pageName);
        $I->seeElement('.ec-layoutRole__header .ec-newsRole');

        // 編集
        LayoutManagePage::go($I)->レイアウト編集($layoutName);
        LayoutEditPage::at($I)
            ->ブロックを移動('新着情報', '#position_10')
            ->登録();
        $I->see('保存しました', LayoutEditPage::$登録完了メッセージ);

        // 編集したページの表示確認 (新着情報がフッタエリアに表示されていることを確認)
        $I->amOnPage('/user_data/'.$pageName);
        $I->seeElement('.ec-layoutRole__footer .ec-newsRole');
        $I->dontSeeElement('.ec-layoutRole__header .ec-newsRole');

        // レイアウトの削除 → レイアウトを適用したページがあるため削除できない
        LayoutManagePage::go($I)->削除($layoutName);
        $I->see('削除できませんでした', LayoutManagePage::$登録完了メッセージ);

        // レイアウトを適用したページを削除
        PageManagePage::go($I)->削除($pageName);
        $I->see('削除しました', PageEditPage::$登録完了メッセージ);

        // レイアウトの削除
        LayoutManagePage::go($I)->削除($layoutName);
        $I->see('削除しました', LayoutManagePage::$登録完了メッセージ);
        $I->cantSee($layoutName, '.contentsArea');
    }

    public function contentsmanagement_検索未使用ブロック(AcceptanceTester $I)
    {
        $I->wantTo('EA0605-UC01-T04_レイアウト管理（未使用ブロックの検索）');
        $layoutName = '下層ページ用レイアウト';
        /* レイアウト編集 */
        LayoutManagePage::go($I)->レイアウト編集($layoutName);
        $items = $I->grabMultiple(LayoutEditPage::$未使用ブロックアイテム);
        LayoutEditPage::at($I)
            ->検索ブロック名('トピック');

        $I->seeNumberOfElements(LayoutEditPage::$未使用ブロックアイテム, 1);

        LayoutManagePage::go($I)->レイアウト編集($layoutName);
        LayoutEditPage::at($I)
            ->検索ブロック名('');

        $I->seeNumberOfElements(LayoutEditPage::$未使用ブロックアイテム, count($items));
    }

    /**
     * @group vaddy
     */
    public function contentsmanagement_ブロック管理(AcceptanceTester $I)
    {
        $I->wantTo('EA0604-UC01-T01(& UC01-T02/UC01-T03) ブロック管理');
        $faker = Fixtures::get('faker');
        $block = $faker->word.'_block';

        // EA0604-UC01-T01_ブロック管理（新規作成）
        BlockManagePage::go($I)->新規入力();
        BlockEditPage::at($I)
            ->入力_ブロック名($block)
            ->入力_ファイル名($block)
            ->入力_データ('<div id='.$block.'>block1</div>')
            ->登録();
        $I->see('保存しました', BlockEditPage::$登録完了メッセージ);

        // TOPページにブロックを配置
        LayoutManagePage::go($I)->レイアウト編集('トップページ用レイアウト');
        LayoutEditPage::at($I)
            ->ブロックを移動($block, '#position_3')
            ->登録();

        $I->amOnPage('/');
        $I->see('block1', ['id' => $block]);

        BlockManagePage::go($I)->編集(1);
        BlockEditPage::at($I)
            ->入力_データ('<div id='.$block.'>welcome</div>')
            ->登録();
        $I->see('保存しました', BlockEditPage::$登録完了メッセージ);

        $I->amOnPage('/');
        $I->see('welcome', ['id' => $block]);

        // EA0604-UC01-T03_ブロック管理（削除）
        BlockManagePage::go($I)
            ->削除(1)
            ->ポップアップを受け入れます();

        $I->amOnPage('/');
        $I->dontSeeElement(['id' => $block]);
    }

    public function contentsmanagement_CSS管理(AcceptanceTester $I)
    {
        $I->wantTo('EA0606-UC01-T01_CSS管理');

        CssManagePage::go($I)->入力(
            '.ec-headerNaviRole { display: none; }'
        )->登録();
        $I->amOnPage('/');
        $I->reloadPage();
        $I->dontSee('お気に入り', '.ec-headerNaviRole');

        CssManagePage::go($I)->入力('//')->登録();
        $I->amOnPage('/');
        $I->reloadPage();
        $I->see('お気に入り', '.ec-headerNaviRole');
    }

    public function contentsmanagement_JavaScript管理(AcceptanceTester $I)
    {
        $I->wantTo('EA0607-UC01-T01_JavaScript管理');

        $test_text = 'テストのテキスト';

        JavaScriptManagePage::go($I)->入力(
            "$('.ec-headerNaviRole').append('{$test_text}');"
        )->登録();
        $I->amOnPage('/');
        $I->reloadPage();
        $I->see($test_text, '.ec-headerNaviRole');

        JavaScriptManagePage::go($I)->入力('//')->登録();
        $I->amOnPage('/');
        $I->reloadPage();
        $I->dontSee($test_text, '.ec-headerNaviRole');
    }

    public function contentsmanagement_メンテナンス管理(AcceptanceTester $I)
    {
        $I->wantTo('EA0607-UC08-T01_メンテナンス管理');

        $I->expect('メンテナンスモードを有効にします');
        MaintenanceManagePage::go($I)
            ->メンテナンス有効無効();
        $I->see('メンテナンスモードを有効にしました。', MaintenanceManagePage::$完了メッセージ);

        $I->expect('トップページを確認します');
        $I->amOnPage('/');
        $I->see('メンテナンスモードが有効になっています。', '#page_homepage > div.ec-maintenanceAlert > div');
        $I->see('全ての商品', TopPage::$検索_カテゴリ選択);

        $I->expect('ログアウトします');
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/logout');

        $I->expect('トップページを確認します');
        $I->amOnPage('/');
        $I->dontSee('メンテナンスモードが有効になっています。', '#page_homepage > div.ec-maintenanceAlert > div');
        $I->see('ただいまメンテナンス中です。', 'body > div > div > div > div > p.ec-404Role__title.ec-reportHeading');

        // 画面遷移がスムーズにいかない場合があるため、ログイン画面に遷移させておく
        $account = Fixtures::get('admin_account');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/login');
        $I->submitForm('#form1', [
            'login_id' => $account['member'],
            'password' => $account['password'],
        ]);

        $I->expect('メンテナンスモードを無効にします');

        MaintenanceManagePage::go($I)
            ->メンテナンス有効無効();
        $I->see('メンテナンスモードを無効にしました。', MaintenanceManagePage::$完了メッセージ);

        $I->expect('トップページを確認します');
        $I->amOnPage('/');
        $I->dontSee('メンテナンスモードが有効になっています。', '#page_homepage > div.ec-maintenanceAlert > div');
        $I->see('全ての商品', TopPage::$検索_カテゴリ選択);
    }

    public function contentsmanagement_キャッシュ管理(AcceptanceTester $I)
    {
        $I->wantTo('EA0608-UC01-T01_キャッシュ管理');

        $I->expect('トップページを確認します');
        $I->amOnPage('/');
        $I->see('EC-CUBE SHOP', 'h1');

        $I->expect('キャッシュを削除します');
        $config = Fixtures::get('config');
        $I->amOnPage("/{$config['eccube_admin_route']}/content/cache");

        $I->click('.c-contentsArea .btn-ec-conversion');
        $I->waitForElement('.alert', 10);
        $I->see('削除しました', '.alert');

        $I->expect('トップページを確認します');
        $I->amOnPage('/');
        $I->see('EC-CUBE SHOP', 'h1');
    }
}
