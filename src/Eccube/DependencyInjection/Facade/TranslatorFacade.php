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
     * @return TranslatorFacade|null
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
     *
     * @throws \Exception
     */
    public static function create()
    {
        if (null === self::$instance) {
            throw new \Exception('Facade is not instantiated');
        }

        return self::$Translator;
    }
}
