<?php

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\String\ByteString;

class TradeLawControllerTest extends AbstractAdminWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * 正式的に設定画面が読み込まれ、正しい初期入力フィールドが表示されることを確認するテスト
     * Test to confirm settings index page loads and displays the correct initial input fields
     *
     * @return void
     */
    public function testIndexView()
    {
        $response = $this->client->request('GET', $this->generateUrl('admin_setting_shop_tradelaw'));
        // Has success code response
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $inputFieldsName = $response->filter('input[id*="_name"]');
        $inputFieldsDescription = $response->filter('textarea[id*="_description"]');

        // Contains 15x2 initial input fields + toggle switch
        $this->assertEquals(15, $inputFieldsName->count());
        $this->assertEquals(15, $inputFieldsDescription->count());
        $this->assertEquals(15, $response->filter('.c-toggleSwitch')->count());

        // Check initial fields show and in order
        $notFoundNames = [
            '販売業者',
            '代表責任者',
            '所在地',
            '電話番号',
            'メールアドレス',
            'URL',
            '商品代金以外の必要料金',
            '引き渡し時期',
            'お支払方法',
            '返品・交換について'
        ];

        $loopId = 0;

        // Ensure initial values keys are filled
        $inputFieldsName->each(function ($inputFieldName) use ($notFoundNames, &$loopId) {
            if ($loopId < 10) {
                $this->assertEquals($notFoundNames[$loopId], $inputFieldName->attr('value'));
            }
            $loopId++;
        });

        // Ensure initial value descriptions are empty
        $inputFieldsDescription->each(function ($inputFieldName) {
            $this->assertEquals("", $inputFieldName->attr('value'));
        });
    }

    /**
     * 名称入力欄が255文字以上の場合、バリデーションエラーが発生されるかどうかのチェック
     * Validation check on setting name with characters over 255
     * @return void
     */
    public function testValidationNameMoreThan255Characters()
    {
        $form = $this->createBaseForm();
        $form['TradeLaws'][0]['name'] = ByteString::fromRandom(256)->toString();
        $responseCrawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_tradelaw'),
            ['form' => $form]
        );
        // Validation errors return success response.
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $failedInput = $responseCrawler->filter('#form_TradeLaws_0_name.is-invalid');
        // Check that the correct cell is failing validation with red border
        $this->assertEquals(1, $failedInput->count());

        // Check Text
        $this->assertEquals('<span class="form-error-message">値が長すぎます。255文字以内でなければなりません。</span>',
            $failedInput->nextAll()->filter('.form-error-message')->outerHtml());
    }

    /**
     * 説明入力欄が4000文字以上の場合、バリデーションエラーが発生されるかどうかのチェック
     * Validation check on setting name with characters over 4000
     * @return void
     */
    public function testValidationDescriptionMoreThan4000Characters()
    {
        $form = $this->createBaseForm();
        $form['TradeLaws'][0]['description'] = ByteString::fromRandom(4001)->toString();
        $responseCrawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_tradelaw'),
            ['form' => $form]
        );
        // Validation errors return success response.
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $failedInput = $responseCrawler->filter('#form_TradeLaws_0_description.is-invalid');
        // Check that the correct cell is failing validation with red border
        $this->assertEquals(1, $failedInput->count());

        // Check Text
        $this->assertEquals('<span class="form-error-message">値が長すぎます。4000文字以内でなければなりません。</span>',
            $failedInput->nextAll()->filter('.form-error-message')->outerHtml());
    }

    /**
     * 正しいデータでフォーム内容が更新されるかどうかのチェック
     * With correct input entries, check if the data is correctly saved.
     *
     * @return void
     */
    public function testUpdate()
    {
        $form = $this->createBaseForm();
        $form['TradeLaws'][10]['name'] = 'UTテスト：名称';
        $form['TradeLaws'][10]['description'] = 'UTテスト: 説明';
        $form['TradeLaws'][10]['displayOrderScreen'] = '1';

        $responseCrawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_tradelaw'),
            ['form' => $form]
        );
        // Validation errors return success response with redirect 302 (error will respond 200).
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $responseCrawler = $this->client->followRedirect();

        $editedName = $responseCrawler->filter('#form_TradeLaws_10_name');
        $editedDescription = $responseCrawler->filter('#form_TradeLaws_10_description');
        $editedToggle = $responseCrawler->filter('#form_TradeLaws_10_displayOrderScreen');

        // Check that the correct cell is *not* failing validation with red border and contains registered value
        $this->assertStringNotContainsString('is-invalid', $editedName->attr('class'));
        $this->assertEquals('UTテスト：名称', $editedName->attr('value'));

        $this->assertStringNotContainsString('is-invalid', $editedDescription->attr('class'));
        $this->assertEquals('UTテスト: 説明', $editedDescription->innerText());

        $this->assertStringNotContainsString('is-invalid', $editedToggle->attr('class') ?: "");
        $this->assertEquals('1', $editedToggle->attr('value'));

        // Check save success message exists
        $this->assertEquals(1, $responseCrawler->filter('.alert.alert-success')->count());
    }

    protected function createBaseForm(): array
    {
        return [
            '_token' => 'dummy',
            'TradeLaws' => [
                0 => [
                    'name' => '販売業者',
                    'description' => ''
                ],
                1 => [
                    'name' => '代表責任者',
                    'description' => ''
                ],
                2 => [
                    'name' => '所在地',
                    'description' => ''
                ],
                3 => [
                    'name' => '電話番号',
                    'description' => ''
                ],
                4 => [
                    'name' => 'メールアドレス',
                    'description' => ''
                ],
                5 => [
                    'name' => 'URL',
                    'description' => ''
                ],
                6 => [
                    'name' => '商品代金以外の必要料金',
                    'description' => ''
                ],
                7 => [
                    'name' => '引き渡し時期',
                    'description' => ''
                ], 8 => [
                    'name' => 'お支払方法',
                    'description' => ''
                ],
                9 => [
                    'name' => '返品・交換について',
                    'description' => ''
                ],
                10 => [
                    'name' => '',
                    'description' => ''
                ],
                11 => [
                    'name' => '',
                    'description' => ''
                ],
                12 => [
                    'name' => '',
                    'description' => ''
                ],
                13 => [
                    'name' => '',
                    'description' => ''
                ],
                14 => [
                    'name' => '',
                    'description' => ''
                ]
            ]
        ];
    }
}
