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

namespace Eccube\Twig\Extension;

use Eccube\Service\CookieConsentService;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * クッキー同意機能のTwig拡張。
 *
 * 設定ページ等のキャッシュ対象外ページや、利用側が任意に同意状態を参照する用途で利用する。
 * フルページキャッシュ／CDN 配下のページでは、利用者ごとの同意状態に依存する出し分けに使わないこと。
 */
class CookieConsentExtension extends AbstractExtension
{
    public function __construct(
        private readonly CookieConsentService $cookieConsentService,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @return TwigFunction[]
     */
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_cookie_consent_given', $this->isConsentGiven(...)),
            new TwigFunction('is_cookie_consent_accepted', $this->isConsentAccepted(...)),
            new TwigFunction('get_cookie_consent_status', $this->getConsentStatus(...)),
        ];
    }

    /**
     * 同意が得られているか（同意または拒否のいずれか）をチェックする。
     *
     * @return bool true: 同意/拒否のいずれかが選択済み、false: 未設定
     */
    public function isConsentGiven(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        return $this->cookieConsentService->isConsentGiven($request);
    }

    /**
     * 同意されているかチェックする。
     *
     * @return bool true: 同意済み、false: 拒否または未設定
     */
    public function isConsentAccepted(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        return $this->cookieConsentService->isConsentAccepted($request);
    }

    /**
     * 現在の同意状態を取得する。
     *
     * @return string|null 'accepted'|'rejected'|null
     */
    public function getConsentStatus(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        return $this->cookieConsentService->getConsentStatus($request);
    }
}
