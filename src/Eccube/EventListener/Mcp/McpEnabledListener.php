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

use Eccube\Repository\BaseInfoRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * MCP 機能が無効 (店舗設定の `mcp_enabled` = false、 既定) の間は HTTP エンドポイント `^/<admin>/mcp` を
 * **404** で塞ぐ。 HTTP 経路の tools/list・tools/call・handshake はすべてこの 1 本のルート配下を通るため、
 * ツール層でなく前段リスナ 1 点で止められる。 priority は既存の Origin/CT ガード (16)・レート制限 (14)・
 * admin firewall (8) のいずれよりも早く、 routing (RouterListener=32) よりも前で確定させる 33 に置き、
 * 認証・Origin 検査・レート消費・監査ログの手前で止める。 404 (存在秘匿) を返し、 無効の間は
 * エンドポイントの存在自体を明かさない。
 *
 * 本リスナが塞ぐのは HTTP のみ。 `eccube:cli:*` は同じフラグを {@see \Eccube\Service\Mcp\McpCliToolInvoker::call()}
 * で参照して塞ぐ。 stdio (`mcp:server`) は shell アクセス前提の local-trust 経路のため本フラグの対象外
 * (トランスポート自体の有効化は mcp.yaml の `client_transports.stdio` で制御する)。
 */
final readonly class McpEnabledListener implements EventSubscriberInterface
{
    private string $mcpPathPrefix;

    public function __construct(
        string $eccubeAdminRoute,
        private BaseInfoRepository $baseInfoRepository,
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
            KernelEvents::REQUEST => ['onKernelRequest', 33],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!str_starts_with($event->getRequest()->getPathInfo(), $this->mcpPathPrefix)) {
            return;
        }

        if ($this->baseInfoRepository->get()->isMcpEnabled()) {
            return;
        }

        $event->setResponse(new Response('', Response::HTTP_NOT_FOUND));
    }
}
