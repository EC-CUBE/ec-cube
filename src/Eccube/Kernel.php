<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Eccube\DependencyInjection\Compiler\AutoConfigurationTagPass;
use Eccube\DependencyInjection\Compiler\LazyComponentPass;
use Eccube\DependencyInjection\Compiler\PluginPass;
use Eccube\DependencyInjection\Compiler\QueryCustomizerPass;
use Eccube\DependencyInjection\Compiler\TemplateListenerPass;
use Eccube\DependencyInjection\Compiler\WebServerDocumentRootPass;
use Eccube\DependencyInjection\EccubeExtension;
use Eccube\Doctrine\DBAL\Types\UTCDateTimeType;
use Eccube\Doctrine\DBAL\Types\UTCDateTimeTzType;
use Eccube\Doctrine\Query\QueryCustomizer;
use Eccube\Plugin\ConfigManager;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';
    protected $app;

    public function getCacheDir(): string
    {
        return dirname(__DIR__).'/../var/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return dirname(__DIR__).'/../var/log';
    }

    public function registerBundles()
    {
        $contents = require dirname(__DIR__).'/../app/config/eccube/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    /**
     * {@inheritdoc}
     * @see \Symfony\Component\HttpKernel\Kernel::boot()
     */
    public function boot()
    {
        // Symfonyがsrc/Eccube/Entity以下を読み込む前にapp/proxy/entity以下をロードする
        $this->loadEntityProxies();

        parent::boot();

        // DateTime/DateTimeTzのタイムゾーンを設定.
        UTCDateTimeType::setTimeZone($this->container->getParameter('timezone'));
        UTCDateTimeTzType::setTimeZone($this->container->getParameter('timezone'));

        // Activate to $app
        $app = Application::getInstance(['debug' => $this->isDebug()]);
        $app->setParentContainer($this->container);
        $app->initialize();
        $app->boot();

        $this->container->set('app', $app);
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $confDir = dirname(__DIR__).'/../app/config/eccube';
        $loader->load($confDir.'/packages/*'.self::CONFIG_EXTS, 'glob');
        if (is_dir($confDir.'/packages/'.$this->environment)) {
            $loader->load($confDir.'/packages/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        }
        $loader->load($confDir.'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/services_'.$this->environment.self::CONFIG_EXTS, 'glob');

        // プラグインのservices.phpをロードする.
        $dir = dirname(__DIR__).'/../app/Plugin/*/Resource/config';
        $loader->load($dir.'/services'.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $container = $this->getContainer();

        $scheme = $container->getParameter('eccube.scheme');
        $routes->setSchemes($scheme);

        $confDir = dirname(__DIR__).'/../app/config/eccube';
        if (is_dir($confDir.'/routes/')) {
            $builder = $routes->import($confDir.'/routes/*'.self::CONFIG_EXTS, '/', 'glob');
            $builder->setSchemes($scheme);
        }
        if (is_dir($confDir.'/routes/'.$this->environment)) {
            $builder = $routes->import($confDir.'/routes/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
            $builder->setSchemes($scheme);
        }
        $builder = $routes->import($confDir.'/routes'.self::CONFIG_EXTS, '/', 'glob');
        $builder->setSchemes($scheme);

        // 有効なプラグインのルーティングをインポートする.
        if ($container->hasParameter('eccube.plugins.enabled')) {
            $plugins = $container->getParameter('eccube.plugins.enabled');
            $pluginDir = dirname(__DIR__).'/../app/Plugin';
            foreach ($plugins as $plugin) {
                $dir = $pluginDir.'/'.$plugin['code'].'/Controller';
                if (file_exists($dir)) {
                    $builder = $routes->import($dir, '/', 'annotation');
                    $builder->setSchemes($scheme);
                }
            }
        }
    }

    protected function build(ContainerBuilder $container)
    {
        $this->addEntityExtensionPass($container);

        $container->registerExtension(new EccubeExtension());

        // サービスタグの自動設定を行う
        $container->addCompilerPass(new AutoConfigurationTagPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 11);

        // サービスタグの収集より先に実行し, 付与されているタグをクリアする.
        // FormPassは優先度0で実行されているので, それより速いタイミングで実行させる.
        // 自動登録されるタグやコンパイラパスの登録タイミングは, FrameworkExtension::load(), FrameworkBundle::build()を参考に.
        $container->addCompilerPass(new PluginPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);

        // DocumentRootをルーティディレクトリに設定する.
        $container->addCompilerPass(new WebServerDocumentRootPass('%kernel.project_dir%/'));

        // テスト時はコンテナからコンポーネントを直接取得できるようにしておく
        if ($this->environment === 'test') {
            $container->addCompilerPass(new LazyComponentPass());
        }

        // テンプレートフックポイントを動作させるように.
        $container->addCompilerPass(new TemplateListenerPass());

        $container->register('app', Application::class)
            ->setSynthetic(true)
            ->setPublic(true);

        // クエリカスタマイズの拡張.
        $container->registerForAutoconfiguration(QueryCustomizer::class)
            ->addTag(QueryCustomizerPass::QUERY_CUSTOMIZER_TAG);
        $container->addCompilerPass(new QueryCustomizerPass());
    }

    protected function addEntityExtensionPass(ContainerBuilder $container)
    {
        $projectDir = $container->getParameter('kernel.project_dir');

        $paths = ['%kernel.project_dir%/src/Eccube/Entity'];
        $namespaces = ['Eccube\\Entity'];

        $pluginConfigs = ConfigManager::getPluginConfigAll(true);
        foreach ($pluginConfigs as $config) {
            $code = $config['config']['code'];
            if (file_exists($projectDir.'/app/Plugin/'.$code.'/Entity')) {
                $paths[] = '%kernel.project_dir%/app/Plugin/'.$code.'/Entity';
                $namespaces[] = 'Plugin\\'.$code.'\\Entity';
            }
        }

        $reader = new Reference('annotation_reader');
        $driver = new Definition('Eccube\\Doctrine\\ORM\\Mapping\\Driver\\AnnotationDriver', array($reader, $paths));
        $driver->addMethodCall('setTraitProxiesDirectory', [$projectDir.'/app/proxy/entity']);
        $container->addCompilerPass(new DoctrineOrmMappingsPass($driver, $namespaces, []));
    }

    protected function loadEntityProxies()
    {
        foreach (glob(__DIR__ . '/../../app/proxy/entity/*.php') as $file) {
            require_once $file;
        }
    }
}
