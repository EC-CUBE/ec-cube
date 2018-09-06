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

namespace Eccube\Service\Composer;

use Composer\Console\Application;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Exception\PluginException;
use Eccube\Repository\BaseInfoRepository;
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

    public function __construct(EccubeConfig $eccubeConfig, BaseInfoRepository $baseInfoRepository)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->baseInfoRepository = $baseInfoRepository;
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
     * @param null|OutputInterface $output
     *
     * @return string
     *
     * @throws PluginException
     */
    public function execRequire($packageName, $output = null)
    {
        $packageName = explode(' ', trim($packageName));

        return $this->runCommand([
            'command' => 'require',
            'packages' => $packageName,
            '--no-interaction' => true,
            '--profile' => true,
            '--prefer-dist' => true,
            '--ignore-platform-reqs' => true,
            '--update-with-dependencies' => true,
            '--no-scripts' => true,
        ], $output);
    }

    /**
     * Run remove command
     *
     * @param string $packageName format "foo/bar foo/bar:1.0.0"
     * @param null|OutputInterface $output
     *
     * @return string
     *
     * @throws PluginException
     */
    public function execRemove($packageName, $output = null)
    {
        $packageName = explode(' ', trim($packageName));

        return $this->runCommand([
            'command' => 'remove',
            'packages' => $packageName,
            '--ignore-platform-reqs' => true,
            '--no-interaction' => true,
            '--profile' => true,
            '--no-scripts' => true,
        ], $output);
    }

    /**
     * Run update command
     *
     * @param boolean $dryRun
     * @param null|OutputInterface $output
     *
     * @throws PluginException
     */
    public function execUpdate($dryRun, $output = null)
    {
        $this->runCommand([
            'command' => 'update',
            '--no-interaction' => true,
            '--profile' => true,
            '--no-scripts' => true,
            '--dry-run' => (bool) $dryRun,
        ], $output);
    }

    /**
     * Run install command
     *
     * @param boolean $dryRun
     * @param null|OutputInterface $output
     *
     * @throws PluginException
     */
    public function execInstall($dryRun, $output = null)
    {
        $this->runCommand([
            'command' => 'install',
            '--no-interaction' => true,
            '--profile' => true,
            '--no-scripts' => true,
            '--dry-run' => (bool) $dryRun,
        ], $output);
    }

    /**
     * Get require
     *
     * @param string $packageName
     * @param string|null $version
     * @param string $callback
     * @param null $typeFilter
     * @param int $level
     *
     * @throws PluginException
     */
    public function foreachRequires($packageName, $version, $callback, $typeFilter = null, $level = 0)
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
     */
    public function execConfig($key, $value = null)
    {
        $commands = [
            'command' => 'config',
            'setting-key' => $key,
            'setting-value' => $value,
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
     * @param null|OutputInterface $output
     *
     * @return string
     *
     * @throws PluginException
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
    }

    /**
     * Init composer console application
     * @param BaseInfo|null $BaseInfo
     * @throws PluginException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function init($BaseInfo = null)
    {
        $BaseInfo = $BaseInfo ?: $this->baseInfoRepository->get();

        set_time_limit(0);
        ini_set('memory_limit', '-1');
        // Config for some environment
        putenv('COMPOSER_HOME='.$this->eccubeConfig['plugin_realdir'].'/.composer');
        $this->initConsole();
        $this->workingDir = $this->workingDir ? $this->workingDir : $this->eccubeConfig['kernel.project_dir'];
        $url = $this->eccubeConfig['eccube_package_api_url'];
        $json = json_encode([
            'type' => 'composer',
            'url' => $url,
            'options' => [
                'http' => [
                    'header' => ['X-ECCUBE-KEY: '.$BaseInfo->getAuthenticationKey()]
                ]
            ]
        ]);
        $this->execConfig('repositories.eccube', [$json]);
        if (strpos($url, 'http://') == 0) {
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

    public function configureRepository(BaseInfo $BaseInfo)
    {
        $this->init($BaseInfo);
    }

    /**
     * Get version of composer
     *
     * @return null|string
     *
     * @throws PluginException
     */
    public function composerVersion()
    {
        $this->init();
        $output = $this->runCommand([
            '--version' => true,
        ]);

        return OutputParser::parseComposerVersion($output);
    }

    /**
     * Get mode
     *
     * @return string
     */
    public function getMode()
    {
        return 'API';
    }
}
