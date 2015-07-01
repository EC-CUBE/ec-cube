<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;


class CacheClearCommand extends \Knp\Command\Command
{

    protected $app;

    public function __construct(\Eccube\Application $app, $name = null) {
        parent::__construct($name);
        $this->app = $app;
    }

    protected function configure() {
        $this
            ->setName('cache:clear')
            ->setDefinition(array(
                new InputOption('all', '', InputOption::VALUE_NONE, 'Clear all cache.'),
            ))
            ->setDescription('Clear the cache of Application.')
            ->addOption('all', null, InputOption::VALUE_NONE, 'If set, Clear all cache.')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command clears the application cache;
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $cacheDir = $this->app['config']['root_dir'] . '/app/cache';

        $filesystem = new Filesystem();
        if ($input->getOption('all')) {
            $finder = Finder::create()->in($cacheDir)->notName('.gitkeep');
        } else {
            $finder = Finder::create()->in($cacheDir . '/doctrine');
            $filesystem->remove($finder);
            $finder = Finder::create()->in($cacheDir . '/profiler');
            $filesystem->remove($finder);
            $finder = Finder::create()->in($cacheDir . '/twig');
        }

        $filesystem->remove($finder);
        $output->writeln(sprintf("%s <info>success</info>", 'cache:clear'));

    }

}
