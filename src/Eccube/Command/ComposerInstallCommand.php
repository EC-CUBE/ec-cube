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

use Eccube\Service\Composer\ComposerApiService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerInstallCommand extends Command
{
    protected static $defaultName = 'eccube:composer:install';

    /**
     * @var ComposerApiService
     */
    private $composerService;

    public function __construct(ComposerApiService $composerService)
    {
        parent::__construct();
        $this->composerService = $composerService;
    }

    protected function configure()
    {
        $this->addOption('dry-run');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->composerService->execInstall($input->getOption('dry-run'), $output);

        return 0;
    }
}
