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

class ShippingEditPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('お届け先の変更', 'div.ec-pageHeader h1');

        return $page;
    }

    public function 入力_姓($value)
    {
        $this->tester->fillField(['id' => 'shopping_shipping_name_name01'], $value);

        return $this;
    }

    public function 登録する()
    {
        $this->tester->click('div.ec-RegisterRole__actions button.ec-blockBtn--action');

        return $this;
    }
}
