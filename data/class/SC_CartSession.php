<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
 * カートセッション管理クラス
 *
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_CartSession {
    /** ユニークIDを指定する. */
    var $key_tmp;

    /** カートのセッション変数. */
    var $cartSession;

    /* コンストラクタ */
    function __construct($cartKey = 'cart') {
        if (!isset($_SESSION[$cartKey])) {
            $_SESSION[$cartKey] = array();
        }
        $this->cartSession =& $_SESSION[$cartKey];
    }

    // 商品購入処理中のロック
    function saveCurrentCart($key_tmp, $productTypeId) {
        $this->key_tmp = 'savecart_' . $key_tmp;
        // すでに情報がなければ現状のカート情報を記録しておく
        if (count($_SESSION[$this->key_tmp]) == 0) {
            $_SESSION[$this->key_tmp] = $this->cartSession[$productTypeId];
        }
        // 1世代古いコピー情報は、削除しておく
        foreach ($_SESSION as $k => $val) {
            if ($k != $this->key_tmp && preg_match('/^savecart_/', $k)) {
                unset($this->cartSession[$productTypeId][$k]);
            }
        }
    }

    // 商品購入中の変更があったかをチェックする。
    function getCancelPurchase($productTypeId) {
        $ret = isset($this->cartSession[$productTypeId]['cancel_purchase'])
            ? $this->cartSession[$productTypeId]['cancel_purchase'] : '';
        $this->cartSession[$productTypeId]['cancel_purchase'] = false;
        return $ret;
    }

    // 購入処理中に商品に変更がなかったかを判定
    function checkChangeCart($productTypeId) {
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
            $this->cartSession[$productTypeId][$key]['cancel_purchase'] = true;
        } else {
            $this->cartSession[$productTypeId]['cancel_purchase'] = false;
        }
        return $this->cartSession[$productTypeId]['cancel_purchase'];
    }

    // 次に割り当てるカートのIDを取得する
    function getNextCartID($productTypeId) {
        foreach ($this->cartSession[$productTypeId] as $key => $val) {
            $arrRet[] = $this->cartSession[$productTypeId][$key]['cart_no'];
        }
        return max($arrRet) + 1;
    }

    /**
     * 商品ごとの合計価格
     * XXX 実際には、「商品」ではなく、「カートの明細行(≒商品規格)」のような気がします。
     *
     * @param integer $id
     * @return string 商品ごとの合計価格(税込み)
     * @deprecated SC_CartSession::getCartList() を使用してください
     */
    function getProductTotal($id, $productTypeId) {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if (isset($this->cartSession[$productTypeId][$i]['id'])
                && $this->cartSession[$productTypeId][$i]['id'] == $id
            ) {
                // 税込み合計
                $price = $this->cartSession[$productTypeId][$i]['price'];
                $quantity = $this->cartSession[$productTypeId][$i]['quantity'];
                $incTax = SC_Helper_DB_Ex::sfCalcIncTax($price);
                $total = $incTax * $quantity;
                return $total;
            }
        }
        return 0;
    }

    // 値のセット
    function setProductValue($id, $key, $val, $productTypeId) {
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
    function getMax($productTypeId) {
        $max = 0;
        if (count($this->cartSession[$productTypeId]) > 0) {
            foreach ($this->cartSession[$productTypeId] as $key => $val) {
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
    function getTotalQuantity($productTypeId) {
        $total = 0;
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            $total+= $this->cartSession[$productTypeId][$i]['quantity'];
        }
        return $total;
    }

    // 全商品の合計価格
    function getAllProductsTotal($productTypeId) {
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

            $incTax = SC_Helper_DB_Ex::sfCalcIncTax($price);
            $total+= ($incTax * $quantity);
        }
        return $total;
    }

    // 全商品の合計税金
    function getAllProductsTax($productTypeId) {
        // 税合計
        $total = 0;
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            $price = $this->cartSession[$productTypeId][$i]['price'];
            $quantity = $this->cartSession[$productTypeId][$i]['quantity'];
            $tax = SC_Helper_DB_Ex::sfTax($price);
            $total+= ($tax * $quantity);
        }
        return $total;
    }

    // 全商品の合計ポイント
    function getAllProductsPoint($productTypeId) {
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
                $point = SC_Utils_Ex::sfPrePoint($price, $point_rate);
                $total+= ($point * $quantity);
            }
        }
        return $total;
    }

    // カートへの商品追加
    function addProduct($product_class_id, $quantity) {
        $objProduct = new SC_Product_Ex();
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
            $this->cartSession[$productTypeId][$max+1]['id'] = $product_class_id;
            $this->cartSession[$productTypeId][$max+1]['quantity'] = $quantity;
            $this->cartSession[$productTypeId][$max+1]['cart_no'] = $this->getNextCartID($productTypeId);
        }
    }

    // 前頁のURLを記録しておく
    function setPrevURL($url, $excludePaths = array()) {
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
    function getPrevURL() {
        return isset($_SESSION['prev_url']) ? $_SESSION['prev_url'] : '';
    }

    // キーが一致した商品の削除
    function delProductKey($keyname, $val, $productTypeId) {
        $max = count($this->cartSession[$productTypeId]);
        for ($i = 0; $i < $max; $i++) {
            if ($this->cartSession[$productTypeId][$i][$keyname] == $val) {
                unset($this->cartSession[$productTypeId][$i]);
            }
        }
    }

    function setValue($key, $val, $productTypeId) {
        $this->cartSession[$productTypeId][$key] = $val;
    }

    function getValue($key, $productTypeId) {
        return $this->cartSession[$productTypeId][$key];
    }

    /**
     * セッション中の商品情報データの調整。
     * productsClass項目から、不必要な項目を削除する。
     */
    function adjustSessionProductsClass(&$arrProductsClass) {
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
        foreach (array_keys($arrProductsClass) as $key) {
            if (!isset($arrNecessaryItems[$key])) {
                unset($arrProductsClass[$key]);
            }
        }
    }

    /**
     * 商品種別ごとにカート内商品の一覧を取得する.
     *
     * @param integer $productTypeId 商品種別ID
     * @return array カート内商品一覧の配列
     */
    function getCartList($productTypeId) {
        $objProduct = new SC_Product_Ex();
        $max = $this->getMax($productTypeId);
        $arrRet = array();
        for ($i = 0; $i <= $max; $i++) {
            if (isset($this->cartSession[$productTypeId][$i]['cart_no'])
                && $this->cartSession[$productTypeId][$i]['cart_no'] != '') {

                // 商品情報は常に取得
                // TODO 同一インスタンス内では1回のみ呼ぶようにしたい
                $this->cartSession[$productTypeId][$i]['productsClass']
                    =& $objProduct->getDetailAndProductsClass($this->cartSession[$productTypeId][$i]['id']);

                $price = $this->cartSession[$productTypeId][$i]['productsClass']['price02'];
                $this->cartSession[$productTypeId][$i]['price'] = $price;

                $this->cartSession[$productTypeId][$i]['point_rate']
                    = $this->cartSession[$productTypeId][$i]['productsClass']['point_rate'];

                $quantity = $this->cartSession[$productTypeId][$i]['quantity'];
                $incTax = SC_Helper_DB_Ex::sfCalcIncTax($price);
                $total = $incTax * $quantity;

                $this->cartSession[$productTypeId][$i]['total_inctax'] = $total;

                $arrRet[] = $this->cartSession[$productTypeId][$i];

                // セッション変数のデータ量を抑制するため、一部の商品情報を切り捨てる
                // XXX 上で「常に取得」するのだから、丸ごと切り捨てて良さそうにも感じる。
                $this->adjustSessionProductsClass($this->cartSession[$productTypeId][$i]['productsClass']);
            }
        }
        return $arrRet;
    }

    /**
     * すべてのカートの内容を取得する.
     *
     * @return array すべてのカートの内容
     */
    function getAllCartList() {
        $results = array();
        $cartKeys = $this->getKeys();
        $i = 0;
        foreach ($cartKeys as $key) {
            $cartItems = $this->getCartList($key);
            foreach (array_keys($cartItems) as $itemKey) {
                $cartItem =& $cartItems[$itemKey];
                $results[$key][$i] =& $cartItem;
                $i++;
            }
        }
        return $results;
    }

    // カート内にある商品ＩＤを全て取得する
    /**
     * @deprected getAllProductClassID を使用して下さい
     */
    function getAllProductID($productTypeId) {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] != '') {
                $arrRet[] = $this->cartSession[$productTypeId][$i]['id'][0];
            }
        }
        return $arrRet;
    }

    /**
     * カート内にある商品規格IDを全て取得する.
     *
     * @param integer $productTypeId 商品種別ID
     * @return array 商品規格ID の配列
     */
    function getAllProductClassID($productTypeId) {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] != '') {
                $arrRet[] = $this->cartSession[$productTypeId][$i]['id'];
            }
        }
        return $arrRet;
    }

    /**
     * 商品種別ID を指定して, カート内の商品をすべて削除する.
     *
     * @param integer $productTypeId 商品種別ID
     * @return void
     */
    function delAllProducts($productTypeId) {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            unset($this->cartSession[$productTypeId][$i]);
        }
    }

    // 商品の削除
    function delProduct($cart_no, $productTypeId) {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] == $cart_no) {
                unset($this->cartSession[$productTypeId][$i]);
            }
        }
    }

    // 数量の増加
    function upQuantity($cart_no, $productTypeId) {
        $quantity = $this->getQuantity($cart_no, $productTypeId);
        if (strlen($quantity + 1) <= INT_LEN) {
            $this->setQuantity($quantity + 1, $cart_no, $productTypeId);
        }
    }

    // 数量の減少
    function downQuantity($cart_no, $productTypeId) {
        $quantity = $this->getQuantity($cart_no, $productTypeId);
        if ($quantity > 1) {
            $this->setQuantity($quantity - 1, $cart_no, $productTypeId);
        }
    }

    /**
     * カート番号と商品種別IDを指定して, 数量を取得する.
     *
     * @param integer $cart_no カート番号
     * @param integer $productTypeId 商品種別ID
     * @return integer 該当商品規格の数量
     */
    function getQuantity($cart_no, $productTypeId) {
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
     * @param integer $quantity 設定する数量
     * @param integer $cart_no カート番号
     * @param integer $productTypeId 商品種別ID
     * @retrun void
     */
    function setQuantity($quantity, $cart_no, $productTypeId) {
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
     * @param integer $cart_no カート番号
     * @param integer $productTypeId 商品種別ID
     * @return integer 商品規格ID
     */
    function getProductClassId($cart_no, $productTypeId) {
        for ($i = 0; $i <= $max; $i++) {
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
     * @param string $productTypeId 商品種別ID
     * @return string エラーが発生した場合はエラーメッセージ
     */
    function checkProducts($productTypeId) {
        $objProduct = new SC_Product_Ex();
        $tpl_message = '';

        // カート内の情報を取得
        $arrItems = $this->getCartList($productTypeId);
        foreach ($arrItems as &$arrItem) {
            $product =& $arrItem['productsClass'];
            /*
             * 表示/非表示商品のチェック
             */
            if (SC_Utils_Ex::isBlank($product) || $product['status'] != 1) {
                $this->delProduct($arrItem['cart_no'], $productTypeId);
                $tpl_message .= SC_Utils_Ex::t('SC_CARTSESSION_CHECKPRODUCTS_UNABLE');
            } else {

                /*
                 * 配送業者のチェック
                 */
                $arrDeliv = SC_Helper_Purchase_Ex::getDeliv($productTypeId);
                if (SC_Utils_Ex::isBlank($arrDeliv)) {
                    $tpl_message .= SC_Utils_Ex::t('SC_CARTSESSION_CHECKPRODUCTS_UNDELIVERABLE', array(':product' => $product['name']));
                    $this->delProduct($arrItem['cart_no'], $productTypeId);
                }

                /*
                 * 販売制限数, 在庫数のチェック
                 */
                $limit = $objProduct->getBuyLimit($product);
                if (!is_null($limit) && $arrItem['quantity'] > $limit) {
                    if ($limit > 0) {
                        $this->setProductValue($arrItem['id'], 'quantity', $limit, $productTypeId);
                        $total_inctax = SC_Helper_DB_Ex::sfCalcIncTax($arrItem['price']) * $limit;
                        $this->setProductValue($arrItem['id'], 'total_inctax', $total_inctax, $productTypeId);
                        $tpl_message .= SC_Utils_Ex::t('SC_CARTSESSION_CHECKPRODUCTS_LIMIT', array(':product' => $product['name'], ':limit' => $limit));
                    } else {
                        $this->delProduct($arrItem['cart_no'], $productTypeId);
                        $tpl_message .= SC_Utils_Ex::t('SC_CARTSESSION_CHECKPRODUCTS_SOLDOUT', array(':product' => $product['name']));
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
     * @param integer $productTypeId 商品種別ID
     * @return boolean 送料無料の場合 true
     */
    function isDelivFree($productTypeId) {
        $objDb = new SC_Helper_DB_Ex();

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
        $arrInfo = $objDb->sfGetBasisData();
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
     * @param integer $productTypeId 商品種別ID
     * @param SC_Customer $objCustomer ログイン中の SC_Customer インスタンス
     * @param integer $use_point 今回使用ポイント
     * @param integer|array $deliv_pref 配送先都道府県ID.
                                        複数に配送する場合は都道府県IDの配列
     * @param integer $charge 手数料
     * @param integer $discount 値引き
     * @param integer $deliv_id 配送業者ID
     * @return array カートの計算結果の配列
     */
    function calculate($productTypeId, &$objCustomer, $use_point = 0,
        $deliv_pref = '', $charge = 0, $discount = 0, $deliv_id = 0
    ) {

        $total_point = $this->getAllProductsPoint($productTypeId);
        $results['tax'] = $this->getAllProductsTax($productTypeId);
        $results['subtotal'] = $this->getAllProductsTotal($productTypeId);
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
            && !SC_Utils_Ex::isBlank($deliv_pref)
            && !SC_Utils_Ex::isBlank($deliv_id)) {
            $results['deliv_fee'] += $this->sfGetDelivFee($deliv_pref, $deliv_id);
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
            $results['add_point'] = SC_Helper_DB_Ex::sfGetAddPoint($total_point, $use_point);
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
    function getKeys() {
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
     * @param integer $key 商品種別ID
     * @return void
     */
    function registerKey($key) {
        $_SESSION['cartKey'] = $key;
    }

    /**
     * カートに設定された現在のキー(商品種別ID)を削除する.
     *
     * @return void
     */
    function unsetKey() {
        unset($_SESSION['cartKey']);
    }

    /**
     * カートに設定された現在のキー(商品種別ID)を取得する.
     *
     * @return integer 商品種別ID
     */
    function getKey() {
        return $_SESSION['cartKey'];
    }

    /**
     * 複数商品種別かどうか.
     *
     * @return boolean カートが複数商品種別の場合 true
     */
    function isMultiple() {
        return count($this->getKeys()) > 1;
    }

    /**
     * 引数の商品種別の商品がカートに含まれるかどうか.
     *
     * @param integer $product_type_id 商品種別ID
     * @return boolean 指定の商品種別がカートに含まれる場合 true
     */
    function hasProductType($product_type_id) {
        return in_array($product_type_id, $this->getKeys());
    }

    /**
     * 都道府県から配送料金を取得する.
     *
     * @param integer|array $pref_id 都道府県ID 又は都道府県IDの配列
     * @param integer $deliv_id 配送業者ID
     * @return string 指定の都道府県, 配送業者の配送料金
     */
    function sfGetDelivFee($pref_id, $deliv_id = 0) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        if (!is_array($pref_id)) {
            $pref_id = array($pref_id);
        }
        $sql = <<< __EOS__
            SELECT T1.fee AS fee
            FROM dtb_delivfee T1
                JOIN dtb_deliv T2
                    ON T1.deliv_id = T2.deliv_id
            WHERE T1.pref = ?
                AND T1.deliv_id = ?
                AND T2.del_flg = 0
__EOS__;
        $result = 0;
        foreach ($pref_id as $pref) {
            $result += $objQuery->getOne($sql, array($pref, $deliv_id));
        }
        return $result;
    }

}
