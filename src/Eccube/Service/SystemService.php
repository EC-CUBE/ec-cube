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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Service;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * @Service
 */
class SystemService
{
    /**
     * @var EntityManager
     * @Inject("orm.em")
     */
    protected $em;

    /**
     * 1536 Megabyte
     * @var int
     */
    const MEMORY = 1536;

    public function getDbversion()
    {

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('v', 'v');

        $platform = $this->em->getConnection()->getDatabasePlatform()->getName();
        switch ($platform) {
            case 'sqlite':
                $prefix = 'SQLite version ';
                $func = 'sqlite_version()';
                break;

            case 'mysql':
                $prefix = 'MySQL ';
                $func = 'version()';
                break;

            case 'pgsql':
            default:
                $prefix = '';
                $func = 'version()';
        }

        $version = $this->em
            ->createNativeQuery('select '.$func.' as v', $rsm)
            ->getSingleScalarResult();

        return $prefix.$version;
    }

    /**
     * Get environment php command
     * @return string
     */
    public function getPHP()
    {
        return (new PhpExecutableFinder())->find();
    }

    /**
     * Check permission php.ini and set new memory_limit
     * @return bool
     */
    public function isSetMemoryLimit()
    {
        // Get path php.ini loaded
        $iniPath = php_ini_loaded_file();
        if ($iniPath && is_writable($iniPath)) {
            return true;
        }

        return false;
    }

    /**
     * Get memory_limit | Megabyte
     * @return float|int
     */
    public function getMemoryLimit()
    {
        $memoryLimit = ini_get('memory_limit');
        if (preg_match('/^(\d+)(.)$/', $memoryLimit, $matches)) {
            $memoryValue = $matches[1];
            $memoryUnit = strtoupper($matches[2]);

            if ($memoryUnit == 'M') {
                return $memoryValue;
            } else {
                if ($memoryUnit == 'K') {
                    return $memoryValue / 1024;
                } else {
                    return $memoryValue * 1024;
                }
            }
        }

        return 0;
    }

    /**
     * Get grep memory_limit | Megabyte
     * @return int|string
     */
    public function getCliMemoryLimit(){
        $grepMemory = exec($this->getPHP().' -i | grep "memory_limit"');
        if($grepMemory){
            $grepMemory = explode('=>', $grepMemory);
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
    public function isSetCliMemoryLimit()
    {
        $oldMemory = exec($this->getPHP().' -i | grep "memory_limit"');
        $tmpMem = '2GB';

        if ($oldMemory) {
            $memory = explode('=>', $oldMemory);
            $originGrepMemmory = trim($memory[2]);

            if ($originGrepMemmory == $tmpMem) {
                $tmpMem = '2.5GB';
            }

            $newMemory = exec($this->getPHP().' -d memory_limit=' . $tmpMem . ' -i | grep "memory_limit"');
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
    public function isPhpCommandLine()
    {
        $php = exec('which php');
        if (function_exists('exec') && null != $php) {
            if (strpos(strtolower($php), 'php') !== false) {
                return true;
            }
        }

        return false;
    }
}
