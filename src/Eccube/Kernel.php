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
use Eccube\DependencyInjection\Compiler\PluginFormPass;
use Eccube\DependencyInjection\EccubeExtension;
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
            return require __DIR__.'/../../app/config/eccube/config.php';
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
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = dirname(__DIR__).'/../app/config/eccube';
        if (is_dir($confDir.'/routes/')) {
            $routes->import($confDir.'/routes/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        if (is_dir($confDir.'/routes/'.$this->environment)) {
            $routes->import($confDir.'/routes/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        }
        $routes->import($confDir.'/routes'.self::CONFIG_EXTS, '/', 'glob');

        // 有効なプラグインのルーティングをインポートする.
        $plugins = $this->getContainer()->getParameter('eccube.plugins.enabled');
        $pluginDir = dirname(__DIR__).'/../app/Plugin';
        foreach ($plugins as $plugin) {
            $dir = $pluginDir.'/'.$plugin['code'].'/Controller';
            if (file_exists($dir)) {
                $routes->import($dir, '/', 'annotation');
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
        $container->addCompilerPass(new PluginFormPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);

        // Pimple の ServiceProvider を追加
        // $container->register('ServiceProviderCache', 'ServiceProviderCache');
        // $container->register('EccubeServiceProvider', '\Eccube\ServiceProvider\EccubeServiceProvider');
        // $this->providers[] = new \Eccube\ServiceProvider\EccubeServiceProvider(); // FIXME
        $container->register('app', Application::class)
            ->setSynthetic(true);
        // ->addMethodCall('register', [new \Symfony\Component\DependencyInjection\Reference('ServiceProviderCache')])
        // ->addMethodCall('register', [new \Symfony\Component\DependencyInjection\Reference('EccubeServiceProvider')]);
    }

    protected function addEntityExtensionPass(ContainerBuilder $container)
    {
        $reader = new Reference('annotation_reader');
        $driver = new Definition('Eccube\\Doctrine\\ORM\\Mapping\\Driver\\AnnotationDriver', array($reader, ["%kernel.project_dir%/src/Eccube/Entity"]));
        $driver->addMethodCall('setTraitProxiesDirectory', [$container->getParameter('kernel.project_dir')."/app/proxy/entity"]);
        $container->addCompilerPass(new DoctrineOrmMappingsPass($driver, ['Eccube\\Entity'], []));
    }

    protected function loadEntityProxies()
    {
        foreach (glob(__DIR__ . '/../../app/proxy/entity/*.php') as $file) {
            require_once $file;
        }
    }
}
