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
     * 表示項目設定を作成して永続化する.
     */
    private function createSetting(string $fieldName, bool $enabled, int $sortNo): OrderDisplaySetting
    {
        $setting = new OrderDisplaySetting();
        $setting->setFieldName($fieldName);
        $setting->setDispName($fieldName);
        $setting->setEnabled($enabled);
        $setting->setSortNo($sortNo);
        // タイムスタンプは SaveEventSubscriber が persist 時に自動設定する.
        $this->entityManager->persist($setting);

        return $setting;
    }

    /**
     * getEnabledSettings は有効な項目のみを sort_no 昇順で返す.
     */
    public function testGetEnabledSettings()
    {
        // 有効2件・無効1件を、sort_no が昇順にならない順序で投入する.
        $this->createSetting('test_enabled_2', true, 102);
        $this->createSetting('test_disabled_1', false, 101);
        $this->createSetting('test_enabled_1', true, 100);
        $this->entityManager->flush();

        $enabledSettings = $this->orderDisplaySettingRepository->getEnabledSettings();

        $fieldNames = array_map(fn ($setting) => $setting->getFieldName(), $enabledSettings);

        // 有効な項目のみが含まれ、無効な項目は含まれない.
        $this->assertContains('test_enabled_1', $fieldNames);
        $this->assertContains('test_enabled_2', $fieldNames);
        $this->assertNotContains('test_disabled_1', $fieldNames);

        // すべて有効であること.
        foreach ($enabledSettings as $setting) {
            $this->assertTrue($setting->getEnabled());
        }

        // sort_no 昇順で取得されること.
        $sortNos = array_map(fn ($setting) => $setting->getSortNo(), $enabledSettings);
        $sorted = $sortNos;
        sort($sorted);
        $this->assertSame($sorted, $sortNos);

        // 投入した有効2件は sort_no 順（test_enabled_1 が test_enabled_2 より前）であること.
        $pos1 = array_search('test_enabled_1', $fieldNames, true);
        $pos2 = array_search('test_enabled_2', $fieldNames, true);
        $this->assertLessThan($pos2, $pos1);
    }
}
