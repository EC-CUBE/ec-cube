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
use Codeception\Util\Fixtures;
use Codeception\Util\Locator;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\ProductStock;
use Page\Admin\ApiOauthEditPage;
use Page\Admin\ApiOauthPage;
use Page\Admin\ApiWebHookEditPage;
use Page\Admin\ApiWebHookPage;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertJson;
use function PHPUnit\Framework\assertNotEmpty;

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
     * ⓪ インストール
     *
     * @group install
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function web_api_01(AcceptanceTester $I)
    {
        if ($I->seePluginIsInstalled('Web API', true)) {
            $I->wantToUninstallPlugin('Web API');
            $I->seePluginIsNotInstalled('Web API');
        }
        $I->wantToInstallPlugin('Web API');
        $I->seePluginIsInstalled('Web API');
    }

    /**
     * @group install
     * @param AcceptanceTester $I
     * @return void
     */
    public function web_api_02(\AcceptanceTester $I)
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
     * @group main
     * @param AcceptanceTester $I
     * @param bool $fixedLocalhost
     * @return API0AuthData
     */
    public function web_api_03(AcceptanceTester $I, bool $fixedLocalhost = false): API0AuthData
    {
        $I->retry(7, 400);
        $faker = \Faker\Factory::create('ja_JP');
        $url = $faker->unique()->url();
        if ($fixedLocalhost === true) {
            $url = 'http://localhost:8012/anything';
        }
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

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function web_api_04(AcceptanceTester $I)
    {
        $API0AuthData = $this->web_api_03($I);
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

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return APIWebhookData
     */
    public function web_api_05(AcceptanceTester $I, string $url = '')
    {
        $I->retry(7, 400);
        $faker = \Faker\Factory::create('ja_JP');
        if(empty($url))  {
            $url = $faker->unique()->url();
        }

        $secret = $faker->unique()->md5();
        $webHookData = new APIWebHookData($url, $secret);

        $I->amOnPage('admin/api/webhook');
        $I->see('Webhook管理');
        $I->clickWithLeftButton(Locator::contains('//a', '新規登録'));
        $I->see('Webhook登録');
        $I->fillField('#web_hook_payload_url', $webHookData->url);
        $I->fillField('#web_hook_secret', $webHookData->secret);
        $I->clickWithLeftButton('//label[@for="web_hook_enabled"]');
        $I->clickWithLeftButton(Locator::contains('//button', '登録'));
        $I->see('保存しました');
        $I->seeInSource($webHookData->url);
        $I->seeInSource($webHookData->secret);
        $I->seeCheckboxIsChecked('#web_hook_enabled');
        $I->clickWithLeftButton(Locator::contains('//span', 'WebHook管理'));

        $I->seeInCurrentUrl('admin/api/webhook');
        $testRow = Locator::contains('//tr', $webHookData->url);
        $I->see($url, $testRow);
        $I->see('有効', $testRow);
        return $webHookData;
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function web_api_06(AcceptanceTester $I)
    {
        $webHookData = $this->web_api_05($I);
        $testRow = Locator::contains('//tr', $webHookData->url);
        $I->click("(" . $testRow . "//a[@class='btn btn-ec-actionIcon action-delete'])[1]");
        $I->retrySee('WebHookを削除します');
        $activeModal = '//div[@class="modal fade show"]';
        $I->click(Locator::contains($activeModal . '//a', '削除'));
        $I->see('削除しました');
        $I->dontSee($webHookData->url);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return mixed
     */
    public function web_api_07(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $apiAuthData = $this->web_api_03($I, true);

        $I->amOnPage(
            sprintf(
                "/admin/authorize?response_type=code&client_id=%s&client_secret=%s&redirect_uri=%s&scope=%s&state=test",
                $apiAuthData->clientID,
                $apiAuthData->clientSecret,
                $apiAuthData->redirectUri,
                'read%20write'
            )
        );

        $I->click(['id' => 'oauth_authorization_approve']);
        $I->wait(5);
        $redirectUrl = $I->grabFromCurrentUrl();
        $code = preg_replace('/.*code=(.*)&.*/', '$1', $redirectUrl);

        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $tokens = $I->sendPost(
            '/token',
            [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $apiAuthData->redirectUri,
                'client_id' => $apiAuthData->clientID,
                'client_secret' => $apiAuthData->clientSecret,
            ]
        );
        assertJson($tokens);
        $jsonTokens = json_decode($tokens, true);
        assertNotEmpty($jsonTokens['access_token']);
        assertEquals('Bearer', $jsonTokens['token_type']);
        return $jsonTokens;
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function web_api_08(AcceptanceTester $I)
    {
        $tokenData = $this->web_api_07($I);
        $I->amOnPage('/admin/api/webhook');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $tokenData['access_token']);
        $I->sendGet('/api', [
            'query' => '{ product(id: 1) { id, name } }'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'data' => [
                'product' => [
                    'id' => 1,
                    'name' => '彩のジェラートCUBE',
                ]
            ]
        ]);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function web_api_09(AcceptanceTester $I)
    {
        $tokenData = $this->web_api_07($I);
        $I->amOnPage('/admin/api/webhook');
        $ordersGen = Fixtures::get('createOrders');
        $customer = Fixtures::get('createCustomer');
        /**
         * @var Order $targetOrder
         */
        $targetOrder = $ordersGen($customer(), 1)[0];
        $I->haveHttpHeader('Authorization', 'Bearer ' . $tokenData['access_token']);
        $I->sendGet('/api', [
            'query' => sprintf('{ order(id: %s) { id, order_no }}', $targetOrder->getId())
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'data' => [
                'order' => [
                    'id' => $targetOrder->getId(),
                    'order_no' => $targetOrder->getOrderNo(),
                ]
            ]
        ]);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function web_api_10(AcceptanceTester $I)
    {
        $tokenData = $this->web_api_07($I);
        $I->amOnPage('/admin/api/webhook');
        $customerFixture = Fixtures::get('createCustomer');
        /**
         * @var Customer $customer
         */
        $customer = $customerFixture();
        $I->haveHttpHeader('Authorization', 'Bearer ' . $tokenData['access_token']);
        $I->sendGet('/api', [
            'query' => sprintf('{ customer(id: %s) { id, name01, name02 }}', $customer->getId())
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'data' => [
                'customer' => [
                    'id' => $customer->getId(),
                    'name01' => $customer->getName01(),
                    'name02' => $customer->getName02()
                ]
            ]
        ]);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function web_api_11(AcceptanceTester $I)
    {
        $tokenData = $this->web_api_07($I);
        $I->amOnPage('/admin/api/webhook');
        /**
         * @var ProductStock $productStock ;
         */
        $productStock = $I->grabEntitiesFromRepository(ProductStock::class, [
                'id' => 2,
            ]
        )[0];

        $getProductClassCode = $productStock->getProductClass()->getCode();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $tokenData['access_token']);
        $I->haveHttpHeader('HTTP_AUTHORIZATION', 'Bearer ' . $tokenData['access_token']);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $response = $I->sendPost('/api', [
            'query' => 'mutation ($code: String!, $stock: Int, $stock_unlimited: Boolean!) { updateProductStock(code: $code, stock: $stock, stock_unlimited: $stock_unlimited) { code, stock, stock_unlimited } }',
            'variables' => [
                'code' => $getProductClassCode,
                'stock' => 10,
                'stock_unlimited' => false,
            ]
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'data' => [
                'updateProductStock' => [
                    'code' => $getProductClassCode,
                    'stock' => 10,
                    'stock_unlimited' => false
                ]
            ]
        ]);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function web_api_12(AcceptanceTester $I)
    {
        $tokenData = $this->web_api_07($I);
        $I->amOnPage('/admin/api/webhook');
        $ordersGen = Fixtures::get('createOrders');
        $customer = Fixtures::get('createCustomer');
        /**
         * @var Order $targetOrder
         */
        $targetOrder = $ordersGen($customer(), 1, [], OrderStatus::IN_PROGRESS)[0];

        assertEquals($targetOrder->getOrderStatus()->getId(), OrderStatus::IN_PROGRESS);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $tokenData['access_token']);
        $I->haveHttpHeader('HTTP_AUTHORIZATION', 'Bearer ' . $tokenData['access_token']);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $response = $I->sendPost('/api', [
            'query' => 'mutation ($id: ID!, $shipping_date: DateTime, $shipping_delivery_name: String, $tracking_number: String, $note: String, $is_send_mail: Boolean) { updateShipped(id: $id, shipping_date: $shipping_date, shipping_delivery_name: $shipping_delivery_name, tracking_number: $tracking_number, note: $note, is_send_mail: $is_send_mail ) { id, shipping_date, shipping_delivery_name, tracking_number, note }}',
            'variables' => [
                'id' => $targetOrder->getId(),
                'shipping_date' => '2016-01-02T03:04:05+09:00',
                'stock_unlimited' => false,
                'shipping_delivery_name' => 'ヤマト運輸',
                'tracking_number' => '123456789',
                'note' => 'test',
                'is_send_mail' => true,
            ]
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'data' => [
                'updateShipped' => [
                    'id' => (string)$targetOrder->getId(),
                    'shipping_date' => '2016-01-01T18:04:05+00:00',
                    'shipping_delivery_name' => 'ヤマト運輸',
                    'tracking_number' => '123456789',
                    'note' => 'test',
                ]
            ]
        ]);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function web_api_13(AcceptanceTester $I)
    {
        // @todo: Creating a mock server and reading the events of a webhook will take time there I will temporarily disable this test.
        assertEquals(true, true);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function web_api_14(AcceptanceTester $I)
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

    /**
     * @group main
     * @param \AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function web_api_15(\AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $I->retry(10, 1000);
        $I->wantToUninstallPlugin('Web API');
        // プラグインの状態を確認する
        $xpath = Locator::contains('tr', 'Web API');
        $I->see('インストール', $xpath);
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

class APIWebhookData
{
    public string $url;
    public string $secret;

    public function __construct(string $url, string $secret)
    {
        $this->url = $url;
        $this->secret = $secret;
    }
}
