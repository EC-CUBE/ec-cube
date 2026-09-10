<?php

declare(strict_types=1);

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
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\RateLimiter\RateLimiterFactory;

final class AutoConfigurationTagPassTest extends EccubeTestCase
{
    public function testConfigureDoctrineEventSubscriberTag()
    {
        $container = new ContainerBuilder();
        $container->register(Subscriber::class, Subscriber::class);

        $definition = $container->getDefinition(Subscriber::class);
        $definition->setPublic(true);
        $this->assertFalse($definition->hasTag('doctrine.event_subscriber'));

        $container->addCompilerPass(new AutoConfigurationTagPass());
        $container->compile(true);

        $definition = $container->getDefinition(Subscriber::class);
        $this->assertTrue($definition->hasTag('doctrine.event_subscriber'));
    }

    public function testConfigureRateLimiterTag()
    {
        $container = new ContainerBuilder();
        $container->register('limiter', RateLimiterFactory::class);
        $child = new ChildDefinition('limiter');
        $container->setDefinition('limiter.test', $child);

        $this->assertFalse($child->hasTag('eccube_rate_limiter'));

        $container->addCompilerPass(new AutoConfigurationTagPass());
        $container->compile(true);

        $this->assertTrue($child->hasTag('eccube_rate_limiter'));
    }
}

class Subscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
    }
}
