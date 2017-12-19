<?php

namespace Eccube\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Configuration as DoctrineBundleConfiguration;
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
        // TODO EC-CUBEのインストール状態のチェックを行う
        if (!$container->hasParameter('eccube.install')) {
            //return;
        }
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
        $conn = \Doctrine\DBAL\DriverManager::getConnection($params);

        $stmt = $conn->query('select * from dtb_plugin');
        $plugins = $stmt->fetchAll();

        $enabled = array_filter($plugins, function($plugin) {
            return true === (bool) $plugin['enabled'];
        });

        $disabled = array_filter($plugins, function($plugin) {
            return false === (bool) $plugin['enabled'];
        });

        // 他で使いまわすため, パラメータで保持しておく.
        $container->setParameter('eccube.plugins.enabled', $enabled);
        $container->setParameter('eccube.plugins.disabled', $disabled);

        // mapping情報の構築
        $pluginDir = $container->getParameter('kernel.project_dir').'/app/Plugin';
        $mappings = [];
        foreach ($enabled as $plugin) {
            $code = $plugin['code'];
            $namespace = sprintf('Plugin\%s\Entity', $code);

            $dir = $pluginDir.'/'.$code.'/Entity';

            if (false === file_exists($dir)) {
                continue;
            }

            $mappings[$code] = [
                'is_bundle' => false,
                'type' => 'annotation',
                'dir' => '%kernel.project_dir%/app/Plugin/'.$code.'/Entity',
                'prefix' => $namespace,
                'alias' => $code,
            ];
        }

        // mapping情報の追加
        if (!empty($mappings)) {
            $container->prependExtensionConfig('doctrine', [
                'orm' => [
                    'mappings' => $mappings,
                ],
            ]);
        }
    }
}
