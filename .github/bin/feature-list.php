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
 * 利用者から見た機能の一覧（Issue #7225）の検証と features.html の生成.
 *
 * 正本は docs/features/features.yaml。features.html はこのスクリプトの生成物で、直接編集しない。
 *   - 機能 ID の形式・重複・グループの定義、必須列、未知の列、バージョン表記、関連ディレクトリの実在を検証する
 *   - 検証に通ったら docs/features/features.html を生成する
 *
 * 使い方: php .github/bin/feature-list.php [--check]
 *   --check  生成せず、検証と「features.html が features.yaml から生成した内容と一致するか」だけを見る（CI 用）
 * 終了コード: 問題なし=0 / 問題あり=1
 */

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

$root = \dirname(__DIR__, 2);
$check = \in_array('--check', array_slice($argv, 1), true);

$sourcePath = $root.'/docs/features/features.yaml';
$outputPath = $root.'/docs/features/features.html';
// features.html から見たリポジトリルート
$toRoot = '../../';

if (!is_file($root.'/vendor/autoload.php')) {
    fwrite(STDERR, "vendor/autoload.php がありません。composer install を実行してください（symfony/yaml を使います）。\n");
    exit(1);
}
require $root.'/vendor/autoload.php';

$prefixes = [
    'FR' => ['section' => 'front', 'title' => 'フロント（購入者）'],
    'AD' => ['section' => 'admin', 'title' => '管理画面（店舗運営者）'],
    'DV' => ['section' => 'developer', 'title' => '開発者・運用者（CLI・API・拡張）'],
];
$allowedKeys = ['id', 'name', 'summary', 'dirs', 'since', 'prs', 'removed'];
$versionPattern = '/\A\d+\.\d+\.\d+\z/';

$problems = [];

try {
    $data = Yaml::parseFile($sourcePath);
} catch (ParseException $e) {
    fwrite(STDERR, "$sourcePath: YAML として読めません: {$e->getMessage()}\n");
    exit(1);
}

$features = \is_array($data) ? ($data['features'] ?? null) : null;
if (!\is_array($features) || !array_is_list($features)) {
    fwrite(STDERR, "$sourcePath: features に機能の配列がありません\n");
    exit(1);
}

// グループは機能 ID の中央の 2 桁で決まる（FR-10 = FR-10-01〜FR-10-99）
$groups = $data['groups'] ?? null;
if (!\is_array($groups) || [] === $groups) {
    fwrite(STDERR, "$sourcePath: groups にグループの定義がありません\n");
    exit(1);
}
foreach ($groups as $key => $title) {
    if (!preg_match('/\A(FR|AD|DV)-\d{2}\z/', (string) $key) || str_ends_with((string) $key, '-00')) {
        $problems[] = "groups: キー {$key} は FR-10 / AD-10 / DV-10 の形式（01〜99）で書いてください";
    }
    if (!\is_string($title) || '' === trim($title)) {
        $problems[] = "groups: {$key} にグループ名を書いてください";
    }
}

