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

class ShoppingCompletePage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('ご注文完了', 'div.ec-pageHeader h1');

        return $page;
    }

    public function TOPへ()
    {
        $this->tester->click('div.ec-cartCompleteRole a.ec-blockBtn--cancel');

        return $this;
    }
}
