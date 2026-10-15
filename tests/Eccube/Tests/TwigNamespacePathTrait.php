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

use Twig\Environment;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

/**
 * プラグインの上書き用ディレクトリ (app/template/plugin/[code]) が twig の名前空間へ
 * 登録された状態 (コンテナを作り直した後) を再現するためのヘルパ.
 *
 * EccubeExtension::configureTwigPaths() はコンパイル時にあるディレクトリだけを登録するため,
 * テスト中に作ったディレクトリはそのままでは登録されない.
 *
 * @see \Eccube\DependencyInjection\EccubeExtension::configureTwigPaths()
 */
trait TwigNamespacePathTrait
{
    /**
     * 現在のコンテナの twig ローダへ, 名前空間のパスを追加する.
     *
     * ディレクトリが無いと FilesystemLoader::addPath() が例外を投げるため, 先に作る.
     * Web テストでは, リクエストのたびにカーネルを作り直すと登録が消えるため
     * $this->client->disableReboot() と併せて使う.
     */
    protected function registerTwigNamespacePath(string $namespace, string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $loaders = [static::getContainer()->get(Environment::class)->getLoader()];
        while ([] !== $loaders) {
            $loader = array_shift($loaders);
            if ($loader instanceof ChainLoader) {
                array_push($loaders, ...$loader->getLoaders());
                continue;
            }
            if ($loader instanceof FilesystemLoader) {
                $loader->addPath($dir, $namespace);

                return;
            }
        }

        self::fail('twig の FilesystemLoader が見つかりません.');
    }
}
