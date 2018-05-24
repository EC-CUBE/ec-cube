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
        $container->setParameter('eccube.plugins.enabled', []);
        $container->setParameter('eccube.plugins.disabled', []);

        // FIXME WebTestCase で DATABASE_URL が取得できず落ちる
        if (!array_key_exists('APP_ENV', $_ENV) || $_ENV['APP_ENV'] == 'test') {
            return;
        }

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

        $enabled = array_filter($plugins, function ($plugin) {
            return true === (bool) $plugin['enabled'];
        });

        $disabled = array_filter($plugins, function ($plugin) {
            return false === (bool) $plugin['enabled'];
        });

        // 他で使いまわすため, パラメータで保持しておく.
        $container->setParameter('eccube.plugins.enabled', $enabled);
        $container->setParameter('eccube.plugins.disabled', $disabled);

        $pluginDir = $container->getParameter('kernel.project_dir').'/app/Plugin';
        $this->configureTwigPaths($container, $enabled, $pluginDir);
        $this->configureTranslations($container, $enabled, $pluginDir);
    }

    protected function configureTwigPaths(ContainerBuilder $container, $enabled, $pluginDir)
    {
        $paths = [];

        foreach ($enabled as $plugin) {
            $code = $plugin['code'];
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

    protected function configureTranslations(ContainerBuilder $container, $enabled, $pluginDir)
    {
        $paths = [];

        foreach ($enabled as $plugin) {
            $code = $plugin['code'];
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
}
