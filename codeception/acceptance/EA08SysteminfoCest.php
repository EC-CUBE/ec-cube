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

use Codeception\Util\Fixtures;
use Page\Admin\AuthorityManagePage;

/**
 * @group admin
 * @group admin03
 * @group systeminformation
 * @group ea8
 */
class EA08SysteminfoCest
{
    public function _before(\AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    public function systeminfo_システム情報(\AcceptanceTester $I)
    {
        $I->wantTo('EA0801-UC01-T01 システム情報');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/system');
        $I->see('システム情報システム設定', '.c-pageTitle__titles');

        $I->see('システム情報', '#server_info_box__header > div > span');
        $I->see('PHP情報', '#php_info_box__header > div > span');
    }

    public function systeminfo_メンバー管理表示(\AcceptanceTester $I)
    {
        $I->wantTo('EA0802-UC01-T01 メンバー管理 - 表示');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->see('新規登録', '#ex-member-new > a');
    }

    public function systeminfo_メンバー管理登録実施(\AcceptanceTester $I)
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
        $I->fillField(['id' => 'admin_member_password_first'], 'password');
        $I->fillField(['id' => 'admin_member_password_second'], 'password');
        $I->selectOption(['id' => 'admin_member_Authority'], 'システム管理者');
        $I->selectOption(['id' => 'admin_member_Work_1'], '稼働');
        $I->click('#member_form .c-conversionArea__container button');
        $I->see('保存しました', '.c-contentsArea .alert-success');

        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');
        $I->see('admintest', '.card-body tbody tr:nth-child(1) td:nth-child(1)');
    }

    public function systeminfo_メンバー管理登録未実施(\AcceptanceTester $I)
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
        $I->fillField(['id' => 'admin_member_password_first'], 'password');
        $I->fillField(['id' => 'admin_member_password_second'], 'password');
        $I->selectOption(['id' => 'admin_member_Authority'], 'システム管理者');
        $I->selectOption(['id' => 'admin_member_Work_1'], '稼働');
        $I->click('#member_form .c-conversionArea__container .c-conversionArea__leftBlockItem a');