$seen = [];
foreach ($features as $i => $f) {
    $at = 'features['.$i.']';
    if (!\is_array($f)) {
        $problems[] = "$at: 機能はマッピングで書いてください";
        continue;
    }
    $id = $f['id'] ?? null;
    if (\is_string($id)) {
        $at = $id;
    }

    foreach (array_diff(array_keys($f), $allowedKeys) as $key) {
        $problems[] = "$at: 未知の列 {$key}（使える列: ".implode(', ', $allowedKeys).'）';
    }

    if (!\is_string($id) || !preg_match('/\A(FR|AD|DV)-\d{2}-\d{2}\z/', $id) || str_ends_with($id, '-00')) {
        $problems[] = "$at: id は FR-10-01 の形式（接頭辞-グループ番号 2 桁-連番 01〜99）で書いてください";
    } elseif (!isset($groups[substr($id, 0, 5)])) {
        $problems[] = "$at: グループ ".substr($id, 0, 5).' が groups にありません';
    } elseif (isset($seen[$id])) {
        $problems[] = "$at: id が重複しています（廃止した機能の id も再利用しません）";
    } else {
        $seen[$id] = true;
    }

    foreach (['name', 'summary'] as $key) {
        if (!\is_string($f[$key] ?? null) || '' === trim($f[$key])) {
            $problems[] = "$at: $key は必須です";
        }
    }

    foreach (['since', 'removed'] as $key) {
        if (isset($f[$key]) && (!\is_string($f[$key]) || !preg_match($versionPattern, $f[$key]))) {
            $problems[] = "$at: $key は 4.4.0 の形式で書いてください";
        }
    }

    $dirs = $f['dirs'] ?? [];
    if (!\is_array($dirs) || !array_is_list($dirs)) {
        $problems[] = "$at: dirs はパスの配列で書いてください";
    } else {
        foreach ($dirs as $dir) {
            if (!\is_string($dir) || '' === $dir || str_starts_with($dir, '/') || str_contains($dir, '..')) {
                $problems[] = "$at: dirs にはリポジトリルートからの相対パスを書いてください";
            } elseif (!file_exists($root.'/'.$dir)) {
                $problems[] = "$at: dirs のパスがありません ($dir)";
            }
        }
    }

    $prs = $f['prs'] ?? [];
    if (!\is_array($prs) || ([] !== $prs && array_is_list($prs))) {
        $problems[] = "$at: prs は「バージョン: [PR 番号, ...]」のマッピングで書いてください";
    } else {
        foreach ($prs as $version => $numbers) {
            if (!preg_match($versionPattern, (string) $version)) {
                $problems[] = "$at: prs のキー $version は 4.4.0 の形式で書いてください";
            }
            if (!\is_array($numbers) || [] === $numbers || !array_is_list($numbers)) {
                $problems[] = "$at: prs の $version に PR 番号の配列を書いてください";
                continue;
            }
            foreach ($numbers as $n) {
                if (!\is_int($n) || $n <= 0) {
                    $problems[] = "$at: prs の $version に PR 番号以外の値があります";
                }
            }
            if (\count($numbers) !== \count(array_unique($numbers))) {
                $problems[] = "$at: prs の $version に同じ PR 番号が重複しています";
            }
        }
    }
}

if ([] !== $problems) {
    fwrite(STDERR, "機能の一覧（docs/features/features.yaml）に問題が見つかりました:\n");
    foreach ($problems as $p) {
        fwrite(STDERR, " - $p\n");
    }
    exit(1);
}

$h = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');

$renderDirs = static function (array $dirs) use ($root, $toRoot, $h): string {
    $links = [];
    foreach ($dirs as $dir) {
        $href = is_file($root.'/'.$dir.'/README.html') ? $dir.'/README.html' : $dir;
        $links[] = '<a href="'.$h($toRoot.$href).'"><code>'.$h($dir).'</code></a>';
    }

    return implode('<br>', $links);
};

$renderPrs = static function (array $prs) use ($h): string {
    $lines = [];
    foreach ($prs as $version => $numbers) {
        $links = array_map(
            static fn (int $n): string => '<a href="https://github.com/EC-CUBE/ec-cube/pull/'.$n.'">#'.$n.'</a>',
            $numbers
        );
        $lines[] = $h((string) $version).': '.implode(', ', $links);
    }

    return implode('<br>', $lines);
};

// 接頭辞ごと・グループごとに、機能 ID の順でまとめる
usort($features, static fn (array $a, array $b): int => strcmp($a['id'], $b['id']));
$sections = [];
foreach ($features as $f) {
    $sections[substr($f['id'], 0, 2)][substr($f['id'], 0, 5)][] = $f;
}

$out = [];
foreach ($prefixes as $prefix => $meta) {
    if (!isset($sections[$prefix])) {
        continue;
    }
    $out[] = '<section data-section="'.$meta['section'].'" data-customer="true">';
    $out[] = ' <h2>'.$h($meta['title']).'</h2>';
    ksort($sections[$prefix]);
    foreach ($sections[$prefix] as $group => $rows) {
        $out[] = ' <h3>'.$h($groups[$group]).' <span class="badge">'.$h($group).'-xx</span></h3>';
        $out[] = ' <table>';
        $out[] = '  <tr><th class="id">機能 ID</th><th>機能</th><th>概要</th><th>関連ディレクトリ</th><th class="pr">本体 PR</th><th class="ver">導入バージョン</th></tr>';
        foreach ($rows as $f) {
            $name = $h($f['name']);
            $class = '';
            if (isset($f['removed'])) {
                $class = ' class="removed"';
                $name .= '<br><span class="badge">'.$h($f['removed']).' で廃止</span>';
            }
            $out[] = '  <tr id="'.$h($f['id']).'"'.$class.'>'
                .'<td><a href="#'.$h($f['id']).'"><code>'.$h($f['id']).'</code></a></td>'
                .'<td>'.$name.'</td>'
                .'<td>'.$h($f['summary']).'</td>'
                .'<td>'.$renderDirs($f['dirs'] ?? []).'</td>'
                .'<td>'.$renderPrs($f['prs'] ?? []).'</td>'
                .'<td>'.$h($f['since'] ?? '').'</td>'
                .'</tr>';
        }
        $out[] = ' </table>';
    }
    $out[] = '</section>';
}

