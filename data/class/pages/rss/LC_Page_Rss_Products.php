<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(CLASS_REALDIR . "pages/LC_Page.php");

/**
 * RSS(商品) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Rss_Products extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = "rss/products.tpl";
        $this->encode = "UTF-8";
        $this->title = "商品一覧情報";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objQuery = new SC_Query();
        $objView = new SC_SiteView();
        $objSiteInfo = new SC_SiteInfo();

        //店舗情報をセット
        $this->arrSiteInfo = $objSiteInfo->data;

        //商品IDを取得
        $product_id = $_GET['product_id'];
        $mode = $this->getMode();

        if(($product_id != "" and is_numeric($product_id)) or $mode == "all"){
            //商品詳細を取得
            ($mode == "all") ? $arrProduct = $this->lfGetProductsDetail($objQuery, $mode) : $arrProduct = $this->lfGetProductsDetail($objQuery, $product_id);

            // 値のセットし直し
            foreach($arrProduct as $key => $val){
                //販売価格を税込みに編集
                $arrProduct[$key]["price02"] = SC_Helper_DB_Ex::sfCalcIncTax($arrProduct[$key]["price02"]);

                // 画像ファイルのURLセット
                (file_exists(IMAGE_SAVE_REALDIR . $arrProduct[$key]["main_list_image"])) ? $dir = IMAGE_SAVE_RSS_URL : $dir = IMAGE_TEMP_RSS_URL;
                $arrProduct[$key]["main_list_image"] = $dir . $arrProduct[$key]["main_list_image"];
                (file_exists(IMAGE_SAVE_REALDIR . $arrProduct[$key]["main_image"])) ? $dir = IMAGE_SAVE_RSS_URL : $dir = IMAGE_TEMP_RSS_URL;
                $arrProduct[$key]["main_image"] = $dir . $arrProduct[$key]["main_image"];
                (file_exists(IMAGE_SAVE_REALDIR . $arrProduct[$key]["main_large_image"])) ? $dir = IMAGE_SAVE_RSS_URL : $dir = IMAGE_TEMP_RSS_URL;
                $arrProduct[$key]["main_large_image"] = $dir . $arrProduct[$key]["main_large_image"];

                // ポイント計算
                $arrProduct[$key]["point"] = SC_Utils_Ex::sfPrePoint($arrProduct[$key]["price02"], $arrProduct[$key]["point_rate"], POINT_RULE, $arrProduct[$key]["product_id"]);

                // 在庫無制限
                $arrProduct[$key]["stock_unlimited"] = ($arrProduct[$key]["stock_unlimited"] == 1) ? "在庫無制限" : NULL;
            }
        }elseif($mode == "list"){
            //商品一覧を取得
            $arrProduct = $objQuery->getAll("SELECT product_id, name AS product_name FROM dtb_products");
        }else{
            $arrProduct = $this->lfGetProductsAllclass($objQuery);

            // 値のセットし直し
            foreach($arrProduct as $key => $val){
                //販売価格を税込みに編集
                $arrProduct[$key]["price02_max"] = SC_Helper_DB_Ex::sfCalcIncTax($arrProduct[$key]["price02_max"]);
                $arrProduct[$key]["price02_min"] = SC_Helper_DB_Ex::sfCalcIncTax($arrProduct[$key]["price02_min"]);

                // 画像ファイルのURLセット
                (file_exists(IMAGE_SAVE_REALDIR . $arrProduct[$key]["main_list_image"])) ? $dir = IMAGE_SAVE_RSS_URL : $dir = IMAGE_TEMP_RSS_URL;
                $arrProduct[$key]["main_list_image"] = $dir . $arrProduct[$key]["main_list_image"];
                (file_exists(IMAGE_SAVE_REALDIR . $arrProduct[$key]["main_image"])) ? $dir = IMAGE_SAVE_RSS_URL : $dir = IMAGE_TEMP_RSS_URL;
                $arrProduct[$key]["main_image"] = $dir . $arrProduct[$key]["main_image"];
                (file_exists(IMAGE_SAVE_REALDIR . $arrProduct[$key]["main_large_image"])) ? $dir = IMAGE_SAVE_RSS_URL : $dir = IMAGE_TEMP_RSS_URL;
                $arrProduct[$key]["main_large_image"] = $dir . $arrProduct[$key]["main_large_image"];

                // ポイント計算
                $arrProduct[$key]["point_max"] = SC_Utils_Ex::sfPrePoint($arrProduct[$key]["price02_max"], $arrProduct[$key]["point_rate"], POINT_RULE, $arrProduct[$key]["product_id"]);
                $arrProduct[$key]["point_min"] = SC_Utils_Ex::sfPrePoint($arrProduct[$key]["price02_min"], $arrProduct[$key]["point_rate"], POINT_RULE, $arrProduct[$key]["product_id"]);
            }
        }

        //商品情報をセット
        $this->arrProduct = $arrProduct;
        if(is_array(SC_Utils_Ex::sfswaparray($arrProduct))){
            $this->arrProductKeys = array_keys(SC_Utils_Ex::sfswaparray($arrProduct));
        }

        //セットしたデータをテンプレートファイルに出力
        $objView->assignobj($this);

        //キャッシュしない(念のため)
        header("Pragma: no-cache");

        //XMLテキスト(これがないと正常にRSSとして認識してくれないツールがあるため)
        header("Content-type: application/xml");
        P_DETAIL_URLPATH;

        //画面表示
        $objView->display($this->tpl_mainpage, true);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * 商品情報を取得する
     *
     * @param SC_Query $objQuery DB操作クラス
     * @param integer $product_id 商品ID
     * @return array $arrProduct 取得結果を配列で返す
     */
    function lfGetProductsDetail(&$objQuery, $product_id = "all"){
        $sql = "";
        $sql .= "SELECT ";
        $sql .= "   prod.product_id ";
        $sql .= "   ,prod.name AS product_name ";
        $sql .= "   ,prod.category_id ";
        $sql .= "   ,prod.point_rate ";
        $sql .= "   ,prod.comment3 ";
        $sql .= "   ,prod.main_list_comment ";
        $sql .= "   ,prod.main_list_image ";
        $sql .= "   ,prod.main_comment ";
        $sql .= "   ,prod.main_image ";
        $sql .= "   ,prod.main_large_image ";
        $sql .= "   ,cls.product_code ";
        $sql .= "   ,cls.price01 ";
        $sql .= "   ,cls.price02 ";
        $sql .= "   ,cls.stock ";
        $sql .= "   ,cls.stock_unlimited ";
        $sql .= "   ,cls.classcategory_id1 ";
        $sql .= "   ,cls.classcategory_id2 ";
        $sql .= "   ,(SELECT name FROM dtb_classcategory AS clscat WHERE clscat.classcategory_id = cls.classcategory_id1) AS classcategory_name1 ";
        $sql .= "   ,(SELECT name FROM dtb_classcategory AS clscat WHERE clscat.classcategory_id = cls.classcategory_id2) AS classcategory_name2 ";
        $sql .= "   ,(SELECT category_name FROM dtb_category AS cat WHERE cat.category_id = prod.category_id) AS category_name";
        $sql .= "   ,prod.update_date ";
        $sql .= " FROM dtb_products AS prod, dtb_products_class AS cls";
        $sql .= " WHERE prod.product_id = cls.product_id AND prod.del_flg = 0 AND prod.status = 1";

        if($product_id != "all"){
            $sql .= " AND prod.product_id = ?";
            $arrval = array($product_id);
        }
        $sql .= " ORDER BY prod.product_id, cls.classcategory_id1, cls.classcategory_id2";
        $arrProduct = $objQuery->getAll($sql, $arrval);
        return $arrProduct;
    }

    /**
     * 商品情報を取得する(vw_products_allclass使用)
     *
     * @param SC_Query $objQuery DB操作クラス
     * @return array $arrProduct 取得結果を配列で返す
     */
    function lfGetProductsAllclass($objQuery){
        // FIXME SC_Product クラスを使用した実装
        $sql = "";
        $sql .= "SELECT
                product_id
                ,name as product_name
                ,category_id
                ,point_rate
                ,comment3
                ,main_list_comment
                ,main_image
                ,main_list_image
                ,product_code_min
                ,product_code_max
                ,price01_min
                ,price01_max
                ,price02_min
                ,price02_max
                ,(SELECT category_name FROM dtb_category AS cat WHERE cat.category_id = allcls.category_id) AS category_name
                ,(SELECT main_large_image FROM dtb_products AS prod WHERE prod.product_id = allcls.product_id) AS main_large_image
            FROM  vw_products_allclass as allcls
            WHERE allcls.del_flg = 0 AND allcls.status = 1";

        // 在庫無し商品の非表示
        if (NOSTOCK_HIDDEN === true) {
            $sql .= ' AND (allcls.stock_max >= 1 OR allcls.stock_unlimited_max = 1)';
        }

        $sql .= " ORDER BY allcls.product_id";

        $arrProduct = $objQuery->getAll($sql);
        return $arrProduct;
    }
}
?>
