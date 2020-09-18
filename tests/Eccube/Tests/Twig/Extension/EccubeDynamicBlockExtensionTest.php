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

use Eccube\Application;
use Eccube\Entity\Block;
use Eccube\Repository\BlockRepository;
use Eccube\Tests\EccubeTestCase;
use Eccube\Twig\Environment;
use Eccube\Twig\Extension\EccubeDynamicBlockExtension;
use Eccube\Twig\Extension\TwigIncludeExtension;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Loader\ArrayLoader;

class EccubeDynamicBlockExtensionTest extends EccubeTestCase
{
    /** @var Environment */
    protected $twig;

    public function setUp()
    {
        parent::setUp();

        $this->twig = $this->initializeTwig();

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

    protected function initializeTwig(): \Twig_Environment
    {
        $app = static::createClient()->getContainer()->get('app');
        assert($app instanceof Application);

        $loader = new ArrayLoader();
        $loader->setTemplate('Block/use_controller.twig', '{{ value }}');
        $loader->setTemplate('Block/without_controller.twig', 'foo');
        $dispatcher = $app['dispatcher'];
        assert($dispatcher instanceof EventDispatcherInterface);
        $twig = new \Twig_Environment($loader);
        $eccubeTwig = new Environment($twig, $dispatcher);
        $extensions = [
            new EccubeDynamicBlockExtension($this->container->get(BlockRepository::class)),
            new TwigIncludeExtension($eccubeTwig),
        ];
        foreach ($extensions as $extension) {
            $twig->addExtension($extension);
        }

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
        // TODO
        $this->markTestSkipped();
    }

    public function testEccubeDynamicBlockFunctionUsingControllerWithParameter()
    {
        // TODO
        $this->markTestSkipped();
    }
}
