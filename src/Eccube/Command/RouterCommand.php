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

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;


class RouterCommand extends \Knp\Command\Command
{

    protected $app;

    public function __construct(\Eccube\Application $app, $name = null) {
        parent::__construct($name);
        $this->app = $app;
    }

    protected function configure() {
        $this
            ->setName('router:debug')
            ->setDefinition(array(
                new InputArgument('name', InputArgument::OPTIONAL, 'A route name'),
            ))
            ->setDescription('Displays current routes for an application')
            ->setHelp(<<<EOF
The <info>%command.name%</info> displays the configured routes:
  <info>php %command.full_name%</info>
EOF
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->app->initialize();
        $this->app->boot();

        $console = new Application();

        $table = $console->getHelperSet()->get('table');
        $table->setHeaders(array('Name', 'Path', 'Pattern'));
        $table->setLayout(TableHelper::LAYOUT_DEFAULT);

        $controllers    = $this->app['controllers'];
        $collection     = $controllers->flush();

        foreach ($collection as $name => $route) {
            $requirements = array();
            foreach ($route->getRequirements() as $key => $requirement) {
                // $requirements[] = $key . ' => ' . $requirement;
                $requirements[] = $requirement;
            }

            $table->addRow(array(
                $name,
                $route->getPath(),
                join(', ', $requirements)
            ));

        }


        $routes = $this->app['routes']->all();

        $maxName = 4;
        $maxMethod = 6;
        foreach ($routes as $name => $route) {
            $requirements = $route->getRequirements();
            $method = isset($requirements['_method'])
                ? strtoupper(is_array($requirements['_method'])
                    ? implode(', ', $requirements['_method']) : $requirements['_method']
                )
                : 'ANY';

            if (strlen($name) > $maxName) {
                $maxName = strlen($name);
            }

            if (strlen($method) > $maxMethod) {
                $maxMethod = strlen($method);
            }
        }

        foreach ($routes as $name => $route) {
            $requirements = $route->getRequirements();
            $method = isset($requirements['_method'])
                ? strtoupper(is_array($requirements['_method'])
                    ? implode(', ', $requirements['_method']) : $requirements['_method']
                )
                : 'ANY';

            $table->addRow(array(
                $name,
                $route->getPattern(),
                $method,
            ));
        }

        $table->render($output);
    }

}
