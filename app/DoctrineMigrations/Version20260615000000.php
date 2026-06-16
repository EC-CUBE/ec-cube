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
use Eccube\Entity\Master\RefundRequestStatus;

/**
 * 返品申請機能の初期データを既存環境へ投入する.
 *
 * - mtb_refund_request_status（返品申請ステータス・マスタ）
 * - dtb_mail_template（管理者向け返品申請通知メール / id=10）
 * - dtb_csv（商品CSV出力項目 refund_allowed / id=215）
 *
 * テーブル自体は Entity 属性＋schema:update で反映されるため、ここでは INSERT のみを扱う。
 */
final class Version20260615000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $lang = env('ECCUBE_LOCALE');

        // mtb_refund_request_status
        $statuses = [
            RefundRequestStatus::NEW => $lang === 'en' ? 'New' : '新規申請',
            RefundRequestStatus::PROCESSING => $lang === 'en' ? 'Processing' : '処理中',
            RefundRequestStatus::ACCEPTED => $lang === 'en' ? 'Accepted' : '承認済',
            RefundRequestStatus::DECLINED => $lang === 'en' ? 'Declined' : '却下',
            RefundRequestStatus::INFO_REQUESTED => $lang === 'en' ? 'Information Requested' : '追加情報依頼',
        ];
        $sortNo = 0;
        foreach ($statuses as $id => $name) {
            $exists = $this->connection->fetchOne(
                'SELECT COUNT(*) FROM mtb_refund_request_status WHERE id = :id',
                ['id' => $id]
            );
            if ($exists == 0) {
                $this->addSql(
                    "INSERT INTO mtb_refund_request_status (id, name, sort_no, discriminator_type) VALUES (?, ?, ?, 'refundrequeststatus')",
                    [$id, $name, $sortNo]
                );
            }
            $sortNo++;
        }

        // dtb_mail_template（管理者向け返品申請通知メール）
        $mailExists = $this->connection->fetchOne(
            'SELECT COUNT(*) FROM dtb_mail_template WHERE id = 10'
        );
        if ($mailExists == 0) {
            $name = $lang === 'en' ? 'Refund Request Notification' : '返品申請通知メール';
            $subject = $lang === 'en' ? 'A refund request has been submitted' : '返品申請を受け付けました';
            $this->addSql(
                'INSERT INTO dtb_mail_template (id, creator_id, name, file_name, mail_subject, deletable, create_date, update_date, discriminator_type) '
                ."VALUES (10, null, ?, 'Mail/refund_request_notify.twig', ?, false, '2017-03-07 10:14:52', '2017-03-07 10:14:52', 'mailtemplate')",
                [$name, $subject]
            );
        }

        // dtb_csv（商品CSV出力項目 refund_allowed）
        $csvExists = $this->connection->fetchOne(
            'SELECT COUNT(*) FROM dtb_csv WHERE id = 215'
        );
        if ($csvExists == 0) {
            $dispName = $lang === 'en' ? 'Refund Allowed Flag' : '返品許可フラグ';
            $this->addSql(
                'INSERT INTO dtb_csv (id, csv_type_id, creator_id, entity_name, field_name, reference_field_name, disp_name, sort_no, enabled, create_date, update_date, discriminator_type) '
                ."VALUES (215, 1, null, 'Eccube\\Entity\\Product', 'refund_allowed', null, ?, 33, false, '2017-03-07 10:14:00', '2017-03-07 10:14:00', 'csv')",
                [$dispName]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM dtb_csv WHERE id = 215 AND entity_name = 'Eccube\\\\Entity\\\\Product' AND field_name = 'refund_allowed'");
        $this->addSql("DELETE FROM dtb_mail_template WHERE id = 10 AND file_name = 'Mail/refund_request_notify.twig'");
        $this->addSql("DELETE FROM mtb_refund_request_status WHERE id IN (1, 2, 3, 4, 5) AND discriminator_type = 'refundrequeststatus'");
    }
}
