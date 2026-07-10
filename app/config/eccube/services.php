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

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

/*
 * app/Plugin/* 配下のクラスを PSR-4 で自動登録する定義。
 *
 * 従来は services.yaml の `Plugin\` ブロックで静的な exclude のみを指定していたが,
 * プラグインが同梱する非 PSR-4 のライブラリ (例: phpseclib の bootstrap.php) を拾って
 * Symfony DI の FileLoader::registerClasses が "Expected to find class ... but it was not found!"
 * 例外を投げる問題があった (#6915)。
 *
 * ここではライブラリ名に依存しないよう, プラグイン配下にネストした composer.json を持つ
 * サブディレクトリ (= 同梱パッケージ) を検出し, そのサブツリーを exclude へ動的に追加する。
 * あわせて composer の慣例ディレクトリ `vendor` も静的に除外する。
 */
return function (ContainerConfigurator $configurator): void {
    // このファイルは app/config/eccube/ 配下にあるため, 3 階層上がプロジェクトルート。
    $projectDir = \dirname(__DIR__, 3);
    $pluginDir = $projectDir.'/app/Plugin';

    // 従来 services.yaml が持っていた静的 exclude (+ 同梱ライブラリ集約用に vendor を追加)。
    $excludes = [
        $pluginDir.'/*/{Entity,Resource,ServiceProvider,Tests,Codeception,DoctrineMigrations,vendor}',
    ];

    // プラグイン配下のネストした composer.json (= 同梱パッケージ) を検出して除外する。
    foreach (glob($pluginDir.'/*', GLOB_ONLYDIR) ?: [] as $plugin) {
        $scan = static function (string $dir) use (&$scan, &$excludes): void {
            foreach (scandir($dir) ?: [] as $entry) {
                // vendor は上の静的 exclude 済み。走査コスト削減のため潜らない。
                if ('.' === $entry || '..' === $entry || 'vendor' === $entry) {
                    continue;
                }
                $path = $dir.'/'.$entry;
                if (!is_dir($path)) {
                    continue;
                }
                // 同梱パッケージのルートを見つけたらサブツリーごと除外し, これ以上潜らない。
                if (is_file($path.'/composer.json')) {
                    $excludes[] = $path;

                    continue;
                }
                $scan($path);
            }
        };
        // プラグインルート直下の composer.json (プラグイン自身の定義) は対象外。サブディレクトリのみ走査する。
        $scan($plugin);
    }

    $services = $configurator->services();

    // services.yaml の _defaults と等価な既定 (autowire / autoconfigure / private)。
    // PurchaseFlow / OrderStateMachine の名前付き引数 ($cartPurchaseFlow 等) は,
    // services.yaml の名前付きオートワイヤリング別名でコンテナ全体に解決されるため, ここでは bind しない
    // (この scope のどのサービスからも使われない bind は "unused binding" エラーになるため)。
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->load('Plugin\\', $pluginDir.'/*')
        ->exclude($excludes);
};
