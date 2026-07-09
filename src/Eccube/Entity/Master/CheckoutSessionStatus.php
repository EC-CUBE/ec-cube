<?php

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

namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\Master\CheckoutSessionStatusRepository;

if (!class_exists(CheckoutSessionStatus::class, false)) {
    /**
     * CheckoutSessionStatus.
     *
     * エージェントコマース (ACP/UCP) チェックアウトセッションの正規化ステータスマスタ。
     * `name` にはプロトコル横断の正準識別子
     * (`incomplete`/`ready`/`requires_action`/`in_progress`/`completed`/`canceled`/`expired`)
     * を保持する (ロケール非依存・ja/en 共通)。表示ラベルではなく機械可読値である点に注意。
     *
     * `requires_action` / `in_progress` は**非終端だが在庫を引当済み**の中間状態であり、
     * complete の状態機械 ({@link \Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutCompletionService})
     * が遷移させる。期限切れ回収 ({@link \Eccube\Repository\CheckoutSessionRepository::findExpired}) の
     * 対象に含まれる (終端は completed/canceled/expired のみ)。
     */
    #[ORM\Table(name: 'mtb_checkout_session_status')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: CheckoutSessionStatusRepository::class)]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    class CheckoutSessionStatus extends AbstractMasterEntity
    {
        /** 未完了 (作成直後・住所/配送/支払が未確定). */
        public const INCOMPLETE = 1;

        /** 確定可能 (必須項目が揃い complete を実行できる). */
        public const READY = 2;

        /** 完了 (Order 生成済). */
        public const COMPLETED = 3;

        /** 取消. */
        public const CANCELED = 4;

        /** 期限切れ. */
        public const EXPIRED = 5;

        /** 追加対応待ち (3DS challenge / escalation)。在庫は引当のまま保持し再開 complete を待つ. */
        public const REQUIRES_ACTION = 6;

        /** 非同期決済の確定待ち (PSP の IPN/Webhook で completed へ遷移). */
        public const IN_PROGRESS = 7;
    }
}
