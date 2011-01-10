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
 * @version $Id:LC_Page_Shopping_Confirm.php 15532 2007-08-31 14:39:46Z nanasess $
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
        $objView = new SC_SiteView();
        $objCartSess = new SC_CartSession();
        $objSiteInfo = $objView->objSiteInfo;
        $objSiteSess = new SC_SiteSession();
        $objCustomer = new SC_Customer();
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        // 前のページで正しく登録手続きが行われた記録があるか判定
        SC_Utils_Ex::sfIsPrePage($objSiteSess);

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $uniqid = SC_Utils_Ex::sfCheckNormalAccess($objSiteSess, $objCartSess);
        $this->tpl_uniqid = $uniqid;

        //ダウンロード商品判定
        $this->cartdown = $objDb->chkCartDown($objCartSess);


        $this->cartKey = $_SESSION['cartKey'];

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
        $tmpData = $objDb->sfGetOrderTemp($uniqid);

        // カート集計を元に最終計算
        // FIXME 使用ポイント, 配送都道府県, 支払い方法, 手数料の扱い
        $arrData = array_merge($tmpData, $objCartSess->calculate($this->cartKey, $objCustomer, $tmpData['use_point'], $tmpData['deliv_pref'], $tmpData['payment_id'], $tmpData['charge'], $tmpData['discount']));
        unset($arrData['deliv_fee']); // FIXME
        // 会員ログインチェック
        if($objCustomer->isLoginSuccess()) {
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

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        // 前のページに戻る
        case 'return':
            // 正常な推移であることを記録しておく
            $objSiteSess->setRegistFlag();
            $this->objDisplay->redirect($this->getLocation(SHOPPING_PAYMENT_URL_PATH));
            exit;
            break;
        case 'confirm':
            // この時点で注文番号を確保しておく（クレジット、コンビニ決済で必要なため）
            $arrData["order_id"] = $objQuery->nextval("dtb_order_order_id");

            // セッション情報を保持
            $arrData['session'] = serialize($_SESSION);

            // 集計結果を受注一時テーブルに反映
            unset($arrData[0]); // FIXME
            unset($arrData[1]);
            $objDb->sfRegistTempOrder($uniqid, $arrData);
            // 正常に登録されたことを記録しておく
            $objSiteSess->setRegistFlag();

            // 決済方法により画面切替
            if($payment_type != "") {
                $_SESSION["payment_id"] = $arrData['payment_id'];
                $objPurchase = new SC_Helper_Purchase_Ex();
                $objPurchase->completeOrder(ORDER_PENDING);
                $this->objDisplay->redirect($this->getLocation(SHOPPING_MODULE_URL_PATH));
            }else{
                // 受注を完了し, 購入完了ページへ
                $objPurchase = new SC_Helper_Purchase_Ex();
                $objPurchase->completeOrder(ORDER_NEW);
                $objPurchase->sendOrderMail($arrData["order_id"]);
                $this->objDisplay->redirect($this->getLocation(SHOPPING_COMPLETE_URL_PATH));
            }
            exit;
            break;
        default:
            break;
        }
        $this->arrData = $arrData;
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->init();
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        parent::mobileProcess();
        $this->mobileAction();
        $this->sendResponse();
    }

    /**
     * Page のアクション(モバイル).
     *
     * @return void
     */
    function mobileAction() {
        $objView = new SC_MobileView();
        $objCartSess = new SC_CartSession();
        $objSiteInfo = $objView->objSiteInfo;
        $objSiteSess = new SC_SiteSession();
        $objCustomer = new SC_Customer();
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        // 前のページで正しく登録手続きが行われた記録があるか判定
        SC_Utils_Ex::sfIsPrePage($objSiteSess);

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $uniqid = SC_Utils_Ex::sfCheckNormalAccess($objSiteSess, $objCartSess);
        $this->tpl_uniqid = $uniqid;

        //ダウンロード商品判定
        $this->cartdown = $objDb->chkCartDown($objCartSess);

        // カート集計処理
        $objDb->sfTotalCart($this, $objCartSess);
        if (strlen($this->tpl_message) >= 1) {
            SC_Utils_Ex::sfDispSiteError(SOLD_OUT, '', true);
        }
        // 一時受注テーブルの読込
        $arrData = $objDb->sfGetOrderTemp($uniqid);
        // カート集計を元に最終計算
        $arrData = $objDb->sfTotalConfirm($arrData, $this, $objCartSess, null, $objCustomer);

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

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
            // 前のページに戻る
        case 'return':
            // 正常な推移であることを記録しておく
            $objSiteSess->setRegistFlag();
            $this->objDisplay->redirect($this->getLocation(MOBILE_SHOPPING_PAYMENT_URL_PATH));
            exit;
            break;
        case 'confirm':
            // この時点で注文番号を確保しておく（クレジット、コンビニ決済で必要なため）
            $arrData["order_id"] = $objQuery->nextVal("dtb_order_order_id");

            // セッション情報を保持
            $arrData['session'] = serialize($_SESSION);

            // 集計結果を受注一時テーブルに反映
            $objDb->sfRegistTempOrder($uniqid, $arrData);
            // 正常に登録されたことを記録しておく
            $objSiteSess->setRegistFlag();

            // 決済方法により画面切替
            if($payment_type != "") {
                $_SESSION["payment_id"] = $arrData['payment_id'];
                $this->objDisplay->redirect($this->getLocation(MOBILE_SHOPPING_MODULE_URL_PATH));
            }else{
                $this->objDisplay->redirect($this->getLocation(MOBILE_SHOPPING_COMPLETE_URL_PATH));
            }
            exit;
            break;
        default:
            break;
        }
        $this->arrData = $arrData;
        $this->arrInfo = $objSiteInfo->data;
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
