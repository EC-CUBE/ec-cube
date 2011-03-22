<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once(realpath(dirname(__FILE__)) . '/../../require.php');
require_once(realpath(dirname(__FILE__)) . '/../../../data/class/pages/admin/products/LC_Page_Admin_Products_ProductClass.php');

/**
 * LC_Page_Admin_Products_ProductClass のテストケース.
 *
 * @package Page
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class LC_Page_Admin_Products_ProductClass_Test extends PHPUnit_Framework_TestCase {

    function setUp() {
        $this->objQuery =& SC_Query::getSingletonInstance();
        $this->objQuery->begin();

        $this->objPage = new LC_Page_Admin_Products_ProductClass_Mock();
        $this->objPage->init();
        $this->now = "2010-01-01 00:00:00";
    }

    function tearDown() {
        $this->objQuery->rollback();
        $this->objQuery = null;
        $this->objPage = null;
    }

    function testInit() {

        $this->assertEquals('products/product_class.tpl',
                            $this->objPage->tpl_mainpage);
        $this->assertEquals('products/subnavi.tpl',
                            $this->objPage->tpl_subnavi);
        $this->assertEquals('products',
                            $this->objPage->tpl_mainno);
        $this->assertEquals('product',
                            $this->objPage->tpl_subno);
        $this->assertEquals('商品登録(商品規格)',
                            $this->objPage->tpl_subtitle);
    }

    function testProcess() {
        $this->objPage->process();
    }

    function testCreateSearchParams() {
        $keys = array('search_product_id',
                      'search_product_code',
                      'search_category_id',
                      'search_product_class_name',
                      'search_name',
                      'search_startyear',
                      'search_startmonth',
                      'search_startday',
                      'search_endyear',
                      'search_endmonth',
                      'search_endday',
                      'search_page_max',
                      'search_pageno',
                      'mode');
        foreach ($keys as $key) {
            $_POST[$key] = $key . " in value.";
        }

        $this->expected = array('search_product_id' => 'search_product_id in value.',
                                'search_product_code' => 'search_product_code in value.',
                                'search_category_id' => 'search_category_id in value.',
                                'search_product_class_name' => 'search_product_class_name in value.',
                                'search_name' => 'search_name in value.',
                                'search_startyear' => 'search_startyear in value.',
                                'search_startmonth' => 'search_startmonth in value.',
                                'search_startday' => 'search_startday in value.',
                                'search_endyear' => 'search_endyear in value.',
                                'search_endmonth' => 'search_endmonth in value.',
                                'search_endday' => 'search_endday in value.',
                                'search_page_max' => 'search_page_max in value.',
                                'search_pageno' => 'search_pageno in value.');

        $this->actual = $this->objPage->createSearchParams($_POST);

        $this->verify();
    }

    function testGetProductName() {
        $product_id = 10000000;
        $this->expected = "テスト商品";
        $this->setProduct($product_id, $this->expected);

        $this->actual = $this->objPage->getProductName($product_id);

        $this->verify();
    }

    function testGetProductsClass() {
        $product_id = 10000000;
        $product_class_id = 10000;
        $this->setProductsClass($product_id, $product_class_id);
        $this->expected = array('product_class_id' => $product_class_id,
                                'product_id' => $product_id,
                                'class_combination_id' => null,
                                'product_code' => 'product_code' . $product_class_id,
                                'stock' => null,
                                'stock_unlimited' => 0,
                                'sale_limit' => null,
                                'price01' => 10000,
                                'price02' => null,
                                'deliv_fee' => null,
                                'point_rate' => null,
                                'status' => null,
                                'creator_id' => 1,
                                'create_date' => $this->now,
                                'update_date' => null,
                                'del_flg' => 0
                                );

        $this->actual = $this->objPage->getProductsClass($product_id);

        $this->verify();
    }

    function testGetAllClass() {
        $this->clearClass();
        $this->setClass(1000, "大きさ", 1, array("S", "M", "L", "LL"));
        $this->setClass(2, "色", 2, array("赤", "青", "黄", "緑"));
        $this->setClass(3, "味", 3, array());

        $this->expected = array("1000" => "大きさ",
                                "2" => "色");
        $this->actual = $this->objPage->getAllClass();

        $this->verify();
    }

    function testGetAllClassCategory規格1のみ() {
        $this->clearClass();
        $this->setClass(1000, "大きさ", 1, array("S", "M", "L", "LL"));
        $this->setClass(2, "色", 2, array("赤", "青", "黄", "緑"));
        $this->setClass(3, "味", 3, array("甘口", "中辛", "辛口"));

        $this->expected = array(
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000004,
                                      "name1" => "LL",
                                      "rank1" => 4),
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000003,
                                      "name1" => "L",
                                      "rank1" => 3),
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000002,
                                      "name1" => "M",
                                      "rank1" => 2),
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000001,
                                      "name1" => "S",
                                      "rank1" => 1));

        $this->actual = $this->objPage->getAllClassCategory(1000);
        $this->verify();
    }

    function testGetAllClassCategory規格1と3() {
        $this->clearClass();
        $this->setClass(1000, "大きさ", 1, array("S", "M", "L", "LL"));
        $this->setClass(2, "色", 2, array("赤", "青", "黄", "緑"));
        $this->setClass(3, "味", 3, array("甘口", "中辛", "辛口"));

        $this->expected = array(
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000004,
                                      "name1" => "LL",
                                      "rank1" => 4,
                                      "class_id2" => 3,
                                      "classcategory_id2" => 3000003,
                                      "name2" => "辛口",
                                      "rank2" => 3),
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000004,
                                      "name1" => "LL",
                                      "rank1" => 4,
                                      "class_id2" => 3,
                                      "classcategory_id2" => 3000002,
                                      "name2" => "中辛",
                                      "rank2" => 2),
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000004,
                                      "name1" => "LL",
                                      "rank1" => 4,
                                      "class_id2" => 3,
                                      "classcategory_id2" => 3000001,
                                      "name2" => "甘口",
                                      "rank2" => 1),

                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000003,
                                      "name1" => "L",
                                      "rank1" => 3,
                                      "class_id2" => 3,
                                      "classcategory_id2" => 3000003,
                                      "name2" => "辛口",
                                      "rank2" => 3),
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000003,
                                      "name1" => "L",
                                      "rank1" => 3,
                                      "class_id2" => 3,
                                      "classcategory_id2" => 3000002,
                                      "name2" => "中辛",
                                      "rank2" => 2),
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000003,
                                      "name1" => "L",
                                      "rank1" => 3,
                                      "class_id2" => 3,
                                      "classcategory_id2" => 3000001,
                                      "name2" => "甘口",
                                      "rank2" => 1),

                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000002,
                                      "name1" => "M",
                                      "rank1" => 2,
                                      "class_id2" => 3,
                                      "classcategory_id2" => 3000003,
                                      "name2" => "辛口",
                                      "rank2" => 3),
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000002,
                                      "name1" => "M",
                                      "rank1" => 2,
                                      "class_id2" => 3,
                                      "classcategory_id2" => 3000002,
                                      "name2" => "中辛",
                                      "rank2" => 2),
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000002,
                                      "name1" => "M",
                                      "rank1" => 2,
                                      "class_id2" => 3,
                                      "classcategory_id2" => 3000001,
                                      "name2" => "甘口",
                                      "rank2" => 1),

                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000001,
                                      "name1" => "S",
                                      "rank1" => 1,
                                      "class_id2" => 3,
                                      "classcategory_id2" => 3000003,
                                      "name2" => "辛口",
                                      "rank2" => 3),
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000001,
                                      "name1" => "S",
                                      "rank1" => 1,
                                      "class_id2" => 3,
                                      "classcategory_id2" => 3000002,
                                      "name2" => "中辛",
                                      "rank2" => 2),
                                array("class_id1" => 1000,
                                      "classcategory_id1" => 1000000001,
                                      "name1" => "S",
                                      "rank1" => 1,
                                      "class_id2" => 3,
                                      "classcategory_id2" => 3000001,
                                      "name2" => "甘口",
                                      "rank2" => 1),
                                );

        $this->actual = $this->objPage->getAllClassCategory(1000, 3);
        $this->verify();
    }

    function testGetProductsClassAndClasscategory() {
        $product_id = 10000;
        $product_class_id = 1000;
        $class_combination_id = 200;
        $this->clearClass();
        $this->setClass(1000, "大きさ", 1, array("S", "M", "L", "LL"));
        $this->setClass(2, "色", 2, array("赤", "青", "黄", "緑"));
        $this->setClass(3, "味", 3, array("甘口", "中辛", "辛口"));
        $this->setProductsClass($product_id, $product_class_id,
                                $class_combination_id);
        $this->setClassCombination($class_combination_id, 100, 3000001, 2);
        $this->setClassCombination(100, null, 2000001, 1);


        $this->expected = array(
                                array("class_id1" => 2,
                                      "class_id2" => 3,
                                      "name1" => "赤",
                                      "name2" => "甘口",
                                      "rank1" => 1,
                                      "rank2" => 1,
                                      "product_class_id" => 1000,
                                      "product_id" => 10000,
                                      "classcategory_id1" => 2000001,
                                      "classcategory_id2" => 3000001,
                                      "product_code" => "product_code1000",
                                      "stock" => null,
                                      "stock_unlimited" => 0,
                                      "sale_limit" => null,
                                      "price01" => 10000,
                                      "price02" => null));

        $this->actual = $this->objPage->getProductsClassAndClasscategory($product_id);

        $this->verify();
    }

    function verify() {
        $this->assertEquals($this->expected, $this->actual);
    }

    function setProduct($product_id, $name) {
        $val['product_id'] = $product_id;
        $val['name'] = $name;
        $val['creator_id'] = 1;
        $val['deliv_date_id'] = 1;
        $this->objQuery->insert("dtb_products", $val);
    }

    function setProductsClass($product_id, $product_class_id, $class_combination_id = null) {
        $val['product_class_id'] = $product_class_id;
        $val['product_id'] = $product_id;
        $val['class_combination_id'] = $class_combination_id;
        $val['product_code'] = 'product_code' . $product_class_id;
        $val['price01'] = 10000;
        $val['creator_id'] = 1;
        $val['create_date'] = $this->now;
        $val['del_flg'] = 0;
        $this->objQuery->insert("dtb_products_class", $val);
    }

    function setClassCombination($class_combination_id,
                                 $parent_class_combination_id, $classcategory_id,
                                 $level) {
        $val['class_combination_id'] = $class_combination_id;
        $val['parent_class_combination_id'] = $parent_class_combination_id;
        $val['classcategory_id'] = $classcategory_id;
        $val['level'] = $level;
        $this->objQuery->insert("dtb_class_combination", $val);
    }

    function clearClass() {
        $this->objQuery->delete("dtb_class");
        $this->objQuery->delete("dtb_classcategory");
    }

    /**
     * 規格と規格分類を生成する.
     *
     * @param integer $class_id 規格ID
     * @param string $class_name 規格名
     * @param integer $rank 規格の表示順
     * @param array $classcategory 規格分類名の配列
     */
    function setClass($class_id, $class_name, $rank, $classcategory) {
        $val['class_id'] = $class_id;
        $val['name'] = $class_name;
        $val['creator_id'] = 1;
        $val['del_flg'] = 0;
        $val['rank'] = $rank;

        $this->objQuery->insert("dtb_class", $val);
        $i = 1;
        foreach ($classcategory as $name) {
            $val['classcategory_id'] = $class_id . "00000" . $i;
            $val['name'] = $name;
            $val['rank'] = $i;
            $this->objQuery->insert("dtb_classcategory", $val);
            $i++;
        }
    }
}

class LC_Page_Admin_Products_ProductClass_Mock extends LC_Page_Admin_Products_ProductClass {

    function authorization() {
        // quiet.
    }

    function assignView() {
        // quiet.
    }
}
?>
