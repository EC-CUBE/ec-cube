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

    public string $pluginFileBackUpContent = '';

    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        if (file_exists(__DIR__ . '/../../../../app/Plugin/Securitychecker42/Controller/ConfigController.php')) {
            $this->pluginFileBackUpContent = file_get_contents(__DIR__ . '/../../../../app/Plugin/Securitychecker42/Controller/ConfigController.php');
        }
    }

    public function _after(AcceptanceTester $I)
    {
        if (file_exists(__DIR__ . '/../../../../app/Plugin/Securitychecker42/Controller/ConfigController.php')) {
            file_put_contents(__DIR__ . '/../../../../app/Plugin/Securitychecker42/Controller/ConfigController.php', $this->pluginFileBackUpContent);
        }
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
     * @return void
     * @dataProvider securityCheckProvider
     */
    public function security_03(AcceptanceTester $I, \Codeception\Example $example)
    {
        // @todo: Only run NG pattern for now
        // directly touch the plugin code to run the tests.
        if ($example['mock_up'] === true) {
            if (!is_dir(__DIR__ . '/../../../../testspace')) {
                mkdir(__DIR__ . '/../../../../testspace');
            }
            foreach($example['files'] as $file) {
                fopen(sprintf(__DIR__ . '/../../../../testspace/%s', $file), 'I see inside the cube.');
            }
            $newSecurityCheckerContents = str_replace('$kernel_project_dir = $this->getParameter(\'kernel.project_dir\');', '$kernel_project_dir = $this->getParameter(\'kernel.project_dir\') . \'/testspace\';', $this->pluginFileBackUpContent);
            file_put_contents(__DIR__ . '/../../../../app/Plugin/Securitychecker42/Controller/ConfigController.php', $newSecurityCheckerContents);
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

        switch ($example['path']):
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
        endswitch;

        $I->see($example['result'], $resultRow);

        //$debugModeResult = Locator::contains('//div[@class="row"]', 'デバッグモードが有効になっていないか'); // <<- hide debug mode during check
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

    private function securityCheckProvider(): array
    {
        return [
            [
                'type' => 'open',
                'mock_up' => true,
                'files' => ['var/bad.html'],
                'path' => 'var',
                'result' => '問題ありません',
            ],
            [
                'type' => 'closed',
                'mock_up' => false,
                'path' => 'var',
                'result' => '問題ありません',
            ],
            [
                'type' => 'open',
                'mock_up' => true,
                'files' => ['autoload.php', 'autoload_classmap.php'],
                'path' => 'vendor',
                'result' => '問題ありません',
            ],
            [
                'type' => 'closed',
                'mock_up' => false,
                'path' => 'vendor',
                'result' => '問題ありません',
            ],

            [
                'type' => 'closed',
                'mock_up' => true,
                'files' => [],
                'path' => 'codeception',
                'result' => '問題ありません',
            ],
            [
                'type' => 'open',
                'mock_up' => false,
                'path' => 'codeception',
                'result' => '問題ありません',
            ],
            [
                'type' => 'closed',
                'mock_up' => false,
                'path' => '.env',
                'result' => '問題ありません',
            ],
            [
                'type' => 'open',
                'mock_up' => false,
                'files' => ['.env'],
                'path' => '.env',
                'result' => '問題ありません',
            ],
//            [
//                'type' => 'open',
//                'mock_up' => false,
//                'env' => 'dev',
//                'result' => '問題ありません',
//            ],
//            [
//                'type' => 'open',
//                'mock_up' => false,
//                'env' => 'prod',
//                'result' => '問題ありません',
//            ]
        ];
    }
}
