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

namespace Eccube\Service\AgentCommerce\Ucp\Signature;

use Eccube\Service\AgentCommerce\Exception\UcpSignatureException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * UCP checkout ルートに RFC 9421 署名検証を適用するサブスクライバ.
 *
 * UCP のインバウンド認証は HTTP Message Signatures が標準であり、OAuth2/eccube-api4 に依存しない。
 * 署名が提示されていれば必ず検証し (present-then-verify)、失敗時はプロトコル系エラー (HTTP 401) を返す。
 * 署名が無いリクエストを拒否するか (`$requireSignature`) は設定で決める。GET (取得) は状態変更を
 * 伴わないため署名必須対象から除外する (提示されていれば検証はする)。
 *
 * 検証成功時はエージェントの profile URL を request 属性 `ucp_agent_profile` に格納する。
 */
class UcpSignatureSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UcpRequestSignatureVerifier $verifier,
        private readonly bool $requireSignature = false,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onController',
        ];
    }

    public function onController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $route = (string) $request->attributes->get('_route');
        if (!str_starts_with($route, 'ucp_checkout_')) {
            return;
        }

        $isStateChanging = !$request->isMethod('GET');
        $isSigned = $this->verifier->isSigned($request);

        if (!$isSigned) {
            // 署名が無い場合: 状態変更操作かつ必須設定のときのみ拒否する。
            if ($isStateChanging && $this->requireSignature) {
                $this->reject($event, 'Request signature is required.');
            }

            return;
        }

        try {
            $agent = $this->verifier->verify($request);
        } catch (UcpSignatureException) {
            // 内部エラー詳細 (鍵の問題・profile 取得失敗理由等) はクライアントへ露出させない。
            $this->reject($event, 'Request signature verification failed.');

            return;
        }

        $request->attributes->set('ucp_agent_profile', $agent->profileUrl);
    }

    private function reject(ControllerEvent $event, string $message): void
    {
        $event->setController(static fn (): JsonResponse => new JsonResponse(
            ['code' => 'signature_invalid', 'content' => $message],
            Response::HTTP_UNAUTHORIZED,
        ));
    }
}
