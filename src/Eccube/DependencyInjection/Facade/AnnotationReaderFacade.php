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

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AnnotationReaderFacade
{
    /** @var self|null */
    private static $instance = null;

    /** @var Reader */
    private static $Reader;

    /**
     * @param Reader $Reader
     */
    public function __construct(Reader $Reader)
    {
        self::$Reader = $Reader;
    }

    /**
     * @param Reader $Reader
     *
     * @return AnnotationReaderFacade|null
     */
    public static function init(Reader $Reader)
    {
        if (null === self::$instance) {
            self::$instance = new self($Reader);
        }

        return self::$instance;
    }

    /**
     * @return Reader
     *
     * @throws \Exception
     */
    public static function create()
    {
        if (null === self::$instance) {
            throw new \Exception('Facade is not instantiated');
        }

        return self::$Reader;
    }

    public function getAnnotationReader()
    {
        return self::$Reader;
    }
}
