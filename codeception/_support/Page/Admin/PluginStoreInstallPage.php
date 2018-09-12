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

namespace Page\Admin;

class PluginStoreInstallPage extends AbstractAdminPageStyleGuide
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);

        return $page->atPage('インストール確認 オーナーズストア');
    }

    /**
     * @return PluginManagePage
     *
     * @throws \Exception
     */
    public function インストール()
    {
        $this->tester->click(['css' => '#plugin-list > div.card-body > div:nth-child(2) > div > button.btn.btn-primary']);
        $this->tester->waitForElementVisible(['id' => 'installBtn']);
        $this->tester->click(['id' => 'installBtn']);
        $this->tester->waitForElementVisible(['css' => '#installModal > div > div > div.modal-footer > a'], 30);
        $this->tester->click(['css' => '#installModal > div > div > div.modal-footer > a']);

        return PluginManagePage::at($this->tester);
    }
}
