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

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Eccube\Common\Constant;
use Eccube\Repository\PluginRepository;
use Eccube\Service\Composer\ComposerApiService;
use Eccube\Service\PluginApiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'eccube:composer:require-already-installed')]
class ComposerRequireAlreadyInstalledPluginsCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly ComposerApiService $composerService,
        private readonly PluginRepository $pluginRepository,
        private readonly PluginApiService $pluginApiService,
    ) {
        parent::__construct();
    }

    #[\Override]
    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $packageNames = [];
        $unSupportedPlugins = [];

        $criteria = Criteria::create()->where(Criteria::expr()->notIn('source', ['', '0']))->orderBy(['code' => Order::Ascending]);
        $Plugins = $this->pluginRepository->matching($criteria);

        foreach ($Plugins as $Plugin) {
            $packageNames[] = 'ec-cube/'.strtolower((string) $Plugin->getCode()).':'.$Plugin->getVersion();
            $data = $this->pluginApiService->getPlugin($Plugin->getCode());
            if (isset($data['version_check']) && !$data['version_check']) {
                $unSupportedPlugins[] = $Plugin;
            }
        }

        foreach ($unSupportedPlugins as $Plugin) {
            $message = trans('command.composer_require_already_installed.not_supported_plugin', [
                '%name%' => $Plugin->getName(),
                '%plugin_version%' => $Plugin->getVersion(),
                '%eccube_version%' => Constant::VERSION,
            ]);
            $question = new ConfirmationQuestion($message);
            if (!$this->io->askQuestion($question)) {
                return Command::SUCCESS;
            }
        }

        if ($packageNames) {
            $this->composerService->execRequire(implode(' ', $packageNames), $this->io);
        }

        return Command::SUCCESS;
    }
}
