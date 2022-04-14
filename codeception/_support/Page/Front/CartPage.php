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

class CartPage extends AbstractFrontPage
{
    public static $加算ポイント = '//span[contains(text(), "加算ポイント")]/../../dd/span';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);
        $page->goPage('/cart');

        return $page;
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('ショッピングカート', ['css' => 'div.ec-pageHeader h1']);

        return $page;
    }

    public function 商品名($index)
    {
        return $this->tester->grabTextFrom(['xpath' => "//div[@class='ec-cartRole']//ul[@class='ec-cartRow'][position()=${index}]//div[@class='ec-cartRow__name']"]);
    }

    public function 商品数量($index)
    {
        return $this->tester->grabTextFrom(['xpath' => "//div[@class='ec-cartRole']//ul[@class='ec-cartRow'][position()=${index}]//div[@class='ec-cartRow__amount']"]);
    }

    public function 明細数()
    {
        return count($this->tester->grabMultiple(['css' => 'ul.ec-cartRow']));
    }

    public function 商品数量増やす($index)
    {
        $this->tester->click(['xpath' => "//div[@class='ec-cartRole']//ul[@class='ec-cartRow'][position()=${index}]//div[@class='ec-cartRow__amountUpDown']/a[contains(@class, 'ec-cartRow__amountUpButton')]"]);

        return $this;
    }

    public function 商品数量減らす($index)
    {
        $this->tester->click(['xpath' => "//div[@class='ec-cartRole']//ul[@class='ec-cartRow'][position()=${index}]//div[@class='ec-cartRow__amountUpDown']/a[contains(@class, 'ec-cartRow__amountDownButton')]"]);

        return $this;
    }

    public function 商品削除($index)
    {
        $this->tester->click(['xpath' => "//div[@class='ec-cartRole']//ul[@class='ec-cartRow'][position()=${index}]//li[@class='ec-cartRow__delColumn']/a"]);
        $this->tester->acceptPopup();

        return $this;
    }

    public function エラーメッセージ()
    {
        return $this->tester->grabTextFrom(['css' => 'div.ec-cartRole__error div.ec-alert-warning__text']);
    }

    /**
     * @return ShoppingPage
     */
    public function レジに進む()
    {
        $this->tester->click(['css' => 'div.ec-cartRole__actions a.ec-blockBtn--action']);

        return new ShoppingPage($this->tester);
    }

    /**
     * @return TopPage
     */
    public function お買い物を続ける()
    {
        $this->tester->click(['css' => 'div.ec-cartRole__actions a.ec-blockBtn--cancel']);

        return new TopPage($this->tester);
    }
}
