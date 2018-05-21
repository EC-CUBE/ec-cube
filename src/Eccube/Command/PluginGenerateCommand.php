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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class PluginGenerateCommand extends Command
{
    protected static $defaultName = 'eccube:plugin:generate';

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->addArgument('name', InputOption::VALUE_REQUIRED, 'plugin name')
            ->addArgument('code', InputOption::VALUE_REQUIRED, 'plugin code')
            ->addArgument('ver', InputOption::VALUE_REQUIRED, 'plugin version')
            ->setDescription('Generate plugin skeleton.');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('name') && null !== $input->getArgument('code') && null !== $input->getArgument('ver')) {
            return;
        }

        $this->io->title('EC-CUBE Plugin Generator Interactive Wizard');

        // Plugin name.
        $name = $input->getArgument('name');
        if (null !== $name) {
            $this->io->text(' > <info>name</info>: '.$name);
        } else {
            $name = $this->io->ask('name', 'EC-CUBE Sample Plugin');
            $input->setArgument('name', $name);
        }

        // Plugin code.
        $code = $input->getArgument('code');
        if (null !== $code) {
            $this->io->text(' > <info>code</info>: '.$code);
        } else {
            $code = $this->io->ask('code', 'Sample', [$this, 'validateCode']);
            $input->setArgument('code', $code);
        }

        // Plugin version.
        $version = $input->getArgument('ver');
        if (null !== $version) {
            $this->io->text(' > <info>ver</info>: '.$version);
        } else {
            $version = $this->io->ask('ver', '1.0.0', [$this, 'validateVersion']);
            $input->setArgument('ver', $version);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $code = $input->getArgument('code');
        $version = $input->getArgument('ver');

        $this->validateCode($code);
        $this->validateVersion($version);

        $pluginDir = $this->container->getParameter('kernel.project_dir').'/app/Plugin/'.$code;
        $fs = new Filesystem();
        $fs->mkdir($pluginDir);

        $configYml = <<<CONFIG
name: $name
code: $code
version: $version
CONFIG;

        $fs->dumpFile($pluginDir.'/config.yml', $configYml);

        $dirs = [
            'Command',
            'Controller',
            'Entity',
            'Repository',
            'EventListener',
            'Form',
            'Resource/doctrine',
            'Resource/locale',
            'Resource/template',
        ];

        foreach ($dirs as $dir) {
            $fs->mkdir($pluginDir.'/'.$dir);
        }

        $this->io->success(sprintf('Plugin was successfully created: %s %s %s', $name, $code, $version));
    }

    public function validateCode($code)
    {
        if (empty($code)) {
            throw new InvalidArgumentException('The code can not be empty.');
        }
        if (strlen($code) > 255) {
            throw new InvalidArgumentException('The code can enter up to 255 characters');
        }
        if (1 !== preg_match('/^\w+$/', $code)) {
            throw new InvalidArgumentException('The code [a-zA-Z_] is available.');
        }

        $pluginDir = $this->container->getParameter('kernel.project_dir').'/app/Plugin/'.$code;
        if (file_exists($pluginDir)) {
            throw new InvalidArgumentException('Plugin directory exists.');
        }

        return $code;
    }

    public function validateVersion($version)
    {
        // TODO
        return $version;
    }
}
