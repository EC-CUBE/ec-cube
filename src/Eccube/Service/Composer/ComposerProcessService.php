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
namespace Eccube\Service\Composer;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Annotation\Service;

/**
 * Class ComposerProcessService
 * @package Eccube\Service\Composer
 * @Service
 */
class ComposerProcessService implements ComposerServiceInterface
{
    /**
     * @var array config parameter
     */
    protected $appConfig;

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
     * @param array                  $appConfig
     * @param EntityManagerInterface $entityManager
     * @param string                 $pathPHP
     */
    public function __construct($appConfig, $entityManager, $pathPHP)
    {
        $this->appConfig = $appConfig;
        $this->entityManager = $entityManager;
        $this->pathPHP = $pathPHP;
    }

    /**
     * This function to install a plugin by composer require
     *
     * @param string $packageName format "foo/bar foo/bar2:1.0.0"
     * @return bool
     */
    public function execRequire($packageName)
    {
        set_time_limit(0);
        if (false === $this->init()) {
            return false;
        }
        // Build command
        $command = $this->pathPHP.' '.$this->composerFile.' require '.$packageName;
        $command .= ' --prefer-dist --no-progress --no-suggest --no-scripts --ignore-platform-reqs --profile --no-ansi --no-interaction -d ';
        $command .= $this->workingDir.' 2>&1';
        log_info($command);
        $this->runCommand($command);

        return true;
    }

    /**
     * This function to remove a plugin by composer remove
     *
     * @param string $packageName format "foo/bar foo/bar2"
     * @return bool
     */
    public function execRemove($packageName)
    {
        set_time_limit(0);
        if (false === $this->init()) {
            return false;
        }
        // Build command
        $command = $this->pathPHP.' '.$this->composerFile.' remove '.$packageName;
        $command .= ' --no-progress --no-scripts --ignore-platform-reqs --profile --no-ansi --no-interaction -d ';
        $command .= $this->workingDir.' 2>&1';
        log_info($command);

        // Execute command
        $this->runCommand($command);

        return true;
    }

    /**
     * Run command
     *
     * @param string $command
     * @return void
     */
    public function runCommand($command)
    {
        // Execute command
        $output = array();
        exec($command, $output);
        log_info(PHP_EOL.implode(PHP_EOL, $output).PHP_EOL);
    }

    /**
     * Set working dir
     * @param string $workingDir
     */
    public function setWorkingDir($workingDir)
    {
        $this->workingDir = $workingDir;
    }

    /**
     * Set init
     * @return bool
     */
    private function init()
    {
        if (!$this->isPhpCommandLine()) {
            return false;
        }

        if (!$this->isSetCliMemoryLimit()) {
            $composerMemory = $this->appConfig['composer_memory_limit'];
            if ($this->getCliMemoryLimit() < $composerMemory && $this->getCliMemoryLimit() != -1) {
                return false;
            }
        }

        /**
         * Mysql lock in transaction
         * @link https://dev.mysql.com/doc/refman/5.7/en/lock-tables.html
         * @var EntityManagerInterface $em
         */
        $em = $this->entityManager;
        if ($em->getConnection()->isTransactionActive()) {
            $em->getConnection()->commit();
            $em->getConnection()->beginTransaction();
        }

        @ini_set('memory_limit', '1536M');
        // Config for some environment
        putenv('COMPOSER_HOME='.$this->appConfig['plugin_realdir'].'/.composer');
        $this->workingDir = $this->workingDir ? $this->workingDir : $this->appConfig['root_dir'];
        $this->setupComposer();

        return true;
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
     * @return bool
     */
    private function isSetCliMemoryLimit()
    {
        $oldMemory = exec($this->pathPHP.' -i | grep "memory_limit"');
        $tmpMem = '1.5GB';
        if ($oldMemory) {
            $memory = explode('=>', $oldMemory);
            $originGrepMemmory = trim($memory[2]);

            if ($originGrepMemmory == $tmpMem) {
                $tmpMem = '1.49GB';
            }

            $newMemory = exec($this->pathPHP.' -d memory_limit='.$tmpMem.' -i | grep "memory_limit"');
            if ($newMemory) {
                $newMemory = explode('=>', $newMemory);
                $grepNewMemory = trim($newMemory[2]);
                if ($grepNewMemory != $originGrepMemmory) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check php command line
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
     * @return null|string
     */
    public function composerVersion()
    {
        $this->init();
        $command = $this->pathPHP . ' ' . $this->composerFile . ' -V';
        return exec($command);
    }

    /**
     * Get mode
     * @return mixed|string
     */
    public function getMode()
    {
        return 'EXEC';
    }
}
