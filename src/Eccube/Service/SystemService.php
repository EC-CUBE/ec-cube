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

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Eccube\Common\EccubeConfig;
use Eccube\Util\StringUtil;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SystemService implements EventSubscriberInterface
{
    public const MAINTENANCE_TOKEN_KEY = 'maintenance_token';
    public const AUTO_MAINTENANCE = 'auto_maintenance';
    public const AUTO_MAINTENANCE_UPDATE = 'auto_maintenance_update';

    /**
     * メンテナンスモードを無効にする場合はtrue
     */
    private bool $disableMaintenanceAfterResponse = false;

    /**
     * メンテナンスモードの識別子
     */
    private string $maintenanceMode;

    /**
     * SystemService constructor.
     */
    public function __construct(protected EntityManagerInterface $entityManager, protected EccubeConfig $eccubeConfig)
    {
    }

    /**
     * get DB version
     */
    public function getDbversion(): string
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('v', 'v');

        $platform = $this->entityManager->getConnection()->getDatabasePlatform();
        [$prefix, $func] = match (true) {
            $platform instanceof SQLitePlatform => ['SQLite version ', 'sqlite_version()'],
            $platform instanceof AbstractMySQLPlatform => ['MySQL ', 'version()'],
            default => ['', 'version()'],
        };

        $version = $this->entityManager
            ->createNativeQuery('select '.$func.' as v', $rsm)
            ->getSingleScalarResult();

        return $prefix.$version;
    }

    /**
     * Try to set new values memory_limit | return true
     *
     * @param string $memory | EX: 1536M
     */
    public function canSetMemoryLimit(string $memory): bool
    {
        try {
            $ret = ini_set('memory_limit', $memory);
        } catch (\Exception) {
            return false;
        }

        return ($ret === false) ? false : true;
    }

    /**
     * Get memory_limit | Megabyte
     */
    public function getMemoryLimit(): float|int
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
     */
    public function switchMaintenance(bool $isEnable = false, string $mode = self::AUTO_MAINTENANCE, bool $force = false): void
    {
        if ($isEnable) {
            $this->enableMaintenance($mode, $force);
        } else {
            $this->disableMaintenanceNow($mode, $force);
        }
    }

    public function getMaintenanceToken(): ?string
    {
        $path = $this->eccubeConfig->get('eccube_content_maintenance_file_path');
        if (!\file_exists($path)) {
            return null;
        }

        $contents = \file_get_contents($path);

        return \explode(':', $contents)[1] ?? null;
    }

    /**
     * KernelEvents::TERMINATE で設定されるEvent
     */
    public function disableMaintenanceEvent(TerminateEvent $event): void
    {
        if ($this->disableMaintenanceAfterResponse) {
            $this->switchMaintenance(false, $this->maintenanceMode);
        }
    }

    public function enableMaintenance(string $mode = self::AUTO_MAINTENANCE, bool $force = false): void
    {
        if ($force || !$this->isMaintenanceMode()) {
            $path = $this->eccubeConfig->get('eccube_content_maintenance_file_path');
            $token = StringUtil::random(32);
            \file_put_contents($path, "{$mode}:{$token}");
        }
    }

    /**
     * メンテナンスモードを解除する
     *
     * KernelEvents::TERMINATE で解除のEventを設定し、メンテナンスモードを解除する
     */
    public function disableMaintenance(string $mode = self::AUTO_MAINTENANCE): void
    {
        $this->disableMaintenanceAfterResponse = true;
        $this->maintenanceMode = $mode;
    }

    public function disableMaintenanceNow(string $mode = self::AUTO_MAINTENANCE, bool $force = false): void
    {
        if (!$this->isMaintenanceMode()) {
            return;
        }

        $path = $this->eccubeConfig->get('eccube_content_maintenance_file_path');
        $contents = \file_get_contents($path);
        $currentMode = \explode(':', $contents)[0];

        if ($force || $currentMode === $mode) {
            \unlink($path);
        }
    }

    /**
     *　メンテナンスモードの状態を判定する
     */
    public function isMaintenanceMode(): bool
    {
        // .maintenanceが存在しているかチェック
        return \file_exists($this->eccubeConfig->get('eccube_content_maintenance_file_path'));
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::TERMINATE => 'disableMaintenanceEvent'];
    }
}
