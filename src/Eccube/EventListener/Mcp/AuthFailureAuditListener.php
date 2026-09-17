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
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * `^/<admin>/mcp` の認証失敗 (401) を監査ログ (mcp.log) に残す。
 *
 * scope 拒否やレート制限と並び、 認証失敗も MCP 境界で起きたイベントとして client / IP 粒度で記録する。
 * 401 応答自体は api44 の OAuth2 resource server / firewall が生成するため、 ここでは記録のみを行う。
 *
 * 認証失敗は `LoginFailureEvent` だけでは拾い切れない (トークン未送信の 401 は authenticator が起動せず
 * イベントが発火しない)。 そのため mcp パスの **401 レスポンスそのもの** を `kernel.response` で拾う。
 * mcp パスで 401 を返すのは認証失敗のみ (scope 拒否は 200、 レート制限は 429/503、 Origin/CT 違反は 403/415)。
 */
final readonly class AuthFailureAuditListener implements EventSubscriberInterface
{
    private string $mcpPathPrefix;

    public function __construct(
        string $eccubeAdminRoute,
        private McpAuditLogger $auditLogger,
        private LoggerInterface $logger,
    ) {
        $this->mcpPathPrefix = '/'.$eccubeAdminRoute.'/mcp';
    }

    /**
     * @return array<string, string>
     */
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        if (!str_starts_with($event->getRequest()->getPathInfo(), $this->mcpPathPrefix)) {
            return;
        }
        if (Response::HTTP_UNAUTHORIZED !== $event->getResponse()->getStatusCode()) {
            return;
        }

        $this->safeAudit($event->getResponse());
    }

    /**
     * 監査ログ書き込みが失敗しても 401 応答を壊さないよう、 例外は default チャネルに記録して握り潰す。
     */
    private function safeAudit(Response $response): void
    {
        try {
            // client_id は best-effort。 401 時点でトークンは無効なため取得できず IP のみで記録する
            // (IP は McpAuditLogger が常に付与する)。 reason は WWW-Authenticate ヘッダ (PII を含まない)。
            $this->auditLogger->logAuthEvent(
                AuditResult::TokenInvalid,
                null,
                ['reason' => $response->headers->get('WWW-Authenticate') ?? 'unauthorized'],
            );
        } catch (\Throwable $e) {
            $this->logger->error('mcp 認証失敗の監査ログ書き込みに失敗', ['exception' => $e]);
        }
    }
}
