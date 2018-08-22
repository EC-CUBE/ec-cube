<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Configuration as DoctrineBundleConfiguration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EccubeExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $container)
    {
        // FrameworkBundleの設定を動的に変更する.
        $this->configureFramework($container);

        // プラグインの有効無効判定および初期化を行う.
        $this->configurePlugins($container);
    }

    protected function configureFramework(ContainerBuilder $container)
    {
        // SSL強制時は, cookie_secureをtrueにする
        $forceSSL = $container->resolveEnvPlaceholders('%env(ECCUBE_FORCE_SSL)%', true);
        // envから取得した内容が文字列のため, booleanに変換
        if ('true' === $forceSSL) {
            $forceSSL = true;
        } elseif ('false' === $forceSSL) {
            $forceSSL = false;
        }

        // framework.yamlでは制御できないため, ここで定義する.
        $container->prependExtensionConfig('framework', [
            'session' => [
                'cookie_secure' => $forceSSL,
            ],
        ]);
    }

    protected function configurePlugins(ContainerBuilder $container)
    {
        $pluginDir = $container->getParameter('kernel.project_dir').'/app/Plugin';
        $pluginDirs = $this->getPluginDirectories($pluginDir);

        $container->setParameter('eccube.plugins.enabled', []);
        // ファイル設置のみの場合は, 無効なプラグインとみなす.
        // DB接続後, 有効無効の判定を行う.
        $container->setParameter('eccube.plugins.disabled', $pluginDirs);

        // doctrine.yml, または他のprependで差し込まれたdoctrineの設定値を取得する.
        $configs = $container->getExtensionConfig('doctrine');

        // $configsは, env変数(%env(xxx)%)やパラメータ変数(%xxx.xxx%)がまだ解決されていないため, resolveEnvPlaceholders()で解決する
        // @see https://github.com/symfony/symfony/issues/22456
        $configs = $container->resolveEnvPlaceholders($configs, true);

        // doctrine bundleのconfigurationで設定値を正規化する.
        $configration = new DoctrineBundleConfiguration($container->getParameter('kernel.debug'));
        $config = $this->processConfiguration($configration, $configs);

        // prependのタイミングではコンテナのインスタンスは利用できない.
        // 直接dbalのconnectionを生成し, dbアクセスを行う.
        $params = $config['dbal']['connections'][$config['dbal']['default_connection']];
        $conn = DriverManager::getConnection($params);

        if (!$this->isConnected($conn)) {
            return;
        }

        $stmt = $conn->query('select * from dtb_plugin');
        $plugins = $stmt->fetchAll();

        $enabled = [];
        foreach ($plugins as $plugin) {
            if ($plugin['enabled']) {
                $enabled[] = $plugin['code'];
            }
        }

        $disabled = [];
        foreach ($pluginDirs as $dir) {
            if (!in_array($dir, $enabled)) {
                $disabled[] = $dir;
            }
        }

        // 他で使いまわすため, パラメータで保持しておく.
        $container->setParameter('eccube.plugins.enabled', $enabled);
        $container->setParameter('eccube.plugins.disabled', $disabled);

        $pluginDir = $container->getParameter('kernel.project_dir').'/app/Plugin';
        $this->configureTwigPaths($container, $enabled, $pluginDir);
        $this->configureTranslations($container, $enabled, $pluginDir);
    }

    /**
     * @param string $pluginDir
     */
    protected function configureTwigPaths(ContainerBuilder $container, $enabled, $pluginDir)
    {
        $paths = [];

        foreach ($enabled as $code) {
            $dir = $pluginDir.'/'.$code.'/Resource/template';
            if (file_exists($dir)) {
                $paths[$dir] = $code;
            }
        }

        if (!empty($paths)) {
            $container->prependExtensionConfig('twig', [
                'paths' => $paths,
            ]);
        }
    }

    /**
     * @param string $pluginDir
     */
    protected function configureTranslations(ContainerBuilder $container, $enabled, $pluginDir)
    {
        $paths = [];

        foreach ($enabled as $code) {
            $dir = $pluginDir.'/'.$code.'/Resource/locale';
            if (file_exists($dir)) {
                $paths[] = $dir;
            }
        }

        if (!empty($paths)) {
            $container->prependExtensionConfig('framework', [
                'translator' => [
                    'paths' => $paths,
                ],
            ]);
        }
    }

    protected function isConnected(Connection $conn)
    {
        try {
            if (!$conn->ping()) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        $sm = $conn->getSchemaManager();
        $tables = array_filter(
            $sm->listTables(),
            function ($table) {
                return $table->getName() === 'dtb_plugin';
            }
        );

        return empty($tables) ? false : true;
    }

    /**
     * @param string $pluginDir
     */
    protected function getPluginDirectories($pluginDir)
    {
        $finder = (new Finder())
            ->in($pluginDir)
            ->sortByName()
            ->depth(0)
            ->directories();

        $dirs = [];
        foreach ($finder as $dir) {
            $dirs[] = $dir->getBaseName();
        }

        return $dirs;
    }
}
