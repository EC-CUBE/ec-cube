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
use Twig\Loader\LoaderInterface;

class TwigExtensionPassTest extends TestCase
{
    /** @var ContainerBuilder */
    protected $containerBuilder;

    public function setUp()
    {
        $this->containerBuilder = new ContainerBuilder();

        $this->containerBuilder->register(RouteCollection::class);
        $this->containerBuilder->register(RequestContext::class);
        $this->containerBuilder->register(UrlGeneratorInterface::class, UrlGenerator::class)
            ->setAutowired(true);
        $this->containerBuilder->register(IgnoreRoutingNotFoundExtension::class)
            ->setAutowired(true);
        $this->containerBuilder->register(LoaderInterface::class, ArrayLoader::class);
        $this->containerBuilder->register('twig', Environment::class)
            ->setPublic(true)
            ->setAutowired(true);
    }

    public function testProcess()
    {
        $this->containerBuilder->setParameter('kernel.debug', false);
        $this->containerBuilder->addCompilerPass(new TwigExtensionPass());
        $this->containerBuilder->compile();

        /** @var Environment $twig */
        $twig = $this->containerBuilder->get('twig');
        self::assertTrue($twig->hasExtension(IgnoreRoutingNotFoundExtension::class));
        self::assertInstanceOf(
            IgnoreRoutingNotFoundExtension::class,
            $twig->getExtension(IgnoreRoutingNotFoundExtension::class)
        );
    }
}
