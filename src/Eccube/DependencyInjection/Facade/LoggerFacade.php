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

use Eccube\Log\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoggerFacade
{
    /** @var self|null */
    private static $instance;

    /** @var ContainerInterface */
    private static $Container;

    /** @var Logger */
    private static $Logger;

    private function __construct(ContainerInterface $container, Logger $Logger)
    {
        self::$Container = $container;
        self::$Logger = $Logger;
    }

    /**
     * @return LoggerFacade|null
     */
    public static function init(ContainerInterface $container, Logger $Logger): ?LoggerFacade
    {
        if (null === self::$instance) {
            self::$instance = new self($container, $Logger);
        }

        return self::$instance;
    }

    /**
     * @return Logger
     *
     * @throws \Exception
     */
    public static function create(): Logger
    {
        if (null === self::$instance) {
            throw new \Exception('Facade is not instantiated');
        }

        return self::$Logger;
    }

    /**
     * @return \Monolog\Logger
     */
    public static function getLoggerBy(string $channel): \Monolog\Logger
    {
        return self::$Container->get('monolog.logger.'.$channel);
    }
}
