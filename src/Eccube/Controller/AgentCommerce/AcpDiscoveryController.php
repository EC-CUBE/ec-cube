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

namespace Eccube\Controller\AgentCommerce;

use Eccube\Controller\AbstractController;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\AgentCommerce\Acp\AcpDiscoveryDocumentBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * ACP discovery 文書 (`/.well-known/acp.json`) を配信するコントローラ.
 *
 * capability discovery (checkout の入口) を**認証不要**で公開する。文書は origin ルート固定で
 * 1 オリジン 1 枚 (RFC 8615)。`acp_checkout_enabled` が false のときは NotFoundHttpException
 * (ルートは削除しない既存パターン)。Cache-Control は public・max-age≥3600 (SHOULD)。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.discovery.md §4
 */
class AcpDiscoveryController extends AbstractController
{
    public function __construct(
        private readonly BaseInfoRepository $baseInfoRepository,
        private readonly AcpDiscoveryDocumentBuilder $documentBuilder,
    ) {
    }

    #[Route(path: '/.well-known/acp.json', name: 'acp_discovery', methods: ['GET'])]
    public function index(): JsonResponse
    {
        if (!$this->baseInfoRepository->get()->isAcpCheckoutEnabled()) {
            throw new NotFoundHttpException('ACP checkout is not enabled.');
        }

        $response = new JsonResponse($this->documentBuilder->build(), Response::HTTP_OK);
        // 認証不要・キャッシュ可 (SHOULD: public, max-age>=3600)。
        $response->setPublic();
        $response->setMaxAge(3600);

        return $response;
    }
}
