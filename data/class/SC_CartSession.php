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
    var $key_tmp;	// ユニークIDを指定する。

    /* コンストラクタ */
    function SC_CartSession() {
    }

    // 商品購入処理中のロック
    function saveCurrentCart($key_tmp, $key) {
        $this->key_tmp = "savecart_" . $key_tmp;
        // すでに情報がなければ現状のカート情報を記録しておく
        if(count($_SESSION[$this->key_tmp]) == 0) {
            $_SESSION[$this->key_tmp] = $_SESSION[$key];
        }
        // 1世代古いコピー情報は、削除しておく
        foreach($_SESSION as $k => $val) {
            if($k != $this->key_tmp && preg_match("/^savecart_/", $k)) {
                unset($_SESSION[$key][$k]);
            }
        }
        $this->registerKey($key);
    }

    // 商品購入中の変更があったかをチェックする。
    function getCancelPurchase($key) {
        $this->addKey($key);
        $ret = isset($_SESSION[$key]['cancel_purchase'])
            ? $_SESSION[$key]['cancel_purchase'] : "";
        $_SESSION[$key]['cancel_purchase'] = false;
        return $ret;
    }

    // 購入処理中に商品に変更がなかったかを判定
    function checkChangeCart($key) {
        $this->addKey($key);
        $change = false;
        $max = $this->getMax();
        for($i = 1; $i <= $max; $i++) {
            if ($_SESSION[$key][$i]['quantity'] != $_SESSION[$this->key_tmp][$i]['quantity']) {
                $change = true;
                break;
            }
            if ($_SESSION[$key][$i]['id'] != $_SESSION[$this->key_tmp][$i]['id']) {
                $change = true;
                break;
            }
        }
        if ($change) {
            // 一時カートのクリア
            unset($_SESSION[$this->key_tmp]);
            $_SESSION[$key]['cancel_purchase'] = true;
        } else {
            $_SESSION[$key]['cancel_purchase'] = false;
        }
        return $_SESSION[$key]['cancel_purchase'];
    }

    // 次に割り当てるカートのIDを取得する
    function getNextCartID($key) {
        $this->addKey($key);
        foreach($_SESSION[$key] as $k => $val){
            $arrRet[] = $_SESSION[$key][$k]['cart_no'];
        }
        return (max($arrRet) + 1);
    }

    /**
     * 商品ごとの合計価格
     * XXX 実際には、「商品」ではなく、「カートの明細行(≒商品規格)」のような気がします。
     *
     * @param integer $id
     * @return string 商品ごとの合計価格(税込み)
     */
    function getProductTotal($id, $key) {
        $this->addKey($key);
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            if(isset($_SESSION[$key][$i]['id'])
               && $_SESSION[$key][$i]['id'] == $id) {

                // 税込み合計
                $price = $_SESSION[$key][$i]['price'];
                $quantity = $_SESSION[$key][$i]['quantity'];
                $pre_tax = SC_Helper_DB_Ex::sfPreTax($price);
                $total = $pre_tax * $quantity;
                return $total;
            }
        }
        return 0;
    }

    // 値のセット
    function setProductValue($id, $k, $val, $key) {
        $this->addKey($key);
        $max = $this->getMax($key);
        for($i = 0; $i <= $max; $i++) {
            if(isset($_SESSION[$key][$i]['id'])
               && $_SESSION[$key][$i]['id'] == $id) {
                $_SESSION[$key][$i][$k] = $val;
            }
        }
    }

    // カート内商品の最大要素番号を取得する。
    function getMax($key) {
        $this->addKey($key);
        $cnt = 0;
        $pos = 0;
        $max = 0;
        if (count($_SESSION[$key]) > 0){
            foreach($_SESSION[$key] as $k => $val) {
                if (is_numeric($k)) {
                    if($max < $k) {
                        $max = $k;
                    }
                }
            }
        }
        return ($max);
    }

    // カート内商品数の合計
    function getTotalQuantity($key) {
        $this->addKey($key);
        $total = 0;
        $max = $this->getMax($key);
        for($i = 0; $i <= $max; $i++) {
            $total+= $_SESSION[$key][$i]['quantity'];
        }
        return $total;
    }


    // 全商品の合計価格
    function getAllProductsTotal($key) {
        $this->addKey($key);
        // 税込み合計
        $total = 0;
        $max = $this->getMax($key);
        for($i = 0; $i <= $max; $i++) {

            if (!isset($_SESSION[$key][$i]['price'])) {
                $_SESSION[$key][$i]['price'] = "";
            }
            $price = $_SESSION[$key][$i]['price'];

            if (!isset($_SESSION[$key][$i]['quantity'])) {
                $_SESSION[$key][$i]['quantity'] = "";
            }
            $quantity = $_SESSION[$key][$i]['quantity'];

            $pre_tax = SC_Helper_DB_Ex::sfPreTax($price);
            $total+= ($pre_tax * $quantity);
        }
        return $total;
    }

    // 全商品の合計税金
    function getAllProductsTax($key) {
        $this->addKey($key);
        // 税合計
        $total = 0;
        $max = $this->getMax($key);
        for($i = 0; $i <= $max; $i++) {
            $price = $_SESSION[$key][$i]['price'];
            $quantity = $_SESSION[$key][$i]['quantity'];
            $tax = SC_Helper_DB_Ex::sfTax($price);
            $total+= ($tax * $quantity);
        }
        return $total;
    }

    // 全商品の合計ポイント
    function getAllProductsPoint($key) {
        $this->addKey($key);
        // ポイント合計
        $total = 0;
        if (USE_POINT !== false) {
            $max = $this->getMax($key);
            for($i = 0; $i <= $max; $i++) {
                $price = $_SESSION[$key][$i]['price'];
                $quantity = $_SESSION[$key][$i]['quantity'];

                if (!isset($_SESSION[$key][$i]['point_rate'])) {
                    $_SESSION[$key][$i]['point_rate'] = "";
                }
                $point_rate = $_SESSION[$key][$i]['point_rate'];

                if (!isset($_SESSION[$key][$i]['id'][0])) {
                    $_SESSION[$key][$i]['id'][0] = "";
                }
                $id = $_SESSION[$key][$i]['id'][0];
                $point = SC_Utils_Ex::sfPrePoint($price, $point_rate, POINT_RULE, $id);
                $total+= ($point * $quantity);
            }
        }
        return $total;
    }

    // カートへの商品追加
    function addProduct($id, $quantity, $key) {
        $this->addKey($key);
        $find = false;
        $max = $this->getMax($key);
        for($i = 0; $i <= $max; $i++) {

            if($_SESSION[$key][$i]['id'] == $id) {
                $val = $_SESSION[$key][$i]['quantity'] + $quantity;
                if(strlen($val) <= INT_LEN) {
                    $_SESSION[$key][$i]['quantity']+= $quantity;
                }
                $find = true;
            }
        }
        if(!$find) {
            $_SESSION[$key][$max+1]['id'] = $id;
            $_SESSION[$key][$max+1]['quantity'] = $quantity;
            $_SESSION[$key][$max+1]['cart_no'] = $this->getNextCartID($key);
        }
    }

    // 前頁のURLを記録しておく
    function setPrevURL($url, $key) {
        $this->addKey($key);
        // 前頁として記録しないページを指定する。
        $arrExclude = array(
            "/shopping/"
        );
        $exclude = false;
        // ページチェックを行う。
        foreach($arrExclude as $val) {
            if(ereg($val, $url)) {
                $exclude = true;
                break;
            }
        }
        // 除外ページでない場合は、前頁として記録する。
        if(!$exclude) {
            $_SESSION[$key]['prev_url'] = $url;
        }
    }

    // 前頁のURLを取得する
    function getPrevURL($key) {
        $this->addKey($key);
        return isset($_SESSION[$key]['prev_url'])
            ? $_SESSION[$key]['prev_url'] : "";
    }

    // キーが一致した商品の削除
    function delProductKey($keyname, $val, $key) {
        $this->addKey($key);
        $max = count($_SESSION[$key]);
        for($i = 0; $i < $max; $i++) {
            if($_SESSION[$key][$i][$keyname] == $val) {
                unset($_SESSION[$key][$i]);
            }
        }
    }

    function setValue($k, $val, $key) {
        $this->addKey($key);
        $_SESSION[$key][$k] = $val;
    }

    function getValue($k, $key) {
        $this->addKey($key);
        return $_SESSION[$key][$k];
    }

    function getCartList($key) {
        $this->addKey($key);
        $max = $this->getMax($key);
        $arrRet = array();
        for($i = 0; $i <= $max; $i++) {
            if(isset($_SESSION[$key][$i]['cart_no'])
               && $_SESSION[$key][$i]['cart_no'] != "") {
                $arrRet[] = $_SESSION[$key][$i];
            }
        }
        return $arrRet;
    }

    // カート内にある商品ＩＤを全て取得する
    function getAllProductID($key) {
        $this->addKey($key);
        $max = $this->getMax($key);
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$key][$i]['cart_no'] != "") {
                $arrRet[] = $_SESSION[$key][$i]['id'][0];
            }
        }
        return $arrRet;
    }
    // カート内にある商品ＩＤ＋カテゴリＩＤを全て取得する
    function getAllProductClassID($key) {
        $this->addKey($key);
        $max = $this->getMax($key);
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$key][$i]['cart_no'] != "") {
                $arrRet[] = $_SESSION[$key][$i]['id'];
            }
        }
        return $arrRet;
    }

    function delAllProducts($key) {
        $this->addKey($key);
        $max = $this->getMax($key);
        for($i = 0; $i <= $max; $i++) {
            unset($_SESSION[$key][$i]);
        }
    }

    // 商品の削除
    function delProduct($cart_no, $key) {
        $this->addKey($key);
        $max = $this->getMax($key);
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$key][$i]['cart_no'] == $cart_no) {
                unset($_SESSION[$key][$i]);
            }
        }
    }

    // 数量の増加
    function upQuantity($cart_no, $key) {
        $this->addKey($key);
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$key][$i]['cart_no'] == $cart_no) {
                if(strlen($_SESSION[$key][$i]['quantity'] + 1) <= INT_LEN) {
                    $_SESSION[$key][$i]['quantity']++;
                }
            }
        }
    }

    // 数量の減少
    function downQuantity($cart_no, $key) {
        $this->addKey($key);
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$key][$i]['cart_no'] == $cart_no) {
                if($_SESSION[$key][$i]['quantity'] > 1) {
                    $_SESSION[$key][$i]['quantity']--;
                }
            }
        }
    }

    function addKey($key) {
        if (!in_array($this->keys, $key)) {
            $this->keys[] = $key;
        }
    }

    function getKeys() {
        return $this->keys;
    }

    function registerKey($key) {
        $_SESSION['cartKey'] = $key;
    }

    function unsetKey() {
        unset($_SESSION['cartKey']);
    }
}
?>
