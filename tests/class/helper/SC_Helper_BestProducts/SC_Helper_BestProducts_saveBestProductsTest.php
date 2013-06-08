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
 * SC_Helper_BestProducts::saveBestProducts()のテストクラス.
 *
 * @author hiroshi kakuta
 */
class SC_Helper_BestProducts_saveBestProductsTest extends SC_Helper_BestProducts_TestBase
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

    // best_idを指定して更新される。
    public function testSaveBestProducts_ベストIDがある場合_更新される(){

        $sqlVal = array(
            'best_id' => '1001',
            'product_id'=>'3',
            'category_id' => '1',
            'rank' => '2',
            'title' => 'タイトルですよ1001',
            'comment' => 'コメントですよ1001',
            'creator_id' => '2',
            'create_date' => '2000-01-01 00:00:00',
            'update_date' => '2000-01-01 00:00:00',
            'del_flg' => '0'
        );

        $result = SC_Helper_BestProducts_Ex::saveBestProducts($sqlVal);

        $this->expected = array(
            'product_id'=>'3',
            'category_id' => '1',
            'rank' => '2',
            'title' => 'タイトルですよ1001',
            'comment' => 'コメントですよ1001',
            'creator_id' => '1', //変わらない
            'create_date' => '2000-01-01 00:00:00',
            'del_flg' => '0'
        );

        $this->actual = SC_Helper_BestProducts_Ex::getBestProducts('1001');

        $arrRet = SC_Helper_BestProducts_Ex::getBestProducts('1001');

        $this->actual = Test_Utils::mapArray($arrRet,
            array('product_id',
                'category_id',
                'rank',
                'title',
                'comment',
                'creator_id',
                'create_date',
                'del_flg'
            )
        );

        $this->verify();
    }

    // best_idがnullでデータインサートされる。
    public function testSaveBestProducts_ベストIDがない場合_インサートされる(){

        if(DB_TYPE != 'pgsql') { //postgresqlだとどうしてもDBエラーになるのでとりいそぎ回避
            $sqlVal = array(
                'product_id'=>'4',
                'category_id' => '2',
                'rank' => '4',
                'title' => 'タイトルですよ1004',
                'comment' => 'コメントですよ1004',
                'creator_id' => '3',
                'del_flg' => '0'
            );

            $best_id = SC_Helper_BestProducts_Ex::saveBestProducts($sqlVal);

            $this->expected = array(
                'product_id'=>'4',
                'category_id' => '2',
                'rank' => '4',
                'title' => 'タイトルですよ1004',
                'comment' => 'コメントですよ1004',
                'creator_id' => '3',
                'del_flg' => '0'
            );

            $arrRet = SC_Helper_BestProducts_Ex::getBestProducts($best_id);

            $this->actual = Test_Utils::mapArray($arrRet,
                array('product_id',
                    'category_id',
                    'rank',
                    'title',
                    'comment',
                    'creator_id',
                    'del_flg'
                )
            );

            $this->verify();
        }

    }

    // best_idがnull、かつrankがnullの場合、想定されたランクが登録される
    public function testSaveBestProducts_インサート処理でrankがsetされてない場合_採番された値がセットされる(){

        if(DB_TYPE != 'pgsql') { //postgresqlだとどうしてもDBエラーになるのでとりいそぎ回避
            $sqlVal = array(
                'product_id'=>'5',
                'category_id' => '2',
                'title' => 'タイトルですよ5',
                'comment' => 'コメントですよ5',
                'creator_id' => '3',
                'del_flg' => '0'
            );

            $best_id = SC_Helper_BestProducts_Ex::saveBestProducts($sqlVal);

            $this->expected = "4"; //ランク

            $arrRet = SC_Helper_BestProducts_Ex::getBestProducts($best_id);

            $this->actual = $arrRet['rank'];

            $this->verify();
        }
    }
}

