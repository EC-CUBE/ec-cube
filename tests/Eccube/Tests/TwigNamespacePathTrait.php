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

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Repository\PageLayoutRepository;
use Eccube\Repository\PageRepository;
use Eccube\Service\Content\PageContentService;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactoryInterface;
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

    /**
     * $codes を有効なプラグイン (eccube.plugins.enabled) として扱う PageContentService を作る.
     *
     * 有効なプラグインはコンテナのコンパイル時に決まり, テスト中に足せないため, 設定だけを差し替えた
     * インスタンスを作る. コンテナのサービスと置き換える場合は, そのサービスを取得する前に
     * static::getContainer()->set() する.
     */
    protected function createPageContentServiceWithEnabledPlugins(string ...$codes): PageContentService
    {
        $container = static::getContainer();
        $eccubeConfig = new class($container->get(ContainerBagInterface::class), $codes) extends EccubeConfig {
            /**
             * @param list<string> $enabledPlugins
             */
            public function __construct(ContainerBagInterface $container, private readonly array $enabledPlugins)
            {
                parent::__construct($container);
            }

            #[\Override]
            public function get(string $key): mixed
            {
                $value = parent::get($key);

                return 'eccube.plugins.enabled' === $key ? [...(array) $value, ...$this->enabledPlugins] : $value;
            }
        };

        return new PageContentService(
            $container->get(EntityManagerInterface::class),
            $container->get(PageRepository::class),
            $container->get(PageLayoutRepository::class),
            $container->get(FormFactoryInterface::class),
            $container->get(Environment::class),
            new Filesystem(),
            $eccubeConfig,
        );
    }
}
