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

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Annotation\Service;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * @Service
 */
class SystemService
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * SystemService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * get DB version
     * @return string
     */
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
     * Try to set new values memory_limit | return true
     * @param string $memory | EX: 1536M
     * @return bool
     */
    public function canSetMemoryLimit($memory)
    {
        try {
            $ret = ini_set('memory_limit', $memory);
        } catch (\Exception $exception) {
            return false;
        }

        return ($ret === false) ? false : true;
    }

    /**
     * Get memory_limit | Megabyte
     * @return float|int
     */
    public function getMemoryLimit()
    {
        // Data type: bytes
        $memoryLimit = (new MemoryDataCollector())->getMemoryLimit();
        if (-1 == $memoryLimit) {
            return -1;
        }

        return ($memoryLimit == 0) ? 0 : ($memoryLimit / 1024) / 1024;
    }
}
