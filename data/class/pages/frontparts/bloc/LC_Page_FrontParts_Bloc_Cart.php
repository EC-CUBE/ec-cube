<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * カート のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc_Cart extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = BLOC_PATH . 'cart.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objSubView = new SC_SiteView();
        $objCart = new SC_CartSession();
        $objSiteInfo = new SC_SiteInfo;

        if (count($_SESSION[$objCart->key]) > 0){
            // カート情報を取得
            $arrCartList = $objCart->getCartList();

            // カート内の商品ＩＤ一覧を取得
            $arrAllProductID = $objCart->getAllProductID();
            // 商品が1つ以上入っている場合には商品名称を取得
            if (count($arrAllProductID) > 0){
                $objQuery = new SC_Query();
                $arrVal = array();
                $sql = "";
                $sql = "SELECT name FROM dtb_products WHERE product_id IN ( ?";
                $arrVal = array($arrAllProductID[0]);
                for($i = 1 ; $i < count($arrAllProductID) ; $i++){
                    $sql.= " ,? ";
                    array_push($arrVal, $arrAllProductID[$i]);
                }
                $sql.= " )";

                $arrProduct_name = $objQuery->getAll($sql, $arrVal);

                foreach($arrProduct_name as $key => $val){
                    $arrCartList[$key]['product_name'] = $val['name'];
                }
            }
            // 店舗情報の取得
            $arrInfo = $objSiteInfo->data;
            // 購入金額合計
            $ProductsTotal = $objCart->getAllProductsTotal($arrInfo);

            // 合計個数
            $TotalQuantity = $objCart->getTotalQuantity();

            // 送料無料までの金額
            $arrCartList[0]['ProductsTotal'] = $ProductsTotal;
            $arrCartList[0]['TotalQuantity'] = $TotalQuantity;
            $deliv_free = $arrInfo['free_rule'] - $ProductsTotal;
            $arrCartList[0]['free_rule'] = $arrInfo['free_rule'];
            $arrCartList[0]['deliv_free'] = $deliv_free;

            $this->arrCartList = $arrCartList;
        }

        $objSubView->assignobj($this);
        $objSubView->display($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
?>
