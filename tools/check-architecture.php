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
 * コントローラ/サービスの Fat 化・責務分離（レイヤ違反）の観点を可視化する助言用スクリプト。
 *
 * 追加の依存を必要としない（token_get_all のみ使用）。
 * CI を落とすためのものではなく、実装・レビュー時の自己チェック用。
 * docs/rules/controller.md, docs/rules/service.md と対で使う。
 *
 *   php tools/check-architecture.php --changed              # git で変更された Controller/Service のみ
 *   php tools/check-architecture.php src/Eccube/Service/...  # ファイル/ディレクトリ指定
 *   php tools/check-architecture.php                        # 既定: Controller と Service 全体
 *
 * 終了コードは常に 0（助言用）。--strict を付けると指摘ありで 1 を返す。
 */

const MAX_METHOD_LINES = 50;  // 1 メソッドの行数の目安
const MAX_CTOR_ARGS = 7;      // コンストラクタ依存数の目安

$args = array_slice($argv, 1);
$strict = in_array('--strict', $args, true);
$changed = in_array('--changed', $args, true);
$paths = array_values(array_filter($args, static fn ($a) => !str_starts_with($a, '--')));

$root = dirname(__DIR__);

// 対象ファイルの収集
$files = [];
if ($changed) {
    $cmds = [
        'git -C '.escapeshellarg($root).' diff --name-only --diff-filter=ACM HEAD',
        'git -C '.escapeshellarg($root).' diff --name-only --diff-filter=ACM --cached',
        // 未追跡（git add 前）の新規ファイルも対象に含める（新規 Controller/Service を書いた直後を拾う）
        'git -C '.escapeshellarg($root).' ls-files --others --exclude-standard',
    ];
    foreach ($cmds as $cmd) {
        $out = [];
        exec($cmd, $out);
        foreach ($out as $rel) {
            if (preg_match('#(Controller|Service)/.*\.php$#', $rel)) {
                $files[] = $root.'/'.$rel;
            }
        }
    }
} else {
    if ($paths === []) {
        $paths = [
            $root.'/src/Eccube/Controller', $root.'/app/Customize/Controller',
            $root.'/src/Eccube/Service', $root.'/app/Customize/Service',
        ];
    }
    foreach ($paths as $path) {
        if (is_file($path)) {
            $files[] = $path;
        } elseif (is_dir($path)) {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                if ($f->isFile() && str_ends_with($f->getFilename(), '.php')) {
                    $files[] = $f->getPathname();
                }
            }
        }
    }
}

$files = array_values(array_unique(array_filter($files, 'is_file')));
if ($files === []) {
    echo "対象（Controller/Service）が見つかりませんでした。\n";
    exit(0);
}

/**
 * メソッド長とコンストラクタ引数数を解析する。
 *
 * @return array{methods: array<string, int>, ctorArgs: int}
 */
