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

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\Customer;
use Eccube\Framework\Product;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\TaxRuleHelper;
use Eccube\Framework\Helper\DeliveryHelper;
use Eccube\Framework\Util\Utils;

/**
 * カートセッション管理クラス
 *
 * @author LOCKON CO.,LTD.
 */
class CartSession
{
    /** ユニークIDを指定する. */
    public $key_tmp;

    /** カートのセッション変数. */
    public $cartSession;

    /* コンストラクタ */
    public function __construct($cartKey = 'cart')
    {
        if (!isset($_SESSION[$cartKey])) {
            $_SESSION[$cartKey] = array();
        }
        $this->cartSession =& $_SESSION[$cartKey];
    }

    // 商品購入処理中のロック

    /**
     * @param string $key_tmp
     * @param integer $productTypeId
     */
    public function saveCurrentCart($key_tmp, $productTypeId)
    {
        $this->key_tmp = 'savecart_' . $key_tmp;
        // すでに情報がなければ現状のカート情報を記録しておく
        if (count($_SESSION[$this->key_tmp]) == 0) {
            $_SESSION[$this->key_tmp] = $this->cartSession[$productTypeId];
        }
        // 1世代古いコピー情報は、削除しておく
        foreach ($_SESSION as $key => $value) {
            if ($key != $this->key_tmp && preg_match('/^savecart_/', $key)) {
                unset($_SESSION[$key]);
            }
        }
    }

    // 商品購入中の変更があったかをチェックする。
    public function getCancelPurchase($productTypeId)
    {
        $ret = isset($this->cartSession[$productTypeId]['cancel_purchase'])
            ? $this->cartSession[$productTypeId]['cancel_purchase'] : '';
        $this->cartSession[$productTypeId]['cancel_purchase'] = false;

        return $ret;
    }

    // 購入処理中に商品に変更がなかったかを判定

    /**
     * @param integer $productTypeId
     */
    public function checkChangeCart($productTypeId)
    {
        $change = false;
        $max = $this->getMax($productTypeId);
        for ($i = 1; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['quantity']
                != $_SESSION[$this->key_tmp][$i]['quantity']) {
                $change = true;
                break;
            }
            if ($this->cartSession[$productTypeId][$i]['id']
                != $_SESSION[$this->key_tmp][$i]['id']) {
                $change = true;
                break;
            }
        }
        if ($change) {
            // 一時カートのクリア
            unset($_SESSION[$this->key_tmp]);
            $this->cartSession[$productTypeId]['cancel_purchase'] = true;
        } else {
            $this->cartSession[$productTypeId]['cancel_purchase'] = false;
        }

