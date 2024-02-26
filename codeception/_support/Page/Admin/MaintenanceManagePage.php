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

class MaintenanceManagePage extends AbstractAdminPageStyleGuide
{
    public static $完了メッセージ = '#page_admin_content_maintenance > div.c-container > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span';

    /**
     * MaintenanceManagePage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/content/maintenance', 'メンテナンス管理コンテンツ管理');
    }

    public function メンテナンス有効無効()
    {
        $this->tester->click('#page_admin_content_maintenance > div.c-container > div.c-contentsArea > form > div > div > div > div > div.card-body > div:nth-child(2) > div > button');

        return $this;
    }
}
