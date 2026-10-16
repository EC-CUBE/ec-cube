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
final class CsrfProtectionTest extends TestCase
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

    #[DataProvider(methodName: 'controllerProvider')]
    public function testStateChangingActionsAreCsrfProtected(string $path): void
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $this->assertNotFalse($lines);

        $violations = $this->findViolations($lines);

        $this->assertSame([], $violations, sprintf(
            "%s の以下のアクションに CSRF 保護が見当たりません: %s\n".
            "GET 以外の状態変更には \$this->isTokenValid() を呼ぶか、フォーム経由（handleRequest）にしてください。\n".
            'Ajax 専用なら $request->isXmlHttpRequest() で XHR に限定してください。',
            basename($path), implode(', ', $violations)
        ));
    }

    /**
     * 検査自体が属性やメソッドを取りこぼさないことのテスト.
     *
     * 走査を固定の行数で打ち切ると, 長い属性の methods や, 属性から離れたメソッドを読めず,
     * 保護の無いアクションが検査を素通りする.
     */
    public function testFindViolationsReadsLongAttributeAndDistantMethod(): void
    {
        $lines = [
            '    #[Route(',
            "        path: '/%eccube_admin_route%/example/{id}/delete',",
            "        name: 'admin_example_delete',",
            "        requirements: ['id' => '\\d+'],",
            '        defaults: [',
            "            'a' => 1,",
            "            'b' => 2,",
            "            'c' => 3,",
            "            'd' => 4,",
            "            'e' => 5,",
            "            'f' => 6,",
            '        ],',
            "        methods: ['DELETE']",
            '    )]',
            '    #[Template(template: \'@admin/example.twig\')]',
            '    /**',
            '     * 属性からメソッドまでが離れている.',
            '     *',
            '     * @return array<string, mixed>',
            '     */',
            '    public function delete(Request $request): array',
            '    {',
            '        return [];',
            '    }',
        ];

        $this->assertSame(['delete'], $this->findViolations($lines));
    }

    /**
     * GET 以外だけを受けるルートのうち, CSRF 保護が見当たらないアクション名を返す.
     *
     * @param list<string> $lines
     *
     * @return list<string>
     */
    private function findViolations(array $lines): array
    {
        $violations = [];
        foreach ($lines as $i => $line) {
            if (!str_contains($line, '#[Route(')) {
                continue;
            }
            // 属性は複数行に分かれることがあるため, 閉じ括弧までを 1 つの文字列として集める
            [$attribute, $attributeEnd] = $this->collectAttribute($lines, $i);

            // GET 以外だけを受けるルートに絞る（methods の値は単一引用符・二重引用符の両方を許す）
            if (!preg_match('/methods:\s*\[[^\]]*[\'"](?:POST|DELETE|PUT|PATCH)[\'"]/', $attribute)) {
                continue;
            }
            if (preg_match('/methods:\s*\[[^\]]*[\'"]GET[\'"]/', $attribute)) {
                continue;
            }

            $method = $this->findMethodName($lines, $attributeEnd);
            if (null === $method) {
                continue;
            }
            if (!$this->isProtected($this->extractBody($lines, $method['line']))) {
                $violations[] = $method['name'];
            }
        }

        return $violations;
    }

    /**
     * `#[Route(` から属性が閉じるまでを 1 つの文字列として返す. 閉じた行の位置も返す.
     *
     * 行単位で `methods:` を探すと, 複数行に分けて書かれた属性を取りこぼす.
     * 行数で打ち切ると長い属性を取りこぼすため, 閉じるまで読む.
     *
     * @param list<string> $lines
     *
     * @return array{string, int}
     */
    private function collectAttribute(array $lines, int $routeLine): array
    {
        $collected = [];
        for ($j = $routeLine, $n = count($lines); $j < $n; ++$j) {
            $collected[] = $lines[$j];
            if (str_contains($lines[$j], ')]')) {
                break;
            }
        }

        return [implode(' ', $collected), $j];
    }

    /**
     * 属性の直後から, 次のメソッド宣言を探す.
     *
     * 間に別の属性や docblock が挟まっても読み飛ばす. 行数では打ち切らない.
     *
     * @param list<string> $lines
     *
     * @return array{name: string, line: int}|null
     */
    private function findMethodName(array $lines, int $attributeEnd): ?array
    {
        for ($j = $attributeEnd + 1, $n = count($lines); $j < $n; ++$j) {
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
