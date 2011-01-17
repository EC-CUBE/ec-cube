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
        if (!$objSiteSession->isPrePage()) {
            SC_Utils::sfDispSiteError(PAGE_ERROR, $objSiteSession);
        }

        $uniqId = $objSiteSession->getUniqId();
        $this->verifyChangeCart($uniqId, $objCartSession);

        $orderTemp = $this->getOrderTemp($uniqId);

        $orderTemp['status'] = $orderStatus;
        $orderId = $this->registerOrder($orderTemp, $objCartSession,
                                        $objCartSession->getKey());
        $shippingTemp =& $this->getShippingTemp();
        if (count($shippingTemp) > 1) {
            foreach ($shippingTemp as $shippingId => $val) {
                $this->registerShipmentItem($orderId, $shippingId,
                                            $val['shipment_item']);
            }
        }

        $this->registerShipping($orderId, $shippingTemp);
        $objQuery->commit();
        $this->unsetShippingTemp();
        $objCustomer->updateSession();
    }

    /**
     * カートに変化が無いか検証する.
     *
     * ユニークIDとセッションのユニークIDを比較し, 異なる場合は
     * エラー画面を表示する.
     *
     * カートが空の場合, 購入ボタン押下後にカートが変更された場合は
     * カート画面へ遷移する.
     *
     * @param string $uniqId ユニークID
     * @param SC_CartSession $objCartSession
     * @return void
     */
    function verifyChangeCart($uniqId, &$objCartSession) {
        $cartkeys = $objCartSession->getKeys();

        foreach ($cartKeys as $cartKey) {
            // 初回のみカートの内容を保存
            $objCartSess->saveCurrentCart($uniqid, $cartKey);
            /*
             * POSTのユニークIDとセッションのユニークIDを比較
             *(ユニークIDがPOSTされていない場合はスルー)
             */
            if(!SC_SiteSession::checkUniqId()) {
                // エラーページの表示
                // XXX $objSiteSess インスタンスは未使用？
                SC_Utils_Ex::sfDispSiteError(CANCEL_PURCHASE, $objSiteSess);
            }

            // カート内が空でないか || 購入ボタンを押してから変化がないか
            $quantity = $objCartSess->getTotalQuantity($cartKey);
            if($objCartSess->checkChangeCart($cartKey) || !($quantity > 0)) {
                // カート情報表示に強制移動する
                if (Net_UserAgent_Mobile::isMobile()) {
                    header("Location: ". MOBILE_CART_URLPATH
                           . "?" . session_name() . "=" . session_id());
                } else {
                    header("Location: ".CART_URLPATH);
                }
                exit;
            }
        }
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
     * 受注一時情報を保存する.
     *
     * 既存のデータが存在しない場合は新規保存. 存在する場合は更新する.
     * 既存のデータが存在せず, ユーザーがログインしている場合は,
     * 会員情報をコピーする.
     *
     * @param integer $uniqId 受注一時情報ID
     * @param array $params 登録する受注情報の配列
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @return array void
     */
    function saveOrderTemp($uniqId, $params, &$objCustomer) {
        if (SC_Utils_Ex::isBlank($uniqId)) {
            return;
        }

        $objQuery =& SC_Query::getSingletonInstance();
        // 存在するカラムのみを対象とする
        $cols = $objQuery->listTableFields('dtb_order_temp');
        foreach ($params as $key => $val) {
            if (in_array($key, $cols)) {
                $sqlval[$key] = $val;
            }
        }

        $sqlval['session'] = serialize($_SESSION);
        $exists = $this->getOrderTemp($uniqId);
        if (SC_Utils_Ex::isBlank($exists)) {
            $this->copyFromCustomer($sqlval, $objCustomer);
            $sqlval['order_temp_id'] = $uniqId;
            $sqlval['create_date'] = "now()";
            $objQuery->insert("dtb_order_temp", $sqlval);
        } else {
            $objQuery->update("dtb_order_temp", $sqlval, 'order_temp_id = ?',
                              array($uniqId));
        }
    }

    /**
     * セッションの配送情報を取得する.
     */
    function getShippingTemp() {
        return $_SESSION['shipping'];
    }

    /**
     * 配送商品を設定する.
     */
    function setShipmentItemTemp($otherDelivId, $productClassId, $quantity) {
        $_SESSION['shipping'][$otherDelivId]['shipment_item'][$productClassId]['shipping_id'] = $otherDelivId;
        $_SESSION['shipping'][$otherDelivId]['shipment_item'][$productClassId]['product_class_id'] = $productClassId;
        $_SESSION['shipping'][$otherDelivId]['shipment_item'][$productClassId]['quantity'] += $quantity;

        $objProduct = new SC_Product();
        if (empty($_SESSION['shipping'][$otherDelivId]['shipment_item'][$productClassId]['productsClass'])) {
            $product =& $objProduct->getDetailAndProductsClass($productClassId);
            $_SESSION['shipping'][$otherDelivId]['shipment_item'][$productClassId]['productsClass'] = $product;
        }
        $incTax = SC_Helper_DB_Ex::sfCalcIncTax($_SESSION['shipping'][$otherDelivId]['shipment_item'][$productClassId]['productsClass']['price02']);
        $_SESSION['shipping'][$otherDelivId]['shipment_item'][$productClassId]['total_inctax'] = $incTax * $_SESSION['shipping'][$otherDelivId]['shipment_item'][$productClassId]['quantity'];
    }

    /**
     * 配送商品の情報でカートを更新する.
     */
    function shippingItemTempToCart(&$objCartSession) {
        $shipmentItems = array();

        foreach (array_keys($_SESSION['shipping']) as $otherDelivId) {
            foreach (array_keys($_SESSION['shipping'][$otherDelivId]['shipment_item']) as $productClassId) {
                $shipmentItems[$productClassId] += $_SESSION['shipping'][$otherDelivId]['shipment_item'][$productClassId]['quantity'];
           }
        }
        foreach ($shipmentItems as $productClassId => $quantity) {
            $objCartSession->setProductValue($productClassId, 'quantity',
                                             $quantity,$objCartSession->getKey());
        }
    }

    /**
     * 配送先都道府県の配列を返す.
     */
    function getShippingPref() {
        $results = array();
        foreach ($_SESSION['shipping'] as $val) {
            $results[] = $val['shipping_pref'];
        }
        return $results;
    }

    /**
     * 複数配送指定の購入かどうか.
     *
     * @return boolean 複数配送指定の購入の場合 true
     */
    function isMultiple() {
        return (count($this->getShippingTemp()) > 1);
    }

    /**
     * 配送情報をセッションに保存する.
     */
    function saveShippingTemp(&$src, $otherDelivId = 0) {
        if (empty($_SESSION['shipping'][$otherDelivId])) {
            $_SESSION['shipping'][$otherDelivId] = $src;
        } else {
            $_SESSION['shipping'][$otherDelivId] = array_merge($_SESSION['shipping'][$otherDelivId], $src);
        }
    }

    /**
     * セッションの配送情報を破棄する.
     */
    function unsetShippingTemp() {
        unset($_SESSION['shipping']);
    }

    /**
     * 会員情報を受注情報にコピーする.
     *
     * ユーザーがログインしていない場合は何もしない.
     * 会員情報を $dest の order_* へコピーする.
     * customer_id は強制的にコピーされる.
     *
     * @param array $dest コピー先の配列
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @param string $prefix コピー先の接頭辞. デフォルト order
     * @param array $keys コピー対象のキー
     * @return void
     */
    function copyFromCustomer(&$dest, &$objCustomer, $prefix = 'order',
                              $keys = array('name01', 'name02', 'kana01', 'kana02',
                                            'sex', 'zip01', 'zip02', 'pref',
                                            'addr01', 'addr02',
                                            'tel01', 'tel02', 'tel03', 'job',
                                            'birth', 'email')) {
        if ($objCustomer->isLoginSuccess(true)) {

            foreach ($keys as $key) {
                if (in_array($key, $keys)) {
                    $dest[$prefix . '_' . $key] = $objCustomer->getValue($key);
                }
            }

            if (Net_UserAgent_Mobile::isMobile()
                && in_array('email', $keys)) {
                $email_mobile = $objCustomer->getValue('email_mobile');
                if (empty($email_mobile)) {
                    $dest[$prefix . '_email'] = $objCustomer->getValue('email');
                } else {
                    $dest[$prefix . '_email'] = $email_mobile;
                }
            }

            $dest['customer_id'] = $objCustomer->getValue('customer_id');
            $dest['update_date'] = 'Now()';
        }
    }

    /**
     * 受注情報を配送情報にコピーする.
     *
     * 受注情報($src)を $dest の order_* へコピーする.
     *
     * TODO 汎用的にして SC_Utils へ移動
     *
     * @param array $dest コピー先の配列
     * @param array $src コピー元の配列
     * @param array $keys コピー対象のキー
     * @param string $prefix コピー先の接頭辞. デフォルト shipping
     * @param string $src_prefix コピー元の接頭辞. デフォルト order
     * @return void
     */
    function copyFromOrder(&$dest, $src,
                           $prefix = 'shipping', $src_prefix = 'order',
                           $keys = array('name01', 'name02', 'kana01', 'kana02',
                                         'sex', 'zip01', 'zip02', 'pref',
                                         'addr01', 'addr02',
                                         'tel01', 'tel02', 'tel03')) {
        if (!SC_Utils_Ex::isBlank($prefix)) {
            $prefix = $prefix . '_';
        }
        if (!SC_Utils_Ex::isBlank($src_prefix)) {
            $src_prefix = $src_prefix . '_';
        }
        foreach ($keys as $key) {
            if (in_array($key, $keys)) {
                $dest[$prefix . $key] = $src[$src_prefix . $key];
            }
        }
    }

    /**
     * 購入金額に応じた支払方法を取得する.
     *
     * @param integer $total 購入金額
     * @param array $productClassIds 購入する商品規格IDの配列
     * @return array 購入金額に応じた支払方法の配列
     */
    function getPayment($total, $productClassIds) {
        // 有効な支払方法を取得
        $objProduct = new SC_Product();
        $paymentIds = $objProduct->getEnablePaymentIds($productClassIds);

        $objQuery =& SC_Query::getSingletonInstance();

        // 削除されていない支払方法を取得
        $where = 'del_flg = 0 AND payment_id IN (' . implode(', ', array_pad(array(), count($paymentIds), '?')) . ')';
        $objQuery->setOrder("rank DESC");
        $payments = $objQuery->select("payment_id, payment_method, rule, upper_rule, note, payment_image", "dtb_payment", $where, $paymentIds);

        foreach ($payments as $data) {
            // 下限と上限が設定されている
            if (strlen($data['rule']) != 0 && strlen($data['upper_rule']) != 0) {
                if ($data['rule'] <= $total_inctax && $data['upper_rule'] >= $total_inctax) {
                    $arrPayment[] = $data;
                }
            }
            // 下限のみ設定されている
            elseif (strlen($data['rule']) != 0) {
                if($data['rule'] <= $total_inctax) {
                    $arrPayment[] = $data;
                }
            }
            // 上限のみ設定されている
            elseif (strlen($data['upper_rule']) != 0) {
                if($data['upper_rule'] >= $total_inctax) {
                    $arrPayment[] = $data;
                }
            }
            // いずれも設定なし
            else {
                $arrPayment[] = $data;
            }
          }
        return $arrPayment;
    }

    /**
     * お届け日一覧を取得する.
     */
    function getDelivDate(&$objCartSess, $productTypeId) {
        $cartList = $objCartSess->getCartList($productTypeId);
        $delivDateIds = array();
        foreach ($cartList as $item) {
            $delivDateIds[] = $item['productsClass']['deliv_date_id'];
        }
        $max_date = max($delivDateIds);
        //発送目安
        switch($max_date) {
        //即日発送
        case '1':
            $start_day = 1;
            break;
            //1-2日後
        case '2':
            $start_day = 3;
            break;
            //3-4日後
        case '3':
            $start_day = 5;
            break;
            //1週間以内
        case '4':
            $start_day = 8;
            break;
            //2週間以内
        case '5':
            $start_day = 15;
            break;
            //3週間以内
        case '6':
            $start_day = 22;
            break;
            //1ヶ月以内
        case '7':
            $start_day = 32;
            break;
            //2ヶ月以降
        case '8':
            $start_day = 62;
            break;
            //お取り寄せ(商品入荷後)
        case '9':
            $start_day = "";
            break;
        default:
            //お届け日が設定されていない場合
            $start_day = "";
        }
        //お届け可能日のスタート値から、お届け日の配列を取得する
        $arrDelivDate = $this->getDateArray($start_day, DELIV_DATE_END_MAX);
        return $arrDelivDate;
    }

    /**
     * お届け可能日のスタート値から, お届け日の配列を取得する.
     */
    function getDateArray($start_day, $end_day) {
        $masterData = new SC_DB_MasterData();
        $arrWDAY = $masterData->getMasterData("mtb_wday");
        //お届け可能日のスタート値がセットされていれば
        if($start_day >= 1) {
            $now_time = time();
            $max_day = $start_day + $end_day;
            // 集計
            for ($i = $start_day; $i < $max_day; $i++) {
                // 基本時間から日数を追加していく
                $tmp_time = $now_time + ($i * 24 * 3600);
                list($y, $m, $d, $w) = split(" ", date("Y m d w", $tmp_time));
                $val = sprintf("%04d/%02d/%02d(%s)", $y, $m, $d, $arrWDAY[$w]);
                $arrDate[$val] = $val;
            }
        } else {
            $arrDate = false;
        }
        return $arrDate;
    }

    /**
     * 商品種別ID からお届け時間の配列を取得する.
     */
    function getDelivTime($productTypeId) {
        $objQuery =& SC_Query::getSingletonInstance();
        $from = <<< __EOS__
                 dtb_deliv T1
            JOIN dtb_delivtime T2
              ON T1.deliv_id = T2. deliv_id
__EOS__;
            $objQuery->setOrder("time_id");
            $where = "deliv_id = ?";
            $results = $objQuery->select("time_id, deliv_time", $from,
                                         "product_type_id = ?", array($productTypeId));
            $arrDelivTime = array();
            foreach ($results as $val) {
                $arrDelivTime[$val['time_id']] = $val['deliv_time'];
            }
            return $arrDelivTime;
    }

    /**
     * 商品種別ID から配送業者ID を取得する.
     */
    function getDeliv($productTypeId) {
        $objQuery =& SC_Query::getSingletonInstance();
        return $objQuery->get("deliv_id", "dtb_deliv", "product_type_id = ?",
                                 array($productTypeId));
    }

    /**
     * 配送情報を登録する.
     */
    function registerShipping($orderId, $params) {
        $objQuery =& SC_Query::getSingletonInstance();

        $cols = $objQuery->listTableFields('dtb_shipping');

        foreach ($params as $shipping_id => $shipping_val) {
            // 存在するカラムのみ INSERT
            foreach ($shipping_val as $key => $val) {
                if (in_array($key, $cols)) {
                    $sqlval[$key] = $val;
                }
            }

            // 配送日付を timestamp に変換
            if (!SC_Utils_Ex::isBlank($sqlval['shipping_date'])) {
                $d = mb_strcut($sqlval["shipping_date"], 0, 10);
                $arrDate = split("/", $d);
                $ts = mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]);
                $sqlval['shipping_date'] = date("Y-m-d", $ts);
            }

            $sqlval['order_id'] = $orderId;
            $sqlval['shipping_id'] = $shipping_id;
            $sqlval['create_date'] = 'Now()';
            $sqlval['update_date'] = 'Now()';
            $objQuery->insert("dtb_shipping", $sqlval);
        }
    }

    /**
     * 配送商品を登録する.
     */
    function registerShipmentItem($orderId, $shippingId, $params) {
        $objQuery =& SC_Query::getSingletonInstance();
        $objProduct = new SC_Product();
        foreach ($params as $productClassId => $val) {
            $d = $objProduct->getDetailAndProductsClass($productClassId);
            $sqlval['order_id'] = $orderId;
            $sqlval['shipping_id'] = $shippingId;
            $sqlval['product_class_id'] = $productClassId;
            $sqlval['product_name'] = $d['name'];
            $sqlval['product_code'] = $d['product_code'];
            $sqlval['classcategory_name1'] = $d['classcategory_name1'];
            $sqlval['classcategory_name2'] = $d['classcategory_name2'];
            $sqlval['price'] = $d['price02'];
            $sqlval['quantity'] = $val['quantity'];
            $objQuery->insert("dtb_shipment_item", $sqlval);
        }
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
