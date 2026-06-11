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

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\OrderDisplaySetting;
use Eccube\Repository\OrderDisplaySettingRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * OrderDisplaySettingControllerTest
 */
final class OrderDisplaySettingControllerTest extends AbstractAdminWebTestCase
{
    protected ?OrderDisplaySettingRepository $orderDisplaySettingRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderDisplaySettingRepository = $this->entityManager->getRepository(OrderDisplaySetting::class);
    }

    /**
     * テストで変更した表示項目設定を初期状態（全項目表示・登録順）へ戻す.
     * Web テストはトランザクションロールバックされないため、他テストへの影響を防ぐ.
     */
    protected function tearDown(): void
    {
        $this->entityManager->clear();
        $settings = $this->orderDisplaySettingRepository->findBy([], ['id' => 'ASC']);
        $sortNo = 1;
        foreach ($settings as $setting) {
            $setting->setEnabled(true);
            $setting->setSortNo($sortNo++);
        }
        $this->entityManager->flush();

        parent::tearDown();
    }

    /**
     * 表示項目設定画面の表示テスト
     */
    public function testIndex()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_order_display_setting'));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 表示項目設定の保存テスト（表示/非表示の振り分け）
     */
    public function testIndexWithPost()
    {
        $settings = $this->orderDisplaySettingRepository->getAllSettings();
        $this->assertNotEmpty($settings, '初期データ（import_csv）が投入されている前提');

        // 最初の4項目を表示項目、残りを非表示項目として設定
        $displayItems = [];
        $nonDisplayItems = [];
        foreach ($settings as $index => $setting) {
            if ($index < 4) {
                $displayItems[] = $setting->getId();
            } else {
                $nonDisplayItems[] = $setting->getId();
            }
        }

        $formData = [
            '_token' => 'dummy',
            'display_items' => $displayItems,
            'non_display_items' => $nonDisplayItems,
        ];

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_display_setting'),
            ['form' => $formData]
        );

        $this->assertTrue(
            $this->client->getResponse()->isRedirect($this->generateUrl('admin_order_display_setting'))
        );

        // データが正しく保存されたか確認
        $this->entityManager->clear();
        $updatedSettings = $this->orderDisplaySettingRepository->getAllSettings();
        $enabledCount = 0;
        $disabledCount = 0;
        foreach ($updatedSettings as $setting) {
            $setting->getEnabled() ? $enabledCount++ : $disabledCount++;
        }

        $this->assertSame(count($displayItems), $enabledCount);
        $this->assertSame(count($nonDisplayItems), $disabledCount);
    }

    /**
     * 並び順が保存されることのテスト
     */
    public function testIndexSavesSortOrder()
    {
        $settings = $this->orderDisplaySettingRepository->getAllSettings();
        $ids = array_map(fn ($setting) => $setting->getId(), $settings);

        // 表示項目の並びを反転させて保存
        $displayItems = array_reverse($ids);

        $formData = [
            '_token' => 'dummy',
            'display_items' => $displayItems,
            'non_display_items' => [],
        ];

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_display_setting'),
            ['form' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->entityManager->clear();
        $enabled = $this->orderDisplaySettingRepository->getEnabledSettings();
        $savedOrder = array_map(fn ($setting) => $setting->getId(), $enabled);

        $this->assertSame($displayItems, $savedOrder);
    }

    /**
     * 受注一覧画面が表示項目設定を反映することのテスト
     */
    public function testOrderListReflectsDisplaySetting()
    {
        // 受注一覧に行が描画されるよう受注を1件用意する（0件だとテーブル自体が非表示）.
        // createOrder は仮受注（PROCESSING）で作られ既定の一覧フィルタから除外されるため、NEW へ変更する.
        $Order = $this->createOrder($this->createCustomer());
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush();

        // 「支払方法」列を非表示にする
        $paymentMethod = $this->orderDisplaySettingRepository->findByFieldName('payment_method');
        $this->assertInstanceOf(OrderDisplaySetting::class, $paymentMethod);

        $enabledIds = array_map(
            fn ($setting) => $setting->getId(),
            $this->orderDisplaySettingRepository->getEnabledSettings()
        );
        $displayItems = array_values(array_filter($enabledIds, fn ($id) => $id !== $paymentMethod->getId()));

        $formData = [
            '_token' => 'dummy',
            'display_items' => $displayItems,
            'non_display_items' => [$paymentMethod->getId()],
        ];
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_display_setting'),
            ['form' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect());

        // 受注一覧を開き、支払方法の列見出しが消えていることを確認
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_order'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $headerText = $crawler->filter('#search_result thead')->text();
        $this->assertStringNotContainsString(trans('admin.common.payment_method'), $headerText);
    }

    /**
     * コア外の field_name（プラグインが追加した項目を想定）に対して,
     * 受注一覧にプラグイン拡張用の注入スロットが描画されることのテスト.
     *
     * プラグインは @admin/Order/index.twig の TemplateEvent でこの空セルを置換し,
     * 列を注入する. 列順は displaySettings の sort_no に従う.
     */
    public function testOrderListRendersPluginExtensionSlot()
    {
        $Order = $this->createOrder($this->createCustomer());
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush();

        // プラグインが追加した項目を想定したレコードを登録する.
        $fieldName = 'plugin_extension_col';
        $pluginSetting = new OrderDisplaySetting();
        $pluginSetting->setFieldName($fieldName);
        $pluginSetting->setDispName('拡張列');
        $pluginSetting->setEnabled(true);
        $pluginSetting->setSortNo(99);
        $this->entityManager->persist($pluginSetting);
        $this->entityManager->flush();

        try {
            $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_order'));
            $this->assertTrue($this->client->getResponse()->isSuccessful());

            // ヘッダ・ボディ双方に field_name をキーにした注入スロットが出力される.
            $this->assertGreaterThan(
                0,
                $crawler->filter('th[data-order-display-header="'.$fieldName.'"]')->count(),
                'ヘッダにプラグイン拡張用スロットが描画されること'
            );
            $this->assertGreaterThan(
                0,
                $crawler->filter('td[data-order-display-cell="'.$fieldName.'"]')->count(),
                'ボディにプラグイン拡張用スロットが描画されること'
            );
        } finally {
            // 追加したレコードは tearDown の一括リセット対象外のため明示的に削除する.
            $this->entityManager->remove($pluginSetting);
            $this->entityManager->flush();
        }
    }

    /**
     * 不正な HTTP メソッドでのテスト（GET/POST のみ許可）
     */
    public function testIndexWithInvalidHttpMethod()
    {
        $this->client->request(Request::METHOD_PUT, $this->generateUrl('admin_order_display_setting'));

        $this->assertTrue($this->client->getResponse()->isClientError());
    }
}
