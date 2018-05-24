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

/**
 * Interface ComposerServiceInterface
 */
interface ComposerServiceInterface
{
    /**
     * Run execute command
     *
     * @param string $packageName format foo/bar or foo/bar:1.0.0 or "foo/bar 1.0.0"
     *
     * @throws \Eccube\Exception\PluginException
     */
    public function execRequire($packageName);

    /**
     * Run remove command
     *
     * @param string $packageName format foo/bar or foo/bar:1.0.0 or "foo/bar 1.0.0"
     *
     * @throws \Eccube\Exception\PluginException
     */
    public function execRemove($packageName);

    /**
     * Run composer command
     *
     * @param array|string $commands
     *
     * @return string|mixed
     */
    public function runCommand($commands);

    /**
     * Get version of composer
     *
     * @return null|string
     */
    public function composerVersion();

    /**
     * Get mode
     *
     * @return mixed|string
     */
    public function getMode();
}
