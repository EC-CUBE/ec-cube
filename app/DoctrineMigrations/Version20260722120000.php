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
 * dtb_base_info へ納品書PDFの店舗情報出力項目トグルを追加する (#6197).
 *
 * 納品書PDFに出力する店舗情報（店名・住所・会社名・営業時間・メッセージ等）の表示/非表示を
 * 管理者が基本設定画面で切り替えられるようにするための boolean 列。
 * 既定は「現状出力している項目＋インボイス要件の会社名」を ON、新規項目は OFF とし、
 * 既存インストールのアップグレード時も従来の納品書の見た目を極力維持する。
 * 列単位で存在を確認する冪等実装（schema:update と重複しても二重追加しない）。
 */
final class Version20260722120000 extends AbstractMigration
{
    public const NAME = 'dtb_base_info';

    /**
     * 追加する列名 => 既定値（true/false）.
     *
     * @var array<string, bool>
     */
    private const COLUMNS = [
        'order_pdf_visible_shop_name' => true,
        'order_pdf_visible_shop_kana' => false,
        'order_pdf_visible_shop_name_eng' => false,
        'order_pdf_visible_address' => true,
        'order_pdf_visible_company_name' => true,
        'order_pdf_visible_company_kana' => false,
        'order_pdf_visible_phone_number' => true,
        'order_pdf_visible_business_hour' => false,
        'order_pdf_visible_email' => true,
        'order_pdf_visible_invoice_number' => true,
        'order_pdf_visible_message' => false,
    ];

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $table = $schema->getTable(self::NAME);
        foreach (self::COLUMNS as $column => $default) {
            if ($table->hasColumn($column)) {
                continue;
            }

            $defaultSql = $default ? 'true' : 'false';
            $this->addSql(sprintf('ALTER TABLE dtb_base_info ADD %s BOOLEAN NOT NULL DEFAULT %s', $column, $defaultSql));
        }
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $table = $schema->getTable(self::NAME);
        foreach (array_keys(self::COLUMNS) as $column) {
            if (!$table->hasColumn($column)) {
                continue;
            }

            $this->addSql(sprintf('ALTER TABLE dtb_base_info DROP COLUMN %s', $column));
        }
    }
}
