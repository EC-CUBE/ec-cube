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

namespace Eccube\Tests\Service\Mcp\Contract;

use Eccube\Tests\EccubeTestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * MCP 監査ログ (mcp チャネル) が専用ファイルに分離され、 通常の site.log に漏れないことを保証する契約テスト
 * (設計 §4.2 / §7.1)。
 *
 * monolog は「チャネルを宣言しただけ」 では専用ファイルに振り分けられず、 ハンドラ未設定だと default
 * ハンドラ (site.log) にフォールスルーする。 この沈黙したドリフトを CI で検出する。
 */
final class McpAuditLogIsolationContractTest extends EccubeTestCase
{
    public function testMcpChannelWritesToDedicatedLogNotSiteLog(): void
    {
        $probe = 'mcp-isolation-probe-'.uniqid();

        $logger = static::getContainer()->get('monolog.logger.mcp');
        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $logger->info($probe);

        $logDir = $this->currentLogDir();

        // 専用ファイル mcp.log (rotating → mcp-YYYY-MM-DD.log) に書かれる
        $mcpLogs = glob($logDir.'/mcp*.log') ?: [];
        $this->assertNotEmpty($mcpLogs, 'mcp 専用ログファイルが生成される');
        $this->assertTrue(
            $this->anyContains($mcpLogs, $probe),
            'mcp チャネルのログは mcp.log に書かれる',
        );

        // site.log に漏れない (site ログハンドラが存在する環境でのみ意味を持つ)
        foreach (glob($logDir.'/site*.log') ?: [] as $siteLog) {
            $this->assertStringNotContainsString(
                $probe,
                (string) file_get_contents($siteLog),
                'mcp チャネルのログが site.log に漏れている',
            );
        }
    }

    public function testMainHandlerExcludesMcpChannelPerEnv(): void
    {
        $root = (string) static::getContainer()->getParameter('kernel.project_dir');

        // site.log の main ハンドラを持つ環境はすべて mcp チャネルを除外する必要がある。
        // e2e は Playwright が MCP を実行する環境なので必須。
        foreach (['prod', 'dev', 'e2e'] as $env) {
            $config = Yaml::parseFile($root.'/app/config/eccube/packages/'.$env.'/monolog.yml');
            $channels = $config['monolog']['handlers']['main']['channels'] ?? [];

            $this->assertContains(
                '!mcp',
                $channels,
                sprintf('%s の main(site.log) ハンドラは mcp チャネルを除外する必要がある', $env),
            );
        }
    }

    /**
     * @param list<string> $files
     */
    private function anyContains(array $files, string $needle): bool
    {
        foreach ($files as $file) {
            if (str_contains((string) file_get_contents($file), $needle)) {
                return true;
            }
        }

        return false;
    }

    private function currentLogDir(): string
    {
        $container = static::getContainer();

        return $container->getParameter('kernel.logs_dir').'/'.$container->getParameter('kernel.environment');
    }
}
