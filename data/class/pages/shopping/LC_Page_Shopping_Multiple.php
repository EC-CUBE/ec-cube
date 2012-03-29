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
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

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
        $this->tpl_title = 'お届け先の複数指定';
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function action() {
        // フックポイント.
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
        $objPlugin->doAction('LC_Page_Shopping_Multiple_action_before', array($this));

        $objSiteSess = new SC_SiteSession_Ex();
        $objCartSess = new SC_CartSession_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objCustomer = new SC_Customer_Ex();
        $objFormParam = new SC_FormParam_Ex();

        // 複数配送先指定が無効な場合はエラー
        if (USE_MULTIPLE_SHIPPING === false) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, '', true);
            exit;
        }

        $this->tpl_uniqid = $objSiteSess->getUniqId();

        $this->addrs = $this->getDelivAddrs($objCustomer, $objPurchase,
                                            $this->tpl_uniqid);
        $this->tpl_addrmax = count($this->addrs);
        $this->lfInitParam($objFormParam);

        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        switch ($this->getMode()) {
            case 'confirm':
                $objFormParam->setParam($_POST);
                $this->arrErr = $this->lfCheckError($objFormParam);
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    // フォームの情報を一時保存しておく
                    $_SESSION['multiple_temp'] = $objFormParam->getHashArray();
                    $this->saveMultipleShippings($this->tpl_uniqid, $objFormParam,
                                                 $objCustomer, $objPurchase,
                                                 $objCartSess);
                    $objSiteSess->setRegistFlag();

                    // フックポイント.
                    $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
                    $objPlugin->doAction('LC_Page_Shopping_Multiple_action_confirm', array($this));

                    SC_Response_Ex::sendRedirect('payment.php');
                    exit;
                }
                break;

            default:
                $this->setParamToSplitItems($objFormParam, $objCartSess);
        }

        // 前のページから戻ってきた場合
        if ($_GET['from'] == 'multiple') {
            $objFormParam->setParam($_SESSION['multiple_temp']);
        }
        $this->arrForm = $objFormParam->getFormParamList();

        // フックポイント.
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
        $objPlugin->doAction('LC_Page_Shopping_Multiple_action_after', array($this));
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
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('商品規格ID', 'product_class_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('商品名', 'name');
        $objFormParam->addParam('規格1', 'class_name1');
        $objFormParam->addParam('規格2', 'class_name2');
        $objFormParam->addParam('規格分類1', 'classcategory_name1');
        $objFormParam->addParam('規格分類2', 'classcategory_name2');
        $objFormParam->addParam('メイン画像', 'main_image');
        $objFormParam->addParam('メイン一覧画像', 'main_list_image');
        $objFormParam->addParam('販売価格', 'price');
        $objFormParam->addParam('数量', 'quantity', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam('お届け先', 'shipping', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('カート番号', 'cart_no', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('行数', 'line_of_num', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
    }

    /**
     * カートの商品を数量ごとに分割し, フォームに設定する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param SC_CartSession $objCartSess SC_CartSession インスタンス
     * @return void
     */
    function setParamToSplitItems(&$objFormParam, &$objCartSess) {
        $cartLists =& $objCartSess->getCartList($objCartSess->getKey());
        $arrItems = array();
        $index = 0;
        foreach (array_keys($cartLists) as $key) {
            $arrProductsClass = $cartLists[$key]['productsClass'];
            $quantity = (int) $cartLists[$key]['quantity'];
            for ($i = 0; $i < $quantity; $i++) {
                foreach ($arrProductsClass as $key2 => $val) {
                    $arrItems[$key2][$index] = $val;
                }
                $arrItems['quantity'][$index] = 1;
                $arrItems['price'][$index] = $cartLists[$key]['price'];
                $index++;
            }
        }
        $objFormParam->setParam($arrItems);
        $objFormParam->setValue('line_of_num', $index);
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

        $arrResults = array('' => '選択してください');
        // 会員ログイン時
        if ($objCustomer->isLoginSuccess(true)) {
            $arrAddrs = $objCustomer->getCustomerAddress($objCustomer->getValue('customer_id'));
            foreach ($arrAddrs as $val) {
                $other_deliv_id = SC_Utils_Ex::isBlank($val['other_deliv_id']) ? 0 : $val['other_deliv_id'];
                $arrResults[$other_deliv_id] = $val['name01'] . $val['name02']
                    . ' ' . $arrPref[$val['pref']] . $val['addr01'] . $val['addr02'];
            }
        }
        // 非会員
        else {
            $arrShippings = $objPurchase->getShippingTemp();
            foreach ($arrShippings as $shipping_id => $val) {
                $arrResults[$shipping_id] = $val['shipping_name01'] . $val['shipping_name02']
                    . ' ' . $arrPref[$val['shipping_pref']]
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
        $objCartSess = new SC_CartSession_Ex();

        $objFormParam->convParam();
        // 数量未入力は0に置換
        $objFormParam->setValue('quantity', $objFormParam->getValue('quantity', 0));

        $arrErr = $objFormParam->checkError();

        $arrParams = $objFormParam->getSwapArray();

        if (empty($arrErr)) {
            foreach ($arrParams as $index => $arrParam) {
                // 数量0で、お届け先を選択している場合
                if ($arrParam['quantity'] == 0 && !SC_Utils_Ex::isBlank($arrParam['shipping'])) {
                    $arrErr['shipping'][$index] = '※ 数量が0の場合、お届け先を入力できません。<br />';;
                }
                // 数量の入力があり、お届け先を選択していない場合
                if ($arrParam['quantity'] > 0 && SC_Utils_Ex::isBlank($arrParam['shipping'])) {
                    $arrErr['shipping'][$index] = '※ お届け先が入力されていません。<br />';
                }
            }
        }

        // 入力エラーが無い場合、カゴの中身との数量の整合を確認
        if (empty($arrErr)) {
            $arrQuantity = array();
            // 入力内容を集計
            foreach ($arrParams as $arrParam) {
                $product_class_id = $arrParam['product_class_id'];
                $arrQuantity[$product_class_id] += $arrParam['quantity'];
            }
            // カゴの中身と突き合わせ
            $cartLists =& $objCartSess->getCartList($objCartSess->getKey());
            foreach ($cartLists as $arrCartRow) {
                $product_class_id = $arrCartRow['id'];
                // 差異がある場合、エラーを記録
                if ($arrCartRow['quantity'] != $arrQuantity[$product_class_id]) {
                    foreach ($arrParams as $index => $arrParam) {
                        if ($arrParam['product_class_id'] == $product_class_id) {
                            $arrErr['quantity'][$index] = '※ 数量合計を「' . $arrCartRow['quantity'] .'」にしてください。<br />';
                        }
                    }
                }
            }
        }
        return $arrErr;
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
    function saveMultipleShippings($uniqid, &$objFormParam, &$objCustomer, &$objPurchase, &$objCartSess) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $arrParams = $objFormParam->getSwapArray();

        foreach ($arrParams as $arrParam) {
            $other_deliv_id = $arrParam['shipping'];

            if ($objCustomer->isLoginSuccess(true)) {
                if ($other_deliv_id != 0) {
                    $otherDeliv = $objQuery->select('*', 'dtb_other_deliv',
                                                    'other_deliv_id = ?',
                                                    array($other_deliv_id));
                    foreach ($otherDeliv[0] as $key => $val) {
                        $arrValues[$other_deliv_id]['shipping_' . $key] = $val;
                    }
                } else {
                    $objPurchase->copyFromCustomer($arrValues[0], $objCustomer,
                                                   'shipping');
                }
            } else {
                $arrValues = $objPurchase->getShippingTemp();
            }
            $arrItemTemp[$other_deliv_id][$arrParam['product_class_id']] += $arrParam['quantity'];
        }

        $objPurchase->clearShipmentItemTemp();

        foreach ($arrValues as $shipping_id => $arrVal) {
            $objPurchase->saveShippingTemp($arrVal, $shipping_id);
        }

        foreach ($arrItemTemp as $other_deliv_id => $arrProductClassIds) {
            foreach ($arrProductClassIds as $product_class_id => $quantity) {
                if ($quantity == 0) continue;
                $objPurchase->setShipmentItemTemp($other_deliv_id,
                                                  $product_class_id,
                                                  $quantity);
            }
        }

        // $arrValues[0] には, 購入者の情報が格納されている
        $objPurchase->saveOrderTemp($uniqid, $arrValues[0], $objCustomer);
    }
}
