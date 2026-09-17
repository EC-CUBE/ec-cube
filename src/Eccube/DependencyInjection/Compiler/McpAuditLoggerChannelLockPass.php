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

namespace Eccube\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * `mcp` チャンネル (EC-CUBE 初の監査ログ) への書き込みを `McpAuditLogger` 1 クラスに縛るための DI 配線。
 *
 * monolog は channel ごとに「引数名 autowire alias」 を自動生成する (`LoggerInterface $mcpLogger` →
 * `monolog.logger.mcp`)。 本 pass はこの **`$mcpLogger` の autowire alias だけを削除**し、 mcp 監査
 * チャンネルへの到達経路を `@monolog.logger.mcp` の名指し参照 (= McpAuditLogger) のみに限定する。
 * 他チャンネルの alias と `monolog.logger.mcp` サービス本体には触れない。
 *
 * alias 削除後、 `LoggerInterface $mcpLogger` を inject する他クラスは型フォールバックで default
 * チャンネル (`monolog.logger`) に解決され、 mcp 監査チャンネルには到達しない。
 *
 * monolog の `LoggerChannelPass` が alias を作った後、 かつ `AutowirePass` (optimization フェーズ) が
 * alias を消費する前に削除する必要があるため、 Kernel 側で before-optimization の負優先度で登録する。
 *
 * alias id は monolog の `registerAliasForArgument(..., 'mcp.logger')` が生成する固定値をハードコード
 * している。 monolog のバージョン/命名規約変更で id がズレると削除が空振りし、 監査チャンネルが誰でも
 * 書ける状態に黙って戻る。 これを防ぐため、 mcp チャンネルが存在するのに想定 id の alias が見つからない
 * 場合は例外で build を止める。
 */
final class McpAuditLoggerChannelLockPass implements CompilerPassInterface
{
    /** mcp チャンネルのロガー本体サービス ID。 これがあれば mcp チャンネルが構成されている。 */
    private const MCP_LOGGER_SERVICE_ID = 'monolog.logger.mcp';

    /** monolog が `$mcpLogger` 引数向けに張る主 autowire alias ID (これが保護対象)。 */
    private const MCP_LOGGER_AUTOWIRE_ALIAS_ID = 'Psr\Log\LoggerInterface $mcpLogger';

    /** 同じく内部用のドット名 alias ID (バージョンにより存在しないことがある)。 */
    private const MCP_LOGGER_INTERNAL_ALIAS_ID = '.Psr\Log\LoggerInterface $mcp.logger';

    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        // mcp チャンネル自体が無い (機能無効化等) なら保護対象も無いのでスキップ
        if (!$container->hasDefinition(self::MCP_LOGGER_SERVICE_ID) && !$container->has(self::MCP_LOGGER_SERVICE_ID)) {
            return;
        }

        // チャンネルがあるのに想定 id の alias が無い = 命名規約変更等で本 pass が空振りした証拠。
        // 黙って通すと監査チャンネルが誰でも書ける状態に戻るため、 build を止めて気付かせる (fail loud)。
        if (!$container->hasAlias(self::MCP_LOGGER_AUTOWIRE_ALIAS_ID)) {
            throw new \LogicException(sprintf('MCP 監査ログ保護に失敗しました: autowire alias "%s" が見つかりません。 monolog のバージョン/命名規約変更の可能性があるため、 %s の alias id を見直してください。', self::MCP_LOGGER_AUTOWIRE_ALIAS_ID, self::class));
        }

        $container->removeAlias(self::MCP_LOGGER_AUTOWIRE_ALIAS_ID);

        if ($container->hasAlias(self::MCP_LOGGER_INTERNAL_ALIAS_ID)) {
            $container->removeAlias(self::MCP_LOGGER_INTERNAL_ALIAS_ID);
        }
    }
}
