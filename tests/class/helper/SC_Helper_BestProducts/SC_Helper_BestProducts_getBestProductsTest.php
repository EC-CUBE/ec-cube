<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_BestProducts/SC_Helper_BestProducts_TestBase.php");
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * SC_Helper_BestProducts::getBestProducts()のテストクラス.
 *
 * @author hiroshi kakuta
 */
class SC_Helper_BestProducts_getBestProductsTest extends SC_Helper_BestProducts_TestBase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setUpBestProducts();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**　best_idが存在しない場合、空を返す。
     */
    public function testGetBestProducts_おすすめidが存在しない場合_空を返す()
    {
        $best_id = '9999';

        $this->expected = null;
        $this->actual = SC_Helper_BestProducts_Ex::getBestProducts($best_id);

        $this->verify();
    }

    // best_idが存在する場合、対応した結果を取得できる。
    public function testGetBestProducts_おすすめIDが存在する場合_対応した結果を取得できる(){

        $best_id = '1001';


        $this->expected = array(
            'category_id' => '0',
            'rank' => '1',
            'title' => 'タイトルですよ',
            'comment' => 'コメントですよ',
            'del_flg' => '0'
        );

        $result = SC_Helper_BestProducts_Ex::getBestProducts($best_id);
        $this->actual = Test_Utils::mapArray($result,
            array('category_id',
                'rank',
                'title',
                'comment',
                'del_flg'
            ));

        $this->verify();

    }


    // best_idが存在するが、del_flg=1の場合、空が帰る。
    public function testGetBestProducts_おすすめIDがあり_かつ削除済みの場合_空が返る(){

        $best_id = '1002';

        $this->expected = null;
        $this->actual = SC_Helper_BestProducts_Ex::getBestProducts($best_id);

        $this->verify();

    }

    // best_idが存在するが、del_flg=1の場合、かつ。$has_deleted=trueを指定
    public function testGetBestProducts_削除済みでかつhas_deletedがtrueの場合_対応した結果が返る(){

        $best_id = '1002';


        $this->expected = array(
            'category_id' => '0',
            'rank' => '2',
            'title' => 'タイトルですよ',
            'comment' => 'コメントですよ',
            'del_flg' => '1'
        );

        $result = SC_Helper_BestProducts_Ex::getBestProducts($best_id,true);
        $this->actual = Test_Utils::mapArray($result,
            array('category_id',
                'rank',
                'title',
                'comment',
                'del_flg'
            ));

        $this->verify();
    }
}