        $I->see('メンバー管理システム設定', '.c-pageTitle');
        $I->dontSee('admintest2', '#search_result tbody tr:nth-child(1) td:nth-child(1)');
    }

    public function systeminfo_メンバー管理登録異常(\AcceptanceTester $I)
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

    public function systeminfo_メンバー管理編集実施(\AcceptanceTester $I)
    {
        $I->wantTo('EA0803-UC02-T01 メンバー管理 - 編集 - 編集実施');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(5) .action-edit');
        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');

        $I->fillField(['id' => 'admin_member_name'], 'administrator');
        $I->click('#member_form .c-conversionArea__container button');

        $I->see('保存しました', '.c-contentsArea .alert-success');
        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');

        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');
        $I->see('administrator', '.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(1)');
    }

    public function systeminfo_メンバー管理編集未実施(\AcceptanceTester $I)
    {
        $I->wantTo('EA0803-UC02-T02 メンバー管理 - 編集 - 編集未実施');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(5) .action-edit');
        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');

        $I->fillField(['id' => 'admin_member_name'], 'administrator2');
        $I->click('#member_form .c-conversionArea__container .c-conversionArea__leftBlockItem a');

        $I->see('メンバー管理システム設定', '.c-pageTitle');
        $I->dontSee('administrator2', '.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(1)');
    }

    public function systeminfo_メンバー管理編集異常(\AcceptanceTester $I)
    {
        $I->wantTo('EA0803-UC03-T01 メンバー管理 - 編集 - 異常パターン');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(5) .action-edit');
        $I->see('メンバー登録', '#member_form .c-contentsArea__primaryCol .card-header .card-title');

        $I->fillField(['id' => 'admin_member_name'], '');
        $I->click('#member_form .c-conversionArea__container button');

        $I->see('入力されていません。', '#member_form div:nth-child(1) div');
    }

    public function systeminfo_メンバー管理登録下へ(\AcceptanceTester $I)
    {
        $I->wantTo('EA0802-UC01-T02 メンバー管理 - 下へ');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(5) .action-down');

        $I->waitForElementNotVisible(['css' => '.modal-backdrop']);

        $I->see('管理者', '.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(1)');
    }

    public function systeminfo_メンバー管理登録上へ(\AcceptanceTester $I)
    {
        $I->wantTo('EA0802-UC01-T03 メンバー管理 - 上へ');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(2) td:nth-child(5) .action-up');

        $I->waitForElementNotVisible(['css' => '.modal-backdrop']);

        $I->see('管理者', '.c-primaryCol .card-body table tbody tr:nth-child(2) td:nth-child(1)');
    }

    public function systeminfo_メンバー管理削除(\AcceptanceTester $I)
    {
        $I->wantTo('EA0802-UC01-T06 メンバー管理 - 削除');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(5) .action-delete');
        $I->waitForElementVisible(['css' => '.c-primaryCol .card-body table tbody tr:nth-child(1) .modal']);
        $I->click('.c-primaryCol .card-body table tbody tr:nth-child(1) .modal .btn-ec-delete');

        $I->see('削除しました', '.c-contentsArea .alert-success');
        $I->see('管理者', '.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(1)');
    }

    public function systeminfo_メンバー管理自ユーザー削除(\AcceptanceTester $I)
    {
        $I->wantTo('EA0802-UC01-T07 メンバー管理 - 自ユーザー削除');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member');
        $I->see('メンバー管理システム設定', '.c-pageTitle');

        $href = $I->grabAttributeFrom('.c-primaryCol .card-body table tbody tr:nth-child(1) td:nth-child(5) .action-delete', 'href');
        $I->assertEquals('', $href, $href.' が一致しません');
    }

    public function systeminfo_セキュリティ管理表示(\AcceptanceTester $I)
    {
        $I->wantTo('EA0804-UC01-T01 セキュリティ管理 - 表示');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/security');
        $I->see('セキュリティ管理システム設定', '#page_admin_setting_system_security .c-pageTitle__titles');
        $I->see('セキュリティ設定', '#page_admin_setting_system_security > div.c-container > div.c-contentsArea > form > div > div.c-contentsArea__primaryCol > div > div > div.card-header > div > div.col-8 > span');
    }

    public function systeminfo_セキュリティ管理ディレクトリ名(\AcceptanceTester $I)
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

    public function systeminfo_セキュリティ管理SSL(\AcceptanceTester $I)
    {
        $I->wantTo('EA0804-UC01-T04 セキュリティ管理 - SSL強制');

        $I->getScenario()->skip('このテストを通すと以降のテストが通らなくなってしまっているので一時的にスキップ');

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

    public function systeminfo_権限管理追加(\AcceptanceTester $I)
    {
        $I->wantTo('EA0805-UC01-T01 権限管理 - 追加');

        AuthorityManagePage::go($I)
            ->行追加()
            ->入力(1, ['0' => 'システム管理者'], '/content')
            ->入力(2, ['0' => 'システム管理者'], '/store')
            ->登録();

        $I->see('保存しました', AuthorityManagePage::$完了メッセージ);
        $I->dontSee('コンテンツ管理', 'nav .c-mainNavArea__nav');
        $I->dontSee('オーナーズストア', 'nav .c-mainNavArea__nav');
    }

    public function systeminfo_権限管理削除(\AcceptanceTester $I)
    {
        $I->wantTo('EA0805-UC02-T01 権限管理 - 削除');

        AuthorityManagePage::go($I)
            ->行削除(2)
            ->行削除(1)
            ->登録();

        $I->see('保存しました', AuthorityManagePage::$完了メッセージ);
        $I->see('コンテンツ管理', 'nav .c-mainNavArea__nav');
        $I->see('オーナーズストア', 'nav .c-mainNavArea__nav');
    }

    public function systeminfo_ログ表示(\AcceptanceTester $I)
    {
        $I->wantTo('EA0806-UC01-T01 ログ表示');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/log');
        $I->see('ログ表示システム設定', '.c-pageTitle');

        $option = $I->grabTextFrom('#admin_system_log_files option:nth-child(1)');
        $I->selectOption('#admin_system_log_files', $option);

        $I->fillField(['id' => 'admin_system_log_line_max'], '1');
        $I->click(['css' => '#form1 button']);

        $I->seeInField(['id' => 'admin_system_log_line_max'], '1');
    }

    public function systeminfo_マスターデータ管理(\AcceptanceTester $I)
    {
        $I->wantTo('EA0807-UC01-T01 マスターデータ管理');

        // 表示
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/masterdata');
        $I->see('マスタデータ管理システム設定', '.c-pageTitle');

        $I->selectOption(['id' => 'admin_system_masterdata_masterdata'], ['Eccube-Entity-Master-Sex' => 'mtb_sex']);
        $I->click('#form1 button');

        $I->fillField(['css' => '#form2 table tbody tr:nth-child(3) td:nth-child(1) input'], '3');
        $I->fillField(['css' => '#form2 table tbody tr:nth-child(3) td:nth-child(2) input'], '無回答');

        $I->click(['css' => '#form2 .c-conversionArea .ladda-button']);

        $I->see('保存しました', '.c-contentsArea .alert-success');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/customer/new');
        $I->see('無回答', '#customer_form #admin_customer_sex');
    }

    /**
     * ATTENTION 後続のテストが失敗するため、最後に実行する必要がある
     */
    public function systeminfo_セキュリティ管理IP制限(\AcceptanceTester $I)
    {
        $I->wantTo('EA0804-UC01-T03 セキュリティ管理 - IP制限');

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
