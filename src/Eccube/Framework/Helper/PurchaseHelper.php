<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework\Helper;

use Eccube\Application;
use Eccube\Framework\Display;
use Eccube\Framework\Product;
use Eccube\Framework\Query;
use Eccube\Framework\SiteSession;
use Eccube\Framework\CartSession;
use Eccube\Framework\Customer;
use Eccube\Framework\Response;
use Eccube\Framework\DB\DBFactory;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\MailHelper;
use Eccube\Framework\Helper\PurchaseHelper;
use Eccube\Framework\Helper\TaxRuleHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/**
 * 商品購入関連のヘルパークラス.
 *
 * TODO 購入時強制会員登録機能(#521)の実装を検討
 * TODO dtb_customer.buy_times, dtb_customer.buy_total の更新
 *
 * @package Helper
 * @author Kentaro Ohkouchi
 */
class PurchaseHelper
{

    public $arrShippingKey = array(
        'name01', 'name02', 'kana01', 'kana02', 'company_name',
        'sex', 'zip01', 'zip02', 'country_id', 'zipcode', 'pref', 'addr01', 'addr02',
        'tel01', 'tel02', 'tel03', 'fax01', 'fax02', 'fax03',
    );

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
     * 決済モジュールを使用する場合は対応状況を「決済処理中」に設定し,
     * 決済完了後「新規受付」に変更すること
     *
     * @param  integer $orderStatus 受注処理を完了する際に設定する対応状況
     * @return void
     */
    public function completeOrder($orderStatus = ORDER_NEW)
    {
        $objQuery = Application::alias('eccube.query');
        /* @var $objSiteSession SiteSession */
        $objSiteSession = Application::alias('eccube.site_session');
        /* @var $objCartSession CartSession */
        $objCartSession = Application::alias('eccube.cart_session');
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        $customerId = $objCustomer->getValue('customer_id');

        $objQuery->begin();
        if (!$objSiteSession->isPrePage()) {
            Utils::sfDispSiteError(PAGE_ERROR, $objSiteSession);
        }

        $uniqId = $objSiteSession->getUniqId();
        $this->verifyChangeCart($uniqId, $objCartSession);

        $orderTemp = $this->getOrderTemp($uniqId);

        $orderTemp['status'] = $orderStatus;
        $cartkey = $objCartSession->getKey();
        $order_id = $this->registerOrderComplete($orderTemp, $objCartSession, $cartkey);
        $isMultiple = static::isMultiple();
        $shippingTemp =& $this->getShippingTemp($isMultiple);
        foreach ($shippingTemp as $shippingId => $val) {
            $this->registerShipmentItem($order_id, $shippingId, $val['shipment_item']);
        }

        $this->registerShipping($order_id, $shippingTemp);
        $objQuery->commit();

        //会員情報の最終購入日、購入合計を更新
        if ($customerId > 0) {
            Application::alias('eccube.customer')->updateOrderSummary($customerId);
        }

        $this->cleanupSession($order_id, $objCartSession, $objCustomer, $cartkey);

        GcUtils::gfPrintLog('order complete. order_id=' . $order_id);
    }

    /**
     * 受注をキャンセルする.
     *
     * 受注完了後の受注をキャンセルする.
     * この関数は, 主に決済モジュールにて, 受注をキャンセルする場合に使用する.
     *
     * 対応状況を引数 $orderStatus で指定した値に変更する.
     * (デフォルト ORDER_CANCEL)
     * 引数 $is_delete が true の場合は, 受注データを論理削除する.
     * 商品の在庫数は, 受注前の在庫数に戻される.
     *
     * @param  integer $order_id    受注ID
     * @param  integer $orderStatus 対応状況
     * @param  boolean $is_delete   受注データを論理削除する場合 true
     * @return void
     */
    public function cancelOrder($order_id, $orderStatus = ORDER_CANCEL, $is_delete = false)
    {
        $objQuery = Application::alias('eccube.query');
        $in_transaction = $objQuery->inTransaction();
        if (!$in_transaction) {
            $objQuery->begin();
        }

        $arrParams = array();
        $arrParams['status'] = $orderStatus;
        if ($is_delete) {
            $arrParams['del_flg'] = 1;
        }

        $this->registerOrder($order_id, $arrParams);

        $arrOrderDetail = $this->getOrderDetail($order_id);
        foreach ($arrOrderDetail as $arrDetail) {
            $objQuery->update('dtb_products_class', array(),
                              'product_class_id = ?', array($arrDetail['product_class_id']),
                              array('stock' => 'stock + ?'), array($arrDetail['quantity']));
        }
        if (!$in_transaction) {
            $objQuery->commit();
        }
    }

