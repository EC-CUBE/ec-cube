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
 * お届け先の複数指定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: $
 */
class LC_Page_Shopping_Multiple extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $masterData = new SC_DB_MasterData();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
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
        $objCartSess = new SC_CartSession();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objCustomer = new SC_Customer();
        $objQuery = SC_Query::getSingletonInstance();
        $this->objFormParam = new SC_FormParam();

        $uniqid = $objSiteSess->getUniqId();

        $this->addrs = $this->getDelivAddrs($objCustomer, $objPurchase, $uniqid);
        $this->items = $this->splitItems($objCartSess);

        $this->lfInitParam($this->items);
        $this->objFormParam->setParam($_POST);

        $objPurchase->verifyChangeCart($uniqid, $objCartSess);

        $this->tpl_uniqid = $uniqid;

        $this->cartKey = $objCartSess->getKey();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!SC_Helper_Session_Ex::isValidToken()) {
                SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            }
        }

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch ($_POST['mode']) {
            case 'delete':
                // TODO
                break;

            case 'confirm':
                $this->arrErr = $this->lfCheckError($this->objFormParam);
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    // TODO リファクタリング
                    $params = $this->objFormParam->getHashArray();
                    $i = 0;
                    while ($params['cart_no' . $i] != null) {
                        $other_deliv_id = $params['shipping' . $i];
                        if ($objCustomer->isLoginSuccess(true)) {
                            if ($other_deliv_id != 0) {
                                $otherDeliv = $objQuery->select("*", "dtb_other_deliv",
                                                                "other_deliv_id = ?",
                                                                array($other_deliv_id));
                                foreach ($otherDeliv[0] as $key => $val) {
                                    $sqlval[$other_deliv_id]['shipping_' . $key] = $val;
                                }
                            } else {
                                $objPurchase->copyFromCustomer($sqlval[0], $objCustomer,
                                                               "shipping");
                            }
                        }
                        $sqlval[$other_deliv_id]['deliv_id'] = $objPurchase->getDeliv($this->cartKey);
                        $objPurchase->setShipmentItemTemp($other_deliv_id, $params['product_class_id' . $i], $params['quantity' . $i]);
                        $i++;
                    }

                    foreach ($sqlval as $shipping_id => $val) {
                        $objPurchase->saveShippingTemp($val, $shipping_id);
                    }

                    $objPurchase->shippingItemTempToCart($objCartSess);
                    $objPurchase->saveOrderTemp($uniqid, $sqlval[0], $objCustomer);
                    $objSiteSess->setRegistFlag();
                    SC_Response_Ex::sendRedirect("payment.php");
                    exit;
                }
                break;

        default:
        }

        $this->arrForm = $this->objFormParam->getFormParamList();
        $this->transactionid = SC_Helper_Session_Ex::getToken();
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
     */
    function lfInitParam($items) {
        for ($i = 0; $i < count($items); $i++) {
            $this->objFormParam->addParam("商品規格ID", "product_class_id" . $i, INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("数量", "quantity" . $i, INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
            $this->objFormParam->addParam("配送先住所", "shipping" . $i, INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("カート番号", "cart_no" . $i, INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        }
    }


    /**
     * カートの商品を数量ごとに分割する
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
     */
    function getDelivAddrs(&$objCustomer, &$objPurchase, $uniqid) {
        if ($objCustomer->isLoginSuccess(true)) {
            $addrs = $objCustomer->getCustomerAddress($_SESSION['customer']['customer_id']);
            $results = array();
            foreach ($addrs as $key => $val) {
                $other_deliv_id = SC_Utils_Ex::isBlank($val['other_deliv_id']) ? 0 : $val['other_deliv_id'];
                $results[$other_deliv_id] = $val['name01'] . $val['name02']
                    . " " . $this->arrPref[$val['pref']] . $val['addr01'] . $val['addr02'];
            }
        } else {
            $shipping = $objPurchase->getShippingTemp();
            foreach ($shipping as $shipping_id => $val) {
                $results[$shipping_id] = $val['shipping_name01'] . $val['shipping_name02']
                    . " " . $this->arrPref[$val['shipping_pref']]
                    . $val['shipping_addr01'] . $val['shipping_addr02'];
            }
        }
        return $results;
    }

    function lfCheckError(&$objFormParam) {
        $objFormParam->convParam();
        return $objFormParam->checkError();
    }
}
?>
