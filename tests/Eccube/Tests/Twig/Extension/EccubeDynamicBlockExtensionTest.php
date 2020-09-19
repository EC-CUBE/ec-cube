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

namespace Eccube\Tests\Twig\Extension;

use Eccube\Entity\Block;
use Eccube\Repository\BlockRepository;
use Eccube\Tests\EccubeTestCase;
use Eccube\Twig\Extension\EccubeDynamicBlockExtension;
use Eccube\Twig\Extension\TwigIncludeExtension;
use Symfony\Bridge\Twig\Extension\HttpKernelRuntime;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

class EccubeDynamicBlockExtensionTest extends EccubeTestCase
{
    /** @var Environment */
    protected $twig;

    /** @var UrlGeneratorInterface */
    protected $router;

    public function setUp()
    {
        parent::setUp();

        $this->router = $this->createMock(UrlGeneratorInterface::class);
        $this->twig = $this->initializeTwig($this->router);

        $BlockUsingController = new Block();
        $BlockUsingController
            ->setUseController(true)
            ->setFileName('use_controller');
        $this->entityManager->persist($BlockUsingController);

        $BlockWithoutController = new Block();
        $BlockWithoutController
            ->setUseController(false)
            ->setFileName('without_controller');
        $this->entityManager->persist($BlockWithoutController);

        $this->entityManager->flush();
    }

    protected function initializeTwig(UrlGeneratorInterface $router): Environment
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->any())->method('dispatch');
        $twig = new Environment(new ArrayLoader([
            'Block/use_controller.twig' => '{{ value }}',
            'Block/without_controller.twig' => 'foo',
        ]));
        $extensions = [
            new EccubeDynamicBlockExtension($this->container->get(BlockRepository::class)),
            new TwigIncludeExtension($twig),
            new RoutingExtension($router),
        ];
        foreach ($extensions as $extension) {
            $twig->addExtension($extension);
        }

        $runtime = new HttpKernelRuntime($this->container->get('fragment.handler'));
        $runtimeLoader = $this->createMock(RuntimeLoaderInterface::class);
        $runtimeLoader->expects($this->any())->method('load')->willReturn($runtime);
        $twig->addRuntimeLoader($runtimeLoader);

        return $twig;
    }

    public function testEccubeDynamicBlockFunctionWithoutController()
    {
        $template = $this->twig->createTemplate('{{ eccube_dynamic_block("without_controller") }}');
        $this->expected = 'foo';
        $this->actual = $template->render([]);
        $this->verify();
    }

    public function testEccubeDynamicBlockFunctionUsingController()
    {
        $template = $this->twig->createTemplate('{{ eccube_dynamic_block("use_controller") }}');
        $this->markTestSkipped();
    }

    public function testEccubeDynamicBlockFunctionUsingControllerWithParameter()
    {
        $template = $this->twig->createTemplate('{{ eccube_dynamic_block("use_controller", {"param": 5}) }}');
        $this->markTestSkipped();
    }
}
