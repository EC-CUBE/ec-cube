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

namespace Eccube\Service\Composer;

use Eccube\Entity\BaseInfo;

/**
 * Interface ComposerServiceInterface
 */
interface ComposerServiceInterface
{
    /**
     * Run execute command
     *
     * @param string $packageName format foo/bar or foo/bar:1.0.0 or "foo/bar 1.0.0"
     * @param null $output
     *
     * @return string
     */
    public function execRequire($packageName, $output = null);

    /**
     * Run remove command
     *
     * @param string $packageName format foo/bar or foo/bar:1.0.0 or "foo/bar 1.0.0"
     * @param null $output
     *
     * @return string
     */
    public function execRemove($packageName, $output = null);

    public function execConfig($key, $value = null);

    public function configureRepository(BaseInfo $BaseInfo);

    public function foreachRequires($packageName, $version, $callback, $typeFilter = null, $level = 0);
}
