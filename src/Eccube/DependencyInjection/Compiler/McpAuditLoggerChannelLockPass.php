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
 * monolog は channel ごとに「引数名 autowire alias」 を自動生成する (例: `LoggerInterface $mcpLogger` →
 * `monolog.logger.mcp`)。 このままだと **どのクラスでも `$mcpLogger` を inject すれば mcp 監査チャンネルに
 * 直接書けてしまい**、 設計 §8 #4 の「監査ログは McpAuditLogger を単一入口とする」 が破れる。
 *
 * 本 pass は **`$mcpLogger` の autowire alias だけを削除**する (他チャンネルの alias、 および
 * `monolog.logger.mcp` サービス本体には触れない)。 これにより:
 *   - McpAuditLogger は services.yaml で `@monolog.logger.mcp` を名指し注入するので動き続ける
 *   - それ以外のクラスが `LoggerInterface $mcpLogger` を inject しようとすると、 解決先が無く **起動失敗**
 *     (= 監査ログの単一入口を「静的解析を回すか否か」 に依存しない実行時保証にする)
 *
 * monolog の `LoggerChannelPass` (優先度 0 / before-optimization) が alias を作った後、 かつ Symfony の
 * `AutowirePass` (optimization フェーズ) が alias を消費する前に削除する必要がある。 Kernel 側で
 * before-optimization の負優先度で登録することで、 この区間に確実に収める。
 */
final class McpAuditLoggerChannelLockPass implements CompilerPassInterface
{
    /**
     * monolog が `registerAliasForArgument(..., 'mcp.logger')` で生成する alias の ID。
     * 主 alias (camelCase) と、 内部用のドット名 alias の両方を消す。
     *
     * @var list<string>
     */
    private const MCP_LOGGER_ALIAS_IDS = [
        'Psr\Log\LoggerInterface $mcpLogger',
        '.Psr\Log\LoggerInterface $mcp.logger',
    ];

    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        foreach (self::MCP_LOGGER_ALIAS_IDS as $aliasId) {
            if ($container->hasAlias($aliasId)) {
                $container->removeAlias($aliasId);
            }
        }
    }
}
