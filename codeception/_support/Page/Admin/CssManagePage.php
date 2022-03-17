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

class CssManagePage extends AbstractAdminPageStyleGuide
{
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

        return $page->goPage('/content/css', 'CSS管理コンテンツ管理');
    }

    public function 入力($value)
    {
        $this->tester->fillField('.ace_text-input', $value);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#save-button');

        return $this;
    }
}
