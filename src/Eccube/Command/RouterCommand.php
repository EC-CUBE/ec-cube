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

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;


class RouterCommand extends \Knp\Command\Command
{

    protected $app;

    protected function configure() {
        $this
            ->setName('router:debug')
            ->setDefinition(array(
                new InputArgument('name', InputArgument::OPTIONAL, 'A route name'),
            ))
            ->addOption('sort', null, InputOption::VALUE_OPTIONAL, '[null/ASC/DESC]. If argument orderby set, Default is ASC.')
            ->addOption('orderby', null, InputOption::VALUE_OPTIONAL, '[null/name/path]. If argument sort set, Default is name.')
            ->setDescription('Displays current routes for an application')
            ->setHelp(<<<EOF
The <info>%command.name%</info> displays the configured routes:
  <info>php %command.full_name%</info>
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->app = $this->getSilexApplication();
        $this->app->initialize();
        $this->app->boot();

        $console = new Application();

        $filtername = $input->getArgument('name');
        $sort = $input->getOption('sort');
        $orderby = $input->getOption('orderby');

        $table = $console->getHelperSet()->get('table');
        $table->setHeaders(array('Name', 'Path', 'Pattern'));
        $table->setLayout(TableHelper::LAYOUT_DEFAULT);

        $controllers    = $this->app['controllers'];
        $collection     = $controllers->flush();

        foreach ($collection as $name => $route) {
            if (!empty($filtername) && !preg_match("/$filtername/", $name)) {
                continue;
            }

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

        // 引数で並び替える。
        if (!empty($sort)) {
            $orderby = (!empty($orderby)) ? $orderby : "name";
        }
        if (!empty($orderby)) {
            $sort = (!empty($sort)) ? $sort : "ASC";
        }

        if (strtoupper($orderby) === "NAME") {
            if (strtoupper($sort) === "DESC") {
                krsort($routes);
            } else {
                ksort($routes);
            }
        } else if (strtoupper($orderby) === "PATH") {
            uasort($routes, function($a, $b) {
                return strcmp($a->getPattern(), $b->getPattern());
            });
        }

        $maxName = 4;
        $maxMethod = 6;
        foreach ($routes as $name => $route) {
            if (!empty($filtername) && !preg_match("/$filtername/", $name)) {
                unset($routes[$name]);
                continue;
            }

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
