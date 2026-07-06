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
use Eccube\Repository\AgentCheckoutIdempotencyRepository;

if (!class_exists(AgentCheckoutIdempotency::class)) {
    /**
     * エージェントチェックアウトの冪等性記録 (Idempotency-Key).
     *
     * 状態変更操作 (complete 等) の `Idempotency-Key` を **DB の一意制約**で記録し、再送時は
     * 副作用を再実行せず保存済みレスポンスをリプレイする。`(idempotency_key, subject)` の一意制約により、
     * **マルチインスタンス (AWS 等) でも単一の共有 DB だけで**並行・越境の二重実行を防ぐ
     * (ノードローカルな分散ロックや共有キャッシュに依存しない)。
     *
     * - `subject` は認証済みエージェント識別子 (UCP-Agent profile 等)。主体ごとに名前空間化し、
     *   別エージェントによる越境リプレイを防ぐ。未認証は空文字で扱う (一意制約で NULL を区別しないため)。
     * - `response_status` が NULL の行は「処理中 (予約のみ)」を表す。確定後にレスポンスを書き込む。
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6777
     */
    #[ORM\Table(name: 'dtb_agent_checkout_idempotency')]
    #[ORM\Index(columns: ['create_date'], name: 'dtb_agent_checkout_idempotency_create_date_idx')]
    #[ORM\UniqueConstraint(name: 'dtb_agent_checkout_idempotency_key_idx', columns: ['idempotency_key', 'subject'])]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: AgentCheckoutIdempotencyRepository::class)]
    class AgentCheckoutIdempotency extends AbstractEntity
    {
        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        /**
         * エージェントが指定した Idempotency-Key.
         */
        #[ORM\Column(name: 'idempotency_key', type: Types::STRING, length: 255)]
        private string $idempotency_key = '';

        /**
         * 主体 (認証済みエージェント識別子)。未認証は空文字.
         */
        #[ORM\Column(name: 'subject', type: Types::STRING, length: 255)]
        private string $subject = '';

        /**
         * リクエスト内容のハッシュ (同一キーの異パラメータ再利用検知用).
         */
        #[ORM\Column(name: 'request_hash', type: Types::STRING, length: 255)]
        private string $request_hash = '';

        /**
         * 保存済みレスポンスの HTTP ステータス (処理中は NULL).
         */
        #[ORM\Column(name: 'response_status', type: Types::INTEGER, nullable: true)]
        private ?int $response_status = null;

        /**
         * 保存済みレスポンスボディ (処理中は NULL).
         *
         * @var array<string, mixed>|null
         */
        #[ORM\Column(name: 'response_body', type: Types::JSON, nullable: true)]
        private ?array $response_body = null;

        #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
        private \DateTime $create_date;

        #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
        private \DateTime $update_date;

        public function getId(): ?int
        {
            return $this->id;
        }

        public function getIdempotencyKey(): string
        {
            return $this->idempotency_key;
        }

        public function setIdempotencyKey(string $idempotency_key): self
        {
            $this->idempotency_key = $idempotency_key;

            return $this;
        }

        public function getSubject(): string
        {
            return $this->subject;
        }

        public function setSubject(string $subject): self
        {
            $this->subject = $subject;

            return $this;
        }

        public function getRequestHash(): string
        {
            return $this->request_hash;
        }

        public function setRequestHash(string $request_hash): self
        {
            $this->request_hash = $request_hash;

            return $this;
        }

        public function getResponseStatus(): ?int
        {
            return $this->response_status;
        }

        public function setResponseStatus(?int $response_status = null): self
        {
            $this->response_status = $response_status;

            return $this;
        }

        /**
         * @return array<string, mixed>|null
         */
        public function getResponseBody(): ?array
        {
            return $this->response_body;
        }

        /**
         * @param array<string, mixed>|null $response_body
         */
        public function setResponseBody(?array $response_body = null): self
        {
            $this->response_body = $response_body;

            return $this;
        }

        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date ?? null;
        }

        public function setCreateDate(\DateTime $create_date): self
        {
            $this->create_date = $create_date;

            return $this;
        }

        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date ?? null;
        }

        public function setUpdateDate(\DateTime $update_date): self
        {
            $this->update_date = $update_date;

            return $this;
        }

        /**
         * レスポンスが確定済か (処理中=予約のみは false).
         */
        public function hasResponse(): bool
        {
            return $this->response_status !== null;
        }
    }
}
