<?php

namespace Eccube\DependencyInjection\Facade;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * XXX ContainerInterface は不要かも
 */
class AnnotationReaderFacade
{
    /** @var self|null */
    private static $instance = null;

    /** @var ContainerInterface */
    private static $Container;

    /** @var Reader */
    private static $Reader;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, Reader $Reader)
    {
        self::$Container = $container;
        self::$Reader = $Reader;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return null|AnnotationReaderFacade
     */
    public static function init(ContainerInterface $container, Reader $Reader)
    {
        if (null === self::$instance) {
            self::$instance = new self($container, $Reader);
        }

        return self::$instance;
    }

    /**
     * @return Reader
     * @throws \Exception
     */
    public static function create()
    {
        if (null === self::$instance) {
            throw new \Exception("Facade is not instantiated");
        }

        return self::$Reader;
    }

    public function getAnnotationReader()
    {
        return self::$Reader;
    }
}
