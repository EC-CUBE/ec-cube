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
use Eccube\Event\TemplateEvent;
use Eccube\Repository\OrderDisplaySettingRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
        $settings = $this->orderDisplaySettingRepository->findBy([], ['sort_no' => 'ASC']);
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
        $updatedSettings = $this->orderDisplaySettingRepository->findBy([], ['sort_no' => 'ASC']);
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
        $settings = $this->orderDisplaySettingRepository->findBy([], ['sort_no' => 'ASC']);
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
        $paymentMethod = $this->orderDisplaySettingRepository->findOneBy(['field_name' => 'payment_method']);
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
     * プラグイン相当の TemplateEvent リスナがソースを書き換え,
     * 注入スロットに列内容が実際に描画されることのテスト.
     *
     * 実プラグインをパッケージ化せず, @admin/Order/index.twig の TemplateEvent を
     * 購読して getSource()/setSource() でスロットを埋める（プラグインと同じ作法）.
     */
    public function testOrderListSlotIsFilledViaTemplateEvent()
    {
        $Order = $this->createOrder($this->createCustomer());
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush();

        $fieldName = 'plugin_te_injected_col';
        $injectedText = 'INJECTED_BY_TEMPLATE_EVENT';

        $pluginSetting = new OrderDisplaySetting();
        $pluginSetting->setFieldName($fieldName);
        $pluginSetting->setDispName('注入列');
        $pluginSetting->setEnabled(true);
        $pluginSetting->setSortNo(99);
        $this->entityManager->persist($pluginSetting);
        $this->entityManager->flush();

        // プラグインの TemplateListener 相当: ヘッダの空スロットを内容入りに置換する.
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = static::getContainer()->get(EventDispatcherInterface::class);
        $listener = function (TemplateEvent $event) use ($fieldName, $injectedText) {
            $anchor = '<th class="border-top-0 pt-2 pb-2 text-center" data-order-display-header="{{ setting.field_name }}"></th>';
            $filled = '<th class="border-top-0 pt-2 pb-2 text-center" data-order-display-header="{{ setting.field_name }}">'
                .'{% if setting.field_name == \''.$fieldName.'\' %}'.$injectedText.'{% endif %}</th>';
            $event->setSource(str_replace($anchor, $filled, $event->getSource()));
        };
        $eventDispatcher->addListener('@admin/Order/index.twig', $listener);

        try {
            $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_order'));
            $this->assertTrue($this->client->getResponse()->isSuccessful());

            // ソース置換が反映され, スロットに列内容が描画されていること.
            $this->assertStringContainsString(
                $injectedText,
                (string) $this->client->getResponse()->getContent(),
                'TemplateEvent によるソース置換で注入スロットが埋まること'
            );
        } finally {
            $eventDispatcher->removeListener('@admin/Order/index.twig', $listener);
            $this->entityManager->remove($pluginSetting);
            $this->entityManager->flush();
        }
    }

    /**
     * 表示項目設定が1件も登録されていない未投入環境の場合に,
     * 受注一覧がデフォルト（全項目表示）へフォールバックすることのテスト.
     *
     * フォールバックは「有効行ゼロ」ではなく「テーブルが真に空（行数ゼロ）」が条件.
     * import_csv の初期8件を一旦退避・削除して未投入状態を再現し, finally で復元する.
     */
    public function testOrderListFallsBackToDefaultWhenTableIsEmpty()
    {
        $Order = $this->createOrder($this->createCustomer());
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));

        // 既存の全設定を退避してから削除し, テーブルを真に空にする.
        $existing = $this->orderDisplaySettingRepository->findBy([], ['sort_no' => 'ASC']);
        $snapshot = array_map(fn (OrderDisplaySetting $s) => [
            'field_name' => $s->getFieldName(),
            'disp_name' => $s->getDispName(),
            'enabled' => $s->getEnabled(),
            'sort_no' => $s->getSortNo(),
        ], $existing);
        foreach ($existing as $setting) {
            $this->entityManager->remove($setting);
        }
        $this->entityManager->flush();

        try {
            $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_order'));
            $this->assertTrue($this->client->getResponse()->isSuccessful());

            // デフォルト項目が描画される（代表的な列見出しを確認）.
            $headerText = $crawler->filter('#search_result thead')->text();
            foreach ([
                'admin.common.payment_method',
                'admin.order.order_status',
                'admin.order.purchase_price',
                'admin.order.shipping_status',
                'admin.order.tracking_number',
                'admin.order.delivery',
            ] as $key) {
                $this->assertStringContainsString(trans($key), $headerText, $key.' の列がフォールバックで描画されること');
            }

            // チェックボックス列も存在する.
            $this->assertGreaterThan(0, $crawler->filter('#toggle_check_all')->count());
        } finally {
            // 退避した初期設定を復元する（共有DBのため後続テストへ影響させない）.
            foreach ($snapshot as $data) {
                $setting = new OrderDisplaySetting();
                $setting->setFieldName($data['field_name']);
                $setting->setDispName($data['disp_name']);
                $setting->setEnabled($data['enabled']);
                $setting->setSortNo($data['sort_no']);
                $this->entityManager->persist($setting);
            }
            $this->entityManager->flush();
        }
    }

    /**
     * 行は存在するが全項目が非表示の場合は, フォールバックせず設定どおり
     * データ列を一切描画しないことのテスト（管理者の「全部隠す」意図を尊重）.
     * チェックボックス列は設定に関わらず常に表示される.
     */
    public function testAllDisabledDoesNotFallBackAndHidesDataColumns()
    {
        $Order = $this->createOrder($this->createCustomer());
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));

        // 全項目を非表示にする（行は残す）.
        foreach ($this->orderDisplaySettingRepository->findBy([], ['sort_no' => 'ASC']) as $setting) {
            $setting->setEnabled(false);
        }
        $this->entityManager->flush();

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_order'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // フォールバックしないため, デフォルト列（支払方法等）は描画されない.
        $headerText = $crawler->filter('#search_result thead')->text();
        foreach ([
            'admin.common.payment_method',
            'admin.order.order_status',
            'admin.order.purchase_price',
            'admin.order.delivery',
        ] as $key) {
            $this->assertStringNotContainsString(trans($key), $headerText, $key.' の列が描画されないこと');
        }

        // チェックボックス列は常に存在する.
        $this->assertGreaterThan(0, $crawler->filter('#toggle_check_all')->count());
    }

    /**
     * 有効項目を一部のみに絞った（フォールバックしない）状態でも,
     * チェックボックス列が常に表示されることのテスト.
     */
    public function testCheckboxColumnIsAlwaysRenderedWithReducedColumns()
    {
        $Order = $this->createOrder($this->createCustomer());
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));

        // order_info のみ有効, 残りは無効（有効項目あり＝フォールバックしない）.
        $orderInfo = $this->orderDisplaySettingRepository->findOneBy(['field_name' => 'order_info']);
        $this->assertInstanceOf(OrderDisplaySetting::class, $orderInfo);
        foreach ($this->orderDisplaySettingRepository->findBy([], ['sort_no' => 'ASC']) as $setting) {
            $setting->setEnabled($setting->getId() === $orderInfo->getId());
        }
        $this->entityManager->flush();

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_order'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 設定に関わらずチェックボックス列は存在する.
        $this->assertGreaterThan(0, $crawler->filter('#toggle_check_all')->count());

        // フォールバックしていない（無効化した支払方法列が出ていない）ことを確認.
        $headerText = $crawler->filter('#search_result thead')->text();
        $this->assertStringNotContainsString(trans('admin.common.payment_method'), $headerText);
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
