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
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class AutoConfigurationTagPassTest extends TestCase
{
    public function testConfigureDoctrineEventSubscriberTag()
    {
        $container = new ContainerBuilder();
        $container->register(Subscriber::class, Subscriber::class);

        $definition = $container->getDefinition(Subscriber::class);
        self::assertFalse($definition->hasTag('doctrine.event_subscriber'));

        $container->addCompilerPass(new AutoConfigurationTagPass());
        $container->compile();

        $definition = $container->getDefinition(Subscriber::class);
        self::assertTrue($definition->hasTag('doctrine.event_subscriber'));
    }

    public function testConfigureFormTypeExtensionTag()
    {
        $container = new ContainerBuilder();
        $container->register(FormTypeExtension::class, FormTypeExtension::class);

        $definition = $container->getDefinition(FormTypeExtension::class);
        self::assertFalse($definition->hasTag('form.type_extension'));

        $container->addCompilerPass(new AutoConfigurationTagPass());
        $container->compile();

        $definition = $container->getDefinition(FormTypeExtension::class);
        self::assertTrue($definition->hasTag('form.type_extension'));

        $attribute = $definition->getTag('form.type_extension');
        self::assertSame(FormType::class, $attribute[0]['extended_type']);
    }
}

class Subscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
    }
}

class FormTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return FormType::class;
    }
}
