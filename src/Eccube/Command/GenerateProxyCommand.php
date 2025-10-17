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

namespace Eccube\Command;

use Eccube\Common\EccubeConfig;
use Eccube\Service\EntityProxyService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'eccube:generate:proxies', description: 'Generate entity proxies')]
class GenerateProxyCommand extends Command
{
    /**
     * @var EntityProxyService
     */
    private $entityProxyService;

    /**
     * @var EccubeConfig
     */
    private $eccubeConfig;

    public function __construct(EntityProxyService $entityProxyService, EccubeConfig $eccubeConfig)
    {
        parent::__construct();
        $this->entityProxyService = $entityProxyService;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * @return void
     */
    #[\Override]
    protected function configure(): void
    {
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectDir = $this->eccubeConfig->get('kernel.project_dir');
        $includeDirs = [$projectDir.'/app/Customize/Entity'];

        $enabledPlugins = $this->eccubeConfig->get('eccube.plugins.enabled');
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

        return 0;
    }
}
