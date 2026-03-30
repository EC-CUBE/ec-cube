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
 * Symfony 7 / EC-CUBE コアで追加された戻り値型宣言にプラグインを自動対応させるオートローダー.
 *
 * PHP では親クラスのメソッドに戻り値型がある場合, 子クラスでも同じ型を宣言しなければ
 * Fatal Error になる. このローダーは Plugin 名前空間のクラスファイルを読み込む際に,
 * 戻り値型が欠けている public/protected メソッドに対して一律 `: void` を補完する.
 *
 * `: void` の補完が安全な理由:
 * - 親に戻り値型がない場合: 子に `: void` を追加するのは合法 (型の狭小化)
 * - 親に `: void` がある場合: 子にも必要なので正しい補完
 * - 戻り値を返すメソッドには既に戻り値型宣言があるはずなのでマッチしない
 *
 * `: mixed` / `: string` 等が必要なメソッドは $specialReturnTypes で個別対応する.
 */
class PluginReturnTypeCompatLoader
{
    /**
     * void 以外の戻り値型が必要なメソッド名のマッピング.
     */
    private static array $specialReturnTypes = [
        'transform' => 'mixed',
        'reverseTransform' => 'mixed',
        'getBlockPrefix' => 'string',
        'getParent' => '?string',
        'getExtendedTypes' => 'iterable',
        'getSubscribedEvents' => 'array',
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
        if ($file === null) {
            return;
        }

        $source = file_get_contents($file);

        // クラス定義がなければスキップ (インターフェース/トレイト/定数ファイル等)
        if (!preg_match('/\bclass\s+\w+/s', $source)) {
            return;
        }

        $patched = $this->patchReturnTypes($source);
        if ($patched === $source) {
            return; // 変更なし: Composer に任せる
        }

        // パッチ済みソースを eval で読み込む
        eval('?>'.$patched);
    }

    /**
     * PSR-4 規約に基づいてクラス名からファイルパスを解決する.
     */
    private function resolveFile(string $class): ?string
    {
        $relativePath = str_replace('\\', '/', $class).'.php';
        $relativePath = preg_replace('#^Plugin/#', 'app/Plugin/', $relativePath);
        $file = $this->projectRoot.'/'.$relativePath;

        return is_file($file) ? $file : null;
    }

    /**
     * 戻り値型宣言が欠けている public/protected メソッドに型を補完する.
     *
     * パターン: `function methodName(...) {` (戻り値型なし)
     * 戻り値型がある場合は `)` と `{` の間に `:` が入るためマッチしない.
     */
    private function patchReturnTypes(string $source): string
    {
        // public/protected function name(...) { で戻り値型がないものにマッチ
        return preg_replace_callback(
            '/((public|protected)\s+function\s+(\w+)\s*\([^)]*\))\s*\{/',
            function ($matches) {
                $methodName = $matches[3];
                $returnType = self::$specialReturnTypes[$methodName] ?? 'void';

                return $matches[1].': '.$returnType.' {';
            },
            $source
        );
    }
}
