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
use Page\Admin\AuthorityManagePage;
use Page\Admin\LoginHistoryPage;
use Page\Admin\MasterDataManagePage;
use Page\Admin\SystemMemberEditPage;

/**
 * @group admin
 * @group admin03
 * @group systeminformation
 * @group ea8
 */
class EA08SysteminfoCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @group vaddy
     */
    public function systeminfo_システム情報(AcceptanceTester $I)
    {
        $I->wantTo('EA0801-UC01-T01 システム情報');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/system');
        $I->see('システム情報システム設定', '.c-pageTitle__titles');

        $I->see('システム情報', '#server_info_box__header > div > span');
        $I->see('EC-CUBE', '#server_info_box__body_inner > div:nth-child(1) > div:first-child');
        $I->see('サーバーOS', '#server_info_box__body_inner > div:nth-child(2) > div:first-child');
        $I->see('DBサーバー', '#server_info_box__body_inner > div:nth-child(3) > div:first-child');
        $I->see('WEBサーバー', '#server_info_box__body_inner > div:nth-child(4) > div:first-child');
        $I->see('PHP', '#server_info_box__body_inner > div:nth-child(5) > div:first-child');
        $I->see('User Agent', '#server_info_box__body_inner > div:nth-child(6) > div:first-child');
        $I->see('PHP情報', '#php_info_box__header > div > span');

        $I->expect('session.save_path をチェックします');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/system/phpinfo');
        $I->scrollTo('a[name=module_session]');
        $I->see(realpath(__DIR__.'/../../var/sessions/'.env('APP_ENV')));
    }

    public function systeminfo_メンバー管理表示(AcceptanceTester $I)
    {
        $I->wantTo('EA0802-UC01-T01 メンバー管理 - 表示');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->see('新規登録', '#ex-member-new > a');
    }

    /**
     * @group vaddy
     */
    public function systeminfo_メンバー管理登録実施(AcceptanceTester $I)
    {
        $I->wantTo('EA0803-UC01-T01 メンバー管理 - 登録 - 登録実施');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('#ex-member-new > a');
        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');

        $I->fillField(['id' => 'admin_member_name'], 'admintest');
        $I->fillField(['id' => 'admin_member_department'], 'admintest department');
        $I->fillField(['id' => 'admin_member_login_id'], 'admintest');
        $I->fillField(['id' => 'admin_member_plain_password_first'], 'password');
        $I->fillField(['id' => 'admin_member_plain_password_second'], 'password');
        $I->selectOption(['id' => 'admin_member_Authority'], 'システム管理者');
        $I->selectOption(['id' => 'admin_member_Work_1'], '稼働');
        $I->click('#member_form .c-conversionArea__container button');
        $I->see('保存しました', '.c-contentsArea .alert-success');

        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');
        $I->see('admintest', '.card-body tbody tr:nth-child(1) td:nth-child(1)');
    }

    public function systeminfo_メンバー管理登録未実施(AcceptanceTester $I)
    {
        $I->wantTo('EA0803-UC01-T02 メンバー管理 - 登録 - 登録未実施');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('#ex-member-new > a');
        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');

        $I->fillField(['id' => 'admin_member_name'], 'admintest2');
        $I->fillField(['id' => 'admin_member_department'], 'admintest department');
        $I->fillField(['id' => 'admin_member_login_id'], 'admintest');
        $I->fillField(['id' => 'admin_member_plain_password_first'], 'password');
        $I->fillField(['id' => 'admin_member_plain_password_second'], 'password');
        $I->selectOption(['id' => 'admin_member_Authority'], 'システム管理者');
        $I->selectOption(['id' => 'admin_member_Work_1'], '稼働');
        $I->click('#member_form .c-conversionArea__container .c-conversionArea__leftBlockItem a');

        $I->see('メンバー管理システム設定', '.c-pageTitle');
        $I->dontSee('admintest2', '#search_result tbody tr:nth-child(1) td:nth-child(1)');
    }

    public function systeminfo_メンバー管理登録異常(AcceptanceTester $I)
    {
        $I->wantTo('EA0803-UC01-T03 メンバー管理 - 登録 - 異常パターン');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('#ex-member-new > a');
        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');

        $I->click('#member_form .c-conversionArea__container button');
        $I->see('入力されていません。', '#member_form div:nth-child(1) div');
    }

    /**
     * @group vaddy
     */
    public function systeminfo_メンバー管理編集実施(AcceptanceTester $I)
    {
        $I->wantTo('EA0803-UC02-T01 メンバー管理 - 編集 - 編集実施');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(6) .action-edit');
        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');

        $I->fillField(['id' => 'admin_member_name'], 'administrator');
        $I->click('#member_form .c-conversionArea__container button');

        $I->see('保存しました', '.c-contentsArea .alert-success');
        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');

        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');
        $I->see('administrator', '.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(1)');
    }

    public function systeminfo_メンバー管理編集未実施(AcceptanceTester $I)
    {
        $I->wantTo('EA0803-UC02-T02 メンバー管理 - 編集 - 編集未実施');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(6) .action-edit');
        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');

        $I->fillField(['id' => 'admin_member_name'], 'administrator2');
        $I->click('#member_form .c-conversionArea__container .c-conversionArea__leftBlockItem a');

        $I->see('メンバー管理システム設定', '.c-pageTitle');
        $I->dontSee('administrator2', '.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(1)');
    }

    public function systeminfo_メンバー管理編集異常(AcceptanceTester $I)
    {
        $I->wantTo('EA0803-UC02-T03 メンバー管理 - 編集 - 異常パターン');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(6) .action-edit');
        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');

        $I->fillField(['id' => 'admin_member_name'], '');
        $I->click('#member_form .c-conversionArea__container button');

        $I->see('入力されていません。', '#member_form div:nth-child(1) div');
    }

    /**
     * @group vaddy
     */
    public function systeminfo_メンバー管理登録下へ(AcceptanceTester $I)
    {
        $I->wantTo('EA0802-UC01-T02 メンバー管理 - 下へ');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(6) .action-down');

        $I->waitForElementNotVisible(['css' => '.modal-backdrop']);

        $I->see('管理者', '.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(1)');
    }

    /**
     * @group vaddy
     */
    public function systeminfo_メンバー管理登録上へ(AcceptanceTester $I)
    {
        $I->wantTo('EA0802-UC01-T03 メンバー管理 - 上へ');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(2) td:nth-child(6) .action-up');

        $I->waitForElementNotVisible(['css' => '.modal-backdrop']);

        $I->see('管理者', '.c-primaryCol .card-body table tbody tr:nth-child(2) td:nth-child(1)');
    }

    /**
     * @group vaddy
     */
    public function systeminfo_メンバー管理削除(AcceptanceTester $I)
    {
        $I->wantTo('EA0802-UC01-T06 メンバー管理 - 削除');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');
        $I->see('administrator', '.card-body tbody tr:nth-child(1) td:nth-child(1)');

        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(6) .action-delete');
        $I->waitForElementVisible(['css' => '.c-primaryCol .card-body table tbody tr:nth-child(1) .modal']);
        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(1) .modal .btn-ec-delete');

        $I->see('削除しました', '.c-contentsArea .alert-success');
        $I->see('管理者', '.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(1)');
    }

    public function systeminfo_メンバー管理自ユーザー削除(AcceptanceTester $I)
    {
        $I->wantTo('EA0802-UC01-T07 メンバー管理 - 自ユーザー削除');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $href = $I->grabAttributeFrom('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(6) .action-delete', 'href');
        $I->assertEquals('', $href, $href.' が一致しません');
    }

    public function systeminfo_セキュリティ管理表示(AcceptanceTester $I)
    {
        $I->wantTo('EA0804-UC01-T01 セキュリティ管理 - 表示');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/security');
        $I->see('セキュリティ管理システム設定', '#page_admin_setting_system_security .c-pageTitle__titles');
        $I->see('セキュリティ設定', '#page_admin_setting_system_security > div.c-container > div.c-contentsArea > form > div > div.c-contentsArea__primaryCol > div > div > div.card-header > div > div.col-8 > span');
    }

    public function systeminfo_セキュリティ管理ディレクトリ名(AcceptanceTester $I)
    {
        $I->wantTo('EA0804-UC01-T02 セキュリティ管理 - ディレクトリ名変更');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/security');
        $I->see('セキュリティ管理システム設定', '#page_admin_setting_system_security .c-pageTitle__titles');

        $I->fillField(['id' => 'admin_security_admin_route_dir'], 'admin2');
        $I->click('#page_admin_setting_system_security form div.c-contentsArea__cols > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');
        $I->loginAsAdmin('', '', 'admin2');

        $I->amOnPage('/admin2/setting/system/security');
        $I->fillField(['id' => 'admin_security_admin_route_dir'], $config['eccube_admin_route']);
        $I->click('#page_admin_setting_system_security form div.c-contentsArea__cols > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');
        $I->loginAsAdmin();
    }

    public function systeminfo_セキュリティ管理SSL(AcceptanceTester $I)
    {
        $I->wantTo('EA0804-UC01-T04 セキュリティ管理 - SSL強制');

        $I->getScenario()->incomplete('このテストを通すと以降のテストが通らなくなってしまっているので一時的にスキップ');

        $httpBaseUrl = $I->getBaseUrl();
        $I->amOnUrl($httpBaseUrl);
        $I->assertRegExp('/^http:\/\//', $I->executeJS('return location.href'), 'httpsにリダイレクトされない');

        $config = Fixtures::get('config');
        $httpsBaseUrl = str_replace('http://', 'https://', $httpBaseUrl);
        $I->amOnUrl($httpsBaseUrl.$config['eccube_admin_route'].'/setting/system/security');
        $I->checkOption(['id' => 'admin_security_force_ssl']);
        $I->click('#page_admin_setting_system_security form div.c-contentsArea__cols > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');

        // httpでアクセスしたらhttpsにリダイレクトされる
        $I->amOnUrl($httpBaseUrl);
        $I->assertRegExp('/^https:\/\//', $I->executeJS('return location.href'), 'httpsにリダイレクトされる');

        // 後続テストのために戻しておく
        $I->amOnUrl($httpsBaseUrl.$config['eccube_admin_route'].'/setting/system/security');
        $I->uncheckOption(['id' => 'admin_security_force_ssl']);
        $I->click('#page_admin_setting_system_security form div.c-contentsArea__cols > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');
    }

    /**
     * GitHub Actions は IPv6で実行されており、アクセス拒否のテストはできない
     */
    public function systeminfo_セキュリティ管理IP制限_拒否リスト(AcceptanceTester $I)
    {
        $I->wantTo('EA0804-UC01-T05 セキュリティ管理 - IP制限（拒否リスト）');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/security');
        $I->see('セキュリティ管理システム設定', '#page_admin_setting_system_security .c-pageTitle__titles');

        $I->fillField(['id' => 'admin_security_admin_deny_hosts'], '1.1.1.1');
        $I->click('#page_admin_setting_system_security form div.c-contentsArea__cols > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');

        $I->see('保存しました', AuthorityManagePage::$完了メッセージ);
    }

    /**
     * @group vaddy
     */
    public function systeminfo_権限管理登録(AcceptanceTester $I)
    {
        // 店舗オーナーアカウントを作成
        $page = SystemMemberEditPage::go_new($I)
            ->メンバー登録([
                'login_id' => 'shop_owner',
                'authority' => '店舗オーナー',
            ])
            ->登録();

        $I->wantTo('EA0805-UC01-T01 権限管理 - 登録');

        // 設定を追加
        AuthorityManagePage::go($I)
            ->行追加()
            ->入力(1, ['1' => '店舗オーナー'], '/setting')
            ->登録();
        $I->see('保存しました', AuthorityManagePage::$完了メッセージ);

        $I->wantTo('EA0805-UC01-T02 権限管理 - 登録');

        // 店舗オーナーでログインし、ナビに表示されないことを確認
        $I->logoutAsAdmin();
        $I->loginAsAdmin('shop_owner', 'password');
        $I->click(['css' => 'a[href="#nav-setting"]']);
        $I->wait(1);
        $I->dontSee('システム設定', '#nav-setting');

        // URL直でもアクセスできないことを確認
        $config = Fixtures::get('config');
        $I->amOnPage("/${config['eccube_admin_route']}/setting/system/member");
        $I->seeInTitle('アクセスできません');

        // 設定を削除
        $I->amOnPage("/{$config['eccube_admin_route']}/logout");
        $I->loginAsAdmin();

        AuthorityManagePage::go($I)
            ->行削除(1)
            ->登録();
        $I->see('保存しました', AuthorityManagePage::$完了メッセージ);

        // 店舗オーナーアカウントでアクセスできることを確認
        $I->logoutAsAdmin();
        $I->loginAsAdmin('shop_owner', 'password');

        $I->click(['css' => 'a[href="#nav-setting"]']);
        $I->wait(1);
        $I->see('システム設定', '#nav-setting');

        $I->amOnPage("/${config['eccube_admin_route']}/setting/system/member");
        $I->seeInTitle('メンバー管理');
    }

    /**
     * @group vaddy
     */
    public function systeminfo_権限管理追加(AcceptanceTester $I)
    {
        $I->wantTo('EA0805-UC03-T01 / UC03-T02 権限管理 - 追加');

        AuthorityManagePage::go($I)
            ->行追加()
            ->入力(1, ['0' => 'システム管理者'], '/content')
            ->入力(2, ['0' => 'システム管理者'], '/store')
            ->登録();

        $I->see('保存しました', AuthorityManagePage::$完了メッセージ);
        $I->dontSee('コンテンツ管理', 'nav .c-mainNavArea__nav');
        $I->dontSee('オーナーズストア', 'nav .c-mainNavArea__nav');

        // アクセスして確認
        $config = Fixtures::get('config');
        $I->amOnPage("/${config['eccube_admin_route']}/content/news");
        $I->seeInTitle('アクセスできません');
    }

    /**
     * @group vaddy
     */
    public function systeminfo_権限管理削除(AcceptanceTester $I)
    {
        $I->wantTo('EA0805-UC04-T01 権限管理 - 削除');

        AuthorityManagePage::go($I)
            ->行削除(2)
            ->行削除(1)
            ->登録();

        $I->see('保存しました', AuthorityManagePage::$完了メッセージ);
        $I->see('コンテンツ管理', 'nav .c-mainNavArea__nav');
        $I->see('オーナーズストア', 'nav .c-mainNavArea__nav');

        // アクセスして確認
        $config = Fixtures::get('config');
        $I->amOnPage("/${config['eccube_admin_route']}/content/news");
        $I->seeInTitle('コンテンツ管理');
    }

    /**
     * @group vaddy
     */
    public function systeminfo_ログ表示(AcceptanceTester $I)
    {
        $I->wantTo('EA0806-UC02-T01 ログ表示');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/log');
        $I->see('ログ表示システム設定', '.c-pageTitle');

        $option = $I->grabTextFrom('#admin_system_log_files option:nth-child(1)');
        $I->selectOption('#admin_system_log_files', $option);

        $I->fillField(['id' => 'admin_system_log_line_max'], '10');
        $I->click(['css' => '#form1 button']);

        $logs = $I->grabTextFrom('.c-contentsArea textarea');
        $I->assertLessThanOrEqual(10, count(explode("\n", $logs)), 'ログ件数を確認');
        $I->seeInField(['id' => 'admin_system_log_line_max'], '10');
    }

    public function systeminfo_ログ表示_異常(AcceptanceTester $I)
    {
        $I->wantTo('EA0806-UC02-T02 / UC02-T03 / UC02-T04 ログ表示異常');

        $config = Fixtures::get('config');

        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/log');
        $I->fillField(['id' => 'admin_system_log_line_max'], '0');
        $I->click(['css' => '#form1 button']);
        $I->see('エラー', '#form1 .invalid-feedback');

        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/log');
        $I->fillField(['id' => 'admin_system_log_line_max'], '-1');
        $I->click(['css' => '#form1 button']);
        $I->see('エラー', '#form1 .invalid-feedback');

        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/log');
        $I->fillField(['id' => 'admin_system_log_line_max'], 'a');
        $I->click(['css' => '#form1 button']);
        $I->see('エラー', '#form1 .invalid-feedback');
    }

    /**
     * @group vaddy
     */
    public function systeminfo_マスターデータ管理(AcceptanceTester $I)
    {
        $I->wantTo('EA0807-UC01-T01 マスターデータ管理(登録/正常)');

        MasterDataManagePage::go($I)->選択('mtb_sex')
            ->入力_ID(3, '3')
            ->入力_Name(3, '無回答')
            ->保存();
        $I->see('保存しました', '.c-contentsArea .alert-success');

        $I->wantTo('EA0807-UC01-T02 マスターデータ管理(登録/正常)');
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/customer/new');
        $I->see('無回答', '#customer_form #admin_customer_sex');

        $I->wantTo('EA0807-UC02-T01 マスターデータ管理(編集/正常)');
        MasterDataManagePage::go($I)->選択('mtb_sex')
            ->入力_ID(3, '3')
            ->入力_Name(3, 'その他')
            ->保存();
        $I->see('保存しました', '.c-contentsArea .alert-success');

        $I->amOnPage('/'.$config['eccube_admin_route'].'/customer/new');
        $I->see('その他', '#customer_form #admin_customer_sex');

        $I->wantTo('EA0807-UC03-T01 マスターデータ管理(編集/異常)');
        $page = MasterDataManagePage::go($I)->選択('mtb_sex');
        $I->seeInField('#form2 input', 'その他');

        $page->入力_ID(3, '3')
            ->入力_Name(3, '')
            ->保存();
        $I->see('入力されていません', '.invalid-feedback');

        $I->wantTo('EA0807-UC04-T01 マスターデータ管理(削除)');
        $page->入力_ID(3, '')
            ->入力_Name(3, '')
            ->保存();
        $I->see('保存しました', '.c-contentsArea .alert-success');
        $I->dontSeeInField('#form2 input', 'その他');
    }

    /**
     * @group vaddy
     */
    public function systeminfo_ログイン履歴検索(AcceptanceTester $I)
    {
        $I->wantTo('EA0808-UC01-T01 ログイン履歴 - 検索');

        LoginHistoryPage::go($I)->検索('admin');

        // １項目目をチェック
        $I->see('admin', '//*[@id="search_form"]/div[4]/div/div/div[2]/div/table/tbody/tr[1]/td[2]');
        $I->see('成功', '//*[@id="search_form"]/div[4]/div/div/div[2]/div/table/tbody/tr[1]/td[5]/span');

        LoginHistoryPage::go($I)->検索('admin-failure');

        $I->see('検索結果：0件が該当しました', LoginHistoryPage::$検索結果_メッセージ);

        $I->logoutAsAdmin();

        // ログインに失敗する
        $I->submitForm('#form1', [
            'login_id' => 'admin-failure',
            'password' => 'password',
        ]);

        $I->loginAsAdmin();

        LoginHistoryPage::go($I)->検索('admin-failure');

        // １項目目をチェック
        $I->see('admin-failure', '//*[@id="search_form"]/div[4]/div/div/div[2]/div/table/tbody/tr[1]/td[2]');
        $I->see('失敗', '//*[@id="search_form"]/div[4]/div/div/div[2]/div/table/tbody/tr[1]/td[5]/span');

        // ステータスで詳細検索

        LoginHistoryPage::go($I)->検索();

        $I->see('失敗', '//*[@id="search_form"]/div[4]/div/div/div[2]/div/table/tbody');
        $I->see('成功', '//*[@id="search_form"]/div[4]/div/div/div[2]/div/table/tbody');

        LoginHistoryPage::go($I)->詳細検索_ステータス('0');

        $I->see('失敗', '//*[@id="search_form"]/div[4]/div/div/div[2]/div/table/tbody');
        $I->dontSee('成功', '//*[@id="search_form"]/div[4]/div/div/div[2]/div/table/tbody');
    }

    /**
     * ATTENTION 後続のテストが失敗するため、最後に実行する必要がある
     */
    public function systeminfo_セキュリティ管理IP制限_許可リスト(AcceptanceTester $I)
    {
        $I->wantTo('EA0804-UC01-T03 セキュリティ管理 - IP制限（許可リスト）');

        $findPlugins = Fixtures::get('findPlugins');
        $Plugins = $findPlugins();
        if (is_array($Plugins) && count($Plugins) > 0) {
            $I->getScenario()->skip('プラグインのアンインストールが必要なため、テストをスキップします');
        }

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/security');
        $I->see('セキュリティ管理システム設定', '#page_admin_setting_system_security .c-pageTitle__titles');

        $I->fillField(['id' => 'admin_security_admin_allow_hosts'], '1.1.1.1');
        $I->click('#page_admin_setting_system_security form div.c-contentsArea__cols > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');

        $I->amOnPage('/'.$config['eccube_admin_route']);
        $I->see('アクセスできません。', '//*[@id="error-page"]//h3');
    }
}
