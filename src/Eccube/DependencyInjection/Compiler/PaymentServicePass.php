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

namespace Eccube\DependencyInjection\Compiler;

use Eccube\Service\PaymentServiceInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PaymentServicePass implements CompilerPassInterface
{
    const PAYMENT_TAG = 'eccube.payment';

    public function process(ContainerBuilder $container)
    {
        $ids = $container->findTaggedServiceIds(self::PAYMENT_TAG);

        foreach ($ids as $id => $tags) {
            $def = $container->getDefinition($id);
            $def->setPublic(true);
            $class = $container->getParameterBag()->resolveValue($def->getClass());
            if (!is_subclass_of($class, PaymentServiceInterface::class)) {
                throw new \InvalidArgumentException(
                    sprintf('Service "%s" must implement interface "%s".', $id, PaymentServiceInterface::class));
            }

            $container->setParameter($class, $class);
        }
    }
}
