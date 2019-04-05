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

class PluginSearchPage extends AbstractAdminPageStyleGuide
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/store/plugin/api/search', 'プラグインを探すオーナーズストア');
    }

    /**
     * @param $pluginCode
     *
     * @return PluginStoreInstallPage
     */
    public function 入手する($pluginCode)
    {
        $this->tester->click(['xpath' => '//*[@id="plugin-list"]//a[@data-code="'.$pluginCode.'"]/../../div[3]/form/a[contains(text(), "入手する")]']);

        return PluginStoreInstallPage::at($this->tester);
    }
}
