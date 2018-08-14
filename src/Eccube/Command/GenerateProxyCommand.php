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

namespace Eccube\Command;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Eccube\Service\EntityProxyService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateProxyCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'eccube:generate:proxies';

    /**
     * @var EntityProxyService
     */
    private $entityProxyService;

    public function __construct(EntityProxyService $entityProxyService)
    {
        parent::__construct();
        $this->entityProxyService = $entityProxyService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate entity proxies');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // アノテーションを読み込めるように設定.
        AnnotationRegistry::registerAutoloadNamespace('Eccube\Annotation', __DIR__.'/../../../src');

        $container = $this->getContainer();
        $projectDir = $container->getParameter('kernel.project_dir');
        $includeDirs = [$projectDir.'/app/Customize/Entity'];

        $enabledPlugins = $container->getParameter('eccube.plugins.enabled');
        foreach ($enabledPlugins as $code) {
            if (file_exists($projectDir.'/app/Plugin/'.$code.'/Entity')) {
                $includeDirs[] = $projectDir.'/app/Plugin/'.$code.'/Entity';
            }
        }

        $this->entityProxyService->generate(
            $includeDirs,
            [],
            $projectDir.'/app/proxy/entity',
            $output
        );
    }
}
