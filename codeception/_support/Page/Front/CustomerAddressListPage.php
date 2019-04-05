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

namespace Page\Front;

class CustomerAddressListPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('マイページ/お届け先一覧', 'div.ec-pageHeader h1');

        return $page;
    }

    public function 追加()
    {
        $this->tester->click('div.ec-addressRole div.ec-addressRole__actions a');

        return new CustomerAddressEditPage($this->tester);
    }

    public function 変更($num)
    {
        $this->tester->click("div.ec-addressList div:nth-child(${num}) div.ec-addressList__action a");

        return new CustomerAddressEditPage($this->tester);
    }

    public function 削除($num)
    {
        $this->tester->click("div.ec-addressList div:nth-child(${num}) a.ec-addressList__remove");
        $this->tester->acceptPopup();

        return $this;
    }
}
