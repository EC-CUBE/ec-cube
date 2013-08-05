<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 入力内容確認のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Shopping_Confirm extends LC_Page_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
        $this->tpl_title = '入力内容のご確認';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrCountry = $masterData->getMasterData('mtb_country');
        $this->arrSex = $masterData->getMasterData('mtb_sex');
        $this->arrJob = $masterData->getMasterData('mtb_job');
        $this->arrMAILMAGATYPE = $masterData->getMasterData('mtb_mail_magazine_type');
        $this->arrReminder = $masterData->getMasterData('mtb_reminder');
        $this->arrDeliv = SC_Helper_Delivery_Ex::getIDValueList('service_name');
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process()
    {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action()
    {
		//決済処理中ステータスのロールバック
		SC_Helper_Purchase_Ex::checkSessionPendingOrder();
		SC_Helper_Purchase_Ex::checkDbMyPendignOrder();
		SC_Helper_Purchase_Ex::checkDbAllPendingOrder();
		
        $objCartSess = new SC_CartSession_Ex();
        $objSiteSess = new SC_SiteSession_Ex();
        $objCustomer = new SC_Customer_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();

        $this->is_multiple = $objPurchase->isMultiple();

        // 前のページで正しく登録手続きが行われた記録があるか判定
        if (!$objSiteSess->isPrePage()) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, $objSiteSess);
        }

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $this->tpl_uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        $this->cartKey = $objCartSess->getKey();

        // カート内商品のチェック
        $this->tpl_message = $objCartSess->checkProducts($this->cartKey);
        if (!SC_Utils_Ex::isBlank($this->tpl_message)) {
            SC_Response_Ex::sendRedirect(CART_URLPATH);
            SC_Response_Ex::actionExit();
        }

        // カートの商品を取得
        $this->arrShipping = $objPurchase->getShippingTemp($this->is_multiple);
        $this->arrCartItems = $objCartSess->getCartList($this->cartKey);
        // 合計金額
        $this->tpl_total_inctax[$this->cartKey] = $objCartSess->getAllProductsTotal($this->cartKey);
        // 税額
        $this->tpl_total_tax[$this->cartKey] = $objCartSess->getAllProductsTax($this->cartKey);
        // ポイント合計
        $this->tpl_total_point[$this->cartKey] = $objCartSess->getAllProductsPoint($this->cartKey);

        // 一時受注テーブルの読込
        $arrOrderTemp = $objPurchase->getOrderTemp($this->tpl_uniqid);
        // カート集計を元に最終計算
        $arrCalcResults = $objCartSess->calculate($this->cartKey, $objCustomer,
                                                  $arrOrderTemp['use_point'],
                                                  $objPurchase->getShippingPref($this->is_multiple),
                                                  $arrOrderTemp['charge'],
                                                  $arrOrderTemp['discount'],
                                                  $arrOrderTemp['deliv_id'],
                                                  $arrOrderTemp['order_pref'],  // 税金計算の為に追加　注文者基準
                                                  $arrOrderTemp['order_country_id'] // 税金計算の為に追加　注文者基準
                                                  );

        $this->arrForm = array_merge($arrOrderTemp, $arrCalcResults);

        // 会員ログインチェック
        if ($objCustomer->isLoginSuccess(true)) {
            $this->tpl_login = '1';
            $this->tpl_user_point = $objCustomer->getValue('point');
        }

        // 決済モジュールを使用するかどうか
        $this->use_module = SC_Helper_Payment_Ex::useModule($this->arrForm['payment_id']);

        switch ($this->getMode()) {
            // 前のページに戻る
            case 'return':
                // 正常な推移であることを記録しておく
                $objSiteSess->setRegistFlag();

                SC_Response_Ex::sendRedirect(SHOPPING_PAYMENT_URLPATH);
                SC_Response_Ex::actionExit();
                break;
            case 'confirm':
                /*
                 * 決済モジュールで必要なため, 受注番号を取得
                 */
                $this->arrForm['order_id'] = $objPurchase->getNextOrderID();
                $_SESSION['order_id'] = $this->arrForm['order_id'];

                // 集計結果を受注一時テーブルに反映
                $objPurchase->saveOrderTemp($this->tpl_uniqid, $this->arrForm,
                                            $objCustomer);

                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();

                // 決済モジュールを使用する場合
                if ($this->use_module) {
                    $objPurchase->completeOrder(ORDER_PENDING);

                    SC_Response_Ex::sendRedirect(SHOPPING_MODULE_URLPATH);
                }
                // 購入完了ページ
                else {
                    $objPurchase->completeOrder(ORDER_NEW);
                    SC_Helper_Purchase_Ex::sendOrderMail($this->arrForm['order_id'], $this);

                    SC_Response_Ex::sendRedirect(SHOPPING_COMPLETE_URLPATH);
                }
                SC_Response_Ex::actionExit();
                break;
            default:
                break;
        }

    }
}
