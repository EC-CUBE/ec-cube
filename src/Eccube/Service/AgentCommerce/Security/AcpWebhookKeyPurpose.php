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
 * ACP Webhook (Merchant-Signature) の HMAC-SHA256 共有シークレット.
 *
 * 共通鍵のため公開してよい情報が無い. describe() はアルゴリズムと長さだけを返し,
 * 値そのものは返さない. エージェントへ渡す必要がある場合は, eccube:keystore:show が
 * 表示する保管パスから直接読み出す (端末やコマンド履歴へ残さないため).
 */
final class AcpWebhookKeyPurpose implements KeyPurposeInterface
{
    public const PURPOSE = 'acp_webhook';

    /**
     * 生成するシークレットのバイト数. 16 進数表記で保管するため, 文字数はこの 2 倍になる.
     */
    private const SECRET_BYTES = 32;

    #[\Override]
    public function getPurpose(): string
    {
        return self::PURPOSE;
    }

    #[\Override]
    public function getLabel(): string
    {
        return 'ACP Webhook (Merchant-Signature) の共有シークレット (HMAC-SHA256)';
    }

    #[\Override]
    public function generate(): string
    {
        return bin2hex(random_bytes(self::SECRET_BYTES));
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function describe(string $material): array
    {
        // 共有シークレットは値も, 値から導ける指紋も出さない.
        return [
            'algorithm' => 'HMAC-SHA256',
            'length' => mb_strlen(trim($material), '8bit'),
        ];
    }
}
