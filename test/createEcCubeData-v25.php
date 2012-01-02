#!/usr/local/bin/php -q
<?php
/*
 * EC-CUBE データ生成スクリプト
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
 *
 * @auther Kentaro Ohkouchi
 * @version $Id$
 */

// {{{ requires
/** 適宜、htmlディレクトリへのrequire.phpを読み込めるよう パスを書き換えて下さい */
require_once(dirname(__FILE__) . "/../html/require.php");

// }}}
// {{{ constants

/** 大カテゴリの生成数 */
define("TOP_CATEGORIES_VOLUME", 5);

/** 中カテゴリの生成数 */
define("MIDDLE_CATEGORIES_VOLUME", 2);

/** 小カテゴリの生成数 */
define("SMALL_CATEGORIES_VOLUME", 3);

/** 規格1の生成数 */
define("CLASSCATEGORY1_VOLUME", 10);

/** 規格2の生成数 */
define("CLASSCATEGORY2_VOLUME", 10);

/** 商品の生成数 */
define("PRODUCTS_VOLUME", 100);

// }}}
// {{{ Logic
set_time_limit(0);
while (@ob_end_flush());

$objData = new CreateEcCubeData();
$start = microtime_float();
//$objData->objQuery->begin();

// カテゴリ生成
$objData->createCategories();
// 規格生成
$objData->createClassData();
// 商品生成
$objData->createProducts();
// 商品と規格の関連づけ
$objData->relateClass();
// 商品とカテゴリの関連づけ
$objData->relateProductsCategories();

$objDb = new SC_Helper_DB_Ex();
$objDb->sfCountCategory(NULL, true);

//$objData->objQuery->rollback();
//$objData->objQuery->commit();
$end = microtime_float();
print("データの生成が完了しました!\n");
printf("所要時間 %f 秒\n", $end - $start);


// }}}
// {{{ classes

/**
 * EC-CUBE のデータを生成する
 */
class CreateEcCubeData {

    /** SC_Query インスタンス */
    var $objQuery;

    /** 大カテゴリID の配列 */
    var $arrCategory1  = array();

    /** 中カテゴリID の配列 */
    var $arrCategory2  = array();

    /** 小カテゴリID の配列 */
    var $arrCategory3  = array();

    /** 規格1 */
    var $arrClassCategory_id1 = array();

    /** 規格2 */
    var $arrClassCategory_id2 = array();

    /** 削除するか */
    var $delete = false;

    /**
     * コンストラクタ.
     */
    function CreateEcCubeData() {
        $this->objQuery = new SC_Query();
    }

