<?php

namespace Eccube\DependencyInjection\Facade;

use Eccube\Log\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * XXX ContainerInterface は不要かも
 */
class LoggerFacade
{
    /** @var self|null */
    private static $instance = null;

    /** @var ContainerInterface */
    private static $Container;

    /** @var Logger */
    private static $Logger;

    /**
     * @param ContainerInterface $container
     */
    private function __construct(ContainerInterface $container, Logger $Logger)
    {
        self::$Container = $container;
        self::$Logger = $Logger;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return null|LoggerFacade
     */
    public static function init(ContainerInterface $container, Logger $Logger)
    {
        if (null === self::$instance) {
            self::$instance = new self($container, $Logger);
        }

        return self::$instance;
    }

    /**
     * @return Logger
     * @throws \Exception
     */
    public static function create()
    {
        if (null === self::$instance) {
            throw new \Exception("Facade is not instantiated");
        }

        return self::$Logger;
    }

    /**
     * @deprecated
     * @return ContainerInterface
     */
    public static function getContainer()
    {
        return self::$Container;
    }
}
