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

use Eccube\Service\AgentCommerce\Security\AgentCommerceOAuth2Authenticator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * ACP checkout ルートに OAuth2 Bearer 認証 (scope×protocol 照合) を適用するサブスクライバ.
 *
 * ACP のインバウンド認証は OAuth2 Bearer トークンが標準 (UCP の RFC 9421 署名主とは対照的)。
 * `acp:checkout` scope を要求し、{@link AgentCommerceOAuth2Authenticator} (eccube-api4 のリソース
 * サーバーに依存) で検証する。失敗時はプロトコル系エラー (flat Error) を HTTP ステータス付きで返す:
 * - トークン欠落 / 不正 → 401
 * - scope 不足・protocol 越境 → 403
 * - リソースサーバー (eccube-api4#188) 未導入 → 503
 *
 * 検証成功時は認証済みクライアント識別子を request 属性 `acp_client_id` に格納し、Idempotency-Key の
 * 名前空間 (越境リプレイ防止の subject) に用いる。Discovery (`/.well-known/acp.json`) は no-auth のため対象外。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.agentic_checkout.md §3.1 (Bearer 認証)
 */
class AcpAuthenticationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AgentCommerceOAuth2Authenticator $authenticator,
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
        if (!str_starts_with($route, 'acp_checkout_')) {
            return;
        }

        $token = $this->extractBearerToken($request->headers->get('Authorization'));
        if ($token === null) {
            $this->reject($event, Response::HTTP_UNAUTHORIZED, 'unauthorized', 'Authorization Bearer token is required.');

            return;
        }

        try {
            $userBadge = $this->authenticator->authenticate($token, 'acp', 'checkout');
        } catch (ServiceUnavailableHttpException) {
            $this->reject($event, Response::HTTP_SERVICE_UNAVAILABLE, 'service_unavailable', 'The OAuth2 resource server is not available.');

            return;
        } catch (AccessDeniedException) {
            $this->reject($event, Response::HTTP_FORBIDDEN, 'insufficient_scope', 'The token is not granted the required scope "acp:checkout".');

            return;
        } catch (AuthenticationException) {
            $this->reject($event, Response::HTTP_UNAUTHORIZED, 'invalid_token', 'The access token is invalid.');

            return;
        }

        $request->attributes->set('acp_client_id', $userBadge->getUserIdentifier());
    }

    /**
     * `Authorization: Bearer <token>` からトークンを取り出す (scheme は大文字小文字を区別しない).
     */
    private function extractBearerToken(?string $header): ?string
    {
        if ($header === null) {
            return null;
        }
        if (!preg_match('/\ABearer\s+(.+)\z/i', trim($header), $matches)) {
            return null;
        }

        $token = trim($matches[1]);

        return $token === '' ? null : $token;
    }

    private function reject(ControllerEvent $event, int $status, string $code, string $message): void
    {
        // ACP プロトコル系エラーは flat Error ({type, code, message})。詳細な内部理由は露出させない。
        $event->setController(static fn (): JsonResponse => new JsonResponse(
            ['type' => 'invalid_request', 'code' => $code, 'message' => $message],
            $status,
        ));
    }
}
