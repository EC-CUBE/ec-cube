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

/**
 * 商品購入関連のヘルパークラス.
 *
 * TODO 購入時強制会員登録機能(#521)の実装を検討
 * TODO dtb_customer.buy_times, dtb_customer.buy_total の更新
 *
 * @package Helper
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class SC_Helper_Purchase {

    /**
     * 受注を完了する.
     *
     * 下記のフローで受注を完了する.
     *
     * 1. トランザクションを開始する
     * 2. カートの内容を検証する.
     * 3. 受注一時テーブルから受注データを読み込む
     * 4. ユーザーがログインしている場合はその他の発送先へ登録する
     * 5. 受注データを受注テーブルへ登録する
     * 6. トランザクションをコミットする
     *
     * 実行中に, 何らかのエラーが発生した場合, 処理を中止しエラーページへ遷移する
     *
     * 決済モジュールを使用する場合は受注ステータスを「決済処理中」に設定し,
     * 決済完了後「新規受付」に変更すること
     *
     * @param integer $orderStatus 受注処理を完了する際に設定する受注ステータス
     * @return void
     */
    function completeOrder($orderStatus = ORDER_NEW) {
        $objQuery =& SC_Query::getSingletonInstance();
        $objSiteSession = new SC_SiteSession();
        $objCartSession = new SC_CartSession();
        $objCustomer = new SC_Customer();
        $customerId = $objCustomer->getValue('customer_id');

        $objQuery->begin();
        SC_Utils_Ex::sfIsPrePage($objSiteSession);
        $uniqId = SC_Utils_Ex::sfCheckNormalAccess($objSiteSession,
                                                   $objCartSession);
        $orderTemp = $this->getOrderTemp($uniqId);

        if ($objCustomer->isLoginSuccess(true)) {
            $this->registerOtherDeliv($uniqId, $customerId);
        }

        $orderTemp['status'] = $orderStatus;
        $orderId = $this->registerOrder($orderTemp, $objCartSession,
                                        $_SESSION['cartKey']);
        $objQuery->commit();
        $objCustomer->updateSession();
    }

    /**
     * 受注一時情報を取得する.
     *
     * @param integer $uniqId 受注一時情報ID
     * @return array 受注一時情報の配列
     */
    function getOrderTemp($uniqId) {
        $objQuery =& SC_Query::getSingletonInstance();
        return $objQuery->getRow("*", "dtb_order_temp", "order_temp_id = ?",
                                 array($uniqId));
    }

    /**
     * 受注情報を登録する.
     *
     * 引数の受注情報を受注テーブル及び受注詳細テーブルに登録する.
     * 登録後, 受注一時テーブルに削除フラグを立て, カートの内容を削除する.
     *
     * TODO ダウンロード商品の場合の扱いを検討
     *
     * @param array $orderParams 登録する受注情報の配列
     * @param SC_CartSession $objCartSession カート情報のインスタンス
     * @param integer $cartKey 登録を行うカート情報のキー
     * @param integer 受注ID
     */
    function registerOrder($orderParams, &$objCartSession, $cartKey) {
        $objQuery =& SC_Query::getSingletonInstance();

        // 別のお届け先を指定が無ければ, お届け先に登録住所をコピー
        if ($orderParams['deliv_check'] == "-1") {
            $keys = array('name01', 'name02', 'kana01', 'kana02', 'pref', 'zip01',
                          'zip02', 'addr01', 'addr02', 'tel01', 'tel02', 'tel03');
            foreach ($keys as $key) {
                $orderParams['deliv_' . $key] = $orderParams['order_' . $key];
            }
        }

        // 不要な変数を unset
        $unsets = array('mailmaga_flg', 'deliv_check', 'point_check', 'password',
                        'reminder', 'reminder_answer', 'mail_flag', 'session');
        foreach ($unsets as $unset) {
            unset($orderParams[$unset]);
        }

        // ポイントは別登録
        $addPoint = $orderParams['add_point'];
        $usePoint = $orderParams['use_point'];
        $orderParams['add_point'] = 0;
        $orderParams['use_point'] = 0;

        // 注文ステータスの指定が無い場合は新規受付
        if(SC_Utils_Ex::isBlank($orderParams['status'])) {
            $orderParams['status'] = ORDER_NEW;
        }

        $orderParams['create_date'] = 'Now()';
        $orderParams['update_date'] = 'Now()';

        $objQuery->insert("dtb_order", $orderParams);

        // 受注.対応状況の更新
        SC_Helper_DB_Ex::sfUpdateOrderStatus($orderParams['order_id'],
                                             null, $addPoint, $usePoint);

        // 詳細情報を取得
        $cartItems = $objCartSession->getCartList($cartKey);

        // 既に存在する詳細レコードを消しておく。
        $objQuery->delete("dtb_order_detail", "order_id = ?",
                          array($orderParams['order_id']));

        $objProduct = new SC_Product();
        foreach ($cartItems as $item) {
            $p =& $item['productsClass'];
            $detail['order_id'] = $orderParams['order_id'];
            $detail['product_id'] = $p['product_id'];
            $detail['product_class_id'] = $p['product_class_id'];
            $detail['product_name'] = $p['name'];
            $detail['product_code'] = $p['product_code'];
            $detail['classcategory_name1'] = $p['classcategory_name1'];
            $detail['classcategory_name2'] = $p['classcategory_name2'];
            $detail['point_rate'] = $item['point_rate'];
            $detail['price'] = $item['price'];
            $detail['quantity'] = $item['quantity'];

            // 在庫の減少処理
            if (!$objProduct->reduceStock($p['product_class_id'], $item['quantity'])) {
                $objQuery->rollback();
                SC_Utils_Ex::sfDispSiteError(SOLD_OUT, "", true);
            }
            $objQuery->insert("dtb_order_detail", $detail);
        }

        $objQuery->update("dtb_order_temp", array('del_flg' => 1),
                          "order_temp_id = ?",
                          array(SC_SiteSession::getUniqId()));

        $objCartSession->delAllProducts($cartKey);
        SC_SiteSession::unsetUniqId();
        return $orderParams['order_id'];
    }

    /**
     * 会員登録住所と配送先住所を比較し, 差異があった場合は新規登録を行う.
     *
     * 別のお届け先に同一の配送先住所が存在する場合は登録しない.
     *
     * @param string $uniqId 配送先住所を特定するための一時テーブルのユニークID
     * @param integer $customerId 顧客ID
     * @return boolean 差異があり新規登録を行った場合 true; それ以外は false
     */
    function registerOtherDeliv($uniqId, $customerId) {
        $keys = array('name01', 'name02', 'kana01', 'kana02', 'tel01', 'tel02',
                      'tel03', 'zip01', 'zip02', 'pref', 'addr01', 'addr02');
        $delivCols = "";
        $cols = "";
        $i = 0;
        foreach ($keys as $key) {
            $delivCols .= "deliv_" . $key;
            $cols .= $key;
            if ($i < count($keys) - 1) {
                $delivCols .= ", ";
                $cols .= ", ";
            }
            $i++;
        }

        $objQuery =& SC_Query::getSingletonInstance();
        $orderTemp = $objQuery->select($delivCols, "dtb_order_temp",
                                       "order_temp_id = ?", array($uniqId),
                                       MDB2_FETCHMODE_ORDERED);

        $customerAddrs = $objQuery->select($cols, "dtb_customer",
                                           "customer_id = ?", array($customerId),
                                           MDB2_FETCHMODE_ORDERED);

        $hasAddr = false;
        if ($orderTemp[0] != $customerAddrs[0]) {
            $otherAddrs = $objQuery->select($cols, "dtb_other_deliv",
                                           "customer_id = ?", array($customerId),
                                            MDB2_FETCHMODE_ORDERED);
            foreach ($otherAddrs as $otherAddr) {
                if ($orderTemp[0] == $otherAddr) {
                    $hasAddr = true;
                }
            }
        }
        if ($hasAddr) {
            $i = 0;
            foreach ($keys as $key) {
                $addrs[$key] = $orderTemp[0][$i];
                $i++;
            }
            $addrs['customer_id'] = $customerId;
            $addrs['order_deliv_id'] = $objQuery->nextVal('dtb_other_deliv_other_deliv_id');
            $objQuery->insert("dtb_other_deliv", $addrs);
            return true;
        }
        return false;
    }

    /**
     * 受注完了メールを送信する.
     *
     * HTTP_USER_AGENT の種別により, 携帯電話の場合は携帯用の文面,
     * PC の場合は PC 用の文面でメールを送信する.
     *
     * @param integer $orderId 受注ID
     * @return void
     */
    function sendOrderMail($orderId) {
        $mailHelper = new SC_Helper_Mail_Ex();
        $mailHelper->sfSendOrderMail($orderId,
                                     SC_MobileUserAgent::isMobile() ? 2 : 1);
    }
}
