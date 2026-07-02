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

namespace Eccube\Controller\Admin\AgentCommerce;

use Eccube\Controller\AbstractController;
use Eccube\Service\AgentCommerce\Catalog\Acp\AcpFeedClientInterface;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;
use Eccube\Service\AgentCommerce\Catalog\CatalogMapper;
use Eccube\Service\AgentCommerce\Catalog\CatalogProviderInterface;
use Eccube\Service\AgentCommerce\Catalog\Exception\AcpFeedException;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogCache;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogResponseBuilder;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Agent Commerce (ACP / UCP) の設定 / 運用操作画面 (管理画面).
 *
 * ACP Feed の手動 push (差分 / 全置換) と UCP Catalog キャッシュの操作 (クリア / 生成) を
 * 標準で提供する。いずれも「日本未提供 / 検証目的限定」の機能であり、画面に注意書きを表示する。
 * 各 POST アクションは CSRF トークンを検証し、シークレットは一切出力しない。
 */
class AgentCommerceController extends AbstractController
{
    /**
     * 生成する search のデフォルト件数.
     */
    private const WARMUP_LIMIT = 10;

    public function __construct(
        private readonly AcpFeedClientInterface $acpFeedClient,
        private readonly UcpCatalogCache $ucpCatalogCache,
        private readonly CatalogProviderInterface $catalogProvider,
        private readonly CatalogMapper $catalogMapper,
        private readonly UcpCatalogResponseBuilder $responseBuilder,
    ) {
    }

    /**
     * 設定 / 操作画面を表示する.
     *
     * @return array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/content/agent_commerce', name: 'admin_content_agent_commerce', methods: ['GET'])]
    #[Template(template: '@admin/Content/AgentCommerce/index.twig')]
    public function index(): array
    {
        // discovery / catalog は常時公開のため表示用フラグは不要。
        return [];
    }

    /**
     * ACP「今すぐ push」: 現行 feed への差分 upsert.
     */
    #[Route(path: '/%eccube_admin_route%/content/agent_commerce/acp_feed/push', name: 'admin_content_agent_commerce_acp_feed_push', methods: ['POST'])]
    public function acpFeedPush(Request $request): RedirectResponse
    {
        if (!$this->isTokenValid()) {
            throw new BadRequestHttpException();
        }

        $feedId = (string) $request->request->get('feed_id', '');
        if ($feedId === '') {
            $this->addError('admin.content.agent_commerce.acp.feed_id_required', 'admin');

            return $this->redirectToRoute('admin_content_agent_commerce');
        }

        try {
            // TODO: 差分検出マーク (stock_updated_at 等) に基づく upsert は後続実装。
            //       現状は現行 feed の全商品を再 upsert する。
            $products = $this->acpFeedClient->getFeedProducts($feedId);
            if ($products === []) {
                $this->addWarning('admin.content.agent_commerce.acp.push_no_products', 'admin');

                return $this->redirectToRoute('admin_content_agent_commerce');
            }

            $accepted = $this->acpFeedClient->upsertProducts($feedId, $products);
            if ($accepted) {
                $this->addSuccess('admin.content.agent_commerce.acp.push_success', 'admin');
            } else {
                $this->addWarning('admin.content.agent_commerce.acp.push_not_accepted', 'admin');
            }
        } catch (AcpFeedException $e) {
            $this->addError('admin.content.agent_commerce.acp.push_failed', 'admin');
            log_error('ACP Feed push (differential) failed.', ['message' => $e->getMessage()]);
        }

        return $this->redirectToRoute('admin_content_agent_commerce');
    }

    /**
     * ACP「全置換 push」: products.jsonl / metadata.json を生成して feed を登録.
     */
    #[Route(path: '/%eccube_admin_route%/content/agent_commerce/acp_feed/push_full', name: 'admin_content_agent_commerce_acp_feed_push_full', methods: ['POST'])]
    public function acpFeedPushFull(Request $request): RedirectResponse
    {
        if (!$this->isTokenValid()) {
            throw new BadRequestHttpException();
        }

        $feedId = (string) $request->request->get('feed_id', '');
        if ($feedId === '') {
            $this->addError('admin.content.agent_commerce.acp.feed_id_required', 'admin');

            return $this->redirectToRoute('admin_content_agent_commerce');
        }

        try {
            $result = $this->acpFeedClient->pushFullReplacement($feedId);
            $this->addSuccess(trans('admin.content.agent_commerce.acp.push_full_success', ['%count%' => $result['product_count']]), 'admin');
        } catch (AcpFeedException $e) {
            $this->addError('admin.content.agent_commerce.acp.push_failed', 'admin');
            log_error('ACP Feed push (full replacement) failed.', ['message' => $e->getMessage()]);
        }

        return $this->redirectToRoute('admin_content_agent_commerce');
    }

    /**
     * UCP「キャッシュクリア」.
     */
    #[Route(path: '/%eccube_admin_route%/content/agent_commerce/ucp_catalog/cache_clear', name: 'admin_content_agent_commerce_ucp_catalog_cache_clear', methods: ['POST'])]
    public function ucpCatalogCacheClear(): RedirectResponse
    {
        if (!$this->isTokenValid()) {
            throw new BadRequestHttpException();
        }

        $this->ucpCatalogCache->clear();
        $this->addSuccess('admin.content.agent_commerce.ucp.cache_clear_success', 'admin');

        return $this->redirectToRoute('admin_content_agent_commerce');
    }

    /**
     * UCP「生成」: 既定の search (空ボディ) レスポンスを事前生成する.
     */
    #[Route(path: '/%eccube_admin_route%/content/agent_commerce/ucp_catalog/cache_warmup', name: 'admin_content_agent_commerce_ucp_catalog_cache_warmup', methods: ['POST'])]
    public function ucpCatalogCacheWarmup(): RedirectResponse
    {
        if (!$this->isTokenValid()) {
            throw new BadRequestHttpException();
        }

        try {
            $body = $this->buildDefaultSearchBody(self::WARMUP_LIMIT);
            foreach (['', '{}'] as $rawBody) {
                $key = $this->ucpCatalogCache->buildKey('search', $rawBody);
                $this->ucpCatalogCache->warmup($key, fn (): string => $body);
            }
            $this->addSuccess('admin.content.agent_commerce.ucp.cache_warmup_success', 'admin');
        } catch (\JsonException $e) {
            // JSON_THROW_ON_ERROR による失敗時は 500 にせず管理画面のエラーメッセージへ変換する。
            $this->addError('admin.common.system_error', 'admin');
            log_error('UCP catalog cache warmup failed.', ['message' => $e->getMessage()]);
        }

        return $this->redirectToRoute('admin_content_agent_commerce');
    }

    /**
     * フィルタ無し先頭ページの search レスポンス本文 (JSON) を生成する.
     */
    private function buildDefaultSearchBody(int $limit): string
    {
        $items = [];
        foreach ($this->catalogProvider->provideDisplayProducts() as $product) {
            $item = $this->catalogMapper->mapProduct($product);
            if ($item instanceof AgentCatalogItemDto) {
                $items[] = $item;
            }
            if (\count($items) >= $limit) {
                break;
            }
        }

        $response = $this->responseBuilder->buildSearchResponse($items, ['has_next_page' => false]);

        return json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
