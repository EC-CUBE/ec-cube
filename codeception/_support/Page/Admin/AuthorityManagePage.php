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

class AuthorityManagePage extends AbstractAdminPageStyleGuide
{
    public static $完了メッセージ = '.c-contentsArea .alert-success';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);

        return $page->goPage('/setting/system/authority', '権限管理システム設定');
    }

    public function 行追加()
    {
        $this->tester->click('body > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body > p > button');

        return $this;
    }

    public function 行削除($rowNum)
    {
        $this->tester->click(['css' => "form tbody tr:nth-child($rowNum) td:nth-child(3) button"]);

        return $this;
    }

    public function 入力($rowNum, $role, $url)
    {
        $this->tester->selectOption(['css' => "form #table-authority tbody tr:nth-child(${rowNum}) td:nth-child(1) select"], $role);
        $this->tester->fillField(['css' => "form #table-authority tbody tr:nth-child(${rowNum}) td:nth-child(2) input"], $url);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('form .c-conversionArea button');

        return $this;
    }
}
