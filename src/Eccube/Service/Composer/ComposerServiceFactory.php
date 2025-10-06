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

namespace Eccube\Service\Composer;

use Psr\Container\ContainerInterface;

class ComposerServiceFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return ComposerApiService|null
     */
    public static function createService(ContainerInterface $container): ?ComposerApiService
    {
        return $container->get(ComposerApiService::class);
    }
}
