<?php
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

        $products = $I->grabFromDatabase('dtb_product', 'status', array('product_id'=>1));
        codecept_debug($products);

        $bi = Fixtures::get('baseinfo');
        codecept_debug($bi->getShopName());
        foreach (Fixtures::get('categories') as $category) {
            codecept_debug($category->getName());
        }
    }
}
