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

namespace Eccube\DependencyInjection;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Finder\Finder;

class EccubeExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array<mixed> $configs
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    #[\Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);
    }

    #[\Override]
    public function getAlias(): string
    {
        return 'eccube';
    }

    /**
     * @param array<mixed> $config
     */
    #[\Override]
    public function getConfiguration(array $config, ContainerBuilder $container): ?ConfigurationInterface
    {
        return parent::getConfiguration($config, $container);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    #[\Override]
    public function prepend(ContainerBuilder $container): void
    {
        // FrameworkBundleの設定を動的に変更する.
        $this->configureFramework($container);

        // プラグインの有効無効判定および初期化を行う.
        $this->configurePlugins($container);
    }

    protected function configureFramework(ContainerBuilder $container): void
    {
        $forceSSL = $container->resolveEnvPlaceholders('%env(ECCUBE_FORCE_SSL)%', true);
        // envから取得した内容が文字列のため, booleanに変換
        if ('1' === $forceSSL) {
            $forceSSL = true;
        } elseif ('0' === $forceSSL) {
            $forceSSL = false;
        }

        // SSL強制時は, httpsのみにアクセス制限する
        $accessControl = [
            ['path' => '^/%eccube_admin_route%/login', 'roles' => 'IS_AUTHENTICATED_ANONYMOUSLY'],
            ['path' => '^/%eccube_admin_route%/', 'roles' => 'ROLE_ADMIN'],
            ['path' => '^/mypage/login', 'roles' => 'IS_AUTHENTICATED_ANONYMOUSLY'],
            ['path' => '^/mypage/withdraw_complete', 'roles' => 'IS_AUTHENTICATED_ANONYMOUSLY'],
            ['path' => '^/mypage/change', 'roles' => 'IS_AUTHENTICATED_FULLY'],
            ['path' => '^/mypage/', 'roles' => 'ROLE_USER'],
        ];
        if ($forceSSL) {
            foreach ($accessControl as &$control) {
                $control['requires_channel'] = 'https';
            }
        }

        // security.ymlでは制御できないため, ここで定義する.
        $container->prependExtensionConfig('security', [
            'access_control' => $accessControl,
        ]);

        $configs = $container->getExtensionConfig('eccube');
        $configs = array_reverse($configs);
        $rateLimiterConfigs = [];

        foreach ($configs as $config) {
            if (empty($config['rate_limiter'])) {
                continue;
            }
            foreach ($config['rate_limiter'] as $id => $limiter) {
                $container->prependExtensionConfig('framework', [
                    'rate_limiter' => [
                        $id => [
                            'policy' => 'fixed_window',
                            'limit' => $limiter['limit'],
                            'interval' => $limiter['interval'],
                            'cache_pool' => 'rate_limiter.cache',
                        ],
                    ],
                ]);
                // Customize > Plugin > 本体
                if (isset($limiter['route']) && !isset($rateLimiterConfigs[$limiter['route']][$id])) {
                    $processor = new Processor();
                    $configuration = new Configuration();
                    $processed = $processor->processConfiguration($configuration, ['eccube' => ['rate_limiter' => [$id => $limiter]]]);
                    $rateLimiterConfigs[$limiter['route']][$id] = $processed['rate_limiter']['limiters'][$id];
                }
            }
        }

        $container->setParameter('eccube_rate_limiter_configs', $rateLimiterConfigs);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function configurePlugins(ContainerBuilder $container): void
    {
        $pluginDir = $container->getParameter('kernel.project_dir').'/app/Plugin';
        $pluginDirs = $this->getPluginDirectories($pluginDir);

        $container->setParameter('eccube.plugins.enabled', []);
        // ファイル設置のみの場合は, 無効なプラグインとみなす.
        // DB接続後, 有効無効の判定を行う.
        $container->setParameter('eccube.plugins.disabled', $pluginDirs);

        // prependのタイミングではコンテナのインスタンスは利用できないため,
        // 直接dbalのconnectionを生成し, dbアクセスを行う.
        //
        // DBAL 4 では DriverManager::getConnection() が 'url' パラメータを解析しなくなった
        // (DBAL 3 までは url から driver/host/dbname 等を導出していた). url を渡しても無視され,
        // doctrine.yaml の既定 driver (pdo_sqlite) のまま実 DB とは別の接続が張られてしまい,
        // dtb_plugin を読めず「全プラグインが無効」と誤判定される. そのため DsnParser で
        // DATABASE_URL を明示的に driver/host/dbname 等へ展開してから接続する.
        // スキームマップは DoctrineBundle\ConnectionFactory::DEFAULT_SCHEME_MAP に合わせる.
        $dsnParser = new DsnParser([
            'db2' => 'ibm_db2',
            'mssql' => 'pdo_sqlsrv',
            'mysql' => 'pdo_mysql',
            'mysql2' => 'pdo_mysql',
            'postgres' => 'pdo_pgsql',
            'postgresql' => 'pdo_pgsql',
            'pgsql' => 'pdo_pgsql',
            'sqlite' => 'pdo_sqlite',
            'sqlite3' => 'pdo_sqlite',
        ]);
        $params = $dsnParser->parse(env('DATABASE_URL'));
        $conn = DriverManager::getConnection($params);

        if (!$this->isConnected($conn)) {
            return;
        }

        $stmt = $conn->executeQuery('select * from dtb_plugin');
        $plugins = $stmt->fetchAllAssociative();

        $enabled = [];
        foreach ($plugins as $plugin) {
            if (array_key_exists('enabled', $plugin) && $plugin['enabled']) {
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
     * @param string[] $enabled
     */
    protected function configureTwigPaths(ContainerBuilder $container, array $enabled, string $pluginDir): void
    {
        $paths = [];
        $projectDir = $container->getParameter('kernel.project_dir');

        foreach ($enabled as $code) {
            // app/template/plugin/[plugin code]
            $dir = $projectDir.'/app/template/plugin/'.$code;
            if (file_exists($dir)) {
                $paths[$dir] = $code;
            }
            // app/Plugin/[plugin code]/Resource/template
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
     * @param string[] $enabled
     */
    protected function configureTranslations(ContainerBuilder $container, array $enabled, string $pluginDir): void
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

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function isConnected(Connection $conn): bool
    {
        try {
            $conn->executeQuery('select 1');
        } catch (\Exception) {
            return false;
        }

        $tableNames = $conn->createSchemaManager()->listTableNames();

        return in_array('dtb_plugin', $tableNames);
    }

    /**
     * @return array<int, string>
     */
    protected function getPluginDirectories(string $pluginDir): array
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