function analyzeStructure(string $code): array
{
    $tokens = token_get_all($code);
    $n = count($tokens);

    // トークン index → 行番号のマップ（'{' '}' 等の単一文字トークンは行を持たないため）
    $lineAt = [];
    $line = 1;
    for ($i = 0; $i < $n; $i++) {
        $tok = $tokens[$i];
        if (is_array($tok)) {
            $lineAt[$i] = $tok[2];
            $line = $tok[2] + substr_count($tok[1], "\n");
        } else {
            $lineAt[$i] = $line;
        }
    }

    $methods = [];
    $ctorArgs = 0;
    for ($i = 0; $i < $n; $i++) {
        if (!is_array($tokens[$i]) || $tokens[$i][0] !== T_FUNCTION) {
            continue;
        }
        $name = null;
        for ($j = $i + 1; $j < $n; $j++) {
            if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                $name = $tokens[$j][1];
                break;
            }
            if ($tokens[$j] === '(') {
                break; // 無名関数
            }
        }
        if ($name === null) {
            continue;
        }

        $depth = 0;
        $startLine = $endLine = null;
        $argCount = 0;
        $inSignature = true;
        $parenDepth = 0;
        for ($j = $i + 1; $j < $n; $j++) {
            $tok = $tokens[$j];
            if ($inSignature) {
                if ($tok === '(') {
                    $parenDepth++;
                } elseif ($tok === ')') {
                    $parenDepth--;
                } elseif ($parenDepth === 1 && is_array($tok) && $tok[0] === T_VARIABLE) {
                    $argCount++;
                }
                if ($tok === '{') {
                    $inSignature = false;
                    $depth = 1;
                    $startLine = $lineAt[$j];
                    continue;
                }
                if ($tok === ';') {
                    break; // 抽象メソッド等、本体なし
                }
            } else {
                if ($tok === '{') {
                    $depth++;
                } elseif ($tok === '}') {
                    $depth--;
                    if ($depth === 0) {
                        $endLine = $lineAt[$j];
                        break;
                    }
                }
            }
        }

        if ($name === '__construct') {
            $ctorArgs = $argCount;
        }
        if ($startLine && $endLine && $name !== '__construct') {
            $methods[$name] = $endLine - $startLine + 1;
        }
    }

    return ['methods' => $methods, 'ctorArgs' => $ctorArgs];
}

$findings = 0;

foreach ($files as $file) {
    $code = file_get_contents($file);
    if ($code === false) {
        fwrite(STDERR, "WARN: ファイルを読み込めませんでした（検査スキップ）: {$file}\n");
        continue;
    }
    $rel = str_replace($root.'/', '', $file);
    $isController = (bool) preg_match('#/Controller/#', $file);
    $isService = (bool) preg_match('#/Service/#', $file);
    $structure = analyzeStructure($code);
    $fileFindings = [];

    // 共通: コンストラクタ依存数
    if ($structure['ctorArgs'] > MAX_CTOR_ARGS) {
        $fileFindings[] = sprintf('  コンストラクタ依存 %d 個（目安 %d 超）: 責務過多の可能性 → 分割を検討', $structure['ctorArgs'], MAX_CTOR_ARGS);
    }

    // 共通: メソッド長
    foreach ($structure['methods'] as $name => $lines) {
        if ($lines > MAX_METHOD_LINES) {
            $dest = $isController ? '業務ロジックの Service 抽出' : 'private メソッド分割または別クラス抽出';
            $fileFindings[] = sprintf('  %s(): 約 %d 行（目安 %d 超）→ %s を検討', $name, $lines, MAX_METHOD_LINES, $dest);
        }
    }

    // コントローラ固有: persist/flush 直書き（業務的な永続化は Service へ）
    if ($isController && preg_match_all('/->\s*(persist|flush)\s*\(/', $code, $m)) {
        $fileFindings[] = sprintf('  EntityManager の %s 直書き %d 箇所: 業務的な永続化は Service へ', implode('/', array_unique($m[1])), count($m[0]));
    }

    // サービス固有: Controller への依存（レイヤ違反）
    if ($isService && preg_match_all('/\buse\s+[A-Za-z0-9_\\\\]*Controller(?:\\\\[A-Za-z0-9_]+)?\s*;/', $code, $m)) {
        $fileFindings[] = sprintf('  Controller への依存 %d 件（レイヤ違反）: 依存は Controller → Service の一方向に保つ', count($m[0]));
    }

    if ($fileFindings !== []) {
        $findings += count($fileFindings);
        $layer = $isController ? 'Controller' : ($isService ? 'Service' : '?');
        echo "● [{$layer}] {$rel}\n".implode("\n", $fileFindings)."\n\n";
    }
}

if ($findings === 0) {
    echo "指摘なし（目安の範囲内）。\n";
    exit(0);
}

echo "—— {$findings} 件の観点が見つかりました。docs/rules/controller.md・service.md を参照し、責務の整理を検討してください。\n";
echo "（これは助言であり、必ずしも修正必須ではありません）\n";

exit($strict ? 1 : 0);
