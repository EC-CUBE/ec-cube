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

namespace Eccube\DependencyInjection\Compiler;

use Eccube\Service\Payment\PaymentMethodInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PaymentMethodPass implements CompilerPassInterface
{
    const PAYMENT_METHOD_TAG = 'eccube.payment.method';

    public function process(ContainerBuilder $container)
    {
        $ids = $container->findTaggedServiceIds(self::PAYMENT_METHOD_TAG);

        foreach ($ids as $id => $tags) {
            $def = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($def->getClass());
            if (!is_subclass_of($class, PaymentMethodInterface::class)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, PaymentMethodInterface::class));
            }
            $def->setPublic(true);
        }
    }
}
