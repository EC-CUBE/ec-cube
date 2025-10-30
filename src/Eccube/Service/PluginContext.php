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

use Eccube\Common\EccubeConfig;
use Eccube\Exception\PluginException;

class PluginContext
{
    private const MODE_INSTALL = 'install';
    private const MODE_UNINSTALL = 'uninstall';

    /**
     * @var string
     */
    private string $mode;

    /**
     * @var string
     */
    private string $code;

    /**
     * @var array<string, mixed>
     */
    private array $composerJson;

    /**
     * @var EccubeConfig
     */
    private EccubeConfig $eccubeConfig;

    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    public function isInstall(): bool
    {
        return $this->mode === self::MODE_INSTALL;
    }

    public function isUninstall(): bool
    {
        return $this->mode === self::MODE_UNINSTALL;
    }

    public function setInstall(): string
    {
        return $this->mode = self::MODE_INSTALL;
    }

    public function setUninstall(): string
    {
        return $this->mode = self::MODE_UNINSTALL;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws PluginException
     */
    public function getComposerJson(): array
    {
        if ($this->composerJson) {
            return $this->composerJson;
        }

        $projectRoot = $this->eccubeConfig->get('kernel.project_dir');
        $composerJsonPath = $projectRoot.'/app/Plugin/'.$this->code.'/composer.json';
        if (file_exists($composerJsonPath) === false) {
            throw new PluginException("{$composerJsonPath} not found.");
        }
        $this->composerJson = json_decode(file_get_contents($composerJsonPath), true);
        if ($this->composerJson === null) {
            throw new PluginException("Invalid json format. [{$composerJsonPath}]");
        }

        return $this->composerJson;
    }

    /**
     * @return array<string, string>
     *
     * @throws PluginException
     */
    public function getExtraEntityNamespaces(): array
    {
        $json = $this->getComposerJson();
        if (isset($json['extra'])) {
            if (array_key_exists('entity-namespaces', $json['extra'])) {
                return $json['extra']['entity-namespaces'];
            }
        }

        return [];
    }
}
