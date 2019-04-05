<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

class CacheClearCommand extends \Knp\Command\Command
{

    protected $app;

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

        $this->app = $this->getSilexApplication();
        
        \Eccube\Util\Cache::clear($this->app,$input->getOption('all'));
        $output->writeln(sprintf("%s <info>success</info>", 'cache:clear'));

    }

}
