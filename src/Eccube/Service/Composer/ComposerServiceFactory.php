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

namespace Eccube\Service\Composer;

use Eccube\Entity\BaseInfo;
use Eccube\Repository\BaseInfoRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ComposerServiceFactory
{
    public static function createService(ContainerInterface $container)
    {
        /** @var BaseInfo $BaseInfo */
        $BaseInfo = $container->get(BaseInfoRepository::class)->get();
        if (!empty($BaseInfo->getPhpPath())) {
            return $container->get(ComposerProcessService::class);
        }

        return $container->get(ComposerApiService::class);
    }
}
