<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Command;

use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand as BaseUpdateSchemaDoctrineCommand;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Eccube\Service\SchemaService;
use Eccube\Util\StringUtil;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Command to generate the SQL needed to update the database schema to match
 * the current mapping information.
 */
class UpdateSchemaDoctrineCommand extends BaseUpdateSchemaDoctrineCommand
{
    /**
     * @var PluginRepository
     */
    protected $pluginRepository;

    /**
     * @var PluginService
     */
    protected $pluginService;

    /**
     * @var SchemaService
     */
    protected $schemaService;

    public function __construct(
        PluginRepository $pluginRepository,
        PluginService $pluginService,
        SchemaService $schemaService
    ) {
        parent::__construct();
        $this->pluginRepository = $pluginRepository;
        $this->pluginService = $pluginService;
        $this->schemaService = $schemaService;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('eccube:schema:update')
            ->setAliases(['doctrine:schema:update'])
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command')
            ->addOption('no-proxy', null, InputOption::VALUE_NONE, 'Does not use the proxy class and behaves the same as the original doctrine:schema:update command');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));
        $noProxy = true === $input->getOption('no-proxy');
        $dumpSql = true === $input->getOption('dump-sql');
        $force = true === $input->getOption('force');

        if ($noProxy || $dumpSql === false && $force === false) {
            return parent::execute($input, $output);
        }

        $tmpProxyOutputDir = sys_get_temp_dir().'/proxy_'.StringUtil::random(12);
        $tmpMetaDataOutputDir = sys_get_temp_dir().'/metadata_'.StringUtil::random(12);

        $generateAllFiles = [];
        try {
            // Generate proxy files of plugins
            $Plugins = $this->pluginRepository->findAll();
            foreach ($Plugins as $Plugin) {
                $config = ['code' => $Plugin->getCode()];
                $this->pluginService->generateProxyAndCallback(function ($generateFiles) use (&$generateAllFiles) {
                    $generateAllFiles = array_merge($generateAllFiles, $generateFiles);
                }, $Plugin, $config, false, $tmpProxyOutputDir);
            }

            $result = null;
            $command = $this;

            // Generate Doctrine metadata and execute schema command
            $this->schemaService->executeCallback(function (SchemaTool $schemaTool, array $metaData) use ($command, $input, $output, &$result) {
                $ui = new SymfonyStyle($input, $output);
                if (empty($metaData)) {
                    $ui->success('No Metadata Classes to process.');
                    $result = 0;
                } else {
                    $result = $command->executeSchemaCommand($input, $output, $schemaTool, $metaData, $ui);
                }
            }, $generateAllFiles, $tmpProxyOutputDir, $tmpMetaDataOutputDir);

            return (int) $result;
        } finally {
            $this->removeOutputDir($tmpMetaDataOutputDir);
            $this->removeOutputDir($tmpProxyOutputDir);
        }
    }

    protected function removeOutputDir($outputDir)
    {
        if (file_exists($outputDir)) {
            $files = Finder::create()
                ->in($outputDir)
                ->files();
            $f = new Filesystem();
            $f->remove($files);
        }
    }
}
