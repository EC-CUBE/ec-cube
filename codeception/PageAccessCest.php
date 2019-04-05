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

use AcceptanceTester;
use Codeception\Util\Fixtures;

class PageAccessCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I->wantTo('perform actions and see result');
        $I->amOnPage('/');
        $I->see('くらしを楽しむライフスタイルグッズ', '.copy');

        $shopName = $I->grabFromDatabase('dtb_base_info', 'shop_name');
        $I->assertEquals('EC-CUBE3 SHOP', $shopName);

        $products = $I->grabFromDatabase('dtb_product', 'status', ['product_id' => 1]);
        codecept_debug($products);

        $bi = Fixtures::get('baseinfo');
        codecept_debug($bi->getShopName());
        foreach (Fixtures::get('categories') as $category) {
            codecept_debug($category->getName());
        }
    }
}