    /**
     * カテゴリを生成する.
     *
     * 以下のように, ツリー状のカテゴリを生成する
     *
     *  大カテゴリ -- 中カテゴリ -- 小カテゴリ
     *             |             |- 小カテゴリ
     *             |             |- 小カテゴリ
     *             |
     *             |- 中カテゴリ -- 小カテゴリ
     *                            |- 小カテゴリ
     *                            |- 小カテゴリ
     * @return void
     */
    function createCategories() {

        print("カテゴリを生成しています...\n");

        if ($this->delete) {
            $this->objQuery->delete('dtb_category');
        }

        $count = 0;

        // 全カテゴリ共通の値
        $sqlval['creator_id'] = 2;
        $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['del_flg'] = (string) "0";

        // 大カテゴリを生成
        for ($i = 0; $i < TOP_CATEGORIES_VOLUME; $i++) {
            $sqlval['category_name'] = sprintf("Category%d00", $i);
            $sqlval['parent_category_id'] = (string) "0";
            $sqlval['level'] = 1;
            $sqlval['rank'] = $this->lfGetTotalCategoryrank() - $count;
            $sqlval['category_id'] = $this->objQuery->nextVal("dtb_category_category_id");

            $this->objQuery->insert("dtb_category", $sqlval);
            $this->arrCategory1[] = $sqlval['category_id'];
            $count++;
            print(".");

            // 中カテゴリを生成
            for ($j = 0; $j < MIDDLE_CATEGORIES_VOLUME; $j++) {
                $sqlval['category_name'] = sprintf("Category%d%d0", $i,
                                                   $j + MIDDLE_CATEGORIES_VOLUME);
                $sqlval['parent_category_id'] = (string) $sqlval['category_id'];
                $sqlval['level'] = 2;
                $sqlval['rank'] = $this->lfGetTotalCategoryrank() - $count;
                $sqlval['category_id'] = $this->objQuery->nextVal("dtb_category_category_id");

                $this->objQuery->insert("dtb_category", $sqlval);
                $this->arrCategory2[] = $sqlval['category_id'];
                $count++;
                print(".");

                // 小カテゴリを生成
                for ($k = 0; $k < SMALL_CATEGORIES_VOLUME; $k++) {
                    $sqlval['category_name'] = sprintf("Category%d%d%d",
                                                       $i, $j,
                                                       $k + SMALL_CATEGORIES_VOLUME);
                    $sqlval['parent_category_id'] = (string) $sqlval['category_id'];
                    $sqlval['level'] = 3;
                    $sqlval['rank'] = $this->lfGetTotalCategoryrank() - $count;
                    $sqlval['category_id'] = $this->objQuery->nextVal("dtb_category_category_id");

                    $this->objQuery->insert("dtb_category", $sqlval);
                    $this->arrCategory3[] = $sqlval['category_id'];
                    $count++;
                    print(".");
                }
            }
        }
        print("\n");
    }

    /**
     * 規格を生成する.
     *
     * @return void
     */
    function createClassData() {
        // 規格データ生成
        print("規格データを生成しています...\n");

        if ($this->delete) {
            $this->objQuery->delete('dtb_class');
        }

        $this->createClass("Size");
        $this->createClass("Color");
        print("\n");

        // 規格分類データ生成
        print("規格分類データを生成しています...\n");

        if ($this->delete) {
            $this->objQuery->delete('dtb_classcategory');
        }

        // 規格1
        for ($i = 0; $i < CLASSCATEGORY1_VOLUME; $i++) {
            $this->createClassCategory($this->arrSize[$i],
                                       $this->arrclass_id[0], "size");
        }

        // 規格2
        for ($i = 0; $i < CLASSCATEGORY2_VOLUME; $i++) {
            $this->createClassCategory($this->arrColor[$i],
                                       $this->arrclass_id[1], "color");
        }

        print("\n");
    }

    /**
     * 商品と規格の関連づけを行う.
     *
     * @return void
     */
    function relateClass() {

        print("商品と規格の関連づけを行います...\n");

        if ($this->delete) {
            $this->objQuery->delete('dtb_class_combination');
            $this->objQuery->delete('dtb_products_class');
        }

        foreach ($this->arrProduct_id as $product_id) {
            $this->createProductsClass($product_id);
        }
        print("\n");
    }

    /**
     * 商品を生成する.
     *
     * @return void
     */
    function createProducts() {

        print("商品を生成しています...\n");

        if ($this->delete) {
            $this->objQuery->delete('dtb_products');
        }

        for ($i = 0; $i < PRODUCTS_VOLUME; $i++) {
            $sqlval['product_id'] = $this->objQuery->nextval("dtb_products_product_id");
            $sqlval['name'] = sprintf("商品%d", $i);
            $sqlval['status'] = 1;
            $sqlval['comment3'] = "コメント";
            $sqlval['main_list_comment'] = "コメント";
            $sqlval['main_list_image'] = "08311201_44f65122ee5fe.jpg";
            $sqlval['main_comment'] = "コメント";
            $sqlval['main_image'] = "08311202_44f6515906a41.jpg";
            $sqlval['main_large_image'] = "08311203_44f651959bcb5.jpg";
            $sqlval['sub_comment1'] = "コメント";
            $sqlval['del_flg'] = (string) "0";
            $sqlval['creator_id'] = 2;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['deliv_date_id'] = 2;
            $this->objQuery->insert("dtb_products", $sqlval);

            $this->arrProduct_id[] = $sqlval['product_id'];
            print("*");
        }
        print("\n");
    }

