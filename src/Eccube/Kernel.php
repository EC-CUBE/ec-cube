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
use Eccube\DependencyInjection\Compiler\PluginPass;
use Eccube\DependencyInjection\Compiler\WebServerDocumentRootPass;
use Eccube\DependencyInjection\EccubeExtension;
use Eccube\Plugin\ConfigManager;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Api\EventListenerProviderInterface;
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
    protected $providers = [];
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
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
     * @param array                    $values   An array of values that customizes the provider
     *
     * @return Application
     */
    public function register(ServiceProviderInterface $provider, array $values = array())
    {
        // TODO
        $this->providers[] = $provider;

        $app = $this->container->get('app');
        $app->register($provider, $values);

        return $this;
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

        // Symfony で用意されているコンポーネントはここで追加
        $app = Application::getInstance();
        $em = $this->container->get('doctrine')->getManager();
        $app['orm.em'] = function () use ($em) {
            return $em;
        };
         // TODO
        $app['config'] = function () {
            if ($this->container->hasParameter('eccube.app')) {
                return $this->container->getParameter('eccube.app');
            }

            return [];
        };
        $app['debug'] = true;

        // see Silex\Application::boot()
        foreach ($this->providers as $provider) {
            if ($provider instanceof EventListenerProviderInterface) {
                $provider->subscribe($this->app, $this->container->get('event_dispatcher'));
            }

            if ($provider instanceof BootableProviderInterface) {
                $provider->boot($this->app);
            }
        }

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

        // サービスタグの収集より先に実行し, 付与されているタグをクリアする.
        // FormPassは優先度0で実行されているので, それより速いタイミングで実行させる.
        // 自動登録されるタグやコンパイラパスの登録タイミングは, FrameworkExtension::load(), FrameworkBundle::build()を参考に.
        $container->addCompilerPass(new PluginPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);

        // DocumentRootをルーティディレクトリに設定する.
        $container->addCompilerPass(new WebServerDocumentRootPass('%kernel.project_dir%/'));

        // Pimple の ServiceProvider を追加
        // $container->register('ServiceProviderCache', 'ServiceProviderCache');
        // $container->register('EccubeServiceProvider', '\Eccube\ServiceProvider\EccubeServiceProvider');
        // $this->providers[] = new \Eccube\ServiceProvider\EccubeServiceProvider(); // FIXME
        $container->register('app', Application::class)
            ->setSynthetic(true)
            ->setPublic(true);
        // ->addMethodCall('register', [new \Symfony\Component\DependencyInjection\Reference('ServiceProviderCache')])
        // ->addMethodCall('register', [new \Symfony\Component\DependencyInjection\Reference('EccubeServiceProvider')]);
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
