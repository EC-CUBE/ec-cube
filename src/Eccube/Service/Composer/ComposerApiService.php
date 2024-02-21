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

namespace Eccube\Service\Composer;

use Composer\Console\Application;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Exception\PluginException;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\PluginContext;
use Eccube\Service\SchemaService;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ComposerApiService
 */
class ComposerApiService implements ComposerServiceInterface
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var Application
     */
    private $consoleApplication;

    private $workingDir;
    /**
     * @var BaseInfoRepository
     */
    private $baseInfoRepository;

    /** @var SchemaService */
    private $schemaService;

    /**
     * @var PluginContext
     */
    private $pluginContext;

    public function __construct(
        EccubeConfig $eccubeConfig,
        BaseInfoRepository $baseInfoRepository,
        SchemaService $schemaService,
        PluginContext $pluginContext
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->schemaService = $schemaService;
        $this->baseInfoRepository = $baseInfoRepository;
        $this->pluginContext = $pluginContext;
    }

    /**
     * Run get info command
     *
     * @param string $pluginName format foo/bar or foo/bar:1.0.0 or "foo/bar 1.0.0"
     * @param string|null $version
     *
     * @return array
     *
     * @throws PluginException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function execInfo($pluginName, $version)
    {
        $output = $this->runCommand([
            'command' => 'info',
            'package' => $pluginName,
            'version' => $version,
            '--available' => true,
        ]);

        return OutputParser::parseInfo($output);
    }

    /**
     * Run execute command
     *
     * @param string $packageName format "foo/bar foo/bar:1.0.0"
     * @param OutputInterface|null $output
     * @param string|null $from
     *
     * @return string
     *
     * @throws PluginException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function execRequire($packageName, $output = null, $from = null)
    {
        $packageName = explode(' ', trim($packageName));

        $this->init(null, $packageName, $from);
        $this->execConfig('allow-plugins.symfony/flex', ['false']);

        try {
            return $this->runCommand([
                'command' => 'require',
                'packages' => $packageName,
                '--no-interaction' => true,
                '--profile' => true,
                '--prefer-dist' => true,
                '--update-with-dependencies' => true,
                '--no-scripts' => true,
                '--update-no-dev' => env('APP_ENV') === 'prod',
            ], $output, false);
        } finally {
            $this->execConfig('allow-plugins.symfony/flex', ['true']);
        }
    }

    /**
     * Run remove command
     *
     * @param string $packageName format "foo/bar foo/bar:1.0.0"
     * @param OutputInterface|null $output
     *
     * @return string
     *
     * @throws PluginException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function execRemove($packageName, $output = null)
    {
        $this->dropTableToExtra($packageName);

        $packageName = explode(' ', trim($packageName));

        $this->init();
        $this->execConfig('allow-plugins.symfony/flex', ['false']);

        try {
            return $this->runCommand([
            'command' => 'remove',
            'packages' => $packageName,
            '--ignore-platform-reqs' => true,
            '--no-interaction' => true,
            '--profile' => true,
            '--no-scripts' => true,
            '--update-no-dev' => env('APP_ENV') === 'prod',
            ], $output, false);
        } finally {
            $this->execConfig('allow-plugins.symfony/flex', ['true']);
        }
    }

    /**
     * Run update command
     *
     * @param boolean $dryRun
     * @param OutputInterface|null $output
     *
     * @throws PluginException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function execUpdate($dryRun, $output = null)
    {
        $this->init();
        $this->execConfig('allow-plugins.symfony/flex', ['false']);

        try {
            $this->runCommand([
            'command' => 'update',
            '--no-interaction' => true,
            '--profile' => true,
            '--no-scripts' => true,
            '--dry-run' => (bool) $dryRun,
            '--no-dev' => env('APP_ENV') === 'prod',
            ], $output, false);
        } finally {
            $this->execConfig('allow-plugins.symfony/flex', ['true']);
        }
    }

    /**
     * Run install command
     *
     * @param boolean $dryRun
     * @param OutputInterface|null $output
     *
     * @throws PluginException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function execInstall($dryRun, $output = null)
    {
        $this->init();
        $this->execConfig('allow-plugins.symfony/flex', ['false']);

        try {
            $this->runCommand([
            'command' => 'install',
            '--no-interaction' => true,
            '--profile' => true,
            '--no-scripts' => true,
            '--dry-run' => (bool) $dryRun,
            '--no-dev' => env('APP_ENV') === 'prod',
            ], $output, false);
        } finally {
            $this->execConfig('allow-plugins.symfony/flex', ['true']);
        }
    }

    /**
     * Get require
     *
     * @param string $packageName
     * @param string|null $version
     * @param string $callback
     * @param null $typeFilter
     * @param int $level
     * @return void
     *
     * @throws PluginException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function foreachRequires($packageName, $version, $callback, $typeFilter = null, $level = 0): void
    {
        if (strpos($packageName, '/') === false) {
            return;
        }
        $info = $this->execInfo($packageName, $version);
        if (isset($info['requires'])) {
            foreach ($info['requires'] as $name => $version) {
                if (isset($info['type']) && $info['type'] === $typeFilter) {
                    $this->foreachRequires($name, $version, $callback, $typeFilter, $level + 1);
                    if (isset($info['descrip.'])) {
                        $info['description'] = $info['descrip.'];
                    }
                    if ($level) {
                        $callback($info);
                    }
                }
            }
        }
    }

    /**
     * Run get config information
     *
     * @param string $key
     * @param null $value
     *
     * @return array|mixed
     *
     * @throws PluginException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function execConfig($key, $value = null)
    {
        $commands = [
            'command' => 'config',
            'setting-key' => $key,
            'setting-value' => $value,
            '--no-interaction' => true,
        ];
        if ($value) {
            $commands['setting-value'] = $value;
        }
        $output = $this->runCommand($commands, null, false);

        return OutputParser::parseConfig($output);
    }

    /**
     * Get config list
     *
     * @return array
     *
     * @throws PluginException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getConfig()
    {
        $output = $this->runCommand([
            'command' => 'config',
            '--list' => true,
        ], null, false);

        return OutputParser::parseList($output);
    }

    /**
     * Set work dir
     *
     * @param string $workingDir
     */
    public function setWorkingDir($workingDir)
    {
        $this->workingDir = $workingDir;
    }

    /**
     * Run composer command
     *
     * @param array $commands
     * @param OutputInterface|null $output
     * @param bool $init
     *
     * @return string
     *
     * @throws PluginException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function runCommand($commands, $output = null, $init = true)
    {
        if ($init) {
            $this->init();
        }
        $commands['--working-dir'] = $this->workingDir;
        $commands['--no-ansi'] = true;
        $input = new ArrayInput($commands);
        $useBufferedOutput = $output === null;

        if ($useBufferedOutput) {
            $output = new BufferedOutput();
            ob_start(function ($buffer) use ($output) {
                $output->write($buffer);

                return null;
            });
        }

        $exitCode = $this->consoleApplication->run($input, $output);

        if ($useBufferedOutput) {
            ob_end_clean();
            $log = $output->fetch();
            if ($exitCode) {
                log_error($log);
                throw new PluginException($log);
            }
            log_info($log, $commands);

            return $log;
        } elseif ($exitCode) {
            throw new PluginException();
        }

        return null;
    }

    /**
     * Init composer console application
     *
     * @param BaseInfo|null $BaseInfo
     * @param string[] $packageName
     * @param string|null $from
     *
     * @throws PluginException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function init($BaseInfo = null, $packageName = [], $from = null)
    {
        $BaseInfo = $BaseInfo ?: $this->baseInfoRepository->get();

        set_time_limit(0);

        $composerMemory = $this->eccubeConfig['eccube_composer_memory_limit'];
        ini_set('memory_limit', $composerMemory);

        // Config for some environment
        putenv('COMPOSER_HOME='.$this->eccubeConfig['plugin_realdir'].'/.composer');
        $this->initConsole();
        $this->workingDir = $this->workingDir ? $this->workingDir : $this->eccubeConfig['kernel.project_dir'];
        $url = $this->eccubeConfig['eccube_package_api_url'];
        $config = $this->getConfig();
        $eccube_repository = [
            'type' => 'composer',
            'url' => $url,
            'options' => [
                'http' => [
                    'header' => ['X-ECCUBE-KEY: '.$BaseInfo->getAuthenticationKey()],
                ],
            ],
        ];
        $exclude = [];
        if (array_key_exists('eccube', $config['repositories'])
            && array_key_exists('exclude', $config['repositories']['eccube'])) {
            $exclude = array_map(
                function ($package) {
                    return trim($package);
                },
                explode(',', str_replace(['[', ']'], '', $config['repositories']['eccube']['exclude']))
            );
        }

        if ($from !== null) {
            $exclude = array_unique(array_merge($exclude, [trim(current($packageName))]));
            $this->execConfig('repositories.'.str_replace(['.', '/'], '', strtolower($from)), [json_encode([
                'type' => 'path',
                'url' => $from,
            ])]);
        }

        if (!empty($exclude)) {
            $eccube_repository['exclude'] = $exclude;
        }

        $this->execConfig('platform.php', [PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'.'.PHP_RELEASE_VERSION]);
        $this->execConfig('repositories.eccube', [json_encode($eccube_repository)]);

        if (strpos($url, 'http://') === 0) {
            $this->execConfig('secure-http', ['false']);
        }
        $this->initConsole();
    }

    private function initConsole()
    {
        $consoleApplication = new Application();
        $consoleApplication->resetComposer();
        $consoleApplication->setAutoExit(false);
        $this->consoleApplication = $consoleApplication;
    }

    /**
     * @param BaseInfo $BaseInfo
     * @return void
     * @throws PluginException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function configureRepository(BaseInfo $BaseInfo): void
    {
        $this->init($BaseInfo);
    }

    private function dropTableToExtra($packageNames)
    {
        $projectRoot = $this->eccubeConfig->get('kernel.project_dir');

        foreach (explode(' ', trim($packageNames)) as $packageName) {
            $pluginCode = null;
            // 大文字小文字を区別するファイルシステムを考慮して, ディレクトリ名からプラグインコードを取得する
            foreach (glob($projectRoot.'/app/Plugin/*', GLOB_ONLYDIR) as $dir) {
                if (strtolower(basename($dir)) === strtolower(basename($packageName))) {
                    $pluginCode = basename($dir);
                    break;
                }
            }
            if ($pluginCode === null) {
                throw new PluginException($packageName.' not found');
            }

            $this->pluginContext->setCode($pluginCode);
            $this->pluginContext->setUninstall();

            foreach ($this->pluginContext->getExtraEntityNamespaces() as $namespace) {
                $this->schemaService->dropTable($namespace);
            }
        }
    }
}
