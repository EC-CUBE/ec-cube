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
 * AI コーディングエージェント用の Skill スタブを同期するスクリプト。
 *
 * 正本である .claude/skills/ の内容を、各ツールが読み込むディレクトリへ複製する。
 * symlink は Windows 環境で壊れやすいため、実体コピーで同期する。
 *
 *   実行: php tools/sync-ai-skills.php
 *   確認: php tools/sync-ai-skills.php --check   （差分があれば終了コード 1）
 */

$root = dirname(__DIR__);
$source = $root.'/.claude/skills';
$targets = [
    $root.'/.codex/skills',   // Codex CLI
    $root.'/.agents/skills',  // Google Antigravity
];
// Cursor は .claude/skills/ と .codex/skills/ を互換読み込みするため、専用ターゲットは不要。

$check = in_array('--check', $argv, true);

if (!is_dir($source)) {
    fwrite(STDERR, "source not found: {$source}\n");
    exit(1);
}

/** @return array<string, string> 相対パス => 内容 */
$collect = static function (string $base): array {
    if (!is_dir($base)) {
        return [];
    }
    $files = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file->isFile()) {
            $rel = substr($file->getPathname(), strlen($base) + 1);
            $files[$rel] = (string) file_get_contents($file->getPathname());
        }
    }

    return $files;
};

$srcFiles = $collect($source);
$dirty = false;

foreach ($targets as $target) {
    $dstFiles = $collect($target);

    // 差分検出（追加・更新・削除）
    if ($srcFiles !== $dstFiles) {
        $dirty = true;
        if ($check) {
            fwrite(STDERR, "out of sync: {$target}\n");

            continue;
        }
        // 既存を消してから作り直す（削除も反映）
        foreach (array_keys($dstFiles) as $rel) {
            @unlink($target.'/'.$rel);
        }
        foreach ($srcFiles as $rel => $content) {
            $path = $target.'/'.$rel;
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0o775, true);
            }
            file_put_contents($path, $content);
        }
        echo "synced: {$target}\n";
    }
}

if ($check && $dirty) {
    fwrite(STDERR, "AI skill stubs are out of sync. Run: php tools/sync-ai-skills.php\n");
    exit(1);
}

if (!$dirty) {
    echo "already in sync\n";
}
