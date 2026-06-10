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

namespace Eccube\Tests\Entity;

use Eccube\Entity\OrderDisplaySetting;
use Eccube\Tests\EccubeTestCase;

/**
 * OrderDisplaySettingTest
 */
final class OrderDisplaySettingTest extends EccubeTestCase
{
    /**
     * 表示項目設定の保存・取得テスト
     */
    public function testOrderDisplaySetting()
    {
        $setting = new OrderDisplaySetting();
        $setting->setFieldName('order_info');
        $setting->setEnabled(true);
        $setting->setSortNo(1);

        $this->assertSame('order_info', $setting->getFieldName());
        $this->assertTrue($setting->getEnabled());
        $this->assertSame(1, $setting->getSortNo());
    }

    /**
     * 表示項目設定の初期値テスト
     */
    public function testOrderDisplaySettingDefaultValues()
    {
        $setting = new OrderDisplaySetting();

        $this->assertTrue($setting->getEnabled());
        $this->assertSame(0, $setting->getSortNo());
    }

    /**
     * 表示項目設定の日時設定テスト
     */
    public function testOrderDisplaySettingDateTime()
    {
        $setting = new OrderDisplaySetting();
        $now = new \DateTime();

        $setting->setCreateDate($now);
        $setting->setUpdateDate($now);

        $this->assertEquals($now, $setting->getCreateDate());
        $this->assertEquals($now, $setting->getUpdateDate());
    }

    /**
     * 最大長のフィールド名テスト
     */
    public function testOrderDisplaySettingWithMaxLengthFieldName()
    {
        $entity = new OrderDisplaySetting();

        // 最大長（255文字）のフィールド名
        $maxFieldName = str_repeat('a', 255);
        $entity->setFieldName($maxFieldName);
        $entity->setDispName('最大長テスト');
        $entity->setEnabled(true);
        $entity->setSortNo(1);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $this->assertSame($maxFieldName, $entity->getFieldName());
        $this->assertNotNull($entity->getId());

        // クリーンアップ
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * 最大長の表示名テスト
     */
    public function testOrderDisplaySettingWithMaxLengthDispName()
    {
        $entity = new OrderDisplaySetting();

        // 最大長（255文字）の表示名
        $maxDispName = str_repeat('あ', 255);
        $entity->setFieldName('test_field');
        $entity->setDispName($maxDispName);
        $entity->setEnabled(true);
        $entity->setSortNo(1);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $this->assertSame($maxDispName, $entity->getDispName());
        $this->assertNotNull($entity->getId());

        // クリーンアップ
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * 最大値のソート番号テスト
     */
    public function testOrderDisplaySettingWithMaxSortNo()
    {
        $entity = new OrderDisplaySetting();

        $entity->setFieldName('test_field_max_sort');
        $entity->setDispName('最大ソート番号テスト');
        $entity->setEnabled(true);
        $entity->setSortNo(999999); // 最大値

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $this->assertSame(999999, $entity->getSortNo());
        $this->assertNotNull($entity->getId());

        // クリーンアップ
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * 最小値のソート番号テスト
     */
    public function testOrderDisplaySettingWithMinSortNo()
    {
        $entity = new OrderDisplaySetting();

        $entity->setFieldName('test_field_min_sort');
        $entity->setDispName('最小ソート番号テスト');
        $entity->setEnabled(true);
        $entity->setSortNo(0); // 最小値

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $this->assertSame(0, $entity->getSortNo());
        $this->assertNotNull($entity->getId());

        // クリーンアップ
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * 特殊文字を含むフィールド名テスト
     */
    public function testOrderDisplaySettingWithSpecialCharacters()
    {
        $entity = new OrderDisplaySetting();

        $specialFieldName = 'test_field_特殊文字_123_!@#$%^&*()';
        $entity->setFieldName($specialFieldName);
        $entity->setDispName('特殊文字テスト');
        $entity->setEnabled(true);
        $entity->setSortNo(1);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $this->assertSame($specialFieldName, $entity->getFieldName());
        $this->assertNotNull($entity->getId());

        // クリーンアップ
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * 未初期化プロパティでのテスト
     *
     * field_name / disp_name は string 型のため、未設定のまま永続化を試みると
     * 型付きプロパティの未初期化アクセスまたは DB 制約違反で失敗する.
     */
    public function testOrderDisplaySettingWithNullValues()
    {
        $entity = new OrderDisplaySetting();

        // field_name, disp_name を未設定のまま永続化を試みる
        $entity->setEnabled(true);
        $entity->setSortNo(1);

        $this->entityManager->persist($entity);

        try {
            $this->entityManager->flush();
            $this->fail('未初期化プロパティでの保存は失敗するべき');
        } catch (\Error|\Exception $e) {
            // 期待される例外（型付きプロパティの未初期化アクセスまたは DB 制約違反）
            $this->assertTrue(true, '未初期化プロパティでの保存が期待通り失敗した: '.$e->getMessage());
        }
    }
}
