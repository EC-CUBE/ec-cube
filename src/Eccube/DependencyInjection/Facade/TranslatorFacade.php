<?php

namespace Eccube\DependencyInjection\Facade;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * XXX ContainerInterface は不要かも
 */
class TranslatorFacade
{
    /** @var self|null */
    private static $instance = null;

    /** @var ContainerInterface */
    private static $Container;

    /** @var TranslatorInterface */
    private static $Translator;

    /**
     * @param ContainerInterface $container
     */
    private function __construct(ContainerInterface $container, TranslatorInterface $Translator)
    {
        self::$Container = $container;
        self::$Translator = $Translator;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return null|TranslatorFacade
     */
    public static function init(ContainerInterface $container, TranslatorInterface $Translator)
    {
        if (null === self::$instance) {
            self::$instance = new self($container, $Translator);
        }

        return self::$instance;
    }

    /**
     * @return TranslatorInterface
     * @throws \Exception
     */
    public static function create()
    {
        if (null === self::$instance) {
            throw new \Exception("Facade is not instantiated");
        }

        return self::$Translator;
    }
}
