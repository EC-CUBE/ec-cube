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

use Eccube\Entity\ProxyGenerator;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GenerateProxyCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate-proxies')
            ->setDescription('generate entity proxies');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Eccube\Application $app */
        $app = $this->getSilexApplication();

        $scanDirs = [
            $app['config']['root_dir'].'/app/Acme/Entity',
            $app['config']['root_dir'].'/app/Plugin/*/Entity',
        ];
        $outputDir = $app['config']['root_dir'].'/app/proxy/entity';

        $generator = new ProxyGenerator();
        $generator->generate($scanDirs, $outputDir, $output);
    }
}
