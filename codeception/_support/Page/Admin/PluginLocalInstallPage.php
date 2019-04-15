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

namespace Page\Admin;

class PluginLocalInstallPage extends AbstractAdminPageStyleGuide
{
    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/store/plugin/install', '独自プラグインのアップロードオーナーズストア');
    }

    /**
     * @param $fileName
     *
     * @return PluginManagePage
     */
    public function アップロード($fileName)
    {
        $this->tester->attachFile(['id' => 'plugin_local_install_plugin_archive'], $fileName);
        $this->tester->click(['css' => '#upload-form > div > div > div > div > div.card-body > div > div > button']);

        return PluginManagePage::at($this->tester);
    }
}
