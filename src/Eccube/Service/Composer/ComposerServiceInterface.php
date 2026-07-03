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

namespace Eccube\Service\Composer;

use Eccube\Entity\BaseInfo;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface ComposerServiceInterface
 */
interface ComposerServiceInterface
{
    /**
     * Run execute command
     *
     * @param string $packageName format foo/bar or foo/bar:1.0.0 or "foo/bar 1.0.0"
     * @param string|null $from Path of composer repository
     */
    public function execRequire(string $packageName, ?OutputInterface $output = null, ?string $from = null): string;

    /**
     * Run remove command
     *
     * @param string $packageName format foo/bar or foo/bar:1.0.0 or "foo/bar 1.0.0"
     */
    public function execRemove(string $packageName, ?OutputInterface $output = null): string;

    /**
     * @return array<int|string, array<int, string>>|null
     */
    public function execConfig(string $key, ?string $value = null): ?array;

    public function configureRepository(BaseInfo $BaseInfo): void;

    public function foreachRequires(string $packageName, ?string $version, callable $callback, ?string $typeFilter = null, int $level = 0): void;
}
