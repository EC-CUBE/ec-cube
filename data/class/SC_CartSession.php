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

/* カートセッション管理クラス */
class SC_CartSession {
    /** ユニークIDを指定する. */
    var $key_tmp;

    /** カートのセッション変数. */
    var $cartSession;

    /* コンストラクタ */
    function SC_CartSession($cartKey = "cart") {
        $this->cartSession =& $_SESSION[$cartKey];
    }

    // 商品購入処理中のロック
    function saveCurrentCart($key_tmp, $productTypeId) {
        $this->key_tmp = "savecart_" . $key_tmp;
        // すでに情報がなければ現状のカート情報を記録しておく
        if(count($_SESSION[$this->key_tmp]) == 0) {
            $_SESSION[$this->key_tmp] = $this->cartSession[$productTypeId];
        }
        // 1世代古いコピー情報は、削除しておく
        foreach($_SESSION as $k => $val) {
            if($k != $this->key_tmp && preg_match("/^savecart_/", $k)) {
                unset($this->cartSession[$productTypeId][$k]);
            }
        }
        $this->registerKey($productTypeId);
    }

    // 商品購入中の変更があったかをチェックする。
    function getCancelPurchase($productTypeId) {
        $ret = isset($this->cartSession[$productTypeId]['cancel_purchase'])
            ? $this->cartSession[$productTypeId]['cancel_purchase'] : "";
        $this->cartSession[$productTypeId]['cancel_purchase'] = false;
        return $ret;
    }

    // 購入処理中に商品に変更がなかったかを判定
    function checkChangeCart($productTypeId) {
        $change = false;
        $max = $this->getMax($productTypeId);
        for($i = 1; $i <= $max; $i++) {
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
        foreach($this->cartSession[$productTypeId] as $key => $val){
            $arrRet[] = $this->cartSession[$productTypeId][$key]['cart_no'];
        }
        return (max($arrRet) + 1);
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
        for($i = 0; $i <= $max; $i++) {
            if(isset($this->cartSession[$productTypeId][$i]['id'])
               && $this->cartSession[$productTypeId][$i]['id'] == $id) {

                // 税込み合計
                $price = $this->cartSession[$productTypeId][$i]['price'];
                $quantity = $this->cartSession[$productTypeId][$i]['quantity'];
                $pre_tax = SC_Helper_DB_Ex::sfPreTax($price);
                $total = $pre_tax * $quantity;
                return $total;
            }
        }
        return 0;
    }

    // 値のセット
    function setProductValue($id, $key, $val, $productTypeId) {
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            if(isset($this->cartSession[$productTypeId][$i]['id'])
               && $this->cartSession[$productTypeId][$i]['id'] == $id) {
                $this->cartSession[$productTypeId][$i][$key] = $val;
            }
        }
    }

    // カート内商品の最大要素番号を取得する。
    function getMax($productTypeId) {
        $max = 0;
        if (count($this->cartSession[$productTypeId]) > 0){
            foreach($this->cartSession[$productTypeId] as $key => $val) {
                if (is_numeric($key)) {
                    if($max < $key) {
                        $max = $key;
                    }
                }
            }
        }
        return ($max);
    }

