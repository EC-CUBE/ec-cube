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

namespace Eccube\Tests\Architecture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * 状態を変更するアクションが CSRF から保護されていることを検査する.
 *
 * GET 以外（POST / DELETE / PUT / PATCH）だけを受けるアクションは, 次のいずれかが必要:
 *   - $this->isTokenValid()          … 基底クラスの CSRF 検証（失敗時に AccessDeniedHttpException）
 *   - $form->handleRequest()         … フォーム経由（CSRF 保護込み）
 *   - $request->isXmlHttpRequest()   … XHR 限定（ブラウザからの単純な form POST を弾く）
 *   - 同クラス内の別アクションへ委譲  … 委譲先で検証される
 *
 * 検証漏れは画面上は正常に動くため気づく契機が無く, レビューでも見落としやすい.
 */
#[Group('architecture')]
class CsrfProtectionTest extends TestCase
{
    /**
     * ブラウザセッションを使わない外部 API. CSRF の対象外.
     *
     * @return list<string>
     */
    private static function exemptDirectories(): array
    {
        return ['/AgentCommerce/', '/Mcp/'];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function controllerProvider(): array
    {
        $dir = dirname(__DIR__, 4).'/src/Eccube/Controller';
        self::assertDirectoryExists($dir);

        $cases = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        foreach ($it as $file) {
            if (!$file->isFile() || 'php' !== $file->getExtension()) {
                continue;
            }
            $path = $file->getPathname();
            foreach (self::exemptDirectories() as $exempt) {
                if (str_contains($path, $exempt)) {
                    continue 2;
                }
            }
            $cases[str_replace($dir.'/', '', $path)] = [$path];
        }
        self::assertNotEmpty($cases, 'コントローラが 1 件も見つかりません');

        return $cases;
    }

    #[DataProvider('controllerProvider')]
    public function testStateChangingActionsAreCsrfProtected(string $path): void
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        self::assertNotFalse($lines);

        $violations = [];
        foreach ($lines as $i => $line) {
            if (!str_contains($line, '#[Route(')) {
                continue;
            }
            // GET 以外だけを受けるルートに絞る
            if (!preg_match("/methods:\s*\[[^\]]*'(?:POST|DELETE|PUT|PATCH)'/", $line)) {
                continue;
            }
            if (preg_match("/methods:\s*\[[^\]]*'GET'/", $line)) {
                continue;
            }

            $method = $this->findMethodName($lines, $i);
            if (null === $method) {
                continue;
            }
            if (!$this->isProtected($this->extractBody($lines, $method['line']))) {
                $violations[] = $method['name'];
            }
        }

        self::assertSame([], $violations, sprintf(
            "%s の以下のアクションに CSRF 保護が見当たりません: %s\n".
            "GET 以外の状態変更には \$this->isTokenValid() を呼ぶか、フォーム経由（handleRequest）にしてください。\n".
            'Ajax 専用なら $request->isXmlHttpRequest() で XHR に限定してください。',
            basename($path), implode(', ', $violations)
        ));
    }

    /**
     * @param list<string> $lines
     *
     * @return array{name: string, line: int}|null
     */
    private function findMethodName(array $lines, int $routeLine): ?array
    {
        for ($j = $routeLine + 1, $max = min($routeLine + 8, count($lines)); $j < $max; ++$j) {
            if (preg_match('/public function (\w+)/', $lines[$j], $m)) {
                return ['name' => $m[1], 'line' => $j];
            }
        }

        return null;
    }

    /**
     * @param list<string> $lines
     */
    private function extractBody(array $lines, int $methodLine): string
    {
        $body = [];
        for ($k = $methodLine + 1, $n = count($lines); $k < $n; ++$k) {
            if (preg_match('/^    (public|private|protected) function /', $lines[$k])) {
                break;
            }
            $body[] = $lines[$k];
        }

        return implode("\n", $body);
    }

    private function isProtected(string $body): bool
    {
        // CSRF 検証 / フォーム経由 / XHR 限定 / 同クラス内の別アクションへ委譲
        return (bool) preg_match(
            '/isTokenValid|handleRequest|isCsrfTokenValid|isXmlHttpRequest|return \$this->\w+\(\$request/',
            $body
        );
    }
}
