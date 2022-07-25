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
use Page\Admin\ApiOauthEditPage;
use Page\Admin\ApiOauthPage;
use Page\Admin\ApiWebHookEditPage;
use Page\Admin\ApiWebHookPage;

/**
 * @group plugin
 * @group e2e_plugin
 */
class PL08ApiCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * @skip
     * @param AcceptanceTester $I
     * @return void
     */
    public function web_api_01(AcceptanceTester $I)
    {

    }

    public function web_api_02(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', 'Web API');
        $I->see('Web API', "//tr[contains(.,'Web API')]");
        $I->see('有効', $recommendPluginRow);
        $I->clickWithLeftButton("(" . $recommendPluginRow . "//i[@class='fa fa-pause fa-lg text-secondary'])[1]");
        $I->see('「Web API」を無効にしました。');
        $I->see('Web API', $recommendPluginRow);
        $I->see('無効', $recommendPluginRow);
    }

    public function web_api_03(\AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', 'Web API');
        $I->see('Web API');
        $I->see('無効', $recommendPluginRow);
        $I->clickWithLeftButton("(" . $recommendPluginRow . "//i[@class='fa fa-play fa-lg text-secondary'])[1]");
        $I->see('「Web API」を有効にしました。');
        $I->see('Web API', $recommendPluginRow);
        $I->see('有効', $recommendPluginRow);
    }

    /**
     * @skip
     * @param \AcceptanceTester $I
     * @return void
     */
    public function web_api_04(\AcceptanceTester $I)
    {

    }

    public function web_api_05(AcceptanceTester $I): API0AuthData
    {
        $I->retry(7, 400);
        $faker = \Faker\Factory::create('ja_JP');
        $url = $faker->unique()->url();


        $I->amOnPage('admin/api/oauth');
        $I->see('OAuth管理');
        $I->clickWithLeftButton(Locator::contains('//a', '新規登録'));
        $I->see('OAuthクライアント登録');
        $I->dontSeeInField("#api_admin_client_identifier", "");
        $clientID = $I->grabValueFrom('#api_admin_client_identifier');
        $I->dontSeeInField('#api_admin_client_secret', "");
        $clientSecret = $I->grabValueFrom('#api_admin_client_secret');
        $API0AuthData = new API0AuthData($clientID, $clientSecret, $url);

        # Read + Write
        $I->checkOption('#api_admin_client_scopes_0');
        $I->checkOption('#api_admin_client_scopes_1');

        $I->fillField('#api_admin_client_redirect_uris', $url);

        # Grant Type Check
        $I->seeCheckboxIsChecked('#api_admin_client_grants_0');
        $I->clickWithLeftButton(Locator::contains('//button', '登録'));
        $I->see('保存しました');
        $I->seeInSource($clientID);
        $I->seeInSource($clientSecret);
        $I->seeInSource($url);
        $I->see('read');
        $I->see('write');
        $I->see('authorization_code');
        return $API0AuthData;
    }

    public function web_api_06(AcceptanceTester $I)
    {
        $API0AuthData = $this->web_api_05($I);
        $I->amOnPage('admin/api/oauth');
        $I->see('OAuth管理');
        $testRow = Locator::contains('//tr', $API0AuthData->redirectUri);
        $I->click("(" . $testRow . "//a[@class='btn btn-ec-actionIcon action-delete'])[1]");
        $I->retrySee('OAuthクライアントを削除します。');
        $activeModal = '//div[@class="modal fade show"]';
        $I->click(Locator::contains($activeModal . '//a', '削除'));
        $I->see('削除しました');
        $I->dontSee($API0AuthData->redirectUri);
        $I->dontSeeInSource($API0AuthData->clientID);
        $I->dontSeeInSource($API0AuthData->clientSecret);
    }
}

class API0AuthData
{
    public string $clientID;
    public string $clientSecret;
    public string $redirectUri;

    public function __construct(string $clientID, string $clientSecret, string $redirectUri)
    {
        $this->clientID = $clientID;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }
}
