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

    private $mode;

    private $code;

    private $composerJson;

    /**
     * @var EccubeConfig
     */
    private $eccubeConfig;

    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    public function isInstall()
    {
        return $this->mode === self::MODE_INSTALL;
    }

    public function isUninstall()
    {
        return $this->mode === self::MODE_UNINSTALL;
    }

    public function setInstall()
    {
        return $this->mode = self::MODE_INSTALL;
    }

    public function setUninstall()
    {
        return $this->mode = self::MODE_UNINSTALL;
    }

    public function setCode(string $code)
    {
        $this->code = $code;
    }

    public function getComposerJson(): array
    {
        if ($this->composerJson) {
            return $this->composerJson;
        }

        $projectRoot = $this->eccubeConfig->get('kernel.project_dir');
        $composerJsonPath = $projectRoot.'/app/Plugin/'.$this->code.'/composer.json';
        if (file_exists($composerJsonPath) === false) {
            throw new PluginException("${composerJsonPath} not found.");
        }
        $this->composerJson = json_decode(file_get_contents($composerJsonPath), true);
        if ($this->composerJson === null) {
            throw new PluginException("Invalid json format. [${composerJsonPath}]");
        }

        return $this->composerJson;
    }

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
