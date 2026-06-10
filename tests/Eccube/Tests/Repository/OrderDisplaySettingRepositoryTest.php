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

namespace Eccube\Tests\Repository;

use Eccube\Entity\OrderDisplaySetting;
use Eccube\Repository\OrderDisplaySettingRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * OrderDisplaySettingRepositoryTest
 */
final class OrderDisplaySettingRepositoryTest extends EccubeTestCase
{
    protected ?OrderDisplaySettingRepository $orderDisplaySettingRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderDisplaySettingRepository = $this->entityManager->getRepository(OrderDisplaySetting::class);
    }

    /**
     * 有効な設定のみを取得するテスト
     */
    public function testGetEnabledSettings()
    {
        // テスト用のデータを作成
        $testSettings = [
            [
                'field_name' => 'test_enabled_1',
                'disp_name' => 'テスト有効設定1',
                'enabled' => true,
                'sort_no' => 1,
            ],
            [
                'field_name' => 'test_disabled_1',
                'disp_name' => 'テスト無効設定1',
                'enabled' => false,
                'sort_no' => 2,
            ],
            [
                'field_name' => 'test_enabled_2',
                'disp_name' => 'テスト有効設定2',
                'enabled' => true,
                'sort_no' => 3,
            ],
        ];

        // テストデータを保存
        $this->orderDisplaySettingRepository->saveSettings($testSettings);

        // 有効な設定のみを取得
        $enabledSettings = $this->orderDisplaySettingRepository->getEnabledSettings();

        $this->assertIsArray($enabledSettings);

        // 有効な設定のみが取得されることを確認
        $enabledFieldNames = array_map(fn ($setting) => $setting->getFieldName(), $enabledSettings);

        // テストで作成した有効な設定が含まれていることを確認
        $this->assertContains('test_enabled_1', $enabledFieldNames);
        $this->assertContains('test_enabled_2', $enabledFieldNames);
        $this->assertNotContains('test_disabled_1', $enabledFieldNames);

        // すべての設定が有効であることを確認
        foreach ($enabledSettings as $setting) {
            $this->assertTrue($setting->getEnabled());
        }

        // ソート順で取得されていることを確認
        $sortNos = array_map(fn ($setting) => $setting->getSortNo(), $enabledSettings);

        $sortedSortNos = $sortNos;
        sort($sortedSortNos);
        $this->assertEquals($sortedSortNos, $sortNos);

        // クリーンアップ
        foreach ($testSettings as $testSetting) {
            $setting = $this->orderDisplaySettingRepository->findByFieldName($testSetting['field_name']);
            if ($setting) {
                $this->entityManager->remove($setting);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * 全設定を取得するテスト
     */
    public function testGetAllSettings()
    {
        // テスト用のデータを作成
        $testSettings = [
            [
                'field_name' => 'test_all_1',
                'disp_name' => 'テスト全設定1',
                'enabled' => true,
                'sort_no' => 1,
            ],
            [
                'field_name' => 'test_all_2',
                'disp_name' => 'テスト全設定2',
                'enabled' => false,
                'sort_no' => 2,
            ],
            [
                'field_name' => 'test_all_3',
                'disp_name' => 'テスト全設定3',
                'enabled' => true,
                'sort_no' => 3,
            ],
        ];

        // テストデータを保存
        $this->orderDisplaySettingRepository->saveSettings($testSettings);

        // 全設定を取得
        $allSettings = $this->orderDisplaySettingRepository->getAllSettings();

        $this->assertIsArray($allSettings);

        // テストで作成した設定が含まれていることを確認
        $allFieldNames = array_map(fn ($setting) => $setting->getFieldName(), $allSettings);

        $this->assertContains('test_all_1', $allFieldNames);
        $this->assertContains('test_all_2', $allFieldNames);
        $this->assertContains('test_all_3', $allFieldNames);

        // ソート順で取得されていることを確認
        $sortNos = array_map(fn ($setting) => $setting->getSortNo(), $allSettings);

        $sortedSortNos = $sortNos;
        sort($sortedSortNos);
        $this->assertEquals($sortedSortNos, $sortNos);

        // クリーンアップ
        foreach ($testSettings as $testSetting) {
            $setting = $this->orderDisplaySettingRepository->findByFieldName($testSetting['field_name']);
            if ($setting) {
                $this->entityManager->remove($setting);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * フィールド名で設定を取得するテスト
     */
    public function testFindByFieldName()
    {
        // テスト用のデータを作成
        $testSetting = [
            'field_name' => 'test_find_field',
            'disp_name' => 'テスト検索フィールド',
            'enabled' => true,
            'sort_no' => 1,
        ];

        // テストデータを保存
        $this->orderDisplaySettingRepository->saveSettings([$testSetting]);

        // 存在するフィールド名で検索
        $setting = $this->orderDisplaySettingRepository->findByFieldName('test_find_field');

        $this->assertInstanceOf(OrderDisplaySetting::class, $setting);
        $this->assertSame('test_find_field', $setting->getFieldName());
        $this->assertSame('テスト検索フィールド', $setting->getDispName());
        $this->assertTrue($setting->getEnabled());
        $this->assertSame(1, $setting->getSortNo());

        // 存在しないフィールド名で検索
        $nonExistentSetting = $this->orderDisplaySettingRepository->findByFieldName('non_existent_field');

        $this->assertNotInstanceOf(OrderDisplaySetting::class, $nonExistentSetting);

        // クリーンアップ
        $this->entityManager->remove($setting);
        $this->entityManager->flush();
    }

    /**
     * 設定を保存するテスト
     */
    public function testSaveSettings()
    {
        $settingsData = [
            [
                'field_name' => 'test_field_1',
                'disp_name' => 'テストフィールド1',
                'enabled' => true,
                'sort_no' => 100,
            ],
            [
                'field_name' => 'test_field_2',
                'disp_name' => 'テストフィールド2',
                'enabled' => false,
                'sort_no' => 101,
            ],
        ];

        // 設定を保存
        $this->orderDisplaySettingRepository->saveSettings($settingsData);

        // 保存された設定を確認
        $savedSetting1 = $this->orderDisplaySettingRepository->findByFieldName('test_field_1');
        $this->assertInstanceOf(OrderDisplaySetting::class, $savedSetting1);
        $this->assertSame('test_field_1', $savedSetting1->getFieldName());
        $this->assertSame('テストフィールド1', $savedSetting1->getDispName());
        $this->assertTrue($savedSetting1->getEnabled());
        $this->assertSame(100, $savedSetting1->getSortNo());

        $savedSetting2 = $this->orderDisplaySettingRepository->findByFieldName('test_field_2');
        $this->assertInstanceOf(OrderDisplaySetting::class, $savedSetting2);
        $this->assertSame('test_field_2', $savedSetting2->getFieldName());
        $this->assertSame('テストフィールド2', $savedSetting2->getDispName());
        $this->assertFalse($savedSetting2->getEnabled());
        $this->assertSame(101, $savedSetting2->getSortNo());

        // 既存の設定を更新するテスト
        $updateData = [
            [
                'field_name' => 'test_field_1',
                'disp_name' => '更新されたテストフィールド1',
                'enabled' => false,
                'sort_no' => 200,
            ],
        ];

        $this->orderDisplaySettingRepository->saveSettings($updateData);

        $updatedSetting = $this->orderDisplaySettingRepository->findByFieldName('test_field_1');
        $this->assertInstanceOf(OrderDisplaySetting::class, $updatedSetting);
        $this->assertSame('更新されたテストフィールド1', $updatedSetting->getDispName());
        $this->assertFalse($updatedSetting->getEnabled());
        $this->assertSame(200, $updatedSetting->getSortNo());

        // テストデータをクリーンアップ
        $this->entityManager->remove($savedSetting1);
        $this->entityManager->remove($savedSetting2);
        $this->entityManager->flush();
    }

    /**
     * 新しいエンティティを作成するテスト
     */
    public function testNewEntity()
    {
        $entity = $this->orderDisplaySettingRepository->newEntity();

        $this->assertInstanceOf(OrderDisplaySetting::class, $entity);
        $this->assertNull($entity->getId());
        // タイムスタンプは SaveEventSubscriber が persist 時に自動設定するため、
        // persist 前は \DateTime ではないことを確認
        $this->assertNotInstanceOf(\DateTime::class, $entity->getCreateDate());
    }

    /**
     * 存在しないフィールド名での検索テスト
     */
    public function testFindByFieldNameWithNonExistentField()
    {
        $result = $this->orderDisplaySettingRepository->findByFieldName('non_existent_field_name_12345');

        $this->assertNotInstanceOf(OrderDisplaySetting::class, $result);
    }

    /**
     * 空文字列でのフィールド名検索テスト
     */
    public function testFindByFieldNameWithEmptyString()
    {
        $result = $this->orderDisplaySettingRepository->findByFieldName('');

        $this->assertNotInstanceOf(OrderDisplaySetting::class, $result);
    }

    /**
     * 大量データでの設定保存テスト
     */
    public function testSaveSettingsWithLargeDataSet()
    {
        $largeDataSet = [];

        // 100件のテストデータを作成
        for ($i = 1; $i <= 100; $i++) {
            $largeDataSet[] = [
                'field_name' => "test_field_large_{$i}",
                'disp_name' => "大量データテストフィールド{$i}",
                'enabled' => ($i % 2 === 0),
                'sort_no' => $i,
            ];
        }

        // 大量データを保存
        $this->orderDisplaySettingRepository->saveSettings($largeDataSet);

        // 保存されたデータを確認
        $savedSettings = $this->orderDisplaySettingRepository->getAllSettings();
        $testSettings = array_filter($savedSettings, fn ($setting) => str_starts_with((string) $setting->getFieldName(), 'test_field_large_'));

        $this->assertCount(100, $testSettings);

        // クリーンアップ
        foreach ($testSettings as $setting) {
            $this->entityManager->remove($setting);
        }
        $this->entityManager->flush();
    }

    /**
     * 空の設定配列での保存テスト
     */
    public function testSaveSettingsWithEmptyArray()
    {
        $this->orderDisplaySettingRepository->saveSettings([]);

        // 空の配列でもエラーが発生しないことを確認
        $this->assertTrue(true);
    }

    /**
     * 重複するフィールド名での保存テスト
     */
    public function testSaveSettingsWithDuplicateFieldNames()
    {
        $duplicateData = [
            [
                'field_name' => 'duplicate_test_field',
                'disp_name' => '重複テスト1',
                'enabled' => true,
                'sort_no' => 1,
            ],
            [
                'field_name' => 'duplicate_test_field', // 同じフィールド名
                'disp_name' => '重複テスト2',
                'enabled' => false,
                'sort_no' => 2,
            ],
        ];

        // 重複するフィールド名でも保存される（上書きされる）
        $this->orderDisplaySettingRepository->saveSettings($duplicateData);

        $savedSetting = $this->orderDisplaySettingRepository->findByFieldName('duplicate_test_field');
        $this->assertInstanceOf(OrderDisplaySetting::class, $savedSetting);
        $this->assertSame('重複テスト1', $savedSetting->getDispName()); // 最初の値が保持される
        $this->assertTrue($savedSetting->getEnabled());

        // クリーンアップ
        $this->entityManager->remove($savedSetting);
        $this->entityManager->flush();
    }

    /**
     * 文字列型の値での保存テスト（型変換される）
     */
    public function testSaveSettingsWithStringTypeValues()
    {
        $invalidData = [
            [
                'field_name' => 'invalid_test_field',
                'disp_name' => '型変換テスト',
                'enabled' => 'true', // 文字列の boolean 値
                'sort_no' => '123', // 文字列の integer 値
            ],
        ];

        // 文字列型でも保存される（型変換される）
        $this->orderDisplaySettingRepository->saveSettings($invalidData);

        $savedSetting = $this->orderDisplaySettingRepository->findByFieldName('invalid_test_field');
        $this->assertInstanceOf(OrderDisplaySetting::class, $savedSetting);
        $this->assertTrue($savedSetting->getEnabled()); // 文字列 'true' は true に変換される
        $this->assertSame(123, $savedSetting->getSortNo()); // 文字列 '123' は 123 に変換される

        // クリーンアップ
        $this->entityManager->remove($savedSetting);
        $this->entityManager->flush();
    }

    /**
     * 部分的なデータでの保存テスト
     */
    public function testSaveSettingsWithPartialData()
    {
        $partialData = [
            [
                'field_name' => 'partial_test_field',
                'disp_name' => '部分データテスト',
                'enabled' => true, // enabled は設定
                // sort_no が未設定
            ],
        ];

        // 部分的なデータでも保存される（デフォルト値が使用される）
        $this->orderDisplaySettingRepository->saveSettings($partialData);

        $savedSetting = $this->orderDisplaySettingRepository->findByFieldName('partial_test_field');
        $this->assertInstanceOf(OrderDisplaySetting::class, $savedSetting);
        $this->assertSame('partial_test_field', $savedSetting->getFieldName());
        $this->assertSame('部分データテスト', $savedSetting->getDispName());
        $this->assertTrue($savedSetting->getEnabled());
        $this->assertSame(0, $savedSetting->getSortNo()); // デフォルト値

        // クリーンアップ
        $this->entityManager->remove($savedSetting);
        $this->entityManager->flush();
    }
}
