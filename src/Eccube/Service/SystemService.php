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

namespace Eccube\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SystemService
{
    const AUTO_MAINTENANCE = 'auto_maintenance';
    const AUTO_MAINTENANCE_UPDATE = 'auto_maintenance_update';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * SystemService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container
    ) {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /**
     * get DB version
     *
     * @return string
     */
    public function getDbversion()
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('v', 'v');

        $platform = $this->entityManager->getConnection()->getDatabasePlatform()->getName();
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

        $version = $this->entityManager
            ->createNativeQuery('select '.$func.' as v', $rsm)
            ->getSingleScalarResult();

        return $prefix.$version;
    }

    /**
     * Try to set new values memory_limit | return true
     *
     * @param string $memory | EX: 1536M
     *
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
     *
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

    /**
     *　メンテナンスモードを切り替える
     *
     * @param Bool $isEnable
     */
    public function switchMaintenance($isEnable = false, $mode = self::AUTO_MAINTENANCE)
    {
        $isMaintenanceMode = $this->isMaintenanceMode();
        $path = $this->container->getParameter('eccube_content_maintenance_file_path');

        if ($isEnable && $isMaintenanceMode === false) {
            file_put_contents($path, $mode);
        } elseif ($isEnable === false && $isMaintenanceMode) {
            $contents = file_get_contents($path);
            if ($contents == $mode) {
                unlink($path);
            }
        }
    }

    /**
     *　メンテナンスモードの状態を判定する
     *
     * @return Bool
     */
    public function isMaintenanceMode()
    {
        // .maintenanceが存在しているかチェック
        return file_exists($this->container->getParameter('eccube_content_maintenance_file_path'));
    }

}
