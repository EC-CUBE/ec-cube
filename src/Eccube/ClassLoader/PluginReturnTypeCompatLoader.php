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

namespace Eccube\ClassLoader;

/**
 * Symfony 7 で追加された戻り値型宣言にプラグインを自動対応させるオートローダー.
 *
 * Symfony 7 では AbstractTypeExtension や DataTransformerInterface のメソッドに
 * 戻り値型 (: void, : mixed) が追加された. 既存プラグインがこれらのメソッドを
 * 戻り値型なしでオーバーライドすると PHP Fatal Error が発生する.
 *
 * このローダーは Composer のオートローダーの前に登録され, Plugin 名前空間の
 * クラスファイルを読み込む際に不足している戻り値型を自動補完する.
 */
class PluginReturnTypeCompatLoader
{
    /**
     * パッチ対象のメソッドと追加すべき戻り値型のマッピング.
     *
     * キー: メソッド名
     * 値: [戻り値型, ソース中に存在すべきキーワード (extends/implements)]
     */
    private static array $patches = [
        // FormTypeExtensionInterface / AbstractTypeExtension
        'buildForm' => ['void', 'AbstractTypeExtension'],
        'buildView' => ['void', 'AbstractTypeExtension'],
        'finishView' => ['void', 'AbstractTypeExtension'],
        'configureOptions' => ['void', 'AbstractTypeExtension'],
        // DataTransformerInterface
        'transform' => ['mixed', 'DataTransformerInterface'],
        'reverseTransform' => ['mixed', 'DataTransformerInterface'],
    ];

    private string $projectRoot;

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = $projectRoot;
    }

    /**
     * Composer のオートローダーの前に登録する.
     */
    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass'], true, true);
    }

    /**
     * Plugin 名前空間のクラスを読み込み, 必要に応じて戻り値型を補完する.
     */
    public function loadClass(string $class): void
    {
        if (!str_starts_with($class, 'Plugin\\')) {
            return;
        }

        $file = $this->resolveFile($class);
        if ($file === null || !is_file($file)) {
            return;
        }

        $source = file_get_contents($file);

        // パッチが必要なクラスかどうか簡易判定
        if (!$this->mayNeedPatching($source)) {
            return;
        }

        $patched = $this->patchReturnTypes($source);
        if ($patched === $source) {
            return; // 変更なし: Composer に任せる
        }

        // パッチ済みソースを eval で読み込む
        // (元ファイルはそのまま残り, Composer のオートローダーはスキップされる)
        eval('?>'.$patched);
    }

    /**
     * PSR-4 規約に基づいてクラス名からファイルパスを解決する.
     */
    private function resolveFile(string $class): ?string
    {
        // Plugin\Code\Path\Class -> app/Plugin/Code/Path/Class.php
        $relativePath = str_replace('\\', '/', $class).'.php';
        $relativePath = preg_replace('#^Plugin/#', 'app/Plugin/', $relativePath);
        $file = $this->projectRoot.'/'.$relativePath;

        return is_file($file) ? $file : null;
    }

    /**
     * ソースコードがパッチ対象のクラスを含むか簡易判定する.
     */
    private function mayNeedPatching(string $source): bool
    {
        foreach (self::$patches as [$returnType, $keyword]) {
            if (str_contains($source, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * メソッドの戻り値型が不足している場合に補完する.
     *
     * 対象: function methodName(...) { のうち, 戻り値型宣言がないもの.
     * 戻り値型が既にある場合 (例: ): void {) はマッチしないためスキップされる.
     */
    private function patchReturnTypes(string $source): string
    {
        foreach (self::$patches as $method => [$returnType, $keyword]) {
            if (!str_contains($source, $keyword)) {
                continue;
            }

            // function methodName(任意のパラメータ) { にマッチ
            // 戻り値型がある場合は ) と { の間に : が入るためマッチしない
            $pattern = '/(function\s+'.preg_quote($method, '/').'\s*\([^)]*\))\s*\{/';
            $replacement = '$1: '.$returnType.' {';
            $source = preg_replace($pattern, $replacement, $source);
        }

        return $source;
    }
}
