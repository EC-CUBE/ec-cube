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

namespace Eccube\Service;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Util\StringUtil;
use function explode;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use function unlink;

class SystemService implements EventSubscriberInterface
{
    public const MAINTENANCE_TOKEN_KEY = 'maintenance_token';
    public const AUTO_MAINTENANCE = 'auto_maintenance';
    public const AUTO_MAINTENANCE_UPDATE = 'auto_maintenance_update';

    /**
     * メンテナンスモードを無効にする場合はtrue
     *
     * @var bool
     */
    private $disableMaintenanceAfterResponse = false;

    /**
     * メンテナンスモードの識別子
     *
     * @var string
     */
    private $maintenanceMode = null;

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
     * - $isEnable = true の場合, $mode の文字列が記載された .maintenance ファイルを生成する
     * - $isEnable = false の場合, $mode の文字列が記載された .maintenance ファイルを削除する
     *
     * @param bool $isEnable
     * @param string $mode
     */
    public function switchMaintenance($isEnable = false, $mode = self::AUTO_MAINTENANCE, bool $force = false)
    {
        if ($isEnable) {
            $this->enableMaintenance($mode, $force);
        } else {
            $this->disableMaintenanceNow($mode, $force);
        }
    }

    public function getMaintenanceToken(): ?string
    {
        $path = $this->container->getParameter('eccube_content_maintenance_file_path');
        if (!file_exists($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        return explode(':', $contents)[1] ?? null;
    }

    /**
     * KernelEvents::TERMINATE で設定されるEvent
     */
    public function disableMaintenanceEvent(TerminateEvent $event)
    {
        if ($this->disableMaintenanceAfterResponse) {
            $this->switchMaintenance(false, $this->maintenanceMode);
        }
    }

    public function enableMaintenance($mode = self::AUTO_MAINTENANCE, bool $force = false): void
    {
        if ($force || !$this->isMaintenanceMode()) {
            $path = $this->container->getParameter('eccube_content_maintenance_file_path');
            $token = StringUtil::random(32);
            file_put_contents($path, "{$mode}:{$token}");
        }
    }

    /**
     * メンテナンスモードを解除する
     *
     * KernelEvents::TERMINATE で解除のEventを設定し、メンテナンスモードを解除する
     *
     * @param string $mode
     */
    public function disableMaintenance($mode = self::AUTO_MAINTENANCE)
    {
        $this->disableMaintenanceAfterResponse = true;
        $this->maintenanceMode = $mode;
    }

    public function disableMaintenanceNow($mode = self::AUTO_MAINTENANCE, bool $force = false): void
    {
        if (!$this->isMaintenanceMode()) {
            return;
        }

        $path = $this->container->getParameter('eccube_content_maintenance_file_path');
        $contents = file_get_contents($path);
        $currentMode = explode(':', $contents)[0] ?? null;

        if ($force || $currentMode === $mode) {
            unlink($path);
        }
    }

    /**
     *　メンテナンスモードの状態を判定する
     *
     * @return bool
     */
    public function isMaintenanceMode()
    {
        // .maintenanceが存在しているかチェック
        return file_exists($this->container->getParameter('eccube_content_maintenance_file_path'));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::TERMINATE => 'disableMaintenanceEvent'];
    }
}
