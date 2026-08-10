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

namespace Eccube\Tests\Service\Mcp;

use Eccube\Service\Mcp\AuditResult;
use PHPUnit\Framework\TestCase;

/**
 * `AuditResult` の全 case が実コードから参照されることを保証する。
 *
 * 監査の判別子は「使われない孤児 case」 が紛れると、 設計上カバーしているつもりの事象が実際には記録されない。
 * 過去に `ValidationError` が参照ゼロのまま残っていた。 case 追加時の参照漏れを CI で検出する。
 */
final class AuditResultUsageTest extends TestCase
{
    public function testEveryCaseIsReferencedFromSource(): void
    {
        $sources = $this->mcpSourceContents();

        foreach (AuditResult::cases() as $case) {
            $needle = 'AuditResult::'.$case->name;
            $this->assertStringContainsString(
                $needle,
                $sources,
                sprintf('AuditResult::%s が src から参照されていない (孤児 case)', $case->name),
            );
        }
    }

    /**
     * src/Eccube/{Service,EventListener}/Mcp 配下の PHP を 1 つの文字列に連結して返す
     * (AuditResult.php 自身は case 定義なので除外)。
     */
    private function mcpSourceContents(): string
    {
        // .../src/Eccube/Service/Mcp/AuditResult.php から 5 つ上が project root
        $base = \dirname((string) (new \ReflectionClass(AuditResult::class))->getFileName(), 5);
        $dirs = [
            $base.'/src/Eccube/Service/Mcp',
            $base.'/src/Eccube/EventListener/Mcp',
        ];

        $contents = '';
        foreach ($dirs as $dir) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if (!$file instanceof \SplFileInfo || 'php' !== $file->getExtension()) {
                    continue;
                }
                if ('AuditResult.php' === $file->getFilename()) {
                    continue;
                }
                $fileContents = file_get_contents($file->getPathname());
                if (false === $fileContents) {
                    throw new \RuntimeException(sprintf('ファイル読み取り失敗: %s', $file->getPathname()));
                }
                $contents .= $fileContents;
            }
        }

        return $contents;
    }
}
