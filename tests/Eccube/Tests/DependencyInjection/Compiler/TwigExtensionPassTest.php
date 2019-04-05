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
        $this->container = new ContainerBuilder();

        $this->container->register(RouteCollection::class);
        $this->container->register(RequestContext::class);
        $this->container->register(UrlGeneratorInterface::class, UrlGenerator::class)
            ->setAutowired(true);
        $this->container->register(IgnoreRoutingNotFoundExtension::class)
            ->setAutowired(true);
        $this->container->register(\Twig_LoaderInterface::class, ArrayLoader::class);
        $this->container->register('twig', Environment::class)
            ->setPublic(true)
            ->setAutowired(true);
    }

    public function testProcess()
    {
        $this->container->setParameter('kernel.debug', false);
        $this->container->addCompilerPass(new TwigExtensionPass());
        $this->container->compile();

        /** @var Environment $twig */
        $twig = $this->container->get('twig');
        self::assertTrue($twig->hasExtension(IgnoreRoutingNotFoundExtension::class));
        self::assertInstanceOf(
            IgnoreRoutingNotFoundExtension::class,
            $twig->getExtension(IgnoreRoutingNotFoundExtension::class)
        );
    }
}