    /**
     * 受注をキャンセルし, カートをロールバックして, 受注一時IDを返す.
     *
     * 受注完了後の受注をキャンセルし, カートの状態を受注前の状態へ戻す.
     * この関数は, 主に, 決済モジュールに遷移した後, 購入確認画面へ戻る場合に使用する.
     *
     * 対応状況を引数 $orderStatus で指定した値に変更する.
     * (デフォルト ORDER_CANCEL)
     * 引数 $is_delete が true の場合は, 受注データを論理削除する.
     * 商品の在庫数, カートの内容は受注前の状態に戻される.
     *
     * @param  integer $order_id    受注ID
     * @param  integer $orderStatus 対応状況
     * @param  boolean $is_delete   受注データを論理削除する場合 true
     * @return string  受注一時ID
     */
    public function rollbackOrder($order_id, $orderStatus = ORDER_CANCEL, $is_delete = false)
    {
        $objQuery = Application::alias('eccube.query');
        $in_transaction = $objQuery->inTransaction();
        if (!$in_transaction) {
            $objQuery->begin();
        }

        $this->cancelOrder($order_id, $orderStatus, $is_delete);
        $arrOrderTemp = $this->getOrderTempByOrderId($order_id);
        $_SESSION = array_merge($_SESSION, unserialize($arrOrderTemp['session']));

        /* @var $objSiteSession SiteSession */
        $objSiteSession = Application::alias('eccube.site_session');
        /* @var $objCartSession CartSession */
        $objCartSession = Application::alias('eccube.cart_session');
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');

        // 新たに受注一時情報を保存する
        $objSiteSession->unsetUniqId();
        $uniqid = $objSiteSession->getUniqId();
        $arrOrderTemp['del_flg'] = 0;
        $this->saveOrderTemp($uniqid, $arrOrderTemp, $objCustomer);
        $this->verifyChangeCart($uniqid, $objCartSession);
        $objSiteSession->setRegistFlag();

        if (!$in_transaction) {
            $objQuery->commit();
        }

        return $uniqid;
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
     * @param  string         $uniqId         ユニークID
     * @param  CartSession $objCartSession
     * @return void
     */
    public function verifyChangeCart($uniqId, &$objCartSession)
    {
        $cartKey = $objCartSession->getKey();

        // カート内が空でないか
        if (Utils::isBlank($cartKey)) {
            Application::alias('eccube.response')->sendRedirect(CART_URL);
            exit;
        }

        // 初回のみカートの内容を保存
        $objCartSession->saveCurrentCart($uniqId, $cartKey);

        /*
         * POSTのユニークIDとセッションのユニークIDを比較
         *(ユニークIDがPOSTされていない場合はスルー)
         */
        if (!Application::alias('eccube.site_session')->checkUniqId()) {
            Utils::sfDispSiteError(CANCEL_PURCHASE);
            exit;
        }

        // 購入ボタンを押してから変化がないか
        $quantity = $objCartSession->getTotalQuantity($cartKey);
        if ($objCartSession->checkChangeCart($cartKey) || !($quantity > 0)) {
            Application::alias('eccube.response')->sendRedirect(CART_URL);
            exit;
        }
    }

    /**
     * 受注一時情報を取得する.
     *
     * @param  integer $uniqId 受注一時情報ID
     * @return array   受注一時情報の配列
     */
    public function getOrderTemp($uniqId)
    {
        $objQuery = Application::alias('eccube.query');

        return $objQuery->getRow('*', 'dtb_order_temp', 'order_temp_id = ?', array($uniqId));
    }

    /**
     * 受注IDをキーにして受注一時情報を取得する.
     *
     * @param  integer $order_id 受注ID
     * @return array   受注一時情報の配列
     */
    public function getOrderTempByOrderId($order_id)
    {
        $objQuery = Application::alias('eccube.query');

        return $objQuery->getRow('*', 'dtb_order_temp', 'order_id = ?', array($order_id));
    }

    /**
     * 受注一時情報を保存する.
     *
     * 既存のデータが存在しない場合は新規保存. 存在する場合は更新する.
     *
     * @param  integer     $uniqId      受注一時情報ID
     * @param  array       $params      登録する受注情報の配列
     * @param  Customer $objCustomer Customer インスタンス
     * @return void
     */
    public function saveOrderTemp($uniqId, $params, &$objCustomer = NULL)
    {
        if (Utils::isBlank($uniqId)) {
            return;
        }
        $params['device_type_id'] = Application::alias('eccube.display')->detectDevice();
        $objQuery = Application::alias('eccube.query');
        // 存在するカラムのみを対象とする
        $cols = $objQuery->listTableFields('dtb_order_temp');
        $sqlval = array();
        foreach ($params as $key => $val) {
            if (in_array($key, $cols)) {
                $sqlval[$key] = $val;
            }
        }

        $sqlval['session'] = serialize($_SESSION);
        if (!empty($objCustomer)) {
            // 注文者の情報を常に最新に保つ
            $this->copyFromCustomer($sqlval, $objCustomer);
        }
        $exists = $this->getOrderTemp($uniqId);

        //国ID追加
        $sqlval['order_country_id'] = ($sqlval['order_country_id']) ? $sqlval['order_country_id'] :  DEFAULT_COUNTRY_ID;

        if (Utils::isBlank($exists)) {
            $sqlval['order_temp_id'] = $uniqId;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->insert('dtb_order_temp', $sqlval);
        } else {
            $objQuery->update('dtb_order_temp', $sqlval, 'order_temp_id = ?', array($uniqId));
        }
    }

    /**
     * 配送情報をセッションから取得する.
     *
     * @param bool $has_shipment_item 配送商品を保有している配送先のみ返す。
     */
    public function getShippingTemp($has_shipment_item = false)
    {
        // ダウンロード商品の場合setされていないので空の配列を返す.
        if (!isset($_SESSION['shipping'])) return array();
        if ($has_shipment_item) {
            $arrReturn = array();
            foreach ($_SESSION['shipping'] as $key => $arrVal) {
                if (count($arrVal['shipment_item']) == 0) continue;
                $arrReturn[$key] = $arrVal;
            }

            return $arrReturn;
        }

        return $_SESSION['shipping'];
    }

    /**
     * 配送商品をクリア(消去)する
     *
     * @param  integer $shipping_id 配送先ID
     * @return void
     */
    public function clearShipmentItemTemp($shipping_id = null)
    {
        if (is_null($shipping_id)) {
            foreach ($_SESSION['shipping'] as $key => $value) {
                $this->clearShipmentItemTemp($key);
            }
        } else {
            if (!isset($_SESSION['shipping'][$shipping_id])) return;
            if (!is_array($_SESSION['shipping'][$shipping_id])) return;
            unset($_SESSION['shipping'][$shipping_id]['shipment_item']);
        }
    }

    /**
     * 配送商品を設定する.
     *
     * @param  integer $shipping_id      配送先ID
     * @param  integer $product_class_id 商品規格ID
     * @param  integer $quantity         数量
     * @return void
     */
    public function setShipmentItemTemp($shipping_id, $product_class_id, $quantity)
    {
        // 配列が長くなるので, リファレンスを使用する
        $arrItems =& $_SESSION['shipping'][$shipping_id]['shipment_item'][$product_class_id];

        $arrItems['shipping_id'] = $shipping_id;
        $arrItems['product_class_id'] = $product_class_id;
        $arrItems['quantity'] = $quantity;

        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');

        // カート情報から読みこめば済むと思うが、一旦保留。むしろ、カート情報も含め、セッション情報を縮小すべきかもしれない。
        /*
        $objCartSession = Application::alias('eccube.cart_session');
        $cartKey = $objCartSession->getKey();
        // 詳細情報を取得
        $cartItems = $objCartSession->getCartList($cartKey);
        */

        if (empty($arrItems['productsClass'])) {
            $product =& $objProduct->getDetailAndProductsClass($product_class_id);
            $arrItems['productsClass'] = $product;
        }
        $arrItems['price'] = $arrItems['productsClass']['price02'];
        $inctax = TaxRuleHelper::sfCalcIncTax($arrItems['price'], $arrItems['productsClass']['product_id'],
                                                     $arrItems['productsClass']['product_class_id']);
        $arrItems['total_inctax'] = $inctax * $arrItems['quantity'];
    }

    /**
     * 配送先都道府県の配列を返す.
     * @param boolean $is_multiple
     */
    public function getShippingPref($is_multiple)
    {
        $results = array();
        foreach (static::getShippingTemp($is_multiple) as $val) {
            $results[] = $val['shipping_pref'];
        }

        return $results;
    }

    /**
     * 複数配送指定の購入かどうか.
     *
     * @return boolean 複数配送指定の購入の場合 true
     */
    public function isMultiple()
    {
        return count(static::getShippingTemp(true)) >= 2;
    }

    /**
     * 配送情報をセッションに保存する.
     *
     * XXX マージする理由が不明(なんとなく便利な気はするけど)。分かる方コメントに残してください。
     * @param  array   $arrSrc      配送情報の連想配列
     * @param  integer $shipping_id 配送先ID
     * @return void
     */
    public function saveShippingTemp($arrSrc, $shipping_id = 0)
    {
        // 配送商品は引き継がない
        unset($arrSrc['shipment_item']);

        if (!isset($_SESSION['shipping'][$shipping_id])) {
            $_SESSION['shipping'][$shipping_id] = array();
        }
        $_SESSION['shipping'][$shipping_id] = array_merge($_SESSION['shipping'][$shipping_id], $arrSrc);
        $_SESSION['shipping'][$shipping_id]['shipping_id'] = $shipping_id;
    }

    /**
     * セッションの配送情報を破棄する.
     *
     * @deprecated 2.12.0 から EC-CUBE 本体では使用していない。
     * @return void
     */
    public function unsetShippingTemp()
    {
        PurchaseHelper::unsetAllShippingTemp(true);
    }

    /**
     * セッションの配送情報を全て破棄する
     *
     * @param  bool $multiple_temp 複数お届け先の画面戻り処理用の情報も破棄するか
     * @return void
     */
    public static function unsetAllShippingTemp($multiple_temp = false)
    {
        unset($_SESSION['shipping']);
        if ($multiple_temp) {
            unset($_SESSION['multiple_temp']);
        }
    }

    /**
     * セッションの配送情報を個別に破棄する
     *
     * @param  integer $shipping_id 配送先ID
     * @return void
     */
    public static function unsetOneShippingTemp($shipping_id)
    {
        unset($_SESSION['shipping'][$shipping_id]);
    }

    /**
     * 会員情報を受注情報にコピーする.
     *
     * ユーザーがログインしていない場合は何もしない.
     * 会員情報を $dest の order_* へコピーする.
     * customer_id は強制的にコピーされる.
     *
     * @param  array       $dest        コピー先の配列
     * @param  Customer $objCustomer Customer インスタンス
     * @param  string      $prefix      コピー先の接頭辞. デフォルト order
     * @param  array       $keys        コピー対象のキー
     * @return void
     */
    public function copyFromCustomer(&$dest, &$objCustomer, $prefix = 'order',
        $keys = array('name01', 'name02', 'kana01', 'kana02', 'company_name',
            'sex', 'zip01', 'zip02', 'country_id', 'zipcode', 'pref', 'addr01', 'addr02',
            'tel01', 'tel02', 'tel03', 'fax01', 'fax02', 'fax03',
            'job', 'birth', 'email',
        )
    ) {
        if ($objCustomer->isLoginSuccess(true)) {
            foreach ($keys as $key) {
                if (in_array($key, $keys)) {
                    $dest[$prefix . '_' . $key] = $objCustomer->getValue($key);
                }
            }

            if ((Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE)
                && in_array('email', $keys)
            ) {
                $email_mobile = $objCustomer->getValue('email_mobile');
                if (empty($email_mobile)) {
                    $dest[$prefix . '_email'] = $objCustomer->getValue('email');
                } else {
                    $dest[$prefix . '_email'] = $email_mobile;
                }
            }

            $dest['customer_id'] = $objCustomer->getValue('customer_id');
            $dest['update_date'] = 'CURRENT_TIMESTAMP';
        }
    }

    /**
     * 受注情報を配送情報にコピーする.
     *
     * 受注情報($src)を $dest の order_* へコピーする.
     *
     * TODO 汎用的にして \Eccube\Framework\Util\Utils へ移動
     *
     * @param  array  $dest       コピー先の配列
     * @param  array  $src        コピー元の配列
     * @param  array  $arrKey     コピー対象のキー
     * @param  string $prefix     コピー先の接頭辞. デフォルト shipping
     * @param  string $src_prefix コピー元の接頭辞. デフォルト order
     * @return void
     */
    public function copyFromOrder(&$dest, $src, $prefix = 'shipping', $src_prefix = 'order', $arrKey = null)
    {
        if (is_null($arrKey)) {
            $arrKey = $this->arrShippingKey;
        }
        if (!Utils::isBlank($prefix)) {
            $prefix = $prefix . '_';
        }
        if (!Utils::isBlank($src_prefix)) {
            $src_prefix = $src_prefix . '_';
        }
        foreach ($arrKey as $key) {
            if (isset($src[$src_prefix . $key])) {
                $dest[$prefix . $key] = $src[$src_prefix . $key];
            }
        }
    }

    /**
     * 配送情報のみ抜き出す。
     *
     * @param  string $arrSrc 元となる配列
     * @return void
     */
    public function extractShipping($arrSrc)
    {
        $arrKey = array();
        foreach ($this->arrShippingKey as $key) {
            $arrKey[] = 'shipping_' . $key;
        }

        return Utils::sfArrayIntersectKeys($arrSrc, $arrKey);
    }

    public function setDefaultPurchase($uniqId, $cartKey, &$objCustomer, &$objCartSess)
    {
        // 基本配送情報登録
        $sqlval = array();
        $this->copyFromCustomer($sqlval, $objCustomer, 'shipping');
        $this->saveShippingTemp($sqlval);
        // 基本配送業者・支払方法登録
        $objDelivery = new DeliveryHelper();
        $arrDeliv = $objDelivery->getList($cartKey);
        $deliv_id = $arrDeliv[0]['deliv_id'];
        $sqlval['deliv_id'] = $deliv_id;
        $arrPaymentDeliv = $objDelivery->getPayments($deliv_id);
        $total = $objCartSess->getAllProductsTotal($cartKey);
        $objPayment = new PaymentHelper();
        $arrPaymentTotal = $objPayment->getByPrice($total);
        foreach ($arrPaymentTotal as $payment) {
            if (in_array($payment['payment_id'], $arrPaymentDeliv)) {
                $sqlval['payment_id'] = $payment['payment_id'];
                $sqlval['charge'] = $payment['charge'];
                $sqlval['payment_method'] = $payment['payment_method'];
                break;
            }
        }
        $this->saveOrderTemp($uniqId, $sqlval);
    }

    /**
     * 配送業者IDから, 支払い方法, お届け時間の配列を取得する.
     *
     * 結果の連想配列の添字の値は以下の通り
     * - 'arrDelivTime' - お届け時間の配列
     * - 'arrPayment' - 支払い方法の配列
     * - 'img_show' - 支払い方法の画像の有無
     *
     * @param  SC_CartSession $objCartSess SC_CartSession インスタンス
     * @param  integer        $deliv_id    配送業者ID
     * @return array          支払い方法, お届け時間を格納した配列
     */
    public function getSelectablePayment(&$objCartSess, $deliv_id, $is_list = false)
    {
        $arrPayment = array();
        if (strval($deliv_id) === strval(intval($deliv_id))) {
            $total = $objCartSess->getAllProductsTotal($objCartSess->getKey());
            $payments_deliv = DeliveryHelper::getPayments($deliv_id);
            $objPayment = new PaymentHelper();
            $payments_total = $objPayment->getByPrice($total);
            foreach ($payments_total as $payment) {
                if (in_array($payment['payment_id'], $payments_deliv)) {
                    if ($is_list) {
                        $arrPayment[$payment['payment_id']] = $payment['payment_method'];
                    } else {
                        $arrPayment[] = $payment;
                    }
                }
            }
        }
        return $arrPayment;
    }

    /**
     * お届け日一覧を取得する.
     * @param CartSession $objCartSess
     * @param integer $productTypeId
     */
    public function getDelivDate(&$objCartSess, $productTypeId)
    {
        $cartList = $objCartSess->getCartList($productTypeId);
        $delivDateIds = array();
        foreach ($cartList as $item) {
            $delivDateIds[] = $item['productsClass']['deliv_date_id'];
        }
        $max_date = max($delivDateIds);
        //発送目安
        switch ($max_date) {
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
                $start_day = '';
                break;
            default:
                //お届け日が設定されていない場合
                $start_day = '';
                break;
        }
        //お届け可能日のスタート値から、お届け日の配列を取得する
        $arrDelivDate = $this->getDateArray($start_day, DELIV_DATE_END_MAX);

        return $arrDelivDate;
    }

    /**
     * お届け可能日のスタート値から, お届け日の配列を取得する.
     */
    public function getDateArray($start_day, $end_day)
    {
        $masterData = Application::alias('eccube.db.master_data');
        $arrWDAY = $masterData->getMasterData('mtb_wday');
        //お届け可能日のスタート値がセットされていれば
        if ($start_day >= 1) {
            $now_time = time();
            $max_day = $start_day + $end_day;
            // 集計
            for ($i = $start_day; $i < $max_day; $i++) {
                // 基本時間から日数を追加していく
                $tmp_time = $now_time + ($i * 24 * 3600);
                list($y, $m, $d, $w) = explode(' ', date('Y m d w', $tmp_time));
                $val = sprintf('%04d/%02d/%02d(%s)', $y, $m, $d, $arrWDAY[$w]);
                $arrDate[$val] = $val;
            }
        } else {
            $arrDate = false;
        }

        return $arrDate;
    }

    /**
     * 配送情報の登録を行う.
     *
     * $arrParam のうち, dtb_shipping テーブルに存在するカラムのみを登録する.
     *
     * TODO UPDATE/INSERT にする
     *
     * @param  integer $order_id              受注ID
     * @param  array   $arrParams             配送情報の連想配列
     * @param  boolean $convert_shipping_date yyyy/mm/dd(EEE) 形式の配送日付を変換する場合 true
     * @return void
     */
    public function registerShipping($order_id, $arrParams, $convert_shipping_date = true)
    {
        $objQuery = Application::alias('eccube.query');
        $table = 'dtb_shipping';
        $where = 'order_id = ?';
        $objQuery->delete($table, $where, array($order_id));

        foreach ($arrParams as $key => $arrShipping) {
            $arrValues = $objQuery->extractOnlyColsOf($table, $arrShipping);

            // 配送日付を timestamp に変換
            if (!Utils::isBlank($arrValues['shipping_date'])
                && $convert_shipping_date) {
                $d = mb_strcut($arrValues['shipping_date'], 0, 10);
                $arrDate = explode('/', $d);
                $ts = mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]);
                $arrValues['shipping_date'] = date('Y-m-d', $ts);
            }

            // 非会員購入の場合は shipping_id が存在しない
            if (!isset($arrValues['shipping_id'])) {
                $arrValues['shipping_id'] = $key;
            }
            $arrValues['order_id'] = $order_id;
            $arrValues['create_date'] = 'CURRENT_TIMESTAMP';
            $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
            //国ID追加
            /*いらないかもしれないんでとりあえずコメントアウト
            $arrValues['shipping_country_id'] = DEFAULT_COUNTRY_ID;
            */

            $objQuery->insert($table, $arrValues);
        }
    }

    /**
     * 配送商品を登録する.
     *
     * @param  integer $order_id    受注ID
     * @param  integer $shipping_id 配送先ID
     * @param  array   $arrParams   配送商品の配列
     * @return void
     */
    public function registerShipmentItem($order_id, $shipping_id, $arrParams)
    {
        $objQuery = Application::alias('eccube.query');
        $table = 'dtb_shipment_item';
        $where = 'order_id = ? AND shipping_id = ?';
        $objQuery->delete($table, $where, array($order_id, $shipping_id));

        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        foreach ($arrParams as $arrValues) {
            if (Utils::isBlank($arrValues['product_class_id'])) {
                continue;
            }
            $d = $objProduct->getDetailAndProductsClass($arrValues['product_class_id']);
            $name = Utils::isBlank($arrValues['product_name'])
                ? $d['name']
                : $arrValues['product_name'];

            $code = Utils::isBlank($arrValues['product_code'])
                ? $d['product_code']
                : $arrValues['product_code'];

            $cname1 = Utils::isBlank($arrValues['classcategory_name1'])
                ? $d['classcategory_name1']
                : $arrValues['classcategory_name1'];

            $cname2 = Utils::isBlank($arrValues['classcategory_name2'])
                ? $d['classcategory_name2']
                : $arrValues['classcategory_name2'];

            $price = Utils::isBlank($arrValues['price'])
                ? $d['price']
                : $arrValues['price'];

            $arrValues['order_id'] = $order_id;
            $arrValues['shipping_id'] = $shipping_id;
            $arrValues['product_name'] = $name;
            $arrValues['product_code'] = $code;
            $arrValues['classcategory_name1'] = $cname1;
            $arrValues['classcategory_name2'] = $cname2;
            $arrValues['price'] = $price;

            $arrExtractValues = $objQuery->extractOnlyColsOf($table, $arrValues);
            $objQuery->insert($table, $arrExtractValues);
        }
    }

    /**
     * 受注登録を完了する.
     *
     * 引数の受注情報を受注テーブル及び受注詳細テーブルに登録する.
     * 登録後, 受注一時テーブルに削除フラグを立てる.
     *
     * @param array          $orderParams    登録する受注情報の配列
     * @param CartSession $objCartSession カート情報のインスタンス
     * @param integer        $cartKey        登録を行うカート情報のキー
     * @param integer 受注ID
     */
    public function registerOrderComplete($orderParams, &$objCartSession, $cartKey)
    {
        $objQuery = Application::alias('eccube.query');

        // 不要な変数を unset
        $unsets = array('mailmaga_flg', 'deliv_check', 'point_check', 'password',
                        'reminder', 'reminder_answer', 'mail_flag', 'session');
        foreach ($unsets as $unset) {
            unset($orderParams[$unset]);
        }

        // 対応状況の指定が無い場合は新規受付
        if (Utils::isBlank($orderParams['status'])) {
            $orderParams['status'] = ORDER_NEW;
        }

        $orderParams['del_flg'] = '0';
        $orderParams['create_date'] = 'CURRENT_TIMESTAMP';
        $orderParams['update_date'] = 'CURRENT_TIMESTAMP';

        $this->registerOrder($orderParams['order_id'], $orderParams);

        // 詳細情報を取得
        $cartItems = $objCartSession->getCartList($cartKey, $orderParams['order_pref'], $orderParams['order_country_id']);

        // 詳細情報を生成
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        $i = 0;
        $arrDetail = array();
        foreach ($cartItems as $item) {
            $p =& $item['productsClass'];
            $arrDetail[$i]['order_id'] = $orderParams['order_id'];
            $arrDetail[$i]['product_id'] = $p['product_id'];
            $arrDetail[$i]['product_class_id'] = $p['product_class_id'];
            $arrDetail[$i]['product_name'] = $p['name'];
            $arrDetail[$i]['product_code'] = $p['product_code'];
            $arrDetail[$i]['classcategory_name1'] = $p['classcategory_name1'];
            $arrDetail[$i]['classcategory_name2'] = $p['classcategory_name2'];
            $arrDetail[$i]['point_rate'] = $item['point_rate'];
            $arrDetail[$i]['price'] = $item['price'];
            $arrDetail[$i]['quantity'] = $item['quantity'];
            $arrDetail[$i]['tax_rate'] = $item['tax_rate'];
            $arrDetail[$i]['tax_rule'] = $item['tax_rule'];
            $arrDetail[$i]['tax_adjuts'] = $item['tax_adjust'];

            // 在庫の減少処理
            if (!$objProduct->reduceStock($p['product_class_id'], $item['quantity'])) {
                $objQuery->rollback();
                Utils::sfDispSiteError(SOLD_OUT, '', true);
            }
            $i++;
        }
        $this->registerOrderDetail($orderParams['order_id'], $arrDetail);

        $objQuery->update(
            'dtb_order_temp', array('del_flg' => 1),
            'order_temp_id = ?',
            array(Application::alias('eccube.site_session')->getUniqId()));

        return $orderParams['order_id'];
    }

    /**
     * 受注情報を登録する.
     *
     * 既に受注IDが存在する場合は, 受注情報を更新する.
     * 引数の受注IDが, 空白又は null の場合は, 新しく受注IDを発行して登録する.
     *
     * @param  integer $order_id  受注ID
     * @param  array   $arrParams 受注情報の連想配列
     * @return integer 受注ID
     */
    public function registerOrder($order_id, $arrParams)
    {
        $table = 'dtb_order';
        $where = 'order_id = ?';
        $objQuery = Application::alias('eccube.query');
        $arrValues = $objQuery->extractOnlyColsOf($table, $arrParams);

        $exists = $objQuery->exists($table, $where, array($order_id));
        if ($exists) {
            $this->sfUpdateOrderStatus($order_id, $arrValues['status'],
                                       $arrValues['add_point'],
                                       $arrValues['use_point'],
                                       $arrValues);
            $this->sfUpdateOrderNameCol($order_id);

            $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->update($table, $arrValues, $where, array($order_id));
        } else {
            if (Utils::isBlank($order_id)) {
                $order_id = $this->getNextOrderID();
            }
            /*
             * 新規受付の場合は対応状況 null で insert し,
             * sfUpdateOrderStatus で引数で受け取った値に変更する.
             */
            $status = $arrValues['status'];
            $arrValues['status'] = null;
            $arrValues['order_id'] = $order_id;
            $arrValues['customer_id'] =
                    Utils::isBlank($arrValues['customer_id'])
                    ? 0 : $arrValues['customer_id'];
            $arrValues['create_date'] = 'CURRENT_TIMESTAMP';
            $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->insert($table, $arrValues);

            $this->sfUpdateOrderStatus($order_id, $status,
                                       $arrValues['add_point'],
                                       $arrValues['use_point'],
                                       $arrValues);
            $this->sfUpdateOrderNameCol($order_id);
        }

        return $order_id;
    }

    /**
     * 受注詳細情報を登録する.
     *
     * 既に, 該当の受注が存在する場合は, 受注情報を削除し, 登録する.
     *
     * @param  integer $order_id  受注ID
     * @param  array   $arrParams 受注情報の連想配列
     * @return void
     */
    public function registerOrderDetail($order_id, $arrParams)
    {
        $table = 'dtb_order_detail';
        $where = 'order_id = ?';
        $objQuery = Application::alias('eccube.query');

        $objQuery->delete($table, $where, array($order_id));
        foreach ($arrParams as $arrDetail) {
            $arrValues = $objQuery->extractOnlyColsOf($table, $arrDetail);
            $arrValues['order_detail_id'] = $objQuery->nextVal('dtb_order_detail_order_detail_id');
            $arrValues['order_id'] = $order_id;
            $objQuery->insert($table, $arrValues);
        }
    }

    /**
     * 受注情報を取得する.
     *
     * @param  integer $order_id    受注ID
     * @param  integer $customer_id 会員ID
     * @return array   受注情報の配列
     */
    public function getOrder($order_id, $customer_id = null)
    {
        $objQuery = Application::alias('eccube.query');
        $where = 'order_id = ?';
        $arrValues = array($order_id);
        if (!Utils::isBlank($customer_id)) {
            $where .= ' AND customer_id = ?';
            $arrValues[] = $customer_id;
        }

        return $objQuery->getRow('*', 'dtb_order', $where, $arrValues);
    }

    /**
     * 受注詳細を取得する.
     *
     * @param  integer $order_id         受注ID
     * @param  boolean $has_order_status 対応状況, 入金日も含める場合 true
     * @return array   受注詳細の配列
     */
    public function getOrderDetail($order_id, $has_order_status = true)
    {
        $objQuery = Application::alias('eccube.query');
        /* @var $dbFactory DBFactory */
        $dbFactory = Application::alias('eccube.db.factory');
        $col = <<< __EOS__
            T3.product_id,
            T3.product_class_id as product_class_id,
            T3.product_type_id AS product_type_id,
            T2.product_code,
            T2.product_name,
            T2.classcategory_name1 AS classcategory_name1,
            T2.classcategory_name2 AS classcategory_name2,
            T2.price,
            T2.quantity,
            T2.point_rate,
            T2.tax_rate,
            T2.tax_rule,
__EOS__;
        if ($has_order_status) {
            $col .= 'T1.status AS status, T1.payment_date AS payment_date,';
        }
        $col .= <<< __EOS__
            CASE WHEN
                EXISTS(
                    SELECT * FROM dtb_products
                    WHERE product_id = T3.product_id
                        AND del_flg = 0
                        AND status = 1
                )
                THEN '1'
                ELSE '0'
            END AS enable,
__EOS__;
        $col .= $dbFactory->getDownloadableDaysWhereSql('T1') . ' AS effective';
        $from = <<< __EOS__
            dtb_order T1
            JOIN dtb_order_detail T2
                ON T1.order_id = T2.order_id
            LEFT JOIN dtb_products_class T3
                ON T2.product_class_id = T3.product_class_id
__EOS__;
        $objQuery->setOrder('T2.order_detail_id');

        return $objQuery->select($col, $from, 'T1.order_id = ?', array($order_id));
    }

    /**
     * ダウンロード可能フラグを, 受注詳細に設定する.
     *
     * ダウンロード可能と判断されるのは, 以下の通り.
     *
     * 1. ダウンロード可能期限が期限内かつ, 入金日が入力されている
     * 2. 販売価格が 0 円である
     *
     * 受注詳細行には, is_downloadable という真偽値が設定される.
     * @param array 受注詳細の配列
     * @return void
     */
    public function setDownloadableFlgTo(&$arrOrderDetail)
    {
        foreach ($arrOrderDetail as $key => $value) {
            // 販売価格が 0 円
            if ($arrOrderDetail[$key]['price'] == '0') {
                $arrOrderDetail[$key]['is_downloadable'] = true;
            // ダウンロード期限内かつ, 入金日あり
            } elseif ($arrOrderDetail[$key]['effective'] == '1'
                    && !Utils::isBlank($arrOrderDetail[$key]['payment_date'])) {
                $arrOrderDetail[$key]['is_downloadable'] = true;
            } else {
                $arrOrderDetail[$key]['is_downloadable'] = false;
            }
        }
    }

    /**
     * 配送情報を取得する.
     *
     * @param  integer $order_id  受注ID
     * @param  boolean $has_items 結果に配送商品も含める場合 true
     * @return array   配送情報の配列
     */
    public function getShippings($order_id, $has_items = true)
    {
        $objQuery = Application::alias('eccube.query');
        $arrResults = array();
        $objQuery->setOrder('shipping_id');
        $arrShippings = $objQuery->select('*', 'dtb_shipping', 'order_id = ?',
                                          array($order_id));
        // shipping_id ごとの配列を生成する
        foreach ($arrShippings as $shipping) {
            foreach ($shipping as $key => $val) {
                $arrResults[$shipping['shipping_id']][$key] = $val;
            }
        }

        if ($has_items) {
            foreach ($arrResults as $shipping_id => $value) {
                $arrResults[$shipping_id]['shipment_item']
                        =& $this->getShipmentItems($order_id, $shipping_id);
            }
        }

        return $arrResults;
    }

    /**
     * 配送商品を取得する.
     *
     * @param  integer $order_id    受注ID
     * @param  integer $shipping_id 配送先ID
     * @param  boolean $has_detail  商品詳細も取得する場合 true
     * @return array   商品規格IDをキーにした配送商品の配列
     */
    public function getShipmentItems($order_id, $shipping_id, $has_detail = true)
    {
        $objQuery = Application::alias('eccube.query');
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        $arrResults = array();
        $objQuery->setOrder('order_detail_id');
        $arrItems = $objQuery->select('dtb_shipment_item.*',
                                      'dtb_shipment_item JOIN dtb_order_detail
                                           ON dtb_shipment_item.product_class_id = dtb_order_detail.product_class_id
                                           AND dtb_shipment_item.order_id = dtb_order_detail.order_id',
                                      'dtb_order_detail.order_id = ? AND shipping_id = ?',
                                      array($order_id, $shipping_id));

        foreach ($arrItems as $key => $arrItem) {
            $product_class_id = $arrItem['product_class_id'];

            foreach ($arrItem as $detailKey => $detailVal) {
                $arrResults[$key][$detailKey] = $detailVal;
            }
            // 商品詳細を関連づける
            if ($has_detail) {
                $arrResults[$key]['productsClass']
                    =& $objProduct->getDetailAndProductsClass($product_class_id);
            }
        }

        return $arrResults;
    }

    /**
     * 注文受付メールを送信する.
     *
     * 端末種別IDにより, 携帯電話の場合は携帯用の文面,
     * それ以外の場合は PC 用の文面でメールを送信する.
     *
     * @param integer $order_id 受注ID
     * @param  object  $objPage LC_Page インスタンス
     * @return boolean 送信に成功したか。現状では、正確には取得できない。
     */
    public static function sendOrderMail($order_id, &$objPage = NULL)
    {
        /* @var $objMail MailHelper */
        $objMail = Application::alias('eccube.helper.mail');

        // setPageは、プラグインの処理に必要(see #1798)
        if (is_object($objPage)) {
            $objMail->setPage($objPage);
        }

        $arrOrder = static::getOrder($order_id);
        if (empty($arrOrder)) {
            return false; // 失敗
        }
        $template_id = $arrOrder['device_type_id'] == DEVICE_TYPE_MOBILE ? 2 : 1;
        $objMail->sfSendOrderMail($order_id, $template_id);

        return true; // 成功
    }

    /**
     * 受注.対応状況の更新
     *
     * 必ず呼び出し元でトランザクションブロックを開いておくこと。
     *
     * @param  integer      $orderId     注文番号
     * @param  integer|null $newStatus   対応状況 (null=変更無し)
     * @param  integer|null $newAddPoint 加算ポイント (null=変更無し)
     * @param  integer|null $newUsePoint 使用ポイント (null=変更無し)
     * @param  array        $sqlval      更新後の値をリファレンスさせるためのパラメーター
     * @return void
     */
    public function sfUpdateOrderStatus($orderId, $newStatus = null, $newAddPoint = null, $newUsePoint = null, &$sqlval = array())
    {
        $objQuery = Application::alias('eccube.query');
        $arrOrderOld = $objQuery->getRow('status, add_point, use_point, customer_id', 'dtb_order', 'order_id = ?', array($orderId));

        // 対応状況が変更無しの場合、DB値を引き継ぐ
        if (is_null($newStatus)) {
            $newStatus = $arrOrderOld['status'];
        }

        // 使用ポイント、DB値を引き継ぐ
        if (is_null($newUsePoint)) {
            $newUsePoint = $arrOrderOld['use_point'];
        }

        // 加算ポイント、DB値を引き継ぐ
        if (is_null($newAddPoint)) {
            $newAddPoint = $arrOrderOld['add_point'];
        }

        if (USE_POINT !== false) {
            // 会員.ポイントの加減値
            $addCustomerPoint = 0;

            // ▼使用ポイント
            // 変更前の対応状況が利用対象の場合、変更前の使用ポイント分を戻す
            if ($this->isUsePoint($arrOrderOld['status'])) {
                $addCustomerPoint += $arrOrderOld['use_point'];
            }

            // 変更後の対応状況が利用対象の場合、変更後の使用ポイント分を引く
            if ($this->isUsePoint($newStatus)) {
                $addCustomerPoint -= $newUsePoint;
            }

            // ▲使用ポイント

            // ▼加算ポイント
            // 変更前の対応状況が加算対象の場合、変更前の加算ポイント分を戻す
            if ($this->isAddPoint($arrOrderOld['status'])) {
                $addCustomerPoint -= $arrOrderOld['add_point'];
            }

            // 変更後の対応状況が加算対象の場合、変更後の加算ポイント分を足す
            if ($this->isAddPoint($newStatus)) {
                $addCustomerPoint += $newAddPoint;
            }
            // ▲加算ポイント

            if ($addCustomerPoint != 0) {
                // ▼会員テーブルの更新
                $objQuery->update('dtb_customer', array('update_date' => 'CURRENT_TIMESTAMP'),
                                  'customer_id = ?', array($arrOrderOld['customer_id']),
                                  array('point' => 'point + ?'), array($addCustomerPoint));
                // ▲会員テーブルの更新

                // 会員.ポイントをマイナスした場合、
                if ($addCustomerPoint < 0) {
                    $sql = 'SELECT point FROM dtb_customer WHERE customer_id = ?';
                    $point = $objQuery->getOne($sql, array($arrOrderOld['customer_id']));
                    // 変更後の会員.ポイントがマイナスの場合、
                    if ($point < 0) {
                        // ロールバック
                        $objQuery->rollback();
                        // エラー
                        Utils::sfDispSiteError(LACK_POINT);
                    }
                }
            }
        }

        // ▼受注テーブルの更新
        if (empty($sqlval)) {
            $sqlval = array();
        }

        if (USE_POINT !== false) {
            $sqlval['add_point'] = $newAddPoint;
            $sqlval['use_point'] = $newUsePoint;
        }
        // 対応状況が発送済みに変更の場合、発送日を更新
        if ($arrOrderOld['status'] != ORDER_DELIV && $newStatus == ORDER_DELIV) {
            $sqlval['commit_date'] = 'CURRENT_TIMESTAMP';
        // 対応状況が入金済みに変更の場合、入金日を更新
        } elseif ($arrOrderOld['status'] != ORDER_PRE_END && $newStatus == ORDER_PRE_END) {
            $sqlval['payment_date'] = 'CURRENT_TIMESTAMP';
        }

        $sqlval['status'] = $newStatus;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';

        $dest = $objQuery->extractOnlyColsOf('dtb_order', $sqlval);
        $objQuery->update('dtb_order', $dest, 'order_id = ?', array($orderId));
        // ▲受注テーブルの更新

        //会員情報の最終購入日、購入合計を更新
        if ($arrOrderOld['customer_id'] > 0 and $arrOrderOld['status'] != $newStatus) {
            Application::alias('eccube.customer')->updateOrderSummary($arrOrderOld['customer_id']);
        }
    }

    /**
     * 受注の名称列を更新する
     *
     * @param integer $order_id   更新対象の注文番号
     * @param boolean $temp_table 更新対象は「受注_Temp」か
     * @static
     */
    public function sfUpdateOrderNameCol($order_id, $temp_table = false)
    {
        $objQuery = Application::alias('eccube.query');

        if ($temp_table) {
            $tgt_table = 'dtb_order_temp';
            $sql_where = 'order_temp_id = ?';
        } else {
            $tgt_table = 'dtb_order';
            $sql_where = 'order_id = ?';

            $sql_sub = <<< __EOS__
                SELECT deliv_time
                FROM dtb_delivtime
                WHERE time_id = dtb_shipping.time_id
                    AND deliv_id = (SELECT dtb_order.deliv_id FROM dtb_order WHERE order_id = dtb_shipping.order_id)
__EOS__;
            $objQuery->update('dtb_shipping', array(),
                              $sql_where,
                              array($order_id),
                              array('shipping_time' => "($sql_sub)"));
        }

        $objQuery->update($tgt_table, array(),
                          $sql_where,
                          array($order_id),
                          array('payment_method' =>
                                '(SELECT payment_method FROM dtb_payment WHERE payment_id = ' . $tgt_table . '.payment_id)'));
    }

    /**
     * ポイント使用するかの判定
     *
     * $status が null の場合は false を返す.
     *
     * @param  integer $status 対応状況
     * @return boolean 使用するか(会員テーブルから減算するか)
     */
    public function isUsePoint($status)
    {
        if ($status == null) {
            return false;
        }
        switch ($status) {
            case ORDER_CANCEL:      // キャンセル

                return false;
            default:
                break;
        }

        return true;
    }

    /**
     * ポイント加算するかの判定
     *
     * @param  integer $status 対応状況
     * @return boolean 加算するか
     */
    public function isAddPoint($status)
    {
        switch ($status) {
            case ORDER_NEW:         // 新規注文
            case ORDER_PAY_WAIT:    // 入金待ち
            case ORDER_PRE_END:     // 入金済み
            case ORDER_CANCEL:      // キャンセル
            case ORDER_BACK_ORDER:  // 取り寄せ中

                return false;

            case ORDER_DELIV:       // 発送済み

                return true;

            default:
                break;
        }

        return false;
    }

    /**
     * セッションに保持している情報を破棄する.
     *
     * 通常、受注処理(completeOrder)完了後に呼び出され、
     * セッション情報を破棄する.
     *
     * 決済モジュール画面から確認画面に「戻る」場合を考慮し、
     * セッション情報を破棄しないカスタマイズを、モジュール側で
     * 加える機会を与える.
     *
     * $orderId が使われていない。
     *
     * @param integer        $orderId        注文番号
     * @param CartSession $objCartSession カート情報のインスタンス
     * @param Customer    $objCustomer    Customer インスタンス
     * @param integer        $cartKey        登録を行うカート情報のキー
     */
    public function cleanupSession($orderId, &$objCartSession, &$objCustomer, $cartKey)
    {
        // カートの内容を削除する.
        $objCartSession->delAllProducts($cartKey);
        Application::alias('eccube.site_session')->unsetUniqId();

        // セッションの配送情報を破棄する.
        $this->unsetAllShippingTemp(true);
        $objCustomer->updateSession();
    }

    /**
     * 単一配送指定用に配送商品を設定する
     *
     * @param  CartSession $objCartSession カート情報のインスタンス
     * @param  integer        $shipping_id    配送先ID
     * @return void
     */
    public function setShipmentItemTempForSole(&$objCartSession, $shipping_id = 0)
    {
        /* @var $objCartSess CartSession */
        $objCartSess = Application::alias('eccube.cart_session');

        $this->clearShipmentItemTemp();

        $arrCartList =& $objCartSession->getCartList($objCartSess->getKey());
        foreach ($arrCartList as $arrCartRow) {
            if ($arrCartRow['quantity'] == 0) continue;
            $this->setShipmentItemTemp($shipping_id, $arrCartRow['id'], $arrCartRow['quantity']);
        }
    }

    /**
     * 新規受注の注文IDを返す
     *
     * @return integer
     */
    public function getNextOrderID()
    {
        $objQuery = Application::alias('eccube.query');

        return $objQuery->nextVal('dtb_order_order_id');
    }

    /**
     * 決済処理中スタータスの受注データのキャンセル処理
     * @param bool $cancel_flg 決済処理中ステータスのロールバックをするか(true:する false:しない)
     * @return void
     */
    public function cancelPendingOrder($cancel_flg)
    {
        if ($cancel_flg == true) {
            $this->checkDbAllPendingOrder();
            $this->checkDbMyPendignOrder();
            $this->checkSessionPendingOrder();
        }
    }

    /**
     * 決済処理中スタータスの全受注検索
     */
    public function checkDbAllPendingOrder()
    {
        $term = PENDING_ORDER_CANCEL_TIME;
        if (!Utils::isBlank($term) && preg_match("/^[0-9]+$/", $term)) {
            $target_time = strtotime('-' . $term . ' sec');
            $objQuery = Application::alias('eccube.query');
            $arrVal = array(date('Y/m/d H:i:s', $target_time), ORDER_PENDING);
            $objQuery->begin();
            $arrOrders = $objQuery->select('order_id', 'dtb_order', 'create_date <= ? and status = ? and del_flg = 0', $arrVal);
            if (!Utils::isBlank($arrOrders)) {
                foreach ($arrOrders as $arrOrder) {
                    $order_id = $arrOrder['order_id'];
                    static::cancelOrder($order_id, ORDER_CANCEL, true);
                    GcUtils::gfPrintLog('order cancel.(time expire) order_id=' . $order_id);
                }
            }
            $objQuery->commit();
        }
    }

    public function checkDbMyPendignOrder()
    {
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        if ($objCustomer->isLoginSuccess(true)) {
            $customer_id = $objCustomer->getValue('customer_id');
            $objQuery = Application::alias('eccube.query');
            $arrVal = array($customer_id, ORDER_PENDING);
            $objQuery->setOrder('create_date desc');
            $objQuery->begin();
            $arrOrders = $objQuery->select('order_id,create_date', 'dtb_order', 'customer_id = ? and status = ? and del_flg = 0', $arrVal);
            if (!Utils::isBlank($arrOrders)) {
                foreach ($arrOrders as $key => $arrOrder) {
                    $order_id = $arrOrder['order_id'];
                    if ($key == 0) {
                        /* @var $objCartSess CartSession */
                        $objCartSess = Application::alias('eccube.cart_session');
                        $cartKeys = $objCartSess->getKeys();
                        $term = PENDING_ORDER_CANCEL_TIME;
                        if (preg_match("/^[0-9]+$/", $term)) {
                            $target_time = strtotime('-' . $term . ' sec');
                            $create_time = strtotime($arrOrder['create_date']);
                            if (Utils::isBlank($cartKeys) && $target_time < $create_time) {
                                static::rollbackOrder($order_id, ORDER_CANCEL, true);
                                GcUtils::gfPrintLog('order rollback.(my pending) order_id=' . $order_id);
                            } else {
                                static::cancelOrder($order_id, ORDER_CANCEL, true);
                                if ($target_time > $create_time) {
                                    GcUtils::gfPrintLog('order cancel.(my pending and time expire) order_id=' . $order_id);
                                } else {
                                    GcUtils::gfPrintLog('order cancel.(my pending and set cart) order_id=' . $order_id);
                                }
                            }
                        }
                    } else {
                        static::cancelOrder($order_id, ORDER_CANCEL, true);
                        GcUtils::gfPrintLog('order cancel.(my old pending) order_id=' . $order_id);
                    }
                }
            }
            $objQuery->commit();
        }
    }

    public function checkSessionPendingOrder()
    {
        if (isset($_SESSION['order_id']) && !Utils::isBlank($_SESSION['order_id'])) {
            $order_id = $_SESSION['order_id'];
            unset($_SESSION['order_id']);
            $objQuery = Application::alias('eccube.query');
            $objQuery->begin();
            $arrOrder =  static::getOrder($order_id);
            if ($arrOrder['status'] == ORDER_PENDING) {
                /* @var $objCartSess CartSession */
                $objCartSess = Application::alias('eccube.cart_session');
                $cartKeys = $objCartSess->getKeys();
                if (Utils::isBlank($cartKeys)) {
                    static::rollbackOrder($order_id, ORDER_CANCEL, true);
                    GcUtils::gfPrintLog('order rollback.(session pending) order_id=' . $order_id);
                } else {
                    static::cancelOrder($order_id, ORDER_CANCEL, true);
                    GcUtils::gfPrintLog('order rollback.(session pending and set card) order_id=' . $order_id);
                }
            }
            $objQuery->commit();
        }
    }
}
