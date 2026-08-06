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

namespace Eccube\Tests;

/**
 * 「対象キーが .env 由来ではなく OS のプロセス環境変数で上書きされている」状態を
 * テスト中だけ決定的に再現するためのヘルパ.
 *
 * @see \Eccube\Service\EnvFileService
 * @see https://github.com/EC-CUBE/ec-cube/issues/6130
 */
trait EnvOverrideTrait
{
    /**
     * 指定キーが OS のプロセス環境変数で上書きされている状態を作ってコールバックを実行し,
     * 終了後に $_SERVER を元へ復元する.
     *
     * Symfony Dotenv は putenv を使わず, .env 系から populate したキーを
     * $_SERVER['SYMFONY_DOTENV_VARS'] に列挙する. EnvFileService はこの一覧を根拠に
     * 「.env が実効かどうか」を判定するため, putenv では上書き状態を再現できない.
     * そこで当該キーを SYMFONY_DOTENV_VARS から除外し, $_SERVER に値を注入する.
     *
     * $value には **アプリケーションの実効値と同じ値**（例: 対応するコンテナパラメータの値）を渡すこと.
     * Symfony の EnvVarProcessor は $_ENV → $_SERVER → getenv の順に解決するため,
     * .env に当該キーが書かれている環境では $_ENV が勝ち, 注入値はアプリの挙動に影響しない.
     * しかし CI のように .env に当該キーが無い環境では, 注入値がそのまま実効値になる.
     * ここでダミー値を渡すと, 例えば ECCUBE_TEMPLATE_CODE では twig.paths が
     * 存在しない app/template/<ダミー> を指し, Twig の FilesystemLoader::addPath が
     * LoaderError を投げて全リクエストが 500 になる（実効値なら環境差なく成立する）.
     *
     * @param string   $key   対象の環境変数キー
     * @param string   $value 注入する値. アプリケーションの実効値と同じ値を渡す
     * @param callable $fn    上書き状態で実行する処理
     */
    protected function forceKeyOverridden(string $key, string $value, callable $fn): void
    {
        $sentinel = '__ECCUBE_TEST_UNSET__';
        $origVars = \array_key_exists('SYMFONY_DOTENV_VARS', $_SERVER) ? $_SERVER['SYMFONY_DOTENV_VARS'] : $sentinel;
        $origVal = \array_key_exists($key, $_SERVER) ? $_SERVER[$key] : $sentinel;

        $vars = array_filter(
            explode(',', (string) ($sentinel === $origVars ? '' : $origVars)),
            fn ($k) => $k !== $key && '' !== $k
        );
        $_SERVER['SYMFONY_DOTENV_VARS'] = implode(',', $vars);
        $_SERVER[$key] = $value;

        try {
            $fn();
        } finally {
            if ($sentinel === $origVars) {
                unset($_SERVER['SYMFONY_DOTENV_VARS']);
            } else {
                $_SERVER['SYMFONY_DOTENV_VARS'] = $origVars;
            }
            if ($sentinel === $origVal) {
                unset($_SERVER[$key]);
            } else {
                $_SERVER[$key] = $origVal;
            }
        }
    }
}
