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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * `^/<admin>/mcp` 配下のリクエストに対して Origin / Content-Type の前段ガードを行う。
 *
 * - `Content-Type` が `application/json` で始まらない POST/PUT/DELETE 等は **415** で拒否
 * - `ECCUBE_MCP_ALLOWED_ORIGINS` が設定されている時、`Origin` ヘッダがその許可リストに無ければ **403**
 * - 許可リスト未設定時: dev/test は Origin 検証を skip、 **prod では検証できない Origin (=ブラウザ発) を
 *   403** で拒否 (Origin 無しの非ブラウザは通す)。 未設定のまま prod で DNS リバインディング等に晒さない
 * - GET / HEAD / OPTIONS は対象外 (Content-Type を伴わない、 CORS preflight も含むため)
 *
 * 拒否時のレスポンスは JSON 一本 (MCP クライアント前提)。 違反は `McpAuditLogger` に
 * `origin_invalid` として warning で記録する。 リスナの priority は admin firewall (priority 8)
 * より早い 16 で実行し、 認証層に到達する前にブロックする。
 */
final readonly class OriginContentTypeListener implements EventSubscriberInterface
{
    /** @var list<string> */
    private array $allowedOrigins;

    private string $mcpPathPrefix;

    public function __construct(
        string $eccubeAdminRoute,
        string $mcpAllowedOriginsCsv,
        private McpAuditLogger $auditLogger,
        private bool $skipUnvalidatedOrigin,
    ) {
        $this->mcpPathPrefix = '/'.$eccubeAdminRoute.'/mcp';
        $this->allowedOrigins = array_values(array_filter(
            array_map(trim(...), explode(',', $mcpAllowedOriginsCsv)),
            static fn (string $v): bool => '' !== $v,
        ));
    }

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        // admin firewall (priority 8) より前に動かす。 認証前にブロックすることでログ汚染や
        // 不要な認証処理を避ける。
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 16],
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

        // GET / HEAD / OPTIONS はリクエストボディを持たない (MCP 仕様: GET は SSE 予約、 OPTIONS は preflight)。
        if (\in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return;
        }

        $contentTypeRejection = $this->checkContentType($request->headers->get('Content-Type'));
        if (null !== $contentTypeRejection) {
            $this->auditLogger->logSecurityEvent(AuditResult::OriginInvalid, [
                'path' => $request->getPathInfo(),
                'method' => $request->getMethod(),
                'content_type' => $request->headers->get('Content-Type'),
                'reason' => 'invalid_content_type',
            ]);
            $event->setResponse($contentTypeRejection);

            return;
        }

        if ([] === $this->allowedOrigins) {
            // 許可リスト未設定時:
            // - dev/test ($skipUnvalidatedOrigin=true): 開発利便のため Origin 検証を skip
            // - prod: Origin 無し (curl/サーバ間) は通し、 検証できない Origin (=ブラウザ発) は 403
            if ($this->skipUnvalidatedOrigin || null === $request->headers->get('Origin')) {
                return;
            }

            $this->auditLogger->logSecurityEvent(AuditResult::OriginInvalid, [
                'path' => $request->getPathInfo(),
                'origin' => $request->headers->get('Origin'),
                'reason' => 'origin_not_configured',
            ]);
            $event->setResponse(new JsonResponse(
                ['error' => 'forbidden', 'message' => 'Origin validation is not configured; browser-originated requests are rejected.'],
                Response::HTTP_FORBIDDEN,
            ));

            return;
        }

        $originRejection = $this->checkOrigin($request->headers->get('Origin'));
        if (null !== $originRejection) {
            $this->auditLogger->logSecurityEvent(AuditResult::OriginInvalid, [
                'path' => $request->getPathInfo(),
                'origin' => $request->headers->get('Origin'),
                'allowed' => $this->allowedOrigins,
                'reason' => 'origin_not_allowed',
            ]);
            $event->setResponse($originRejection);
        }
    }

    /**
     * `Content-Type: application/json` で始まらなければ 415 を返す Response を生成する。
     */
    private function checkContentType(?string $contentType): ?Response
    {
        if (null !== $contentType && str_starts_with(strtolower($contentType), 'application/json')) {
            return null;
        }

        return new JsonResponse(
            ['error' => 'unsupported_media_type', 'message' => 'Content-Type must be application/json.'],
            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
        );
    }

    /**
     * Origin ヘッダが許可リストにあるか確認する。 Origin が無いリクエスト (curl 等) は許容。
     */
    private function checkOrigin(?string $origin): ?Response
    {
        if (null === $origin) {
            // ブラウザ以外 (curl 等) は Origin を送らないため通す
            return null;
        }
        if (\in_array($origin, $this->allowedOrigins, true)) {
            return null;
        }

        return new JsonResponse(
            ['error' => 'forbidden', 'message' => 'Origin not allowed.'],
            Response::HTTP_FORBIDDEN,
        );
    }
}
