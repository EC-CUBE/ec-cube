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

use Doctrine\Common\Collections\Criteria;
use Eccube\Common\Constant;
use Eccube\Repository\PluginRepository;
use Eccube\Service\Composer\ComposerApiService;
use Eccube\Service\PluginApiService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class ComposerRequireAlreadyInstalledPluginsCommand extends Command
{
    protected static $defaultName = 'eccube:composer:require-already-installed';

    /**
     * @var ComposerApiService
     */
    private $composerService;

    /**
     * @var PluginApiService
     */
    private $pluginApiService;

    /**
     * @var PluginRepository
     */
    private $pluginRepository;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(
        ComposerApiService $composerService,
        PluginRepository $pluginRepository,
        PluginApiService $pluginApiService
    ) {
        parent::__construct();
        $this->composerService = $composerService;
        $this->pluginApiService = $pluginApiService;
        $this->pluginRepository = $pluginRepository;
    }

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packageNames = [];
        $unSupportedPlugins = [];

        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('source', 0))
            ->orderBy(['code' => 'ASC']);
        $Plugins = $this->pluginRepository->matching($criteria);

        foreach ($Plugins as $Plugin) {
            $packageNames[] = 'ec-cube/'.$Plugin->getCode().':'.$Plugin->getVersion();
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
                return;
            }
        }

        if ($packageNames) {
            $this->composerService->execRequire(implode(' ', $packageNames), $this->io);
        }
    }
}