$html = <<<HTML
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>EC-CUBE 機能の一覧</title>
<!-- このファイルは .github/bin/feature-list.php が docs/features/features.yaml から生成します。直接編集しないでください。-->
<style>
 body{font-family:system-ui,-apple-system,"Segoe UI",sans-serif;line-height:1.7;max-width:1200px;margin:0 auto;padding:2rem;color:#222}
 h1,h2{border-bottom:1px solid #ddd;padding-bottom:.3rem}
 h1{margin-bottom:.2rem}
 .nav{font-size:.9rem;color:#555;margin:.2rem 0 1.5rem}
 table{border-collapse:collapse;width:100%;margin:.5rem 0 1.5rem}
 th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left;vertical-align:top}
 th{background:#f4f4f4}
 th.id{width:6rem}
 th.pr{width:9rem}
 th.ver{width:6rem}
 tr:target{background:#fff8e1}
 tr.removed td{color:#888}
 code{background:#f4f4f4;padding:.1rem .3rem;border-radius:3px;font-size:.92em}
 .note{background:#fff8e1;border-left:4px solid #f0c040;padding:.6rem 1rem;margin:1rem 0}
 .badge{display:inline-block;background:#eef;border:1px solid #ccd;border-radius:3px;padding:0 .4rem;font-size:.8rem}
</style>
</head>
<body>
<h1>EC-CUBE 機能の一覧</h1>
<p class="nav">
 仕様書ポータル: <a href="../../README.html">README.html</a>
 ／ 正本: <a href="./features.yaml">features.yaml</a>
 ／ 索引: <a href="./README.md">README.md</a>
</p>
<section data-section="overview" data-customer="true">
 <h2>このページについて</h2>
 <p>利用者（購入者・店舗運営者・開発者）から見た EC-CUBE の機能を、<strong>機能 ID</strong> で一覧したものです。
 結合試験項目書（EC-CUBE/eccube-specification）の観点表は、この機能 ID を参照して試験観点と手順を持ちます。
 網羅性は、この一覧と観点表を機能 ID で突き合わせ、片方にしかない行を探して確認します。</p>
 <ul>
  <li><strong>機能 ID</strong> — <code>FR</code>＝フロント（購入者）、<code>AD</code>＝管理画面（店舗運営者）、<code>DV</code>＝開発者・運用者（CLI・API・拡張）。<code>AD-20-01</code> の中央の 2 桁がグループです（例: <code>AD-20</code>＝商品管理）。採番したら変えず、廃止した機能も欠番として残します。</li>
  <li><strong>本体 PR</strong> — その機能の仕様を変えることが主な目的の PR を、リリースのバージョンごとに並べます。不具合修正だけの PR と、別の機能を追加した PR の波及（CSV の列が増える等）は載せません。</li>
  <li><strong>導入バージョン</strong> — 4.4 より前からある機能は空欄です。</li>
 </ul>
 <div class="note">このページは <code>docs/features/features.yaml</code> から生成しています。機能を追加・変更する PR では <code>features.yaml</code> を更新し、
 <code>php .github/bin/feature-list.php</code> でこのページを作り直してください。</div>
</section>

HTML;
$html .= implode("\n", $out)."\n</body>\n</html>\n";

if ($check) {
    $current = is_file($outputPath) ? (string) file_get_contents($outputPath) : '';
    if ($current !== $html) {
        fwrite(STDERR, "$outputPath が features.yaml から生成した内容と一致しません。php .github/bin/feature-list.php で作り直してコミットしてください。\n");
        exit(1);
    }
    echo "機能の一覧（docs/features）のチェックに合格しました。\n";
    exit(0);
}

file_put_contents($outputPath, $html);
echo "$outputPath を生成しました（".\count($features)." 件）。\n";
exit(0);
