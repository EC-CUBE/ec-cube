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

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpStormCommand extends \Knp\Command\Command
{
    protected $app;

    public function __construct(\Eccube\Application $app, $name = null)
    {
        parent::__construct($name);
        
        $app['debug'] = true;
        $app->initialize();
        
        // executeでdump($app)を使いたいので.
        $dumper = new \Sorien\Provider\PimpleDumpProvider();
        $app->register($dumper, array('dumper' => $dumper));
        
        $app->boot();
        
        $this->app = $app;
    }

    protected function configure()
    {
        $this
            ->setName('storm:pimple')
            ->setDescription('Generate meta files for PhpStorm')
            ->setHelp(<<<EOF
The <info>%command.name%</info> Generate pimple.json for PhpStorm.
  <info>php %command.full_name%</info>
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // PimpleDumpProviderが5.4以上のため.
        if (!version_compare(phpversion(), "5.4", ">=")){
            return;
        }
        
        $this->app['dumper']->dump($this->app);
        $output->writeln(sprintf("%s <info>success</info>", 'Generate pimple.json'));
    }
}
