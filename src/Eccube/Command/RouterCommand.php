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

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Route;


class RouterCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('router:debug')
            ->setDefinition(
                [
                    new InputArgument('name', InputArgument::OPTIONAL, 'A route name'),
                ]
            )
            ->addOption(
                'sort',
                null,
                InputOption::VALUE_OPTIONAL,
                '[null/ASC/DESC]. If argument orderby set, Default is ASC.'
            )
            ->addOption(
                'orderby',
                null,
                InputOption::VALUE_OPTIONAL,
                '[null/name/path]. If argument sort set, Default is name.'
            )
            ->setDescription('Displays current routes for an application')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> displays the configured routes:
<info>php %command.full_name%</info>
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getSilexApplication();

        $filter = $input->getArgument('name');
        $sort = $input->getOption('sort');
        $orderby = $input->getOption('orderby');

        $table = new Table($output);
        $table->setHeaders(['Name', 'Path', 'Method', 'Controller']);

        $routes = $app['routes']->all();

        // 引数で並び替える。
        if (!empty($sort)) {
            $orderby = !empty($orderby) ? $orderby : "name";
        }
        if (!empty($orderby)) {
            $sort = !empty($sort) ? $sort : "ASC";
        }

        if (strtoupper($orderby) === "NAME") {
            if (strtoupper($sort) === "DESC") {
                krsort($routes);
            } else {
                ksort($routes);
            }
        } else {
            if (strtoupper($orderby) === "PATH") {
                uasort(
                    $routes,
                    function ($a, $b) {
                        return strcmp($a->getPath(), $b->getPath());
                    }
                );
            }
        }

        // filterで指定した条件以外を除外
        foreach ($routes as $name => $route) {
            if (!empty($filter) && !preg_match("/$filter/", $name)) {
                unset($routes[$name]);
                continue;
            }
        }

        foreach ($routes as $name => $route) {
            /** @var Route $route */
            $path = $route->getPath();
            $methods = $route->getMethods();
            $methods = empty($methods)
                ? 'ANY'
                : implode(',', $methods);
            $controller = $route->getDefault('_controller');

            $table->addRow(
                [
                    $name,
                    $path,
                    $methods,
                    $controller,
                ]
            );
        }

        $table->render($output);
    }

}
