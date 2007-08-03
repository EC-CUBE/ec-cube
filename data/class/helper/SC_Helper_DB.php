<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**
 * DB関連のヘルパークラス.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Helper_DB {

    // }}}
    // {{{ functions

    /**
     * 商品規格情報を取得する.
     *
     * @param array $arrID 規格ID
     * @return array 規格情報の配列
     */
    function sfGetProductsClass($arrID) {
        list($product_id, $classcategory_id1, $classcategory_id2) = $arrID;

        if($classcategory_id1 == "") {
            $classcategory_id1 = '0';
        }
        if($classcategory_id2 == "") {
            $classcategory_id2 = '0';
        }

        // 商品規格取得
        $objQuery = new SC_Query();
        $col = "product_id, deliv_fee, name, product_code, main_list_image, main_image, price01, price02, point_rate, product_class_id, classcategory_id1, classcategory_id2, class_id1, class_id2, stock, stock_unlimited, sale_limit, sale_unlimited";
        $table = "vw_product_class AS prdcls";
        $where = "product_id = ? AND classcategory_id1 = ? AND classcategory_id2 = ?";
        $objQuery->setorder("rank1 DESC, rank2 DESC");
        $arrRet = $objQuery->select($col, $table, $where, array($product_id, $classcategory_id1, $classcategory_id2));
        return $arrRet[0];
    }

    /**
     * カート内商品の集計処理を行う.
     *
     * @param LC_Page $objPage ページクラスのインスタンス
     * @param SC_CartSession $objCartSess カートセッションのインスタンス
     * @param array $arrInfo 商品情報の配列
     * @return LC_Page 集計処理後のページクラスインスタンス
     */
    function sfTotalCart($objPage, $objCartSess, $arrInfo) {
        // 規格名一覧
        $arrClassName = SC_Utils::sfGetIDValueList("dtb_class", "class_id", "name");
        // 規格分類名一覧
        $arrClassCatName = SC_Utils::sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");

        $objPage->tpl_total_pretax = 0;		// 費用合計(税込み)
        $objPage->tpl_total_tax = 0;		// 消費税合計
        $objPage->tpl_total_point = 0;		// ポイント合計

        // カート内情報の取得
        $arrCart = $objCartSess->getCartList();
        $max = count($arrCart);
        $cnt = 0;

        for ($i = 0; $i < $max; $i++) {
            // 商品規格情報の取得
            $arrData = $this->sfGetProductsClass($arrCart[$i]['id']);
            $limit = "";
            // DBに存在する商品
            if (count($arrData) > 0) {

                // 購入制限数を求める。
                if ($arrData['stock_unlimited'] != '1' && $arrData['sale_unlimited'] != '1') {
                    if($arrData['sale_limit'] < $arrData['stock']) {
                        $limit = $arrData['sale_limit'];
                    } else {
                        $limit = $arrData['stock'];
                    }
                } else {
                    if ($arrData['sale_unlimited'] != '1') {
                        $limit = $arrData['sale_limit'];
                    }
                    if ($arrData['stock_unlimited'] != '1') {
                        $limit = $arrData['stock'];
                    }
                }

                if($limit != "" && $limit < $arrCart[$i]['quantity']) {
                    // カート内商品数を制限に合わせる
                    $objCartSess->setProductValue($arrCart[$i]['id'], 'quantity', $limit);
                    $quantity = $limit;
                    $objPage->tpl_message = "※「" . $arrData['name'] . "」は販売制限しております、一度にこれ以上の購入はできません。";
                } else {
                    $quantity = $arrCart[$i]['quantity'];
                }

                $objPage->arrProductsClass[$cnt] = $arrData;
                $objPage->arrProductsClass[$cnt]['quantity'] = $quantity;
                $objPage->arrProductsClass[$cnt]['cart_no'] = $arrCart[$i]['cart_no'];
                $objPage->arrProductsClass[$cnt]['class_name1'] = $arrClassName[$arrData['class_id1']];
                $objPage->arrProductsClass[$cnt]['class_name2'] = $arrClassName[$arrData['class_id2']];
                $objPage->arrProductsClass[$cnt]['classcategory_name1'] = $arrClassCatName[$arrData['classcategory_id1']];
                $objPage->arrProductsClass[$cnt]['classcategory_name2'] = $arrClassCatName[$arrData['classcategory_id2']];

                // 画像サイズ
                list($image_width, $image_height) = getimagesize(IMAGE_SAVE_DIR . basename($objPage->arrProductsClass[$cnt]["main_image"]));
                $objPage->arrProductsClass[$cnt]["tpl_image_width"] = $image_width + 60;
                $objPage->arrProductsClass[$cnt]["tpl_image_height"] = $image_height + 80;

                // 価格の登録
                if ($arrData['price02'] != "") {
                    $objCartSess->setProductValue($arrCart[$i]['id'], 'price', $arrData['price02']);
                    $objPage->arrProductsClass[$cnt]['uniq_price'] = $arrData['price02'];
                } else {
                    $objCartSess->setProductValue($arrCart[$i]['id'], 'price', $arrData['price01']);
                    $objPage->arrProductsClass[$cnt]['uniq_price'] = $arrData['price01'];
                }
                // ポイント付与率の登録
                $objCartSess->setProductValue($arrCart[$i]['id'], 'point_rate', $arrData['point_rate']);
                // 商品ごとの合計金額
                $objPage->arrProductsClass[$cnt]['total_pretax'] = $objCartSess->getProductTotal($arrInfo, $arrCart[$i]['id']);
                // 送料の合計を計算する
                $objPage->tpl_total_deliv_fee+= ($arrData['deliv_fee'] * $arrCart[$i]['quantity']);
                $cnt++;
            } else {
                // DBに商品が見つからない場合はカート商品の削除
                $objCartSess->delProductKey('id', $arrCart[$i]['id']);
            }
        }

        // 全商品合計金額(税込み)
        $objPage->tpl_total_pretax = $objCartSess->getAllProductsTotal($arrInfo);
        // 全商品合計消費税
        $objPage->tpl_total_tax = $objCartSess->getAllProductsTax($arrInfo);
        // 全商品合計ポイント
        $objPage->tpl_total_point = $objCartSess->getAllProductsPoint();

        return $objPage;
    }
}
?>
