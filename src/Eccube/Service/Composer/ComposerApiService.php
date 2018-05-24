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
use Eccube\Exception\PluginException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

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

    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * Run get info command
     *
     * @param string $pluginName format foo/bar or foo/bar:1.0.0 or "foo/bar 1.0.0"
     *
     * @return array
     */
    public function execInfo($pluginName)
    {
        $output = $this->runCommand([
            'command' => 'info',
            'package' => $pluginName,
        ]);

        return OutputParser::parseInfo($output);
    }

    /**
     * Run execute command
     *
     * @param string $packageName format "foo/bar foo/bar:1.0.0"
     *
     * @throws PluginException
     */
    public function execRequire($packageName)
    {
        $packageName = explode(' ', trim($packageName));
        $this->runCommand([
            'command' => 'require',
            'packages' => $packageName,
            '--no-interaction' => true,
            '--profile' => true,
            '--prefer-dist' => true,
            '--ignore-platform-reqs' => true,
            '--update-with-dependencies' => true,
        ]);
    }

    /**
     * Run remove command
     *
     * @param string $packageName format "foo/bar foo/bar:1.0.0"
     *
     * @throws PluginException
     */
    public function execRemove($packageName)
    {
        $packageName = explode(' ', trim($packageName));
        $this->runCommand([
            'command' => 'remove',
            'packages' => $packageName,
            '--ignore-platform-reqs' => true,
            '--no-interaction' => true,
            '--profile' => true,
        ]);
    }

    /**
     * Get require
     *
     * @param string $packageName
     * @param string $callback
     * @param null   $typeFilter
     */
    public function foreachRequires($packageName, $callback, $typeFilter = null)
    {
        $info = $this->execInfo($packageName);
        if (isset($info['requires'])) {
            foreach ($info['requires'] as $name => $version) {
                $package = $this->execInfo($name);
                if (is_null($typeFilter) || @$package['type'] === $typeFilter) {
                    $callback($package);
                }
            }
        }
    }

    /**
     * Run get config information
     *
     * @param string $key
     * @param null   $value
     *
     * @return array|mixed
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
        $output = $this->runCommand($commands);

        return OutputParser::parseConfig($output);
    }

    /**
     * Get config list
     *
     * @return array
     */
    public function getConfig()
    {
        $output = $this->runCommand([
            'command' => 'config',
            '--list' => true,
        ]);

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
     * @throws PluginException
     *
     * @param array $commands
     *
     * @return string
     */
    public function runCommand($commands)
    {
        $this->init();
        $commands['--working-dir'] = $this->workingDir;
        $commands['--no-ansi'] = 1;
        $input = new ArrayInput($commands);
        $output = new BufferedOutput();

        $exitCode = $this->consoleApplication->run($input, $output);

        $log = $output->fetch();
        if ($exitCode) {
            log_error($log);
            throw new PluginException($log);
        }
        log_info($log, $commands);

        return $log;
    }

    /**
     * Init composer console application
     */
    private function init()
    {
        set_time_limit(0);
        @ini_set('memory_limit', '1536M');
        // Config for some environment
        putenv('COMPOSER_HOME='.$this->eccubeConfig['plugin_realdir'].'/.composer');
        $consoleApplication = new Application();
        $consoleApplication->resetComposer();
        $consoleApplication->setAutoExit(false);
        $this->consoleApplication = $consoleApplication;
        $this->workingDir = $this->workingDir ? $this->workingDir : $this->eccubeConfig['root_dir'];
    }

    /**
     * Get version of composer
     *
     * @return null|string
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
     * @return mixed|string
     */
    public function getMode()
    {
        return 'API';
    }
}
