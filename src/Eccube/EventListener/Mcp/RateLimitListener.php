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

namespace Eccube\EventListener\Mcp;

use Eccube\Service\Mcp\AuditResult;
use Eccube\Service\Mcp\McpAuditLogger;
use League\Bundle\OAuth2ServerBundle\Security\Authentication\Token\OAuth2Token;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * `^/<admin>/mcp` 配下のリクエストに 2 段のレート制限を適用する (設計 §5 「Rate Limiter 連携」)。
 *
 *   - **IP 単位 (mcp_ip limiter)**: `kernel.request` priority 14 で発火。 admin firewall (priority 8)
 *     より早く動くため、 認証エラー連発攻撃でも IP 単位の枠を消費させて DoS を抑制できる。
 *   - **client_id 単位 (mcp_client limiter)**: `kernel.controller` priority 0 で発火。 firewall を
 *     通過して `OAuth2Token` が `TokenStorage` に乗った後にのみ消費する。 認証済みクライアントを
 *     識別して、 IP 制限より細かい (= 通常は緩い) 制限を適用する。
 *
 * 超過時の挙動は両者共通で:
 *   - HTTP 429 + `{"error":"rate_limited","retry_after_seconds":<n>}` を返す
 *   - `Retry-After: <n>` と `X-RateLimit-Remaining: 0` ヘッダを付与
 *   - `McpAuditLogger::logSecurityEvent(AuditResult::RateLimited)` で監査ログに記録
 *
 * `kernel.controller` イベントは `setResponse()` を持たないため、 controller を「429 を返す
 * callable」に差し替えることでレスポンスを返す。 引数解決等の後続処理は controller の戻り値が
 * Response なのでスキップされる。
 */
final readonly class RateLimitListener implements EventSubscriberInterface
{
    private string $mcpPathPrefix;

    public function __construct(
        string $eccubeAdminRoute,
        private RateLimiterFactory $mcpIpLimiter,
        private RateLimiterFactory $mcpClientLimiter,
        private TokenStorageInterface $tokenStorage,
        private McpAuditLogger $auditLogger,
    ) {
        $this->mcpPathPrefix = '/'.$eccubeAdminRoute.'/mcp';
    }

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            // Origin/CT ガード (priority 16) の後、 admin firewall (priority 8) の前で IP 制限
            KernelEvents::REQUEST => ['onKernelRequest', 14],
            // firewall 通過後、 引数解決前のタイミングで client_id 制限
            KernelEvents::CONTROLLER => ['onKernelController', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!str_starts_with($request->getPathInfo(), $this->mcpPathPrefix)) {
            return;
        }

        $ip = $request->getClientIp() ?? 'unknown';
        $limit = $this->mcpIpLimiter->create('mcp:ip:'.$ip)->consume();
        if (!$limit->isAccepted()) {
            $this->auditLogger->logSecurityEvent(AuditResult::RateLimited, [
                'kind' => 'ip',
                'ip' => $ip,
                'retry_after_seconds' => $this->retryAfterSeconds($limit),
            ]);
            $event->setResponse($this->buildRateLimitedResponse($limit));
        }
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!str_starts_with($request->getPathInfo(), $this->mcpPathPrefix)) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token instanceof OAuth2Token) {
            // firewall 通過後でも OAuth2Token でなければ (= 認証されていない経路) スキップ。
            // 認証エラーは別途 firewall で 401 として処理される。
            return;
        }

        $clientId = $token->getOAuthClientId();
        $limit = $this->mcpClientLimiter->create('mcp:client:'.$clientId)->consume();
        if (!$limit->isAccepted()) {
            $this->auditLogger->logSecurityEvent(AuditResult::RateLimited, [
                'kind' => 'client_id',
                'client_id' => $clientId,
                'retry_after_seconds' => $this->retryAfterSeconds($limit),
            ]);

            $response = $this->buildRateLimitedResponse($limit);
            $event->setController(static fn (): Response => $response);
        }
    }

    private function buildRateLimitedResponse(RateLimit $limit): JsonResponse
    {
        $retryAfter = $this->retryAfterSeconds($limit);

        return new JsonResponse(
            [
                'error' => 'rate_limited',
                'retry_after_seconds' => $retryAfter,
            ],
            Response::HTTP_TOO_MANY_REQUESTS,
            [
                'Retry-After' => (string) $retryAfter,
                'X-RateLimit-Remaining' => '0',
                'X-RateLimit-Limit' => (string) $limit->getLimit(),
            ],
        );
    }

    /**
     * `RateLimit::getRetryAfter()` の DateTimeImmutable から現在時刻までの秒数を求める。
     * 0 以下は 1 に丸める (`Retry-After: 0` は仕様上避ける)。
     */
    private function retryAfterSeconds(RateLimit $limit): int
    {
        $now = new \DateTimeImmutable();
        $diff = $limit->getRetryAfter()->getTimestamp() - $now->getTimestamp();

        return max(1, $diff);
    }
}
