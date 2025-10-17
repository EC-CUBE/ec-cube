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
     * @param OutputInterface|null $output
     * @param string|null $from Path of composer repository
     *
     * @return string
     */
    public function execRequire(string $packageName, ?OutputInterface $output = null, ?string $from = null): string;

    /**
     * Run remove command
     *
     * @param string $packageName format foo/bar or foo/bar:1.0.0 or "foo/bar 1.0.0"
     * @param OutputInterface|null $output
     *
     * @return string
     */
    public function execRemove($packageName, $output = null): string;

    /**
     * @param string $key
     * @param string|null $value
     *
     * @return array<int|string,array<int,string>>|null
     */
    public function execConfig($key, $value = null): ?array;

    /**
     * @param BaseInfo $BaseInfo
     *
     * @return void
     */
    public function configureRepository(BaseInfo $BaseInfo): void;

    /**
     * @param string $packageName
     * @param string|null $version
     * @param callable $callback
     * @param string|null $typeFilter
     * @param int $level
     *
     * @return void
     */
    public function foreachRequires($packageName, $version, $callback, $typeFilter = null, $level = 0): void;
}
