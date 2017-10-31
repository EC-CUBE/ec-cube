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
namespace Eccube\Service;

use Eccube\Annotation\Inject;
use Eccube\Annotation\Service;
use Eccube\Application;

/**
 * Class ComposerProcessService
 * @package Eccube\Service
 * @Service
 */
class ComposerProcessService
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    private $composerFile;
    private $composerSetup;
    private static $vendorName = 'ec-cube';

    /**
     * This function to install a plugin by composer require
     *
     * @param string $packageName
     * @return bool
     */
    public function execRequire($packageName)
    {
        set_time_limit(0);
        $this->setupComposer();
        // Build command
        $packageName = self::$vendorName.'/'.$packageName;
        $command = $this->getPHP().' '.$this->composerFile.' require '.$packageName;
        $command .= ' --prefer-dist --no-progress --no-suggest --no-scripts --ignore-platform-reqs --profile --no-ansi --no-interaction -d ';
        $command .= $this->appConfig['root_dir'].' 2>&1';
        log_info($command);

        // Execute command
        $output = array();
        exec($command, $output);
        log_info(PHP_EOL.implode(PHP_EOL, $output).PHP_EOL);

        return true;
    }

    /**
     * This function to remove a plugin by composer remove
     * Note: Remove with dependency, if not, please add " --no-update-with-dependencies"
     *
     * @param string $packageName
     * @return bool
     */
    public function execRemove($packageName)
    {
        set_time_limit(0);
        $this->setupComposer();
        // Build command
        $packageName = self::$vendorName.'/'.$packageName;
        $command = $this->getPHP().' '.$this->composerFile.' remove '.$packageName;
        $command .= ' --no-progress --no-scripts --ignore-platform-reqs --profile --no-ansi --no-interaction -d ';
        $command .= $this->appConfig['root_dir'].' 2>&1';
        log_info($command);

        // Execute command
        $output = array();
        exec($command, $output);
        log_info(PHP_EOL.implode(PHP_EOL, $output).PHP_EOL);

        return true;
    }

    /**
     * Get environment php command
     *
     * @return string
     */
    private function getPHP()
    {
        return 'php';
    }

    /**
     * Check composer file and setup it
     */
    private function setupComposer()
    {
        @ini_set('memory_limit', '1536M');
        // Config for some environment
        putenv('COMPOSER_HOME='.$this->appConfig['plugin_realdir'].'/.composer');
        $this->composerFile = $this->appConfig['root_dir'].'/composer.phar';
        $this->composerSetup = $this->appConfig['root_dir'].'/composer-setup.php';

        if (!file_exists($this->composerFile)) {
            if (!file_exists($this->composerSetup)) {
                $result = copy('https://getcomposer.org/installer', $this->composerSetup);
                log_info($this->composerSetup.' : '.$result);
            }
            $command = $this->getPHP().' '.$this->composerSetup;
            $output = array();
            exec($command, $output);
            log_info(PHP_EOL.implode(PHP_EOL, $output).PHP_EOL);

            unlink($this->composerSetup);
        }
    }
}
