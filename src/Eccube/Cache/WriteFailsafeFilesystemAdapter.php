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

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;

/**
 * 書き込めない場合に保存を諦める FilesystemAdapter.
 *
 * @see WriteFailsafeTrait 理由
 */
final class WriteFailsafeFilesystemAdapter extends FilesystemAdapter
{
    use WriteFailsafeTrait;

    public function __construct(string $namespace = '', int $defaultLifetime = 0, ?string $directory = null, ?MarshallerInterface $marshaller = null)
    {
        parent::__construct($namespace, $defaultLifetime, $directory, $marshaller);

        $this->initWriteFailsafe($directory);
    }
}
