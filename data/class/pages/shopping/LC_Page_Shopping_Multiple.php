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
require_once(CLASS_EX_REALDIR . "page_extends/LC_Page_Ex.php");

/**
 * お届け先の複数指定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Shopping_Multiple extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "お届け先の複数指定";
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function action() {
        $objSiteSess = new SC_SiteSession();
        $objCartSess = new SC_CartSession_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objCustomer = new SC_Customer();
        $objFormParam = new SC_FormParam();

        $this->tpl_uniqid = $objSiteSess->getUniqId();

        $this->addrs = $this->getDelivAddrs($objCustomer, $objPurchase,
                                            $this->tpl_uniqid);
        $this->items = $this->splitItems($objCartSess);

        $this->lfInitParam($this->items, $objFormParam);
        $objFormParam->setParam($_POST);
        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        switch ($this->getMode()) {
            case 'confirm':
                $this->arrErr = $this->lfCheckError($objFormParam);
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    $this->saveMultipleShippings($this->tpl_uniqid, $objFormParam,
                                                 $objCustomer, $objPurchase,
                                                 $objCartSess);
                    $objSiteSess->setRegistFlag();
                    SC_Response_Ex::sendRedirect("payment.php");
                    exit;
                }
                break;

        default:
        }

        $this->arrForm = $objFormParam->getFormParamList();
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
     * フォームを初期化する.
     *
     * @param array $arrItems 数量ごとに分割した, カートの商品情報の配列
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam($arrItems, $objFormParam) {
        for ($i = 0; $i < count($arrItems); $i++) {
            $objFormParam->addParam("商品規格ID", "product_class_id" . $i, INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
            $objFormParam->addParam("数量", "quantity" . $i, INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
            $objFormParam->addParam("配送先住所", "shipping" . $i, INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
            $objFormParam->addParam("カート番号", "cart_no" . $i, INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        }
    }

    /**
     * カートの商品を数量ごとに分割する
     *
     * @param SC_CartSession $objCartSess SC_CartSession インスタンス
     * @return array 数量ごとに分割した, カートの商品情報の配列
     */
    function splitItems(&$objCartSess) {
        $cartLists =& $objCartSess->getCartList($objCartSess->getKey());
        foreach (array_keys($cartLists) as $key) {
            for ($i = 0; $i < $cartLists[$key]['quantity']; $i++) {
                $items[] =& $cartLists[$key]['productsClass'];
            }
        }
        return $items;
    }

    /**
     * 配送住所のプルダウン用連想配列を取得する.
     *
     * 会員ログイン済みの場合は, 会員登録住所及び追加登録住所を取得する.
     * 非会員の場合は, 「お届け先の指定」画面で入力した住所を取得する.
     *
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param integer $uniqid 受注一時テーブルのユニークID
     * @return array 配送住所のプルダウン用連想配列
     */
    function getDelivAddrs(&$objCustomer, &$objPurchase, $uniqid) {
        $masterData = new SC_DB_MasterData();
        $arrPref = $masterData->getMasterData('mtb_pref');

        // 会員ログイン時
        if ($objCustomer->isLoginSuccess(true)) {
            $arrAddrs = $objCustomer->getCustomerAddress($objCustomer->getValue('customer_id'));
            $arrResults = array();
            foreach ($arrAddrs as $val) {
                $other_deliv_id = SC_Utils_Ex::isBlank($val['other_deliv_id']) ? 0 : $val['other_deliv_id'];
                $arrResults[$other_deliv_id] = $val['name01'] . $val['name02']
                    . " " . $arrPref[$val['pref']] . $val['addr01'] . $val['addr02'];
            }
        }
        // 非会員
        else {
            $arrShippings = $objPurchase->getShippingTemp();
            foreach ($arrShippings as $shipping_id => $val) {
                $arrResults[$shipping_id] = $val['shipping_name01'] . $val['shipping_name02']
                    . " " . $arrPref[$val['shipping_pref']]
                    . $val['shipping_addr01'] . $val['shipping_addr02'];
            }
        }
        return $arrResults;
    }

    /**
     * 入力チェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラー情報の配列
     */
    function lfCheckError(&$objFormParam) {
        $objFormParam->convParam();
        return $objFormParam->checkError();
    }

    /**
     * 複数配送情報を一時保存する.
     *
     * 会員ログインしている場合は, その他のお届け先から住所情報を取得する.
     *
     * @param integer $uniqid 一時受注テーブルのユニークID
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param SC_CartSession $objCartSess SC_CartSession インスタンス
     * @return void
     */
    function saveMultipleShippings($uniqid, &$objFormParam, &$objCustomer,
                                   &$objPurchase, &$objCartSess) {
        $objQuery =& SC_Query::getSingletonInstance();

        $arrParams = $objFormParam->getHashArray();
        $i = 0;
        while ($arrParams['cart_no' . $i] != null) {
            $other_deliv_id = $arrParams['shipping' . $i];

            if ($objCustomer->isLoginSuccess(true)) {
                if ($other_deliv_id != 0) {
                    $otherDeliv = $objQuery->select("*", "dtb_other_deliv",
                                                    "other_deliv_id = ?",
                                                    array($other_deliv_id));
                    foreach ($otherDeliv[0] as $key => $val) {
                        $arrValues[$other_deliv_id]['shipping_' . $key] = $val;
                    }
                } else {
                    $objPurchase->copyFromCustomer($arrValues[0], $objCustomer,
                                                   "shipping");
                }
            }

            $objPurchase->setShipmentItemTemp($other_deliv_id,
                                              $arrParams['product_class_id' . $i],
                                              $arrParams['quantity' . $i]);
            $i++;
        }

        foreach ($arrValues as $shipping_id => $val) {
            $objPurchase->saveShippingTemp($val, $shipping_id);
        }

        $objPurchase->shippingItemTempToCart($objCartSess);
        // $arrValues[0] には, 購入者の情報が格納されている
        $objPurchase->saveOrderTemp($uniqid, $arrValues[0], $objCustomer);
    }
}
?>