    /**
     * 規格を生成する.
     *
     * @param $class_name string 規格名
     * @return void
     */
    function createClass($class_name) {
        // class_idを取得
        $sqlval['class_id'] = $this->objQuery->nextVal("dtb_class_class_id");
        $sqlval['name'] = $class_name;
        $arrRaw['rank'] = "(SELECT x.rank FROM (SELECT CASE
                                      WHEN max(rank) + 1 IS NULL THEN 1
                                      ELSE max(rank) + 1
                                    END as rank
                               FROM dtb_class
                              WHERE del_flg = 0) as x)";
        $sqlval['creator_id'] = 2;
        $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['del_flg'] = (string) "0";
        $this->objQuery->insert("dtb_class", $sqlval, $arrRaw);

        $this->arrclass_id[] = $sqlval['class_id'];
        print("+");
    }

    /**
     * 規格分類を生成する.
     *
     * @param $classcategory_name string 規格名
     * @return void
     */
    function createClassCategory($classcategory_name, $class_id, $class_name) {
        $sqlval['classcategory_id'] = $this->objQuery->nextVal("dtb_classcategory_classcategory_id");
        $sqlval['name'] = $classcategory_name;
        $sqlval['class_id'] = $class_id;
        $arrRaw['rank'] = sprintf("~(SELECT x.rank FROM (SELECT CASE
                                              WHEN max(rank) + 1 IS NULL THEN 1
                                              ELSE max(rank) + 1
                                            END as rank
                                       FROM dtb_classcategory
                                      WHERE del_flg = 0
                                        AND class_id = %d) as x)", $class_id);
        $sqlval['creator_id'] = 2;
        $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['del_flg'] = (string) "0";

        $this->objQuery->insert("dtb_classcategory", $sqlval, $arrRaw);

        switch ($class_name) {
        case "size":
            $this->arrClassCategory_id1[] = $sqlval['classcategory_id'];
            break;

        case "color":
            $this->arrClassCategory_id2[] = $sqlval['classcategory_id'];
            break;
        default:
        }
        print("+");
    }

    /**
     * 商品規格を生成する.
     *
     * @param integer $product_id 商品ID
     * @return void
     */
    function createProductsClass($product_id) {

        printf("商品ID %d の商品規格を生成しています...\n", $product_id);

        $sqlval['product_id'] = $product_id;
        $sqlval['product_type_id'] = 1;
        $sqlval['stock_unlimited'] = 1;
        $sqlval['price01'] = 1000;
        $sqlval['price02'] = 2000;
        $sqlval['point_rate'] = 10;
        $sqlval['creator_id'] = 2;
        $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['del_flg'] = 0;

        $count = 0;
        foreach ($this->arrClassCategory_id1 as $classCategory_id1) {
            foreach ($this->arrClassCategory_id2 as $classCategory_id2) {
                $c1['classcategory_id'] = $classCategory_id1;
                $c1['class_combination_id'] = $this->objQuery->nextVal('dtb_class_combination_class_combination_id');
                $c1['level'] = 1;
                $this->objQuery->insert("dtb_class_combination", $c1);

                $c2['classcategory_id'] = $classCategory_id2;
                $c2['class_combination_id'] = $this->objQuery->nextVal('dtb_class_combination_class_combination_id');
                $c2['parent_class_combination_id'] = $c1['class_combination_id'];
                $c2['level'] = 2;
                $this->objQuery->insert("dtb_class_combination", $c2);

                $sqlval['product_class_id'] =
                    $this->objQuery->nextVal('dtb_products_class_product_class_id');
                $sqlval['product_code'] = sprintf("商品コード%d", $count);

                $sqlval['class_combination_id'] = $c2['class_combination_id'];
                $this->objQuery->insert("dtb_products_class", $sqlval);

                $count++;
                print("#");
            }
        }

        // 規格無し用
        $sqlval['product_class_id'] = $this->objQuery->nextVal('dtb_products_class_product_class_id');
        $sqlval['class_combination_id'] = null;
        $sqlval['del_flg'] = 1;
        $this->objQuery->insert("dtb_products_class", $sqlval);

        print("\n");
    }

    /**
     * 商品とカテゴリの関連づけを行う.
     *
     * @return void
     */
    function relateProductsCategories() {

        print("商品とカテゴリの関連づけを行います...\n");

        if ($this->delete) {
            $this->objQuery->delete('dtb_product_categories');
        }

        $this->createProductsCategories($this->arrCategory1, "大カテゴリ");
        $this->createProductsCategories($this->arrCategory2, "中カテゴリ");
        $this->createProductsCategories($this->arrCategory3, "小カテゴリ");
    }

    /**
     * 商品カテゴリを生成する.
     *
     * @param array $arrCategory_id カテゴリID の配列
     * @return void
     */
    function createProductsCategories($arrCategory_id, $category_name) {

        $count = 0;
        printf("%s の商品カテゴリを生成しています...\n", $category_name);
        foreach ($arrCategory_id as $category_id) {
            $sqlval['category_id'] = $category_id;

            foreach($this->arrProduct_id as $product_id) {
                $sqlval['product_id'] = $product_id;
                $sqlval['rank'] = $count;

                $this->objQuery->insert("dtb_product_categories", $sqlval);
                $count++;
                print("$");
            }
        }
        print("\n");
    }

    /** 規格1 */
    var $arrSize = array("m11(29cm)"
                         ,"m10 1/2(28.5cm)"
                         ,"m10(28cm)"
                         ,"m9 1/2(27.5cm)"
                         ,"m9(27cm)"
                         ,"m8 1/2(26.5cm)"
                         ,"m8(26cm)"
                         ,"43"
                         ,"42"
                         ,"41"
                         ,"43(27.0cm?27.5cm)"
                         ,"42(26.5cm?27.0cm)"
                         ,"37(ladies 23.5?24cm)"
                         ,"42(約27.5cm)"
                         ,"41(約26.5cm)"
                         ,"W36"
                         ,"W34"
                         ,"W32"
                         ,"43"
                         ,"42"
                         ,"41"
                         ,"m11"
                         ,"m10"
                         ,"m9.5"
                         ,"m9"
                         ,"m8"
                         ,"FREE"
                         ,"XS"
                         ,"S"
                         ,"M"
                         ,"L"
                         ,"XL"
                         ,"25-27"
                         ,"27-29"
                         ,"W28"
                         ,"W29"
                         ,"W30"
                         ,"W31"
                         ,"W32"
                         ,"W33"
                         ,"W34"
                         ,"W35"
                         ,"W36"
                         ,"4"
                         ,"6"
                         ,"8"
                         ,"10"
                         ,"12"
                         ,"10cm"
                         ,"12cm"
                         ,"14cm"
                         ,"16cm"
                         ,"18cm"
                         ,"20cm"
                         ,"22cm"
                         ,"24cm"
                         ,"26cm"
                         ,"28cm"
                         ,"30cm"
                         ,"32cm"
                         ,"34cm"
                         ,"36cm"
                         ,"38cm"
                         ,"40cm"
                         ,"10g"
                         ,"20g"
                         ,"30g"
                         ,"40g"
                         ,"50g"
                         ,"60g"
                         ,"70g"
                         ,"80g"
                         ,"90g"
                         ,"100g"
                         ,"110g"
                         ,"120g"
                         ,"130g"
                         ,"140g"
                         ,"150g"
                         ,"160g"
                         ,"170g"
                         ,"180g"
                         ,"190g"
                         ,"200g"
                         ,"8inch"
                         ,"10inch"
                         ,"12inch"
                         ,"14inch"
                         ,"16inch"
                         ,"18inch"
                         ,"20inch"
                         ,"22inch"
                         ,"24inch"
                         ,"26inch"
                         ,"28inch"
                         ,"30inch"
                         ,"32inch"
                         ,"34inch"
                         ,"36inch"
                         ,"38inch"
                    );

    /** 規格2 */
    var $arrColor = array("white"
                         ,"whitesmoke"
                         ,"snow"
                         ,"ghostwhite"
                         ,"mintcream"
                         ,"azure"
                         ,"ivory"
                         ,"floralwhite"
                         ,"aliceblue"
                         ,"lavenderblush"
                         ,"seashell"
                         ,"honeydew"
                         ,"lightyellow"
                         ,"oldlace"
                         ,"cornsilk"
                         ,"linen"
                         ,"lemonchiffon"
                         ,"lavender"
                         ,"beige"
                         ,"lightgoldenrodyellow"
                         ,"mistyrose"
                         ,"papayawhip"
                         ,"antiquewhite"
                         ,"lightcyan"
                         ,"cyan"
                         ,"aqua"
                         ,"darkcyan"
                         ,"teal"
                         ,"darkslategray"
                         ,"turquoise"
                         ,"paleturquoise"
                         ,"mediumturquoise"
                         ,"aquamarine"
                         ,"gainsboro"
                         ,"lightgray"
                         ,"silver"
                         ,"darkgray"
                         ,"gray"
                         ,"dimgray"
                         ,"black"
                         ,"powderblue"
                         ,"lightblue"
                         ,"lightskyblue"
                         ,"skyblue"
                         ,"darkturquoise"
                         ,"deepskyblue"
                         ,"dodgerblue"
                         ,"royalblue"
                         ,"cornflowerblue"
                         ,"cadetblue"
                         ,"lightsteelblue"
                         ,"steelblue"
                         ,"lightslategray"
                         ,"slategray"
                         ,"blue"
                         ,"mediumblue"
                         ,"darkblue"
                         ,"navy"
                         ,"midnightblue"
                         ,"lightsalmon"
                         ,"darksalmon"
                         ,"salmon"
                         ,"tomato"
                         ,"lightcoral"
                         ,"coral"
                         ,"crimson"
                         ,"red"
                         ,"mediumorchid"
                         ,"mediumpurple"
                         ,"mediumslateblue"
                         ,"slateblue"
                         ,"blueviolet"
                         ,"darkviolet"
                         ,"darkorchid"
                         ,"darkslateblue"
                         ,"darkorchid"
                         ,"thistle"
                         ,"plum"
                         ,"violet"
                         ,"magenta"
                         ,"fuchsia"
                         ,"darkmagenta"
                         ,"purple"
                         ,"palegreen"
                         ,"lightgreen"
                         ,"lime"
                         ,"limegreen"
                         ,"forestgreen"
                         ,"green"
                         ,"darkgreen"
                         ,"greenyellow"
                         ,"chartreuse"
                         ,"lawngreen"
                         ,"yellowgreen"
                         ,"olivedrab"
                         ,"darkolivegreen"
                         ,"mediumaquamarine"
                         ,"mediumspringgreen"
                         ,"springgreen"
                         ,"darkseagreen"
                     );

    /**
    * 総カテゴリ数を計算し、dtb_categoryに代入するrankに使う
    */
    function lfGetTotalCategoryrank(){
        $TotalCategoryrank = (TOP_CATEGORIES_VOLUME * MIDDLE_CATEGORIES_VOLUME * SMALL_CATEGORIES_VOLUME) + (MIDDLE_CATEGORIES_VOLUME * TOP_CATEGORIES_VOLUME) + TOP_CATEGORIES_VOLUME;
    return $TotalCategoryrank;
    }

}

/** PHP4対応のための microtime 関数 */
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

?>
