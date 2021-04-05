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

namespace Eccube\Tests\DependencyInjection\Compiler;

use Doctrine\Common\EventSubscriber;
use Eccube\DependencyInjection\Compiler\AutoConfigurationTagPass;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AutoConfigurationTagPassTest extends EccubeTestCase
{
    public function testConfigureDoctrineEventSubscriberTag()
    {
        $container = new ContainerBuilder();
        $container->register(Subscriber::class, Subscriber::class);

        $definition = $container->getDefinition(Subscriber::class);
        $definition->setPublic(true);
        self::assertFalse($definition->hasTag('doctrine.event_subscriber'));

        $container->addCompilerPass(new AutoConfigurationTagPass());
        $container->compile();

        $definition = $container->getDefinition(Subscriber::class);
        self::assertTrue($definition->hasTag('doctrine.event_subscriber'));
    }
}

class Subscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
    }
}
