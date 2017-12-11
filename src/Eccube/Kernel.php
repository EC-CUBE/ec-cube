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

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

use Pimple\Container as PimpleContainer;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Api\EventListenerProviderInterface;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';
    protected $providers = [];
    protected $app;

    public function __construct($environment, $debug)
    {
        /*
         * Workaround to avoid: An exception occurred in driver: SQLSTATE[HY000] [14] unable to open database file
         * As environment variables is not supported yet to be used with configuration parameters.
         *
         * @TODO remove in 3.4
         */
        if (isset($_ENV['DATABASE_URL']) && false !== mb_strpos($_ENV['DATABASE_URL'], '%kernel.project_dir%')) {
            (new Dotenv())->populate([
                'DATABASE_URL' => str_replace('%kernel.project_dir%', $this->getProjectDir(), $_ENV['DATABASE_URL']),
            ]);
        }

        parent::__construct($environment, $debug);
        // $_ENV['DATABASE_URL'] = str_replace('%kernel.project_dir%', $this->getProjectDir(), $_ENV['DATABASE_URL']); //  FIXME
        // $this->app = new \Eccube\Application();
    }

    public function getCacheDir(): string
    {
        return dirname(__DIR__).'/var/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return dirname(__DIR__).'/var/log';
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
        parent::boot();
        if (!class_exists('\ServiceProviderCache')) {
            require __DIR__.'/../../app/cache/provider/ServiceProviderCache.php';
        }

        $em = $this->container->get('doctrine')->getManager();
        $this->app = $this->container->get('app');
        // Symfony で用意されているコンポーネントはここで追加
        $this->app['orm.em'] = function () use ($em) {
            return $em;
        };
         // TODO
        $this->app['config'] = function () {
            return require __DIR__.'/../../app/config/eccube/config.php';
        };
        $this->app['debug'] = true;

        // see Silex\Application::boot()
        foreach ($this->providers as $provider) {
            if ($provider instanceof EventListenerProviderInterface) {
                $provider->subscribe($this->app, $this->container->get('event_dispatcher'));
            }

            if ($provider instanceof BootableProviderInterface) {
                $provider->boot($this->app);
            }
        }
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

        // Pimple の ServiceProvider を追加
        // $container->register('ServiceProviderCache', 'ServiceProviderCache');
        // $container->register('EccubeServiceProvider', '\Eccube\ServiceProvider\EccubeServiceProvider');
        // $this->providers[] = new \Eccube\ServiceProvider\EccubeServiceProvider(); // FIXME
        $container->register('app', 'Eccube\Application');
            // ->addMethodCall('register', [new \Symfony\Component\DependencyInjection\Reference('ServiceProviderCache')])
            // ->addMethodCall('register', [new \Symfony\Component\DependencyInjection\Reference('EccubeServiceProvider')]);
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
    }
}
