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
use Codeception\Example;
use Codeception\Util\Locator;

/**
 * @group plugin
 * @group e2e_plugin
 */
class PL06SecurityCheckCest
{

    public string $pluginFileBackUpContent = '';

    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * @group install
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function security_01(AcceptanceTester $I)
    {
        if ($I->seePluginIsInstalled('EC-CUBEセキュリティチェックプラグイン', true)) {
            $I->wantToUninstallPlugin('Securitychecker42');
            $I->seePluginIsNotInstalled('EC-CUBEセキュリティチェックプラグイン');
        }
        $I->wantToInstallPlugin('EC-CUBEセキュリティチェックプラグイン');
        $I->seePluginIsInstalled('Securitychecker42');
    }

    /**
     * @group install
     * @param AcceptanceTester $I
     * @return void
     */
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

    /**
     * @group main
     * @param AcceptanceTester $I
     * @param Example $example
     * @return void
     * @dataProvider securityCheckProvider
     */
    public function security_03(AcceptanceTester $I, \Codeception\Example $example)
    {
        if ($example['run'] === false) {
            // @todo: Only run NG pattern for now
            $I->assertEquals(true, true);
            return;
        }
        $I->retry(15, 500);
        $I->amOnPage('/admin/store/plugin/Securitychecker4/config');
        $I->see('セキュリティチェックプラグイン');
        $I->scrollTo('#securitychecker42_config_tools_agreement', 0, 200);
        $I->executeJS('window.scrollTo(0,1000000);');
        $I->wait(5);
        $I->checkOption('#securitychecker42_config_tools_agreement');
        $I->wait(5);
        $I->clickWithLeftButton(Locator::contains('//button', 'セキュリティチェックを実行'));
        $I->retrySee('セキュリティチェックが完了しました。');

        $resultRow = '';

        foreach($example as $key => $result) {
            if($key === 'run') {
                continue;
            }
            switch ($key):
                case('var'):
                    $resultRow = Locator::contains('//div[@class="row"]', 'var 以下のファイル、フォルダが公開されていないか');
                    break;
                case('vendor'):
                    $resultRow = Locator::contains('//div[@class="row"]', 'vendor 以下のファイル、フォルダが公開されていないか');
                    break;
                case('codeception'):
                    $resultRow = Locator::contains('//div[@class="row"]', 'codeception が公開されていないか'); // <<- Dev Environment
                    break;
                case('.env'):
                    $resultRow = Locator::contains('//div[@class="row"]', '.env が公開されていないか'); // <<- Dev Environment
                    break;
                case('debug'):
                    $resultRow = Locator::contains('//div[@class="row"]', 'デバッグモードが有効になっていないか');
                    break;
                case('member_data'):
                    $resultRow = Locator::contains('//div[@class="row"]', '会員データが公開されていないか');
                    break;
                case('ssl'):
                    $resultRow = Locator::contains('//div[@class="row"]', 'SSLが導入されているか');
                    break;
                case('admin_access'):
                    $resultRow = Locator::contains('//div[@class="row"]', '管理画面へのアクセスには常に SSL を利用しているか');
                    break;
            endswitch;
            $I->see($result, $resultRow);
        }
    }


    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function security_12(AcceptanceTester $I)
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

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function security_13(AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $I->retry(10, 1000);
        $I->wantToUninstallPlugin('Securitychecker42');
        // プラグインの状態を確認する
        $xpath = Locator::contains('tr', 'EC-CUBEセキュリティチェックプラグイン');
        $I->see('インストール', $xpath);
    }

    protected function securityCheckProvider(): array
    {
        return [
            'OK' => [
                'run' => false,
                // @todo: Run OK pattern when apache server is ready
                'var' => '問題ありません',
                'vendor' => '問題ありません',
                'codeception' => '問題ありません',
                '.env' => '問題ありません',
                'debug' => '問題ありません',
                'member_data' => '問題ありません',
                'ssl' => '問題ありません',
                'admin_access' => '問題ありません',
                'trusted_hosts' => '問題ありません'
            ],
            'NG' => [
                'run' => true,
                'var' => 'po',
                'vendor' => 'po',
                'codeception' => 'codeception フォルダが外部から存在確認出来ます',
                '.env' => 'po',
                'debug' => 'デバッグモードが有効になっているようです',
                'member_data' => 'po',
                'ssl' => 'SSLが強制されておらず、平文で情報がやり取りされておりますので情報が漏洩する可能性があります。',
                'admin_access' => '管理画面へのアクセスでSSLが強制されておらず、平文で情報がやり取りされておりますので情報が漏洩する可能性があります。',
                'trusted_hosts' => '信頼できるホスト名が設定されていません。'
            ],
        ];
    }
}
