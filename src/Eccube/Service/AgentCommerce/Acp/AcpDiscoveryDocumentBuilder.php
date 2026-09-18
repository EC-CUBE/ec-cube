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

namespace Eccube\Service\AgentCommerce\Acp;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * ACP discovery 文書 (`/.well-known/acp.json`) を組み立てる.
 *
 * discovery は capability discovery (checkout の入口) であり、**認証不要**で配信する。
 * `api_base_url` は checkout エンドポイントのベースを `RequestContext` から動的生成する
 * (設置パスをハードコードしない)。`protocol.name` は `"acp"`、`transports` は `["rest"]`、
 * `capabilities.services` に `"checkout"` を宣言する。
 *
 * **MUST NOT**: 文書は未認証配信のため `merchant_id` 等の加盟店固有識別子を含めない (列挙・指紋採取防止)。
 * 鍵・カタログも含めない (商品 discovery は Product Feed の push が担う)。app/Customize で本ビルダーを
 * decoration すれば extensions / supported_currencies 等を拡張できる。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.discovery.md §4 (DiscoveryResponse) / §7.4 (merchant_id 禁止)
 */
class AcpDiscoveryDocumentBuilder
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * ACP discovery 文書を連想配列で返す.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        return [
            'protocol' => [
                'name' => 'acp',
                'version' => AcpCheckoutSessionMapper::ACP_VERSION,
                'supported_versions' => AcpCheckoutSessionMapper::SUPPORTED_VERSIONS,
                'documentation_url' => 'https://agenticcommerce.dev',
            ],
            'api_base_url' => $this->apiBaseUrl(),
            'transports' => ['rest'],
            'capabilities' => [
                'services' => ['checkout'],
            ],
        ];
    }

    /**
     * checkout エンドポイントのベース URL (絶対 HTTPS) を RequestContext から導出する.
     *
     * create ルート (`{base}/checkout_sessions`) の絶対 URL から末尾を除いてベースを得る。
     * 設置パス (サブディレクトリ等) は RequestContext に従い、ハードコードしない。
     */
    private function apiBaseUrl(): string
    {
        $create = $this->urlGenerator->generate('acp_checkout_create', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $base = preg_replace('#/checkout_sessions/?$#', '', $create) ?? $create;

        // discovery / API は HTTPS 必須 (RequestContext が http のときも https に強制)。
        return str_starts_with($base, 'http://') ? substr_replace($base, 'https://', 0, 7) : $base;
    }
}
