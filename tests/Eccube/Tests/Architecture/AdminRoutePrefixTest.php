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
 * 管理画面コントローラのルートが管理プレフィックス配下にあることを検査する.
 *
 * EC-CUBE の管理画面の認可は, 個別アクションの #[IsGranted] ではなく
 * admin ファイアウォール（pattern: ^/%eccube_admin_route%/）が担う.
 * プレフィックス配下から外れたルートは **未認証で到達できてしまう**.
 *
 * 実装時に気づく契機が無く（画面は動いてしまう）, レビューでも見落としやすいため機械で検査する.
 */
#[Group('architecture')]
class AdminRoutePrefixTest extends TestCase
{
    private const ADMIN_PREFIX = '/%eccube_admin_route%';

    /**
     * 管理プレフィックスを持たないことが仕様として正しいルート.
     *
     * 現時点で該当なし. 例外を追加する場合は必ず理由を書くこと.
     *
     * @return list<string>
     */
    private static function allowedRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function adminControllerProvider(): array
    {
        $dir = dirname(__DIR__, 4).'/src/Eccube/Controller/Admin';
        self::assertDirectoryExists($dir);

        $cases = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        foreach ($it as $file) {
            if ($file->isFile() && 'php' === $file->getExtension()) {
                $path = $file->getPathname();
                $cases[str_replace($dir.'/', '', $path)] = [$path];
            }
        }
        self::assertNotEmpty($cases, '管理コントローラが 1 件も見つかりません');

        return $cases;
    }

    #[DataProvider('adminControllerProvider')]
    public function testAdminRoutesAreUnderAdminPrefix(string $path): void
    {
        $source = file_get_contents($path);
        self::assertNotFalse($source);

        $violations = [];
        // #[Route('...')] / #[Route(path: '...')] の第1引数を拾う
        if (preg_match_all('/#\[Route\(\s*(?:path:\s*)?[\'"]([^\'"]+)[\'"]/', $source, $matches)) {
            foreach ($matches[1] as $route) {
                if (in_array($route, self::allowedRoutes(), true)) {
                    continue;
                }
                if (!str_starts_with($route, self::ADMIN_PREFIX)) {
                    $violations[] = $route;
                }
            }
        }

        self::assertSame([], $violations, sprintf(
            "%s の管理ルートが %s 配下にありません: %s\n".
            '管理画面の認可は admin ファイアウォール（pattern: ^/%%eccube_admin_route%%/）が担うため、'.
            '配下から外すと未認証で到達できます。#[Route(path: \'/%%eccube_admin_route%%/...\')] に修正してください。',
            basename($path), self::ADMIN_PREFIX, implode(', ', $violations)
        ));
    }
}
