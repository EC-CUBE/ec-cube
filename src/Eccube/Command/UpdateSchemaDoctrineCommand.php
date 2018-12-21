<?php

namespace Eccube\Command;

use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand as BaseUpdateSchemaDoctrineCommand;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;
use Eccube\Doctrine\ORM\Mapping\Driver\ReloadSafeAnnotationDriver;
use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Eccube\Service\SchemaService;
use Eccube\Util\StringUtil;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
            ->setName('doctrine:schema:update')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));
        $tmpProxyOutputDir = sys_get_temp_dir().'/proxy_'.StringUtil::random(12);
        $outputDir = sys_get_temp_dir().'/proxy_'.StringUtil::random(12);
        $generateAllFiles = [];
        try {
            $Plugins = $this->pluginRepository->findAll();
            foreach ($Plugins as $Plugin) {
                $config = ['code' => $Plugin->getCode()];
                $this->pluginService->generateProxyAndCallback(function ($generateFiles) use (&$generateAllFiles) {
                    $generateAllFiles = array_merge($generateAllFiles, $generateFiles);
                }, $Plugin, $config, false, $tmpProxyOutputDir);
            }

            $result = null;
            $command = $this;
            $this->schemaService->executeCallback(function ($schemaTool, $metaData) use ($command, $input, $output, &$result) {
                $ui = new SymfonyStyle($input, $output);
                $result = $command->executeSchemaCommand($input, $output, $schemaTool, $metaData, $ui);
            }, $generateAllFiles, $tmpProxyOutputDir, $outputDir);

            return $result;
        } finally {
            if (file_exists($outputDir)) {
                foreach (glob("${outputDir}/*") as $f) {
                    unlink($f);
                }
                rmdir($outputDir);
            }

            if (file_exists($tmpProxyOutputDir)) {
                foreach (glob("${tmpProxyOutputDir}/*") as $f) {
                    unlink($f);
                }
                rmdir($tmpProxyOutputDir);
            }
        }
    }
}
