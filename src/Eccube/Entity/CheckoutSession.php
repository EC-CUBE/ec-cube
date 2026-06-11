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

namespace Eccube\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\CheckoutSessionRepository;

if (!class_exists(CheckoutSession::class)) {
    /**
     * エージェントコマース (ACP/UCP) のチェックアウトセッション.
     *
     * プロトコル非依存の中核エンティティ。AI エージェントはブラウザセッション (Cookie) を持たないため、
     * 通常購入の `Cart` がセッション (`cart_key`) で束ねられるのに対し、本エンティティが
     * `session_id` (推測困難な公開識別子) で `Cart` をセッションレスに束ねる役割を担う。
     *
     * - 商品明細は `Cart`/`CartItem` を再利用 (本エンティティは Cart を関連で保持)。
     * - 確定時は通常購入と同様に `pre_order_id` 経由で `Order` を生成・関連付ける。
     * - `status` はプロトコル横断の正規化ステータス。プロトコル固有 status は `metadata` に保持する。
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6777
     */
    #[ORM\Table(name: 'dtb_checkout_session')]
    #[ORM\Index(columns: ['update_date'], name: 'dtb_checkout_session_update_date_idx')]
    #[ORM\UniqueConstraint(name: 'dtb_checkout_session_session_id_idx', columns: ['session_id'])]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: CheckoutSessionRepository::class)]
    class CheckoutSession extends AbstractEntity
    {
        /** 未完了 (作成直後・住所/配送/支払が未確定). */
        public const STATUS_INCOMPLETE = 'incomplete';

        /** 確定可能 (必須項目が揃い complete を実行できる). */
        public const STATUS_READY = 'ready';

        /** 完了 (Order 生成済). */
        public const STATUS_COMPLETED = 'completed';

        /** 取消. */
        public const STATUS_CANCELED = 'canceled';

        /** 期限切れ. */
        public const STATUS_EXPIRED = 'expired';

        public const PROTOCOL_ACP = 'acp';

        public const PROTOCOL_UCP = 'ucp';

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        /**
         * エージェントへ公開するセッション識別子 (推測困難・API パスで使用).
         *
         * 整数 PK は列挙可能なため、外部公開用にはこの不透明な識別子を用いる。
         */
        #[ORM\Column(name: 'session_id', type: Types::STRING, length: 255)]
        private string $session_id = '';

        /**
         * プロトコル名 (`acp` / `ucp`).
         */
        #[ORM\Column(name: 'protocol', type: Types::STRING, length: 255)]
        private string $protocol = '';

        /**
         * セッションを開始したエージェントの識別子.
         */
        #[ORM\Column(name: 'agent_id', type: Types::STRING, length: 255, nullable: true)]
        private ?string $agent_id = null;

        /**
         * 正規化ステータス (`incomplete`/`ready`/`completed`/`canceled`/`expired`).
         */
        #[ORM\Column(name: 'status', type: Types::STRING, length: 255, options: ['default' => self::STATUS_INCOMPLETE])]
        private string $status = self::STATUS_INCOMPLETE;

        /**
         * 通貨コード (ISO 4217 alpha-3).
         */
        #[ORM\Column(name: 'currency_code', type: Types::STRING, length: 3)]
        private string $currency_code = 'JPY';

        /**
         * セッションの有効期限.
         */
        #[ORM\Column(name: 'expires_at', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
        private ?\DateTime $expires_at = null;

        /**
         * 購入者情報 (氏名・住所・連絡先等) の中立表現.
         *
         * @var array<string, mixed>|null
         */
        #[ORM\Column(name: 'buyer_data', type: Types::JSON, nullable: true)]
        private ?array $buyer_data = null;

        /**
         * 配送 (fulfillment) 情報の中立表現.
         *
         * @var array<string, mixed>|null
         */
        #[ORM\Column(name: 'fulfillment_data', type: Types::JSON, nullable: true)]
        private ?array $fulfillment_data = null;

        /**
         * 支払情報の中立表現 (token はマスキング/暗号化して保持).
         *
         * @var array<string, mixed>|null
         */
        #[ORM\Column(name: 'payment_data', type: Types::JSON, nullable: true)]
        private ?array $payment_data = null;

        /**
         * プロトコル固有のメタデータ (正規化ステータスに収まらない情報を保持).
         *
         * @var array<string, mixed>|null
         */
        #[ORM\Column(name: 'metadata', type: Types::JSON, nullable: true)]
        private ?array $metadata = null;

        /**
         * 明細を保持する Cart (セッションレスに束ねる対象).
         */
        #[ORM\ManyToOne(targetEntity: Cart::class)]
        #[ORM\JoinColumn(name: 'cart_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
        private ?Cart $Cart = null;

        /**
         * 確定時に生成される Order (完了前は null).
         */
        #[ORM\ManyToOne(targetEntity: Order::class)]
        #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
        private ?Order $Order = null;

        /**
         * 紐づく会員 (ゲスト購入では null).
         */
        #[ORM\ManyToOne(targetEntity: Customer::class)]
        #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
        private ?Customer $Customer = null;

        #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
        private \DateTime $create_date;

        #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
        private \DateTime $update_date;

        public function getId(): ?int
        {
            return $this->id;
        }

        public function getSessionId(): string
        {
            return $this->session_id;
        }

        public function setSessionId(string $session_id): CheckoutSession
        {
            $this->session_id = $session_id;

            return $this;
        }

        public function getProtocol(): string
        {
            return $this->protocol;
        }

        public function setProtocol(string $protocol): CheckoutSession
        {
            $this->protocol = $protocol;

            return $this;
        }

        public function getAgentId(): ?string
        {
            return $this->agent_id;
        }

        public function setAgentId(?string $agent_id = null): CheckoutSession
        {
            $this->agent_id = $agent_id;

            return $this;
        }

        public function getStatus(): string
        {
            return $this->status;
        }

        public function setStatus(string $status): CheckoutSession
        {
            $this->status = $status;

            return $this;
        }

        public function getCurrencyCode(): string
        {
            return $this->currency_code;
        }

        public function setCurrencyCode(string $currency_code): CheckoutSession
        {
            $this->currency_code = $currency_code;

            return $this;
        }

        public function getExpiresAt(): ?\DateTime
        {
            return $this->expires_at;
        }

        public function setExpiresAt(?\DateTime $expires_at = null): CheckoutSession
        {
            $this->expires_at = $expires_at;

            return $this;
        }

        /**
         * @return array<string, mixed>|null
         */
        public function getBuyerData(): ?array
        {
            return $this->buyer_data;
        }

        /**
         * @param array<string, mixed>|null $buyer_data
         */
        public function setBuyerData(?array $buyer_data = null): CheckoutSession
        {
            $this->buyer_data = $buyer_data;

            return $this;
        }

        /**
         * @return array<string, mixed>|null
         */
        public function getFulfillmentData(): ?array
        {
            return $this->fulfillment_data;
        }

        /**
         * @param array<string, mixed>|null $fulfillment_data
         */
        public function setFulfillmentData(?array $fulfillment_data = null): CheckoutSession
        {
            $this->fulfillment_data = $fulfillment_data;

            return $this;
        }

        /**
         * @return array<string, mixed>|null
         */
        public function getPaymentData(): ?array
        {
            return $this->payment_data;
        }

        /**
         * @param array<string, mixed>|null $payment_data
         */
        public function setPaymentData(?array $payment_data = null): CheckoutSession
        {
            $this->payment_data = $payment_data;

            return $this;
        }

        /**
         * @return array<string, mixed>|null
         */
        public function getMetadata(): ?array
        {
            return $this->metadata;
        }

        /**
         * @param array<string, mixed>|null $metadata
         */
        public function setMetadata(?array $metadata = null): CheckoutSession
        {
            $this->metadata = $metadata;

            return $this;
        }

        public function getCart(): ?Cart
        {
            return $this->Cart;
        }

        public function setCart(?Cart $Cart = null): CheckoutSession
        {
            $this->Cart = $Cart;

            return $this;
        }

        public function getOrder(): ?Order
        {
            return $this->Order;
        }

        public function setOrder(?Order $Order = null): CheckoutSession
        {
            $this->Order = $Order;

            return $this;
        }

        public function getCustomer(): ?Customer
        {
            return $this->Customer;
        }

        public function setCustomer(?Customer $Customer = null): CheckoutSession
        {
            $this->Customer = $Customer;

            return $this;
        }

        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date ?? null;
        }

        public function setCreateDate(\DateTime $create_date): CheckoutSession
        {
            $this->create_date = $create_date;

            return $this;
        }

        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date ?? null;
        }

        public function setUpdateDate(\DateTime $update_date): CheckoutSession
        {
            $this->update_date = $update_date;

            return $this;
        }

        /**
         * 有効期限を過ぎているか.
         */
        public function isExpired(\DateTime $now): bool
        {
            return $this->expires_at !== null && $this->expires_at < $now;
        }
    }
}
