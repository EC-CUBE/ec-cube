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

/*
 * コード近接ドキュメント（README.html / README.md）の鮮度チェック（Issue #6906 §8.2）.
 *
 * アプリを起動せず単体で走る軽量チェック。CI（.github/workflows/docs-check.yml）から実行する。
 *   - README.html の各 <section> に data-section / data-customer 属性があること
 *   - README.html / README.md 内の相対リンク（http/# 以外）が実在すること
 *   - ルート README.html（仕様書ポータル）が全 README.html を列挙していること
 *
 * 使い方: php .github/bin/check-readme-docs.php [対象ディレクトリ(既定: リポジトリルート)]
 * 終了コード: 問題なし=0 / 問題あり=1
 */

$root = $argv[1] ?? \dirname(__DIR__, 2);
$root = rtrim($root, '/');

$excludes = ['/vendor/', '/node_modules/', '/var/', '/.git/'];

/** @return list<string> */
$findReadmes = static function (string $dir) use ($excludes): array {
    $found = [];
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $file) {
        /* @var SplFileInfo $file */
        $name = $file->getFilename();
        if ('README.html' !== $name && 'README.md' !== $name) {
            continue;
        }
        $path = $file->getPathname();
        foreach ($excludes as $ex) {
            if (str_contains((string) $path, $ex)) {
                continue 2;
            }
        }
        $found[] = $path;
    }
    sort($found);

    return $found;
};

$problems = [];

$readmes = $findReadmes($root);

foreach ($readmes as $path) {
    $html = (string) file_get_contents($path);
    $dir = \dirname((string) $path);

    // README.html: 各 section に data-section / data-customer があること
    if (str_ends_with((string) $path, 'README.html')) {
        if (preg_match_all('/<section\b([^>]*)>/', $html, $m)) {
            foreach ($m[1] as $attrs) {
                if (!str_contains($attrs, 'data-section')) {
                    $problems[] = "$path: <section> に data-section がありません";
                }
                if (!str_contains($attrs, 'data-customer')) {
                    $problems[] = "$path: <section> に data-customer がありません";
                }
            }
        } else {
            $problems[] = "$path: <section> がありません";
        }
    }

    // 相対リンクの解決チェック
    if (str_ends_with((string) $path, 'README.html')) {
        preg_match_all('/href="([^"]+)"/', $html, $lm);
        $links = $lm[1];
    } else {
        // Markdown リンク [text](target)
        preg_match_all('/\]\(([^)]+)\)/', $html, $lm);
        $links = $lm[1];
    }
    foreach ($links as $href) {
        if (str_starts_with($href, 'http') || str_starts_with($href, '#') || str_starts_with($href, 'mailto:')) {
            continue;
        }
        $target = $dir.'/'.$href;
        if (!file_exists($target)) {
            $problems[] = "$path: リンク切れ ($href)";
        }
    }
}

// ルート README.html（仕様書ポータル）が全 README.html を列挙しているか（TOP ページの鮮度）
$portalPath = $root.'/README.html';
if (is_file($portalPath)) {
    $portalHtml = (string) file_get_contents($portalPath);
    foreach ($readmes as $path) {
        if (!str_ends_with($path, 'README.html') || $path === $portalPath) {
            continue;
        }
        $relative = ltrim(substr($path, \strlen($root)), '/');
        if (!str_contains($portalHtml, 'href="'.$relative.'"')) {
            $problems[] = "$portalPath: ポータル（TOP ページ）に $relative へのリンクがありません（新しい README.html は目次へ追加してください）";
        }
    }
}

if ([] !== $problems) {
    fwrite(STDERR, "コード近接ドキュメントの問題が見つかりました:\n");
    foreach ($problems as $p) {
        fwrite(STDERR, " - $p\n");
    }
    exit(1);
}

echo "コード近接ドキュメント（README.html / README.md）のチェックに合格しました。\n";
exit(0);
