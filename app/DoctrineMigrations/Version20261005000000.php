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

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * 修正前の Version20260615000000 を適用済みの環境を補正する.
 *
 * - dtb_mail_template / dtb_csv のシーケンスを MAX(id) に合わせる（PostgreSQL のみ）。
 *   ID を指定して INSERT していたがシーケンスを進めておらず、次の採番が既存の ID と重複する。
 *   MySQL は ID を指定した INSERT で AUTO_INCREMENT が進むため対象外。
 * - 返品申請通知メールのテンプレートが無ければ追加する。
 *   id=10 が使用済み（4.3 以前にメールテンプレートを追加した環境）だと INSERT を飛ばしていた。
 */
final class Version20261005000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('dtb_mail_template') || !$schema->hasTable('dtb_csv')) {
            return;
        }

        // 採番する INSERT より前にシーケンスを合わせる
        if ($this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            foreach (['dtb_mail_template', 'dtb_csv'] as $table) {
                $this->addSql(sprintf(
                    "SELECT setval(pg_get_serial_sequence('%1\$s', 'id'), (SELECT COALESCE(MAX(id), 0) + 1 FROM %1\$s), false)",
                    $table
                ));
            }
        }

        $mailExists = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM dtb_mail_template WHERE file_name = 'Mail/refund_request_notify.twig'"
        );
        if ($mailExists == 0) {
            $lang = env('ECCUBE_LOCALE');
            $name = $lang === 'en' ? 'Refund Request Notification' : '返品申請通知メール';
            $subject = $lang === 'en' ? 'A refund request has been submitted' : '返品申請を受け付けました';
            $this->addSql(
                'INSERT INTO dtb_mail_template (creator_id, name, file_name, mail_subject, deletable, create_date, update_date, discriminator_type) '
                ."VALUES (null, ?, 'Mail/refund_request_notify.twig', ?, false, '2017-03-07 10:14:52', '2017-03-07 10:14:52', 'mailtemplate')",
                [$name, $subject]
            );
        }
    }

    public function down(Schema $schema): void
    {
        // シーケンスを MAX(id) より前へ戻すと採番が重複するため、巻き戻さない。
        // テンプレートは Version20260615000000 の down() が削除する
    }
}
