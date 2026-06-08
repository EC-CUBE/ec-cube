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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Eccube\Service\Mcp\EntityArraySerializer;
use Eccube\Service\Mcp\McpScope;
use Eccube\Service\Mcp\ToolInvoker;
use Mcp\Capability\Attribute\McpTool;

/**
 * プラグイン詳細取得ツール (`get_plugin`)。
 *
 * 必要 scope: `mcp:plugin:read`。 Plugin Entity (allow_list 経由) に加えて、
 * `app/Plugin/<code>/composer.json` を読んで `description` と `require` (依存関係) を含める。
 * **個別プラグインの設定値 (機微データ含み得る) には踏み込まない**。
 */
final readonly class GetPluginTool
{
    public function __construct(
        private PluginRepository $pluginRepository,
        private EntityArraySerializer $serializer,
        private EccubeConfig $eccubeConfig,
        private ToolInvoker $invoker,
    ) {
    }

    /**
     * プラグイン ID または code から詳細を取得する。
     *
     * @param int|null    $id   プラグイン ID (id, code いずれか必須)
     * @param string|null $code プラグインコード (id, code いずれか必須)
     *
     * @return array<string, mixed> 見つからなければ空配列
     */
    #[McpTool(
        name: 'get_plugin',
        description: 'EC-CUBE のプラグイン ID または code から詳細 (Plugin entity + composer.json の description / require 依存関係) を取得する。 読み取り専用。 必要 scope: mcp:plugin:read。 プラグイン設定値は含まれない。',
    )]
    public function get(?int $id = null, ?string $code = null): array
    {
        return $this->invoker->invoke(
            toolName: 'get_plugin',
            requiredScope: McpScope::ROLE_PLUGIN_READ,
            args: compact('id', 'code'),
            work: function () use ($id, $code): array {
                $plugin = $this->resolvePlugin($id, $code);
                if (null === $plugin) {
                    return [
                        'data' => ['found' => false],
                        'summary' => ['found' => false],
                    ];
                }

                $entityData = $this->serializer->toArray($plugin);
                $composer = $this->readComposerJson($plugin->getCode());

                return [
                    'data' => [...$entityData, 'composer' => $composer],
                    'summary' => ['found' => true, 'plugin_code' => $plugin->getCode()],
                ];
            },
        );
    }

    private function resolvePlugin(?int $id, ?string $code): ?Plugin
    {
        if (null !== $id) {
            return $this->pluginRepository->find($id);
        }

        if (null !== $code && '' !== trim($code)) {
            return $this->pluginRepository->findByCode($code);
        }

        return null;
    }

    /**
     * `app/Plugin/<code>/composer.json` を読み、 description と require を抽出する。
     * 設定値 (API キー等) は含まれない (composer.json は依存定義のみ)。
     *
     * @return array{description:string|null,require:array<string, string>}|null
     */
    private function readComposerJson(string $code): ?array
    {
        $projectDir = $this->eccubeConfig->get('kernel.project_dir');
        if (!\is_string($projectDir)) {
            return null;
        }

        $path = $projectDir.'/app/Plugin/'.$code.'/composer.json';
        if (!is_file($path) || !is_readable($path)) {
            return null;
        }

        $raw = file_get_contents($path);
        if (false === $raw) {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (!\is_array($decoded)) {
            return null;
        }

        $require = $decoded['require'] ?? [];

        return [
            'description' => \is_string($decoded['description'] ?? null) ? $decoded['description'] : null,
            'require' => \is_array($require) ? array_filter($require, is_string(...)) : [],
        ];
    }
}
