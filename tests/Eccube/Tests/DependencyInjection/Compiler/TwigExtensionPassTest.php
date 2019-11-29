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

use Eccube\DependencyInjection\Compiler\TwigExtensionPass;
use Eccube\Twig\Extension\IgnoreRoutingNotFoundExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TwigExtensionPassTest extends TestCase
{
    protected $contailer;

    public function setUp()
    {
        self::$container = new ContainerBuilder();

        self::$container->register(RouteCollection::class);
        self::$container->register(RequestContext::class);
        self::$container->register(UrlGeneratorInterface::class, UrlGenerator::class)
            ->setAutowired(true);
        self::$container->register(IgnoreRoutingNotFoundExtension::class)
            ->setAutowired(true);
        self::$container->register(\Twig_LoaderInterface::class, ArrayLoader::class);
        self::$container->register('twig', Environment::class)
            ->setPublic(true)
            ->setAutowired(true);
    }

    public function testProcess()
    {
        self::$container->setParameter('kernel.debug', false);
        self::$container->addCompilerPass(new TwigExtensionPass());
        self::$container->compile();

        /** @var Environment $twig */
        $twig = self::$container->get('twig');
        self::assertTrue($twig->hasExtension(IgnoreRoutingNotFoundExtension::class));
        self::assertInstanceOf(
            IgnoreRoutingNotFoundExtension::class,
            $twig->getExtension(IgnoreRoutingNotFoundExtension::class)
        );
    }
}
