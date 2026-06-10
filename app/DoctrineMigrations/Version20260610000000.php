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

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * 受注一覧画面の表示項目設定（dtb_order_display_setting）の初期データを既存環境へ投入する.
 *
 * 新規インストールは import_csv（dtb_order_display_setting.csv）から投入されるため、
 * ここでは既存環境向けに同じ初期8件を冪等に INSERT する.
 */
final class Version20260610000000 extends AbstractMigration
{
    public const NAME = 'dtb_order_display_setting';

    /**
     * 初期表示項目（field_name => disp_name）. sort_no は配列順.
     *
     * @var array<string, string>
     */
    private const DISPLAY_SETTINGS = [
        'order_info' => 'ID・注文者',
        'payment_method' => '支払方法',
        'order_status' => '対応状況',
        'payment_info' => '購入金額',
        'message' => 'お問合せ',
        'shipping_status' => '出荷状況',
        'tracking_number' => 'お問い合わせ番号',
        'delivery_address' => 'お届け先',
    ];

    public function up(Schema $schema): void
    {
        // テーブルが存在しない場合は終了（スキーマは Entity 属性＋schema:update が源泉）
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $sortNo = 1;
        foreach (self::DISPLAY_SETTINGS as $fieldName => $dispName) {
            $exists = $this->connection->fetchOne(
                'SELECT COUNT(*) FROM '.self::NAME.' WHERE field_name = :field_name',
                ['field_name' => $fieldName]
            );

            if ($exists == 0) {
                $this->addSql(
                    'INSERT INTO '.self::NAME.' (field_name, disp_name, enabled, sort_no, create_date, update_date)'
                    .' VALUES (:field_name, :disp_name, :enabled, :sort_no, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)',
                    [
                        'field_name' => $fieldName,
                        'disp_name' => $dispName,
                        'enabled' => true,
                        'sort_no' => $sortNo,
                    ]
                );
            }

            $sortNo++;
        }
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        foreach (array_keys(self::DISPLAY_SETTINGS) as $fieldName) {
            $this->addSql(
                'DELETE FROM '.self::NAME.' WHERE field_name = :field_name',
                ['field_name' => $fieldName]
            );
        }
    }
}
