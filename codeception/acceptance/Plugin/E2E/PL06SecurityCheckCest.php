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

namespace Plugin\E2E;

use AcceptanceTester;
use Codeception\Util\Locator;

/**
 * @group plugin
 * @group e2e_plugin
 */
class PL06SecurityCheckCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * @param AcceptanceTester $I

     *
     * @return void
     */
    public function security_01(AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', 'Securitychecker42');
        $I->see('Securitychecker42', $recommendPluginRow);
        $I->see('有効', $recommendPluginRow);
        $I->clickWithLeftButton("(//tr[contains(.,'Securitychecker42')]//i[@class='fa fa-pause fa-lg text-secondary'])[1]");
        $I->see('「Securitychecker42」を無効にしました。');
        $I->see('Securitychecker42', $recommendPluginRow);
        $I->see('無効', $recommendPluginRow);
    }

    public function security_02(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $couponRow = Locator::contains('//tr', 'Securitychecker42');
        $I->see('Securitychecker42', $couponRow);
        $I->see('無効', $couponRow);
        $I->clickWithLeftButton("(//tr[contains(.,'Securitychecker42')]//i[@class='fa fa-play fa-lg text-secondary'])[1]");
        $I->see('「Securitychecker42」を有効にしました。');
        $I->see('Securitychecker42', $couponRow);
        $I->see('有効', $couponRow);
    }

    public function security_03(AcceptanceTester $I)
    {
        $I->retry(15, 500);
        $I->amOnPage('/admin/store/plugin/Securitychecker4/config');
        $I->see('セキュリティチェックプラグイン');
        $I->scrollTo('#securitychecker42_config_tools_agreement', 0, 200);
        $I->executeJS('window.scrollTo(0,1000000);');
        $I->wait(5);
        $I->checkOption('#securitychecker42_config_tools_agreement');
        $I->wait(20);
        $I->clickWithLeftButton(Locator::contains('//button', 'セキュリティチェックを実行'));
        $I->retrySee('セキュリティチェックが完了しました。');

        $varResult = Locator::contains('//div[@class="row"]', 'var 以下のファイル、フォルダが公開されていないか');
        $vendorResult = Locator::contains('//div[@class="row"]', 'vendor 以下のファイル、フォルダが公開されていないか');
//        $codeCeptionResult = Locator::contains('//div[@class="row"]', 'codeception が公開されていないか'); // <<- hide codeception during check
        $envResult = Locator::contains('//div[@class="row"]', '.env が公開されていないか'); // <<- Dev Environment
        $debugModeResult = Locator::contains('//div[@class="row"]', 'デバッグモードが有効になっていないか'); // <<- hide debug mode during check
        $sslResult = Locator::contains('//div[@class="row"]', 'SSLが導入されているか'); // <<- pretend https is enabled
        $administratorAccessResult = Locator::contains('//div[@class="row"]', '管理画面へのアクセスには常に SSL を利用しているか'); // <<- pretend https is enabled
        $trustedHostsResult = Locator::contains('//div[@class="row"]', 'TRUSTED_HOSTSを設定しているか'); // <<- pretend https is enabled

        $I->see('問題ありません', $varResult);
        $I->see('問題ありません', $vendorResult);
//        $I->see('問題ありません', $codeCeptionResult);
        $I->see('問題ありません', $envResult);
        $I->see('問題ありません', $debugModeResult);
        $I->see('問題ありません', $sslResult);
        $I->see('問題ありません', $administratorAccessResult);
        $I->see('問題ありません', $trustedHostsResult);
    }
}
