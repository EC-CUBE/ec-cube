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

namespace Eccube\Service\Mcp\Tool;

use Eccube\Repository\PluginRepository;
use Eccube\Service\Mcp\EntityArraySerializer;
use Eccube\Service\Mcp\ToolInvoker;
use Mcp\Capability\Attribute\McpTool;

/**
 * インストール済みプラグイン一覧取得ツール (`list_plugins`)。
 *
 * 必要 scope: `mcp:plugin:read`。 出力は Api44 allow_list (`Eccube\Entity\Plugin`) の項目のみ。
 * 設計上、 個別プラグインの設定値 (API キー等の機微データ) には踏み込まない。
 */
final readonly class ListPluginsTool
{
    public function __construct(
        private PluginRepository $pluginRepository,
        private EntityArraySerializer $serializer,
        private ToolInvoker $invoker,
    ) {
    }

    /**
     * インストール済みプラグインの一覧を取得する。
     *
     * @param bool|null $enabledOnly true で enabled のみ、 false で disabled のみ、 null (既定) で全件
     *
     * @return array{total:int,items:list<array<string, mixed>>}
     */
    #[McpTool(
        name: 'list_plugins',
        description: 'EC-CUBE にインストール済みのプラグイン一覧 (code / name / version / enabled / initialized 等) を取得する。 読み取り専用。 必要 scope: mcp:plugin:read。 個別プラグインの設定値は含まれない。',
    )]
    public function list(?bool $enabledOnly = null): array
    {
        /** @var array{total:int,items:list<array<string, mixed>>} $result */
        $result = $this->invoker->invoke(
            toolName: 'list_plugins',
            args: ['enabledOnly' => $enabledOnly],
            work: function () use ($enabledOnly): array {
                $plugins = match ($enabledOnly) {
                    true => $this->pluginRepository->findAllEnabled(),
                    false => $this->pluginRepository->findBy(['enabled' => false]),
                    null => $this->pluginRepository->findBy([], ['code' => 'ASC']),
                };

                $items = $this->serializer->toArrayList($plugins);

                return [
                    'data' => [
                        'total' => \count($items),
                        'items' => $items,
                    ],
                    'summary' => ['total' => \count($items)],
                ];
            },
        );

        return $result;
    }
}
