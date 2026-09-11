<?php

declare(strict_types=1);

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

namespace Eccube\Cache;

use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

/**
 * 書き込めない場合に保存を諦める PhpFilesAdapter.
 *
 * @see WriteFailsafeTrait 理由
 */
final class WriteFailsafePhpFilesAdapter extends PhpFilesAdapter
{
    use WriteFailsafeTrait;

    public function __construct(string $namespace = '', int $defaultLifetime = 0, ?string $directory = null, bool $appendOnly = false)
    {
        parent::__construct($namespace, $defaultLifetime, $directory, $appendOnly);

        $this->initWriteFailsafe($directory);
    }

    /**
     * cache.system 用のファクトリ.
     *
     * 書き込める場合は Symfony の既定 (AbstractAdapter::createSystemCache) をそのまま使う.
     * 書き込めない場合のみ本アダプタへ差し替える.
     */
    #[\Override]
    public static function createSystemCache(string $namespace, int $defaultLifetime, string $version, string $directory, ?LoggerInterface $logger = null): AdapterInterface
    {
        $path = $directory;
        while (!file_exists($path)) {
            $parent = \dirname($path);
            if ($parent === $path) {
                break;
            }
            $path = $parent;
        }

        if (is_writable($path)) {
            return AbstractAdapter::createSystemCache($namespace, $defaultLifetime, $version, $directory, $logger);
        }

        // 書き込めない構成では APCu との ChainAdapter は組まない.
        // (CLI では apc.enable_cli が無効な環境が大半で, 既定でも PhpFilesAdapter 単体になる)
        $adapter = new self($namespace, $defaultLifetime, $directory, true);
        if (null !== $logger) {
            $adapter->setLogger($logger);
        }

        return $adapter;
    }
}
