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

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Exception\PluginException;
use Eccube\Service\SystemService;

/**
 * Class ComposerProcessService
 */
class ComposerProcessService implements ComposerServiceInterface
{
    /**
     * @var EccubeConfig config parameter
     */
    protected $eccubeConfig;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    private $workingDir;
    private $composerFile;
    private $composerSetup;
    private $pathPHP;

    /**
     * ComposerProcessService constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param EntityManagerInterface $entityManager
     * @param SystemService $systemService
     */
    public function __construct(EccubeConfig $eccubeConfig, EntityManagerInterface $entityManager, SystemService $systemService)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->entityManager = $entityManager;
        $this->pathPHP = $systemService->getPHP();
    }

    /**
     * This function to install a plugin by composer require
     *
     * @param string $packageName format "foo/bar foo/bar2:1.0.0"
     *
     * @throws PluginException
     */
    public function execRequire($packageName)
    {
        set_time_limit(0);
        $this->init();

        // Build command
        $command = $this->pathPHP.' '.$this->composerFile.' require '.$packageName;
        $command .= ' --prefer-dist --no-progress --no-suggest --no-scripts --ignore-platform-reqs --update-with-dependencies --profile --no-ansi --no-interaction -d ';
        $command .= $this->workingDir.' 2>&1';
        log_info($command);
        $this->runCommand($command);
    }

    /**
     * This function to remove a plugin by composer remove
     *
     * @param string $packageName format "foo/bar foo/bar2"
     *
     * @throws PluginException
     */
    public function execRemove($packageName)
    {
        set_time_limit(0);
        $this->init();

        // Build command
        $command = $this->pathPHP.' '.$this->composerFile.' remove '.$packageName;
        $command .= ' --no-progress --no-scripts --ignore-platform-reqs --profile --no-ansi --no-interaction -d ';
        $command .= $this->workingDir.' 2>&1';
        log_info($command);

        // Execute command
        $this->runCommand($command);
    }

    /**
     * Run command
     *
     * @throws PluginException
     *
     * @param string $command
     */
    public function runCommand($command)
    {
        $output = [];
        try {
            // Execute command
            $returnValue = -1;
            exec($command, $output, $returnValue);

            $outputString = implode(PHP_EOL, $output);
            if ($returnValue) {
                throw new PluginException($outputString);
            }
            log_info(PHP_EOL.$outputString.PHP_EOL);
        } catch (\Exception $exception) {
            throw new PluginException($exception->getMessage());
        }
    }

    /**
     * Set working dir
     *
     * @param string $workingDir
     */
    public function setWorkingDir($workingDir)
    {
        $this->workingDir = $workingDir;
    }

    /**
     * Set init
     *
     * @throws PluginException
     */
    private function init()
    {
        if (!$this->isPhpCommandLine()) {
            throw new PluginException('Php cli not found.');
        }

        $composerMemory = $this->eccubeConfig['eccube_composer_memory_limit'];
        if (!$this->isSetCliMemoryLimit()) {
            $cliMemoryLimit = $this->getCliMemoryLimit();
            if ($cliMemoryLimit < $composerMemory && $cliMemoryLimit != -1) {
                throw new PluginException('Not enough memory limit.');
            }
        }

        /**
         * Mysql lock in transaction
         *
         * @see https://dev.mysql.com/doc/refman/5.7/en/lock-tables.html
         *
         * @var EntityManagerInterface
         */
        $em = $this->entityManager;
        if ($em->getConnection()->isTransactionActive()) {
            $em->getConnection()->commit();
            $em->getConnection()->beginTransaction();
        }

        @ini_set('memory_limit', $composerMemory.'M');
        // Config for some environment
        putenv('COMPOSER_HOME='.$this->eccubeConfig['plugin_realdir'].'/.composer');
        $this->workingDir = $this->workingDir ? $this->workingDir : $this->eccubeConfig['root_dir'];
        $this->setupComposer();
    }

    /**
     * Check composer file and setup it
     */
    private function setupComposer()
    {
        $this->composerFile = $this->workingDir.'/composer.phar';
        $this->composerSetup = $this->workingDir.'/composer-setup.php';
        if (!file_exists($this->composerFile)) {
            if (!file_exists($this->composerSetup)) {
                $result = copy('https://getcomposer.org/installer', $this->composerSetup);
                log_info($this->composerSetup.' : '.$result);
            }
            $command = $this->pathPHP.' '.$this->composerSetup;
            $this->runCommand($command);

            unlink($this->composerSetup);
        }
    }

    /**
     * Get grep memory_limit | Megabyte
     *
     * @return int|string
     */
    private function getCliMemoryLimit()
    {
        $grepMemory = exec($this->pathPHP.' -i | grep "memory_limit"');
        if ($grepMemory) {
            $grepMemory = explode('=>', $grepMemory);

            // -1 unlimited
            if (trim($grepMemory[2]) == -1) {
                return -1;
            }

            $exp = preg_split('#(?<=\d)(?=[a-z])#i', $grepMemory[2]);
            $memo = trim($exp[0]);
            if ($exp[1] == 'M') {
                return $memo;
            } else {
                if ($exp[1] == 'GB') {
                    return $memo * 1024;
                } else {
                    return 0;
                }
            }
        }

        return 0;
    }

    /**
     * Check to set new value grep "memory_limit"
     *
     * @return bool
     */
    private function isSetCliMemoryLimit()
    {
        $oldMemory = exec($this->pathPHP.' -i | grep "memory_limit"');
        $tmpMem = '1.5GB';
        if ($oldMemory) {
            $memory = explode('=>', $oldMemory);
            $originGrepMemory = trim($memory[2]);

            if ($originGrepMemory == $tmpMem) {
                $tmpMem = '1.49GB';
            }

            $newMemory = exec($this->pathPHP.' -d memory_limit='.$tmpMem.' -i | grep "memory_limit"');
            if ($newMemory) {
                $newMemory = explode('=>', $newMemory);
                $grepNewMemory = trim($newMemory[2]);
                if ($grepNewMemory != $originGrepMemory) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check php command line
     *
     * @return bool
     */
    private function isPhpCommandLine()
    {
        $php = exec('which php');
        if (null != $php) {
            if (strpos(strtolower($php), 'php') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get version of composer
     *
     * @return null|string
     */
    public function composerVersion()
    {
        $this->init();
        $command = $this->pathPHP.' '.$this->composerFile.' -V';

        return exec($command);
    }

    /**
     * Get mode
     *
     * @return string
     */
    public function getMode()
    {
        return 'EXEC';
    }
}
