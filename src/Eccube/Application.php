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

namespace Eccube;

use Eccube\ServiceProvider\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

class Application extends \Pimple
{
    /** @var Application $instance */
    protected static $instance;

    protected $initialized = false;
    protected $booted = false;
    protected $providers = [];
    /** @var ContainerInterface $parentContainer */
    protected $parentContainer;

    /**
     * @param array $values
     *
     * @return Application
     */
    public static function getInstance(array $values = [])
    {
        if (!is_object(self::$instance)) {
            self::$instance = new Application($values);
        }

        return self::$instance;
    }

    public static function clearInstance()
    {
        self::$instance = null;
    }

    final public function __clone()
    {
        throw new \Exception('Clone is not allowed against '.get_class($this));
    }

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        if (is_null(self::$instance)) {
            self::$instance = $this;
        }
    }

    /**
     * Application::runが実行されているか親クラスのプロパティから判定
     *
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * Initialize to Application.
     */
    public function initialize()
    {
        if ($this->initialized) {
            return;
        }
        $this->register(new \Eccube\ServiceProvider\EccubeServiceProvider());
        $this->initialized = true;
    }

    /**
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
     * @param array                    $values   An array of values that customizes the provider
     *
     * @return Application
     *
     * @see https://github.com/silexphp/Silex/blob/1.3/src/Silex/Application.php#L174
     */
    public function register(ServiceProviderInterface $provider, array $values = [])
    {
        $this->providers[] = $provider;
        $provider->register($this);
        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }

        return $this;
    }

    /**
     * Boots all service providers.
     *
     * This method is automatically called by handle(), but you can use it
     * to boot all service providers when not handling a request.
     *
     * @see https://github.com/silexphp/Silex/blob/1.3/src/Silex/Application.php#L193
     */
    public function boot()
    {
        if (!$this->booted) {
            foreach ($this->providers as $provider) {
                $provider->boot($this);
            }
            $this->booted = true;
        }
    }

    /**
     * Set to the Symfony ContainerInterface.
     *
     * @param ContainerInterface The Symfony ContainerInterface.
     */
    public function setParentContainer(ContainerInterface $parentContainer)
    {
        $this->parentContainer = $parentContainer;

        return $this;
    }

    /**
     * Get ParentContainer.
     *
     * @return ContainerInterface
     */
    public function getParentContainer()
    {
        return $this->parentContainer;
    }
}