        return $this->cartSession[$productTypeId]['cancel_purchase'];
    }

    // 次に割り当てるカートのIDを取得する
    public function getNextCartID($productTypeId)
    {
        $count = array();
        foreach ($this->cartSession[$productTypeId] as $key => $value) {
            $count[] = $this->cartSession[$productTypeId][$key]['cart_no'];
        }

        return max($count) + 1;
    }

    // 値のセット

    /**
     * @param string $key
     * @param string $productTypeId
     */
    public function setProductValue($id, $key, $val, $productTypeId)
    {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if (isset($this->cartSession[$productTypeId][$i]['id'])
                && $this->cartSession[$productTypeId][$i]['id'] == $id
            ) {
                $this->cartSession[$productTypeId][$i][$key] = $val;
            }
        }
    }

    // カート内商品の最大要素番号を取得する。
    public function getMax($productTypeId)
    {
        $max = 0;
        if (count($this->cartSession[$productTypeId]) > 0) {
            foreach ($this->cartSession[$productTypeId] as $key => $value) {
                if (is_numeric($key)) {
                    if ($max < $key) {
                        $max = $key;
                    }
                }
            }
        }

        return $max;
    }

    // カート内商品数量の合計
    public function getTotalQuantity($productTypeId)
    {
        $total = 0;
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            $total+= $this->cartSession[$productTypeId][$i]['quantity'];
        }

        return $total;
    }

    // 全商品の合計価格
    public function getAllProductsTotal($productTypeId, $pref_id = 0, $country_id = 0)
    {
        // 税込み合計
        $total = 0;
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if (!isset($this->cartSession[$productTypeId][$i]['price'])) {
                $this->cartSession[$productTypeId][$i]['price'] = '';
            }

            $price = $this->cartSession[$productTypeId][$i]['price'];

            if (!isset($this->cartSession[$productTypeId][$i]['quantity'])) {
                $this->cartSession[$productTypeId][$i]['quantity'] = '';
            }
            $quantity = $this->cartSession[$productTypeId][$i]['quantity'];
            $incTax = TaxRuleHelper::sfCalcIncTax($price,
                $this->cartSession[$productTypeId][$i]['productsClass']['product_id'],
                $this->cartSession[$productTypeId][$i]['productsClass']['product_class_id'],
                $pref_id, $country_id);

            $total+= ($incTax * $quantity);
        }

        return $total;
    }

    // 全商品の合計税金
    public function getAllProductsTax($productTypeId, $pref_id = 0, $country_id = 0)
    {
        // 税合計
        $total = 0;
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            $price = $this->cartSession[$productTypeId][$i]['price'];
            $quantity = $this->cartSession[$productTypeId][$i]['quantity'];
            $tax = TaxRuleHelper::sfTax($price,
                $this->cartSession[$productTypeId][$i]['productsClass']['product_id'],
                $this->cartSession[$productTypeId][$i]['productsClass']['product_class_id'],
                $pref_id, $country_id);

            $total+= ($tax * $quantity);
        }

        return $total;
    }

    // 全商品の合計ポイント

    /**
     * @param integer $productTypeId
     */
    public function getAllProductsPoint($productTypeId)
    {
        // ポイント合計
        $total = 0;
        if (USE_POINT !== false) {
            $max = $this->getMax($productTypeId);
            for ($i = 0; $i <= $max; $i++) {
                $price = $this->cartSession[$productTypeId][$i]['price'];
                $quantity = $this->cartSession[$productTypeId][$i]['quantity'];

                if (!isset($this->cartSession[$productTypeId][$i]['point_rate'])) {
                    $this->cartSession[$productTypeId][$i]['point_rate'] = '';
                }
                $point_rate = $this->cartSession[$productTypeId][$i]['point_rate'];

                if (!isset($this->cartSession[$productTypeId][$i]['id'][0])) {
                    $this->cartSession[$productTypeId][$i]['id'][0] = '';
                }
                $point = Utils::sfPrePoint($price, $point_rate);
                $total+= ($point * $quantity);
            }
        }

        return $total;
    }

    // カートへの商品追加
    public function addProduct($product_class_id, $quantity)
    {
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        $arrProduct = $objProduct->getProductsClass($product_class_id);
        $productTypeId = $arrProduct['product_type_id'];
        $find = false;
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['id'] == $product_class_id) {
                $val = $this->cartSession[$productTypeId][$i]['quantity'] + $quantity;
                if (strlen($val) <= INT_LEN) {
                    $this->cartSession[$productTypeId][$i]['quantity'] += $quantity;
                }
                $find = true;
            }
        }
        if (!$find) {
            $this->cartSession[$productTypeId][] = array(
                'id' => $product_class_id,
                'quantity' => $quantity,
                'cart_no' => $this->getNextCartID($productTypeId),
            );
        }
    }

    // 前頁のURLを記録しておく
    public function setPrevURL($url, $excludePaths = array())
    {
        // 前頁として記録しないページを指定する。
        $arrExclude = array(
            '/shopping/'
        );
        $arrExclude = array_merge($arrExclude, $excludePaths);
        $exclude = false;
        // ページチェックを行う。
        foreach ($arrExclude as $val) {
            if (preg_match('|' . preg_quote($val) . '|', $url)) {
                $exclude = true;
                break;
            }
        }
        // 除外ページでない場合は、前頁として記録する。
        if (!$exclude) {
            $_SESSION['prev_url'] = $url;
        }
    }

    // 前頁のURLを取得する
    public function getPrevURL()
    {
        return isset($_SESSION['prev_url']) ? $_SESSION['prev_url'] : '';
    }

    // キーが一致した商品の削除
    public function delProductKey($keyname, $val, $productTypeId)
    {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i < $max; $i++) {
            if ($this->cartSession[$productTypeId][$i][$keyname] == $val) {
                unset($this->cartSession[$productTypeId][$i]);
            }
        }
    }

    public function setValue($key, $val, $productTypeId)
    {
        $this->cartSession[$productTypeId][$key] = $val;
    }

    public function getValue($key, $productTypeId)
    {
        return $this->cartSession[$productTypeId][$key];
    }

    /**
     * セッション中の商品情報データの調整。
     * productsClass項目から、不必要な項目を削除する。
     */
    public function adjustSessionProductsClass(&$arrProductsClass)
    {
        $arrNecessaryItems = array(
            'product_id'          => true,
            'product_class_id'    => true,
            'name'                => true,
            'price02'             => true,
            'point_rate'          => true,
            'main_list_image'     => true,
            'main_image'          => true,
            'product_code'        => true,
            'stock'               => true,
            'stock_unlimited'     => true,
            'sale_limit'          => true,
            'class_name1'         => true,
            'classcategory_name1' => true,
            'class_name2'         => true,
            'classcategory_name2' => true,
        );

        // 必要な項目以外を削除。
        foreach ($arrProductsClass as $key => $value) {
            if (!isset($arrNecessaryItems[$key])) {
                unset($arrProductsClass[$key]);
            }
        }
    }

    /**
     * getCartList用にcartSession情報をセットする
     *
     * @param  integer $productTypeId 商品種別ID
     * @param  integer $key
     * @return void
     *
     * MEMO: せっかく一回だけ読み込みにされてますが、税率対応の関係でちょっと保留
     */
    public function setCartSession4getCartList($productTypeId, $key)
    {
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');

        $this->cartSession[$productTypeId][$key]['productsClass']
            =& $objProduct->getDetailAndProductsClass($this->cartSession[$productTypeId][$key]['id']);

        $price = $this->cartSession[$productTypeId][$key]['productsClass']['price02'];
        $this->cartSession[$productTypeId][$key]['price'] = $price;

        $this->cartSession[$productTypeId][$key]['point_rate']
            = $this->cartSession[$productTypeId][$key]['productsClass']['point_rate'];

        $quantity = $this->cartSession[$productTypeId][$key]['quantity'];
        $incTax = TaxRuleHelper::sfCalcIncTax($price,
            $this->cartSession[$productTypeId][$key]['productsClass']['product_id'],
            $this->cartSession[$productTypeId][$key]['id'][0]);

        $total = $incTax * $quantity;

        $this->cartSession[$productTypeId][$key]['price_inctax'] = $incTax;
        $this->cartSession[$productTypeId][$key]['total_inctax'] = $total;
    }

    /**
     * 商品種別ごとにカート内商品の一覧を取得する.
     *
     * @param  integer $productTypeId 商品種別ID
     * @param  integer $pref_id       税金計算用注文者都道府県ID
     * @param  integer $country_id    税金計算用注文者国ID
     * @return array   カート内商品一覧の配列
     */
    public function getCartList($productTypeId, $pref_id = 0, $country_id = 0)
    {
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        $max = $this->getMax($productTypeId);
        $arrRet = array();
/*

        $const_name = '_CALLED_CARTSESSION_GETCARTLIST_' . $productTypeId;
        if (defined($const_name)) {
            $is_first = true;
        } else {
            define($const_name, true);
            $is_first = false;
        }

*/
        for ($i = 0; $i <= $max; $i++) {
            if (isset($this->cartSession[$productTypeId][$i]['cart_no'])
                && $this->cartSession[$productTypeId][$i]['cart_no'] != '') {

                // 商品情報は常に取得
                // TODO: 同一インスタンス内では1回のみ呼ぶようにしたい
                // TODO: ここの商品の合計処理は getAllProductsTotalや getAllProductsTaxとで類似重複なので統一出来そう
/*
                // 同一セッション内では初回のみDB参照するようにしている
                if (!$is_first) {
                    $this->setCartSession4getCartList($productTypeId, $i);
                }
*/

                $this->cartSession[$productTypeId][$i]['productsClass']
                    =& $objProduct->getDetailAndProductsClass($this->cartSession[$productTypeId][$i]['id']);

                $price = $this->cartSession[$productTypeId][$i]['productsClass']['price02'];
                $this->cartSession[$productTypeId][$i]['price'] = $price;

                $this->cartSession[$productTypeId][$i]['point_rate']
                    = $this->cartSession[$productTypeId][$i]['productsClass']['point_rate'];

                $quantity = $this->cartSession[$productTypeId][$i]['quantity'];

                $arrTaxRule = TaxRuleHelper::getTaxRule(
                                    $this->cartSession[$productTypeId][$i]['productsClass']['product_id'],
                                    $this->cartSession[$productTypeId][$i]['productsClass']['product_class_id'],
                                    $pref_id,
                                    $country_id);
                $incTax = $price + TaxRuleHelper::calcTax($price, $arrTaxRule['tax_rate'], $arrTaxRule['tax_rule'], $arrTaxRule['tax_adjust']);

                $total = $incTax * $quantity;
                $this->cartSession[$productTypeId][$i]['price_inctax'] = $incTax;
                $this->cartSession[$productTypeId][$i]['total_inctax'] = $total;
                $this->cartSession[$productTypeId][$i]['tax_rate'] = $arrTaxRule['tax_rate'];
                $this->cartSession[$productTypeId][$i]['tax_rule'] = $arrTaxRule['tax_rule'];
                $this->cartSession[$productTypeId][$i]['tax_adjust'] = $arrTaxRule['tax_adjust'];

                $arrRet[] = $this->cartSession[$productTypeId][$i];

                // セッション変数のデータ量を抑制するため、一部の商品情報を切り捨てる
                // XXX 上で「常に取得」するのだから、丸ごと切り捨てて良さそうにも感じる。
                $this->adjustSessionProductsClass($this->cartSession[$productTypeId][$i]['productsClass']);
            }
        }

        return $arrRet;
    }

    /**
     * 全てのカートの内容を取得する.
     *
     * @return array 全てのカートの内容
     */
    public function getAllCartList()
    {
        $results = array();
        $cartKeys = $this->getKeys();
        $i = 0;
        foreach ($cartKeys as $key) {
            $cartItems = $this->getCartList($key);
            foreach ($cartItems as $itemKey => $itemValue) {
                $cartItem =& $cartItems[$itemKey];
                $results[$key][$i] =& $cartItem;
                $i++;
            }
        }

        return $results;
    }

    /**
     * カート内にある商品規格IDを全て取得する.
     *
     * @param  integer $productTypeId 商品種別ID
     * @return array   商品規格ID の配列
     */
    public function getAllProductClassID($productTypeId)
    {
        $max = $this->getMax($productTypeId);
        $productClassIDs = array();
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] != '') {
                $productClassIDs[] = $this->cartSession[$productTypeId][$i]['id'];
            }
        }

        return $productClassIDs;
    }

    /**
     * 商品種別ID を指定して, カート内の商品を全て削除する.
     *
     * @param  integer $productTypeId 商品種別ID
     * @return void
     */
    public function delAllProducts($productTypeId)
    {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            unset($this->cartSession[$productTypeId][$i]);
        }
    }

    // 商品の削除
    public function delProduct($cart_no, $productTypeId)
    {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] == $cart_no) {
                unset($this->cartSession[$productTypeId][$i]);
            }
        }
    }

    // 数量の増加
    public function upQuantity($cart_no, $productTypeId)
    {
        $quantity = $this->getQuantity($cart_no, $productTypeId);
        if (strlen($quantity + 1) <= INT_LEN) {
            $this->setQuantity($quantity + 1, $cart_no, $productTypeId);
        }
    }

    // 数量の減少
    public function downQuantity($cart_no, $productTypeId)
    {
        $quantity = $this->getQuantity($cart_no, $productTypeId);
        if ($quantity > 1) {
            $this->setQuantity($quantity - 1, $cart_no, $productTypeId);
        }
    }

    /**
     * カート番号と商品種別IDを指定して, 数量を取得する.
     *
     * @param  integer $cart_no       カート番号
     * @param  integer $productTypeId 商品種別ID
     * @return integer 該当商品規格の数量
     */
    public function getQuantity($cart_no, $productTypeId)
    {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] == $cart_no) {
                return $this->cartSession[$productTypeId][$i]['quantity'];
            }
        }
    }

    /**
     * カート番号と商品種別IDを指定して, 数量を設定する.
     *
     * @param integer $quantity      設定する数量
     * @param integer $cart_no       カート番号
     * @param integer $productTypeId 商品種別ID
     * @retrun void
     */
    public function setQuantity($quantity, $cart_no, $productTypeId)
    {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] == $cart_no) {
                $this->cartSession[$productTypeId][$i]['quantity'] = $quantity;
            }
        }
    }

    /**
     * カート番号と商品種別IDを指定して, 商品規格IDを取得する.
     *
     * @param  integer $cart_no       カート番号
     * @param  integer $productTypeId 商品種別ID
     * @return integer 商品規格ID
     */
    public function getProductClassId($cart_no, $productTypeId)
    {
        for ($i = 0; $i < count($this->cartSession[$productTypeId]); $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] == $cart_no) {
                return $this->cartSession[$productTypeId][$i]['id'];
            }
        }
    }

    /**
     * カート内の商品の妥当性をチェックする.
     *
     * エラーが発生した場合は, 商品をカート内から削除又は数量を調整し,
     * エラーメッセージを返す.
     *
     * 1. 商品種別に関連づけられた配送業者の存在チェック
     * 2. 削除/非表示商品のチェック
     * 3. 販売制限数のチェック
     * 4. 在庫数チェック
     *
     * @param  string $productTypeId 商品種別ID
     * @return string エラーが発生した場合はエラーメッセージ
     */
    public function checkProducts($productTypeId)
    {
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        /* @var $objDelivery DeliveryHelper */
        $objDelivery = Application::alias('eccube.helper.delivery');
        $arrDeliv = $objDelivery->getList($productTypeId);
        $tpl_message = '';

        // カート内の情報を取得
        $arrItems = $this->getCartList($productTypeId);
        foreach ($arrItems as &$arrItem) {
            $product =& $arrItem['productsClass'];
            /*
             * 表示/非表示商品のチェック
             */
            if (Utils::isBlank($product) || $product['status'] != 1) {
                $this->delProduct($arrItem['cart_no'], $productTypeId);
                $tpl_message .= "※ 現時点で販売していない商品が含まれておりました。該当商品をカートから削除しました。\n";
            } else {
                /*
                 * 配送業者のチェック
                 */
                if (Utils::isBlank($arrDeliv)) {
                    $tpl_message .= '※「' . $product['name'] . '」はまだ配送の準備ができておりません。';
                    $tpl_message .= '恐れ入りますがお問い合わせページよりお問い合わせください。' . "\n";
                    $this->delProduct($arrItem['cart_no'], $productTypeId);
                }

                /*
                 * 販売制限数, 在庫数のチェック
                 */
                $limit = $objProduct->getBuyLimit($product);
                if (!is_null($limit) && $arrItem['quantity'] > $limit) {
                    if ($limit > 0) {
                        $this->setProductValue($arrItem['id'], 'quantity', $limit, $productTypeId);
                        $total_inctax = $limit * TaxRuleHelper::sfCalcIncTax($arrItem['price'],
                            $product['product_id'],
                            $arrItem['id'][0]);
                        $this->setProductValue($arrItem['id'], 'total_inctax', $total_inctax, $productTypeId);
                        $tpl_message .= '※「' . $product['name'] . '」は販売制限(または在庫が不足)しております。';
                        $tpl_message .= "一度に数量{$limit}を超える購入はできません。\n";
                    } else {
                        $this->delProduct($arrItem['cart_no'], $productTypeId);
                        $tpl_message .= '※「' . $product['name'] . "」は売り切れました。\n";
                        continue;
                    }
                }
            }
        }

        return $tpl_message;
    }

    /**
     * 送料無料条件を満たすかどうかチェックする
     *
     * @param  integer $productTypeId 商品種別ID
     * @return boolean 送料無料の場合 true
     */
    public function isDelivFree($productTypeId)
    {
        $subtotal = $this->getAllProductsTotal($productTypeId);

        // 送料無料の購入数が設定されている場合
        if (DELIV_FREE_AMOUNT > 0) {
            // 商品の合計数量
            $total_quantity = $this->getTotalQuantity($productTypeId);

            if ($total_quantity >= DELIV_FREE_AMOUNT) {
                return true;
            }
        }

        // 送料無料条件が設定されている場合
        $arrInfo = Application::alias('eccube.helper.db')->getBasisData();
        if ($arrInfo['free_rule'] > 0) {
            // 小計が送料無料条件以上の場合
            if ($subtotal >= $arrInfo['free_rule']) {
                return true;
            }
        }

        return false;
    }

    /**
     * カートの内容を計算する.
     *
     * カートの内容を計算し, 下記のキーを保持する連想配列を返す.
     *
     * - tax: 税額
     * - subtotal: カート内商品の小計
     * - deliv_fee: カート内商品の合計送料
     * - total: 合計金額
     * - payment_total: お支払い合計
     * - add_point: 加算ポイント
     *
     * @param integer       $productTypeId 商品種別ID
     * @param Customer   $objCustomer   ログイン中の Customer インスタンス
     * @param integer       $use_point     今回使用ポイント
     * @param integer|array $deliv_pref    配送先都道府県ID.
                                        複数に配送する場合は都道府県IDの配列
     * @param  integer $charge           手数料
     * @param  integer $discount         値引き
     * @param  integer $deliv_id         配送業者ID
     * @param  integer $order_pref       注文者の都道府県ID
     * @param  integer $order_country_id 注文者の国
     * @return array   カートの計算結果の配列
     */
    public function calculate($productTypeId, Customer &$objCustomer, $use_point = 0,
        $deliv_pref = '', $charge = 0, $discount = 0, $deliv_id = 0,
        $order_pref = 0, $order_country_id = 0
    ) {

        $results = array();
        $total_point = $this->getAllProductsPoint($productTypeId);
        // MEMO: 税金計算は注文者の住所基準
        $results['tax'] = $this->getAllProductsTax($productTypeId, $order_pref, $order_country_id);
        $results['subtotal'] = $this->getAllProductsTotal($productTypeId, $order_pref, $order_country_id);
        $results['deliv_fee'] = 0;

        // 商品ごとの送料を加算
        if (OPTION_PRODUCT_DELIV_FEE == 1) {
            $cartItems = $this->getCartList($productTypeId);
            foreach ($cartItems as $arrItem) {
                $results['deliv_fee'] += $arrItem['productsClass']['deliv_fee'] * $arrItem['quantity'];
            }
        }

        // 配送業者の送料を加算
        if (OPTION_DELIV_FEE == 1
            && !Utils::isBlank($deliv_pref)
            && !Utils::isBlank($deliv_id)) {
            $results['deliv_fee'] += Application::alias('eccube.helper.delivery')->getDelivFee($deliv_pref, $deliv_id);
        }

        // 送料無料チェック
        if ($this->isDelivFree($productTypeId)) {
            $results['deliv_fee'] = 0;
        }

        // 合計を計算
        $results['total'] = $results['subtotal'];
        $results['total'] += $results['deliv_fee'];
        $results['total'] += $charge;
        $results['total'] -= $discount;

        // お支払い合計
        $results['payment_total'] = $results['total'] - $use_point * POINT_VALUE;

        // 加算ポイントの計算
        if (USE_POINT !== false) {
            $results['add_point'] = Application::alias('eccube.helper.db')->getAddPoint($total_point, $use_point);
            if ($objCustomer != '') {
                // 誕生日月であった場合
                if ($objCustomer->isBirthMonth()) {
                    $results['birth_point'] = BIRTH_MONTH_POINT;
                    $results['add_point'] += $results['birth_point'];
                }
            }
            if ($results['add_point'] < 0) {
                $results['add_point'] = 0;
            }
        }

        return $results;
    }

    /**
     * カートが保持するキー(商品種別ID)を配列で返す.
     *
     * @return array 商品種別IDの配列
     */
    public function getKeys()
    {
        $keys = array_keys($this->cartSession);
        // 数量が 0 の商品種別は削除する
        foreach ($keys as $key) {
            $quantity = $this->getTotalQuantity($key);
            if ($quantity < 1) {
                unset($this->cartSession[$key]);
            }
        }

        return array_keys($this->cartSession);
    }

    /**
     * カートに設定された現在のキー(商品種別ID)を登録する.
     *
     * @param  integer $key 商品種別ID
     * @return void
     */
    public function registerKey($key)
    {
        $_SESSION['cartKey'] = $key;
    }

    /**
     * カートに設定された現在のキー(商品種別ID)を削除する.
     *
     * @return void
     */
    public function unsetKey()
    {
        unset($_SESSION['cartKey']);
    }

    /**
     * カートに設定された現在のキー(商品種別ID)を取得する.
     *
     * @return integer 商品種別ID
     */
    public function getKey()
    {
        return $_SESSION['cartKey'];
    }

    /**
     * 複数商品種別かどうか.
     *
     * @return boolean カートが複数商品種別の場合 true
     */
    public function isMultiple()
    {
        return count($this->getKeys()) > 1;
    }

    /**
     * 引数の商品種別の商品がカートに含まれるかどうか.
     *
     * @param  integer $product_type_id 商品種別ID
     * @return boolean 指定の商品種別がカートに含まれる場合 true
     */
    public function hasProductType($product_type_id)
    {
        return in_array($product_type_id, $this->getKeys());
    }
}
