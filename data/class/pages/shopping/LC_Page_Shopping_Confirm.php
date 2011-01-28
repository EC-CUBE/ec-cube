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
 * 入力内容確認のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Shopping_Confirm extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "ご入力内容のご確認";
        $masterData = new SC_DB_MasterData();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrMAILMAGATYPE = $masterData->getMasterData("mtb_mail_magazine_type");
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");
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
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objCartSess = new SC_CartSession();
        $objSiteSess = new SC_SiteSession();
        $objCustomer = new SC_Customer();
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();

        $this->isMultiple = $objPurchase->isMultiple();

        // 前のページで正しく登録手続きが行われた記録があるか判定
        if (!$objSiteSess->isPrePage()) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, $objSiteSess);
        }

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($uniqid, $objCartSess);
        $this->tpl_uniqid = $uniqid;

        $this->cartKey = $objCartSess->getKey();

        // カート内商品のチェック
        $this->tpl_message = $objCartSess->checkProducts($this->cartKey);
        if (strlen($this->tpl_message) >= 1) {
            SC_Utils_Ex::sfDispSiteError(SOLD_OUT, '', true);
        }

        // カートの商品を取得
        $this->cartItems = $objCartSess->getCartList($this->cartKey);
        // 合計金額
        $this->tpl_total_inctax[$this->cartKey] = $objCartSess->getAllProductsTotal($this->cartKey);
        // 税額
        $this->tpl_total_tax[$this->cartKey] = $objCartSess->getAllProductsTax($this->cartKey);
        // ポイント合計
        $this->tpl_total_point[$this->cartKey] = $objCartSess->getAllProductsPoint($this->cartKey);

        // TODO リファクタリング
        // 一時受注テーブルの読込
        $tmpData = $objPurchase->getOrderTemp($uniqid);

        // カート集計を元に最終計算
        // FIXME 使用ポイント, 手数料の扱い
        $arrData = array_merge($tmpData, $objCartSess->calculate($this->cartKey, $objCustomer, $tmpData['use_point'], $objPurchase->getShippingPref(), $tmpData['charge'], $tmpData['discount']));

        // 会員ログインチェック
        if($objCustomer->isLoginSuccess(true)) {
            $this->tpl_login = '1';
            $this->tpl_user_point = $objCustomer->getValue('point');
        }

        // 決済区分を取得する
        $payment_type = "";
        if($objDb->sfColumnExists("dtb_payment", "memo01")){
            // MEMO03に値が入っている場合には、モジュール追加されたものとみなす
            $sql = "SELECT memo03 FROM dtb_payment WHERE payment_id = ?";
            $arrPayment = $objQuery->getAll($sql, array($arrData['payment_id']));
            $payment_type = $arrPayment[0]["memo03"];
        }
        $this->payment_type = $payment_type;

        $this->shipping = $objPurchase->getShippingTemp();

        switch($this->getMode()) {
        // 前のページに戻る
        case 'return':
            // 正常な推移であることを記録しておく
            $objSiteSess->setRegistFlag();
            SC_Response_Ex::sendRedirect(SHOPPING_PAYMENT_URLPATH);
            exit;
            break;
        case 'confirm':
            // この時点で注文番号を確保しておく（クレジット、コンビニ決済で必要なため）
            $arrData["order_id"] = $objQuery->nextval("dtb_order_order_id");

            // 集計結果を受注一時テーブルに反映
            $objPurchase->saveOrderTemp($uniqid, $arrData, $objCustomer);
            // 正常に登録されたことを記録しておく
            $objSiteSess->setRegistFlag();

            // 決済方法により画面切替
            if($payment_type != "") {
                $_SESSION["payment_id"] = $arrData['payment_id'];

                $objPurchase->completeOrder(ORDER_PENDING);
                SC_Response_Ex::sendRedirect(SHOPPING_MODULE_URLPATH);
            }else{
                // 受注を完了し, 購入完了ページへ
                $objPurchase->completeOrder(ORDER_NEW);
                $objPurchase->sendOrderMail($arrData["order_id"]);
                SC_Response_Ex::sendRedirect(SHOPPING_COMPLETE_URLPATH);
            }
            exit;
            break;
        default:
            break;
        }
        $this->arrData = $arrData;
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
