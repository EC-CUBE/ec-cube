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
use Eccube\Service\EntityProxyService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GenerateProxyCommand extends ContainerAwareCommand
{
    /**
     * @var EntityProxyService
     */
    private $entityProxyService;

    public function __construct(EntityProxyService $entityProxyService)
    {
        parent::__construct();
        $this->entityProxyService = $entityProxyService;
    }

    protected function configure()
    {
        $this
            ->setName('eccube:generate:proxies')
            ->setDescription('Generate entity proxies');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO プラグインディレクトリ
//        $dirs = array_map(function($p) use ($app) {
//            return $app['config']['root_dir'].'/app/Plugin/'.$p->getCode().'/Entity';
//        }, $app[PluginRepository::class]->findAllEnabled());
//

        $projectRoot = $this->getContainer()->getParameter('kernel.project_dir');
        $this->entityProxyService->generate(
            [$projectRoot.'/app/Acme/Entity'], // TODO Acme
            [],
            $projectRoot.'/app/proxy/entity',
            $output
        );
    }
}
