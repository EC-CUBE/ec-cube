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

class OwnersPluginPage extends AbstractAdminPage
{
    public static $完了メッセージ = '#main .container-fluid div:nth-child(1) .alert-success';

    /**
     * OwnersPluginPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/store/plugin', 'オーナーズストアプラグイン一覧');
    }

    public static function goInstall($I)
    {
        $page = new self($I);

        return $page->goPage('/store/plugin/install', 'オーナーズストアプラグインのアップロード');
    }

    public function インストール($fileName)
    {
        $this->tester->attachFile(['id' => 'plugin_local_install_plugin_archive'], $fileName);
        $this->tester->click('#aside_column button');

        return $this;
    }

    public function 有効にする($code)
    {
        $this->tester->click(['xpath' => '//div[contains(@class, "plugin-table")]//td[@class="tc"]/p[text()="'.$code.'"]/parent::td/parent::tr/td[@class="tp"]/a[contains(text(), "有効にする")]']);

        return $this;
    }

    public function 無効にする($code)
    {
        $this->tester->click(['xpath' => '//div[contains(@class, "plugin-table")]//td[@class="tc"]/p[text()="'.$code.'"]/parent::td/parent::tr/td[@class="tp"]/a[contains(text(), "無効にする")]']);

        return $this;
    }

    public function 削除($code)
    {
        $this->tester->click(['xpath' => '//div[contains(@class, "plugin-table")]//td[@class="tc"]/p[text()="'.$code.'"]/parent::td/parent::tr/td[@class="tp"]/a[contains(text(), "削除")]']);
        $this->tester->acceptPopup();

        return $this;
    }
}
