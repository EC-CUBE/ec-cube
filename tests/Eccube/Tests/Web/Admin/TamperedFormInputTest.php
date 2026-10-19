<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Web\Admin;

use Eccube\Entity\Calendar;
use Eccube\Entity\ClassName;
use Eccube\Entity\News;
use Eccube\Entity\TaxRule;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 管理画面のフォームへ改ざんした値を送ったときに 500 にならないことを確かめる.
 *
 * - フォームの名前を含まない POST (フォームが送信済みにならない) で isValid() を呼ばない
 * - 項目の値を空や配列にした POST で, null がエンティティのセッターやテンプレートへ渡っても型エラーにならない
 */
final class TamperedFormInputTest extends AbstractAdminWebTestCase
{
    /**
     * フォームの名前を含まない POST で 500 にならない.
     *
     * @param array<string, string> $parameters
     */
    #[DataProvider(methodName: 'provideUnsubmittedFormRoutes')]
    public function testPostWithoutFormName(string $route, array $parameters = []): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl($route),
            $parameters + ['unrelated' => 'value']
        );

        $this->assertLessThan(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return \Iterator<string, array{0: string, 1?: array<string, string>}>
     */
    public static function provideUnsubmittedFormRoutes(): \Iterator
    {
        yield '商品一覧' => ['admin_product'];
        yield '商品登録' => ['admin_product_product_new'];
        yield '受注一覧' => ['admin_order'];
        yield '帳票出力' => ['admin_order_pdf_download'];
        yield '会員一覧' => ['admin_customer'];
        yield '返品・返金申請一覧' => ['admin_refund_request'];
        yield '出荷 CSV 登録' => ['admin_shipping_csv_import'];
        yield '商品 CSV 登録' => ['admin_product_csv_import'];
        yield 'カテゴリ CSV 登録' => ['admin_product_category_csv_import'];
        yield '規格 CSV 登録' => ['admin_product_class_name_csv_import'];
        yield '規格分類 CSV 登録' => ['admin_product_class_category_csv_import'];
        yield '配送方法登録' => ['admin_setting_shop_delivery_new'];
        yield 'マスタデータ管理' => ['admin_setting_system_masterdata'];
        yield 'マスタデータ編集' => ['admin_setting_system_masterdata_edit'];
        yield 'ログ表示' => ['admin_setting_system_log'];
        yield 'ファイル管理 (フォルダ作成)' => ['admin_content_file', ['mode' => 'create']];
        yield 'ファイル管理 (アップロード)' => ['admin_content_file', ['mode' => 'upload']];
    }

    public function testPostWithoutFormNameToProductEdit(): void
    {
        $Product = $this->createProduct();

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]),
            ['unrelated' => 'value']
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testPostWithoutFormNameToDeliveryEdit(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop_delivery_edit', ['id' => 1]),
            ['unrelated' => 'value']
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testPostWithoutFormNameToOrderMailChange(): void
    {
        $Order = $this->createOrder($this->createCustomer());

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_mail', ['id' => $Order->getId()]),
            ['mode' => 'change']
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testPostWithoutFormNameToTaxRuleInlineEdit(): void
    {
        /** @var TaxRule $TaxRule */
        $TaxRule = $this->entityManager->getRepository(TaxRule::class)->find(1);

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop_tax'),
            ['mode' => 'edit_inline', 'tax_rule_id' => (string) $TaxRule->getId()]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testPostWithoutFormNameToCalendarInlineEdit(): void
    {
        $Calendar = new Calendar();
        $Calendar->setTitle('休業日')->setHoliday(new \DateTime('tomorrow'));
        $this->entityManager->persist($Calendar);
        $this->entityManager->flush();

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop_calendar'),
            ['mode' => 'edit_inline', 'calendar_id' => (string) $Calendar->getId()]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 表示/非表示の選択を空や配列にしても 500 にならず, 入力エラーとして画面へ戻る.
     */
    #[DataProvider(methodName: 'provideInvalidValues')]
    public function testVisibleOfNews(mixed $value): void
    {
        $News = new News();
        $News->setTitle('新着情報')
            ->setPublishDate(new \DateTime())
            ->setVisible(true)
            ->setLinkMethod(false);
        $this->entityManager->persist($News);
        $this->entityManager->flush();

        $this->submitTampered(
            $this->generateUrl('admin_content_news_edit', ['id' => $News->getId()]),
            'admin_news',
            ['admin_news', 'visible'],
            $value
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    #[DataProvider(methodName: 'provideInvalidValues')]
    public function testVisibleOfNewNews(mixed $value): void
    {
        $this->submitTampered(
            $this->generateUrl('admin_content_news_new'),
            'admin_news',
            ['admin_news', 'visible'],
            $value,
            ['admin_news' => ['title' => '新着情報']]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    #[DataProvider(methodName: 'provideInvalidValues')]
    public function testVisibleOfDelivery(mixed $value): void
    {
        $this->submitTampered(
            $this->generateUrl('admin_setting_shop_delivery_edit', ['id' => 1]),
            'delivery',
            ['delivery', 'visible'],
            $value
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    #[DataProvider(methodName: 'provideInvalidValues')]
    public function testVisibleOfPayment(mixed $value): void
    {
        $this->submitTampered(
            $this->generateUrl('admin_setting_shop_payment_edit', ['id' => 1]),
            'payment_register',
            ['payment_register', 'visible'],
            $value
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    #[DataProvider(methodName: 'provideInvalidValues')]
    public function testVisibleOfFaq(mixed $value): void
    {
        $this->submitTampered(
            $this->generateUrl('admin_content_faq_new'),
            'admin_faq',
            ['admin_faq', 'visible'],
            $value,
            ['admin_faq' => ['question' => '質問', 'answer' => '回答']]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    #[DataProvider(methodName: 'provideInvalidValues')]
    public function testVisibleOfCategoryFaq(mixed $value): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_category_faq', ['id' => 1]),
            [
                'admin_category_faq' => [
                    'faqs_rendered' => '1',
                    'faqs' => [
                        ['question' => '質問', 'answer' => '回答', 'sort_no' => '1', 'visible' => $value],
                    ],
                ],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    #[DataProvider(methodName: 'provideInvalidValues')]
    public function testInlineEditOfClassCategory(mixed $value): void
    {
        $ClassName = $this->entityManager->getRepository(ClassName::class)->find(1);
        $ClassCategory = $ClassName->getClassCategories()->first();
        $formName = 'class_category_'.$ClassCategory->getId();
        $url = $this->generateUrl('admin_product_class_category', ['class_name_id' => $ClassName->getId()]);

        foreach (['visible', 'name'] as $field) {
            $this->submitTampered($url, $formName, [$formName, $field], $value);
            $this->assertTrue($this->client->getResponse()->isSuccessful(), $field);
        }
    }

    /**
     * 規格分類の新規登録の画面は表示/非表示を送らないため, 送らなくても登録できる.
     */
    public function testCreateClassCategoryWithoutVisible(): void
    {
        $ClassName = $this->entityManager->getRepository(ClassName::class)->find(1);
        $this->assertInstanceOf(ClassName::class, $ClassName);
        $url = $this->generateUrl('admin_product_class_category', ['class_name_id' => $ClassName->getId()]);

        $this->client->request(Request::METHOD_POST, $url, [
            'admin_class_category' => ['_token' => 'dummy', 'name' => '新しい規格分類'],
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect($url));
    }

    #[DataProvider(methodName: 'provideInvalidValues')]
    public function testInlineEditOfClassName(mixed $value): void
    {
        $ClassName = $this->entityManager->getRepository(ClassName::class)->find(1);
        $this->assertInstanceOf(ClassName::class, $ClassName);
        $formName = 'class_name_'.$ClassName->getId();

        $this->submitTampered(
            $this->generateUrl('admin_product_class_name'),
            $formName,
            [$formName, 'name'],
            $value
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    #[DataProvider(methodName: 'provideInvalidValues')]
    public function testInlineEditOfTaxRule(mixed $value): void
    {
        $this->submitTampered(
            $this->generateUrl('admin_setting_shop_tax'),
            '#edit-form_1',
            ['tax_rule', 'tax_rate'],
            $value
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    #[DataProvider(methodName: 'provideInvalidValues')]
    public function testWorkOfMember(mixed $value): void
    {
        $Member = $this->createMember();

        $this->submitTampered(
            $this->generateUrl('admin_setting_system_member_edit', ['id' => $Member->getId()]),
            'admin_member',
            ['admin_member', 'Work'],
            $value
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    #[DataProvider(methodName: 'provideInvalidValues')]
    public function testBodyOfOrderMail(mixed $value): void
    {
        $Order = $this->createOrder($this->createCustomer());

        $this->submitTampered(
            $this->generateUrl('admin_order_mail', ['id' => $Order->getId()]),
            'admin_order_mail',
            ['admin_order_mail', 'tpl_data'],
            $value,
            ['mode' => 'complete', 'admin_order_mail' => ['mail_subject' => '件名']]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 受注明細の数量・単価と利用ポイントを空や配列にしても 500 にならない.
     */
    #[DataProvider(methodName: 'provideInvalidValuesForOrder')]
    public function testOrderEdit(string $mode, array $path, mixed $value): void
    {
        $Order = $this->createOrder($this->createCustomer());

        $this->submitTampered(
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            'order',
            $path,
            $value,
            ['mode' => $mode]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @return array<string, array{0: string, 1: list<string|int>, 2: mixed}>
     */
    public static function provideInvalidValuesForOrder(): array
    {
        $cases = [];
        foreach (['', 'register'] as $mode) {
            foreach ([
                'quantity' => ['order', 'OrderItems', 0, 'quantity'],
                // 税込で持つ明細 (送料・手数料等) を含めるため, すべての明細の単価を差し替える
                'price' => ['order', 'OrderItems', '*', 'price'],
                'use_point' => ['order', 'use_point'],
            ] as $field => $path) {
                foreach (self::provideInvalidValues() as $label => [$value]) {
                    $cases[sprintf('%s/%s/%s', $mode ?: 'calc', $field, $label)] = [$mode, $path, $value];
                }
            }
        }

        return $cases;
    }

    /**
     * @return array<string, array{0: mixed}>
     */
    public static function provideInvalidValues(): array
    {
        return [
            '空' => [''],
            '配列' => [['x']],
        ];
    }

    /**
     * 画面のフォームの値をそのまま送り, 1 項目だけ値を差し替える.
     *
     * @param string $formName フォームの名前, または `#` で始まるフォームの要素の ID
     * @param list<string|int> $path 差し替える項目 (フォームの値の配列のキーの並び. `*` はその階層のすべてのキー)
     * @param array<string, mixed> $override 差し替える前に上書きする値
     */
    private function submitTampered(string $url, string $formName, array $path, mixed $value, array $override = []): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $form = str_starts_with($formName, '#')
            ? $crawler->filter($formName)->form()
            : $crawler->filter(sprintf('[name^="%s["]', $formName))->first()->closest('form')->form();

        $values = array_replace_recursive($form->getPhpValues(), $override);

        $this->client->request(Request::METHOD_POST, $form->getUri(), $this->replaceValue($values, $path, $value));
    }

    /**
     * @param array<mixed> $values
     * @param list<string|int> $path
     *
     * @return array<mixed>
     */
    private function replaceValue(array $values, array $path, mixed $value): array
    {
        $key = array_shift($path);
        $keys = '*' === $key ? array_keys($values) : [$key];
        foreach ($keys as $k) {
            $values[$k] = [] === $path ? $value : $this->replaceValue((array) ($values[$k] ?? []), $path, $value);
        }

        return $values;
    }
}
