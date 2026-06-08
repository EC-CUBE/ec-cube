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
 * コントローラの Fat 化・責務分離の観点を可視化する助言用スクリプト。
 *
 * 追加の依存を必要としない（token_get_all のみ使用）。
 * CI を落とすためのものではなく、実装・レビュー時の自己チェック用。
 * docs/rules/controller.md と対で使う。
 *
 *   php tools/check-fat-controller.php --changed                 # git で変更されたコントローラのみ
 *   php tools/check-fat-controller.php src/Eccube/Controller/...  # ファイル/ディレクトリ指定
 *   php tools/check-fat-controller.php                           # 既定: src/Eccube/Controller と app/Customize/Controller
 *
 * 終了コードは常に 0（助言用）。--strict を付けると指摘ありで 1 を返す。
 */

const MAX_METHOD_LINES = 50;  // 1 メソッドの行数の目安
const MAX_CTOR_ARGS = 7;      // コンストラクタ依存数の目安

$args = array_slice($argv, 1);
$strict = in_array('--strict', $args, true);
$args = array_values(array_filter($args, static fn ($a) => $a !== '--strict'));

$root = dirname(__DIR__);

// 対象ファイルの収集
$files = [];
if (in_array('--changed', $args, true)) {
    exec('git -C '.escapeshellarg($root).' diff --name-only --diff-filter=ACM HEAD', $out);
    exec('git -C '.escapeshellarg($root).' diff --name-only --diff-filter=ACM --cached', $cached);
    foreach (array_merge($out, $cached) as $rel) {
        if (preg_match('#Controller/.*\.php$#', $rel)) {
            $files[] = $root.'/'.$rel;
        }
    }
} else {
    $paths = array_values(array_filter($args, static fn ($a) => !str_starts_with($a, '--')));
    if ($paths === []) {
        $paths = [$root.'/src/Eccube/Controller', $root.'/app/Customize/Controller'];
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
    echo "対象コントローラが見つかりませんでした。\n";
    exit(0);
}

$findings = 0;

foreach ($files as $file) {
    $code = (string) file_get_contents($file);
    $tokens = token_get_all($code);
    $rel = str_replace($root.'/', '', $file);
    $fileFindings = [];

    // トークン index → 行番号のマップを作る（'{' '}' 等の単一文字トークンは行を持たないため）
    $n = count($tokens);
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

    // メソッドごとの行数・コンストラクタ引数を解析
    for ($i = 0; $i < $n; $i++) {
        if (!is_array($tokens[$i]) || $tokens[$i][0] !== T_FUNCTION) {
            continue;
        }
        // メソッド名を取得
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

        // 本体 { ... } の範囲を求める
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

        if ($name === '__construct' && $argCount > MAX_CTOR_ARGS) {
            $fileFindings[] = sprintf('  コンストラクタ依存 %d 個（目安 %d 超）: 責務過多の可能性 → Service へ集約を検討', $argCount, MAX_CTOR_ARGS);
        }

        if ($startLine && $endLine) {
            $lines = $endLine - $startLine + 1;
            if ($lines > MAX_METHOD_LINES && $name !== '__construct') {
                $fileFindings[] = sprintf('  %s(): 約 %d 行（目安 %d 超）→ 業務ロジックの Service 抽出を検討', $name, $lines, MAX_METHOD_LINES);
            }
        }
    }

    // persist/flush の直書き
    if (preg_match_all('/->\s*(persist|flush)\s*\(/', $code, $m)) {
        $fileFindings[] = sprintf('  EntityManager の %s 直書き %d 箇所: 業務的な永続化は Service へ', implode('/', array_unique($m[1])), count($m[0]));
    }

    if ($fileFindings !== []) {
        $findings += count($fileFindings);
        echo "● {$rel}\n".implode("\n", $fileFindings)."\n\n";
    }
}

if ($findings === 0) {
    echo "指摘なし（目安の範囲内）。\n";
    exit(0);
}

echo "—— {$findings} 件の観点が見つかりました。docs/rules/controller.md を参照し、Service への抽出を検討してください。\n";
echo "（これは助言であり、必ずしも修正必須ではありません）\n";

exit($strict ? 1 : 0);