    // カート内商品数の合計
    function getTotalQuantity($productTypeId) {
        $total = 0;
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            $total+= $this->cartSession[$productTypeId][$i]['quantity'];
        }
        return $total;
    }


    // 全商品の合計価格
    function getAllProductsTotal($productTypeId) {
        // 税込み合計
        $total = 0;
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {

            if (!isset($this->cartSession[$productTypeId][$i]['price'])) {
                $this->cartSession[$productTypeId][$i]['price'] = "";
            }

            $price = $this->cartSession[$productTypeId][$i]['price'];

            if (!isset($this->cartSession[$productTypeId][$i]['quantity'])) {
                $this->cartSession[$productTypeId][$i]['quantity'] = "";
            }
            $quantity = $this->cartSession[$productTypeId][$i]['quantity'];

            $pre_tax = SC_Helper_DB_Ex::sfPreTax($price);
            $total+= ($pre_tax * $quantity);
        }
        return $total;
    }

    // 全商品の合計税金
    function getAllProductsTax($productTypeId) {
        // 税合計
        $total = 0;
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
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
            for($i = 0; $i <= $max; $i++) {
                $price = $this->cartSession[$productTypeId][$i]['price'];
                $quantity = $this->cartSession[$productTypeId][$i]['quantity'];

                if (!isset($this->cartSession[$productTypeId][$i]['point_rate'])) {
                    $this->cartSession[$productTypeId][$i]['point_rate'] = "";
                }
                $point_rate = $this->cartSession[$productTypeId][$i]['point_rate'];

                if (!isset($this->cartSession[$productTypeId][$i]['id'][0])) {
                    $this->cartSession[$productTypeId][$i]['id'][0] = "";
                }
                $id = $this->cartSession[$productTypeId][$i]['id'][0];
                $point = SC_Utils_Ex::sfPrePoint($price, $point_rate, POINT_RULE, $id);
                $total+= ($point * $quantity);
            }
        }
        return $total;
    }

    // カートへの商品追加
    function addProduct($id, $quantity, $productTypeId) {
        $find = false;
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {

            if($this->cartSession[$productTypeId][$i]['id'] == $id) {
                $val = $this->cartSession[$productTypeId][$i]['quantity'] + $quantity;
                if(strlen($val) <= INT_LEN) {
                    $this->cartSession[$productTypeId][$i]['quantity'] += $quantity;
                }
                $find = true;
            }
        }
        if(!$find) {
            $this->cartSession[$productTypeId][$max+1]['id'] = $id;
            $this->cartSession[$productTypeId][$max+1]['quantity'] = $quantity;
            $this->cartSession[$productTypeId][$max+1]['cart_no'] = $this->getNextCartID($productTypeId);
        }
    }

    // 前頁のURLを記録しておく
    function setPrevURL($url) {
        // 前頁として記録しないページを指定する。
        $arrExclude = array(
            "/shopping/"
        );
        $exclude = false;
        // ページチェックを行う。
        foreach($arrExclude as $val) {
            if(preg_match("/" . preg_quote($val) . "/", $url)) {
                $exclude = true;
                break;
            }
        }
        // 除外ページでない場合は、前頁として記録する。
        if(!$exclude) {
            $_SESSION['prev_url'] = $url;
        }
    }

    // 前頁のURLを取得する
    function getPrevURL() {
        return isset($_SESSION['prev_url']) ? $_SESSION['prev_url'] : "";
    }

    // キーが一致した商品の削除
    function delProductKey($keyname, $val, $productTypeId) {
        $max = count($this->cartSession[$productTypeId]);
        for($i = 0; $i < $max; $i++) {
            if($this->cartSession[$productTypeId][$i][$keyname] == $val) {
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

    function getCartList($productTypeId) {
        $objProduct = new SC_Product();
        $max = $this->getMax($productTypeId);
        $arrRet = array();
        for($i = 0; $i <= $max; $i++) {
            if(isset($this->cartSession[$productTypeId][$i]['cart_no'])
               && $this->cartSession[$productTypeId][$i]['cart_no'] != "") {

                if (SC_Utils_Ex::isBlank($this->cartSession[$productTypeId][$i]['productsClass'])) {
                    $this->cartSession[$productTypeId][$i]['productsClass'] =&
                            $objProduct->getDetailAndProductsClass(
                                    $this->cartSession[$productTypeId][$i]['id']);
                }

                $price = $this->cartSession[$productTypeId][$i]['productsClass']['price02'];
                $this->cartSession[$productTypeId][$i]['price'] = $price;

                $this->cartSession[$productTypeId][$i]['point_rate'] =
                        $this->cartSession[$productTypeId][$i]['productsClass']['point_rate'];


                $quantity = $this->cartSession[$productTypeId][$i]['quantity'];
                $pre_tax = SC_Helper_DB_Ex::sfPreTax($price);
                $total = $pre_tax * $quantity;

                $this->cartSession[$productTypeId][$i]['total_pretax'] = $total;

                $arrRet[] =& $this->cartSession[$productTypeId][$i];
            }
        }
        return $arrRet;
    }

    // カート内にある商品ＩＤを全て取得する
    function getAllProductID($productTypeId) {
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            if($this->cartSession[$productTypeId][$i]['cart_no'] != "") {
                $arrRet[] = $this->cartSession[$productTypeId][$i]['id'][0];
            }
        }
        return $arrRet;
    }

    function delAllProducts($productTypeId) {
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            unset($this->cartSession[$productTypeId][$i]);
        }
    }

    // 商品の削除
    function delProduct($cart_no, $productTypeId) {
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            if($this->cartSession[$productTypeId][$i]['cart_no'] == $cart_no) {
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

    function getQuantity($cart_no, $productTypeId) {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] == $cart_no) {
                return $this->cartSession[$productTypeId][$i]['quantity'];
            }
        }
    }

    function setQuantity($quantity, $cart_no, $productTypeId) {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] == $cart_no) {
                $this->cartSession[$productTypeId][$i]['quantity'] = $quantity;
            }
        }
    }

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
     * 1. 削除/非表示商品のチェック
     * 2. 商品購入制限数のチェック
     * 3. 在庫数チェック
     *
     * @param string $key 商品種別ID
     * @return string エラーが発生した場合はエラーメッセージ
     */
    function checkProducts($productTypeId) {
        $objProduct = new SC_Product();
        $tpl_message = "";

        // カート内の情報を取得
        $items = $this->getCartList($productTypeId);
        foreach (array_keys($items) as $key) {
            $item =& $items[$key];
            $product =& $item['productsClass'];
            /*
             * 表示/非表示商品のチェック
             */
            if (SC_Utils_Ex::isBlank($product)) {
                $this->delProduct($item['cart_no'], $productTypeId);
                $tpl_message .= "※ 現時点で販売していない商品が含まれておりました。該当商品をカートから削除しました。\n";
            }

            /*
             * 商品購入制限数, 在庫数のチェック
             */
            $limit = $objProduct->getBuyLimit($product);
            if (!is_null($limit) && $item['quantity'] > $limit) {
                if ($limit > 0) {
                    $this->setProductValue($item['id'], 'quantity', $limit, $productTypeId);
                    $tpl_message .= "※「" . $product['name'] . "」は販売制限(または在庫が不足)しております。一度に数量{$limit}以上の購入はできません。\n";
                } else {
                    $this->delProduct($item['cart_no'], $productTypeId);
                    $tpl_message .= "※「" . $product['name'] . "」は売り切れました。\n";
                    continue;
                }
            }
        }
        return $tpl_message;
    }

    function getKeys() {
        return array_keys($this->cartSession);
    }

    function registerKey($key) {
        $_SESSION['cartKey'] = $key;
    }

    function unsetKey() {
        unset($_SESSION['cartKey']);
    }
}
?>
