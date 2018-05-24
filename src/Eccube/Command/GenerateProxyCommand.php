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
