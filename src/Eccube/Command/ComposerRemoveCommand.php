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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ComposerRemoveCommand extends Command
{
    protected static $defaultName = 'eccube:composer:remove';

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
        $this->addArgument('package', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->composerService->execRemove($input->getArgument('package'), $output);

        $io = new SymfonyStyle($input, $output);
        try {
            /* @var Command $command */
            $command = $this->getApplication()->get('cache:clear');

            return $command->run(new ArrayInput([
                'command' => 'cache:clear',
                '--no-warmup' => true,
            ]), $io);
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return 1;
        }
    }
}
