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

namespace Eccube\Service\AgentCommerce;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * エージェントコマースの discovery / checkout レスポンスに必要な店舗 URL を生成する.
 *
 * UCP checkout レスポンスの `links[]` (privacy_policy / terms_of_service) は標準ページ
 * (`help_privacy` / `help_agreement`) から**絶対 URL を実行時生成**する (BaseInfo にカラム化しない方針)。
 * URL のベースは `RequestContext` (TRUSTED_PROXIES 反映) から決まる。app/Customize で
 * 本サービスを decoration すれば独自ページへ差し替え可能。
 */
class StorefrontUrlResolver
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * プライバシーポリシーページの絶対 URL.
     */
    public function privacyPolicyUrl(): string
    {
        return $this->urlGenerator->generate('help_privacy', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * 利用規約ページの絶対 URL.
     */
    public function termsOfServiceUrl(): string
    {
        return $this->urlGenerator->generate('help_agreement', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * 注文の参照先 (permalink) 絶対 URL.
     *
     * UCP complete レスポンスの `order.permalink_url` に用いる。標準ではマイページの注文履歴
     * (`mypage_history`) を指す。ゲスト注文は会員ログインへ誘導されるが URL 自体は絶対 URL として有効。
     * 独自の注文確認ページへ差し替えたい場合は本サービスを decoration する。
     */
    public function orderPermalinkUrl(string $orderNo): string
    {
        return $this->urlGenerator->generate('mypage_history', ['order_no' => $orderNo], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
