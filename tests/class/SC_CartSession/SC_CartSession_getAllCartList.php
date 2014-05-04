<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_CartSession/SC_CartSession_TestBase.php");

/**
 * SC_CartSession_getAllCartList
 *
 * @package
 * @version $id$
 * @copyright
 * @author Nobuhiko Kimoto <info@nob-log.info>
 * @license
 */
class SC_CartSession_getAllCartList extends SC_CartSession_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->objCartSession = new SC_CartSession_Ex();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////

    /**
     * @test
     */
    public function getAllCartList_商品を追加していなければ空の配列を返す()
    {
        $this->setUpProductClass();
        $this->expected = 0;
        $this->actual = count($this->objCartSession->getAllCartList());

        $this->verify('商品数');
    }

    /**
     * @test
     */
    public function getAllCartList_商品を1つ追加した場合1つの配列を返す()
    {
        $this->setUpProductClass();
        $this->expected = 1;
        $this->objCartSession->addProduct('1001', 1);

        $cartList = $this->objCartSession->getAllCartList();
        $this->actual = count($cartList);

        $this->verify('カート数');

        return $cartList;
    }

    /**
     * @test
     */
    public function getAllCartList_違う商品種別の商品を追加した場合用品種別分の配列を返す()
    {
        $this->setUpProductClass();
        $this->expected = 2;
        $this->objCartSession->addProduct('1001', 1);
        $this->objCartSession->addProduct('1002', 1);

        $cartList = $this->objCartSession->getAllCartList();
        $this->actual = count($cartList);

        $this->verify('カート数');

        return $cartList;
    }

    /**
     * @test
     */
    public function getAllCartList_複数回呼んでも同じ内容が返される()
    {
        $this->setUpProductClass();
        $this->objCartSession->addProduct('1001', 1);
        $this->objCartSession->addProduct('1002', 1);

        $this->expected = $this->objCartSession->getAllCartList();
        $this->actual = $this->objCartSession->getAllCartList();

        $this->verify('カートの内容');

        return $cartList;
    }

}
