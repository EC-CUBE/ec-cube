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

namespace Eccube\Service\AgentCommerce\Security;

/**
 * 1 つの purpose についてキーストアを調べた結果.
 *
 * eccube:keystore:list / show / generate の出力源. 鍵素材そのものは保持しない
 * ($details は KeyPurposeInterface::describe() が返す公開情報のみ).
 */
final readonly class KeyStoreEntry
{
    /**
     * @param bool                 $exists             鍵が保管されているか
     * @param bool                 $readable           実行中のユーザーから鍵を読めるか
     * @param string|null          $path               保管先. ファイル以外のキーストアでは null
     * @param string|null          $permissions        パーミッション (4 桁 8 進数)
     * @param string|null          $owner              所有者 (uid:gid)
     * @param string|null          $updatedAt          最終更新日時
     * @param string|null          $webReadabilityHint 読めない場合の対処
     * @param array<string, mixed> $details            describe() が返した公開情報
     * @param string|null          $error              鍵を解釈できなかった場合のメッセージ
     */
    public function __construct(
        public string $purpose,
        public string $label,
        public bool $exists,
        public bool $readable,
        public ?string $path,
        public ?string $permissions,
        public ?string $owner,
        public ?string $updatedAt,
        public WebReadability $webReadability,
        public ?string $webReadabilityHint,
        public array $details = [],
        public ?string $error = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'purpose' => $this->purpose,
            'label' => $this->label,
            'exists' => $this->exists,
            'readable' => $this->readable,
            'path' => $this->path,
            'permissions' => $this->permissions,
            'owner' => $this->owner,
            'updated_at' => $this->updatedAt,
            'web_readability' => $this->webReadability->value,
            'web_readability_hint' => $this->webReadabilityHint,
            'details' => $this->details,
            'error' => $this->error,
        ];
    }
}
