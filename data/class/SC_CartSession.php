<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/* カートセッション管理クラス */
class SC_CartSession {
    var $key;
    var $key_tmp;	// ユニークIDを指定する。

    /* コンストラクタ */
    function SC_CartSession($key = 'cart') {
        SC_Utils::sfDomainSessionStart();

        if($key == "") $key = "cart";
        $this->key = $key;
    }

    // 商品購入処理中のロック
    function saveCurrentCart($key_tmp) {
        $this->key_tmp = "savecart_" . $key_tmp;
        // すでに情報がなければ現状のカート情報を記録しておく
        if(count($_SESSION[$this->key_tmp]) == 0) {
            $_SESSION[$this->key_tmp] = $_SESSION[$this->key];
        }
        // 1世代古いコピー情報は、削除しておく
        foreach($_SESSION as $key => $val) {
            if($key != $this->key_tmp && ereg("^savecart_", $key)) {
                unset($_SESSION[$key]);
            }
        }
    }

    // 商品購入中の変更があったかをチェックする。
    function getCancelPurchase() {
        $ret = $_SESSION[$this->key]['cancel_purchase'];
        $_SESSION[$this->key]['cancel_purchase'] = false;
        return $ret;
    }

    // 購入処理中に商品に変更がなかったかを判定
    function checkChangeCart() {
        $change = false;
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            if ($_SESSION[$this->key][$i]['quantity'] != $_SESSION[$this->key_tmp][$i]['quantity']) {
                $change = true;
                break;
            }
            if ($_SESSION[$this->key][$i]['id'] != $_SESSION[$this->key_tmp][$i]['id']) {
                $change = true;
                break;
            }
        }
        if ($change) {
            // 一時カートのクリア
            unset($_SESSION[$this->key_tmp]);
            $_SESSION[$this->key]['cancel_purchase'] = true;
        } else {
            $_SESSION[$this->key]['cancel_purchase'] = false;
        }
        return $_SESSION[$this->key]['cancel_purchase'];
    }

    // 次に割り当てるカートのIDを取得する
    function getNextCartID() {
        foreach($_SESSION[$this->key] as $key => $val){
            $arrRet[] = $_SESSION[$this->key][$key]['cart_no'];
        }
        return (max($arrRet) + 1);
    }

    // 商品ごとの合計価格
    function getProductTotal($arrInfo, $id) {
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$this->key][$i]['id'] == $id) {
                // 税込み合計
                $price = $_SESSION[$this->key][$i]['price'];
                $quantity = $_SESSION[$this->key][$i]['quantity'];
                $pre_tax = sfPreTax($price, $arrInfo['tax'], $arrInfo['tax_rule']);
                $total = $pre_tax * $quantity;
                return $total;
            }
        }
        return 0;
    }

    // 値のセット
    function setProductValue($id, $key, $val) {
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$this->key][$i]['id'] == $id) {
                $_SESSION[$this->key][$i][$key] = $val;
            }
        }
    }

    // カート内商品の最大要素番号を取得する。
    function getMax() {
        $cnt = 0;
        $pos = 0;
        $max = 0;
        if (count($_SESSION[$this->key]) > 0){
            foreach($_SESSION[$this->key] as $key => $val) {
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
    function getTotalQuantity() {
        $total = 0;
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            $total+= $_SESSION[$this->key][$i]['quantity'];
        }
        return $total;
    }


    // 全商品の合計価格
    function getAllProductsTotal($arrInfo) {
        // 税込み合計
        $total = 0;
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            $price = $_SESSION[$this->key][$i]['price'];
            $quantity = $_SESSION[$this->key][$i]['quantity'];
            $pre_tax = SC_Utils::sfPreTax($price, $arrInfo['tax'], $arrInfo['tax_rule']);
            $total+= ($pre_tax * $quantity);
        }
        return $total;
    }

    // 全商品の合計税金
    function getAllProductsTax($arrInfo) {
        // 税合計
        $total = 0;
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            $price = $_SESSION[$this->key][$i]['price'];
            $quantity = $_SESSION[$this->key][$i]['quantity'];
            $tax = sfTax($price, $arrInfo['tax'], $arrInfo['tax_rule']);
            $total+= ($tax * $quantity);
        }
        return $total;
    }

    // 全商品の合計ポイント
    function getAllProductsPoint() {
        // ポイント合計
        $total = 0;
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            $price = $_SESSION[$this->key][$i]['price'];
            $quantity = $_SESSION[$this->key][$i]['quantity'];
            $point_rate = $_SESSION[$this->key][$i]['point_rate'];
            $id = $_SESSION[$this->key][$i]['id'][0];
            $point = sfPrePoint($price, $point_rate, POINT_RULE, $id);
            $total+= ($point * $quantity);
        }
        return $total;
    }

    // カートへの商品追加
    function addProduct($id, $quantity, $campaign_id = "") {
        $find = false;
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {

            if($_SESSION[$this->key][$i]['id'] == $id) {
                $val = $_SESSION[$this->key][$i]['quantity'] + $quantity;
                if(strlen($val) <= INT_LEN) {
                    $_SESSION[$this->key][$i]['quantity']+= $quantity;
                    if(!empty($campaign_id)){
                        $_SESSION[$this->key][$i]['campaign_id'] = $campaign_id;
                        $_SESSION[$this->key][$i]['is_campaign'] = true;
                    }
                }
                $find = true;
            }
        }
        if(!$find) {
            $_SESSION[$this->key][$max+1]['id'] = $id;
            $_SESSION[$this->key][$max+1]['quantity'] = $quantity;
            $_SESSION[$this->key][$max+1]['cart_no'] = $this->getNextCartID();
            if(!empty($campaign_id)){
                $_SESSION[$this->key][$max+1]['campaign_id'] = $campaign_id;
                $_SESSION[$this->key][$max+1]['is_campaign'] = true;
            }
        }
    }

    // 前頁のURLを記録しておく
    function setPrevURL($url) {
        // 前頁として記録しないページを指定する。
        $arrExclude = array(
            "detail_image.php",
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
            $_SESSION[$this->key]['prev_url'] = $url;
        }
    }

    // 前頁のURLを取得する
    function getPrevURL() {
        return $_SESSION[$this->key]['prev_url'];
    }

    // キーが一致した商品の削除
    function delProductKey($keyname, $val) {
        $max = count($_SESSION[$this->key]);
        for($i = 0; $i < $max; $i++) {
            if($_SESSION[$this->key][$i][$keyname] == $val) {
                unset($_SESSION[$this->key][$i]);
            }
        }
    }

    function setValue($key, $val) {
        $_SESSION[$this->key][$key] = $val;
    }

    function getValue($key) {
        return $_SESSION[$this->key][$key];
    }

    function getCartList() {
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$this->key][$i]['cart_no'] != "") {
                $arrRet[] = $_SESSION[$this->key][$i];
            }
        }
        return $arrRet;
    }

    // カート内にある商品ＩＤを全て取得する
    function getAllProductID() {
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$this->key][$i]['cart_no'] != "") {
                $arrRet[] = $_SESSION[$this->key][$i]['id'][0];
            }
        }
        return $arrRet;
    }

    function delAllProducts() {
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            unset($_SESSION[$this->key][$i]);
        }
    }

    // 商品の削除
    function delProduct($cart_no) {
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$this->key][$i]['cart_no'] == $cart_no) {
                unset($_SESSION[$this->key][$i]);
            }
        }
    }

    // 個数の増加
    function upQuantity($cart_no) {
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$this->key][$i]['cart_no'] == $cart_no) {
                if(strlen($_SESSION[$this->key][$i]['quantity'] + 1) <= INT_LEN) {
                    $_SESSION[$this->key][$i]['quantity']++;
                }
            }
        }
    }

    // 個数の減少
    function downQuantity($cart_no) {
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$this->key][$i]['cart_no'] == $cart_no) {
                if($_SESSION[$this->key][$i]['quantity'] > 1) {
                    $_SESSION[$this->key][$i]['quantity']--;
                }
            }
        }
    }

    // 全商品の合計送料
    function getAllProductsDelivFee() {
        // ポイント合計
        $total = 0;
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            $deliv_fee = $_SESSION[$this->key][$i]['deliv_fee'];
            $quantity = $_SESSION[$this->key][$i]['quantity'];
            $total+= ($deliv_fee * $quantity);
        }
        return $total;
    }

    // カートの中の売り切れチェック
    function chkSoldOut($arrCartList, $is_mobile = false){
        foreach($arrCartList as $key => $val){
            if($val['quantity'] == 0){
                // 売り切れ商品をカートから削除する
                $this->delProduct($val['cart_no']);
                sfDispSiteError(SOLD_OUT, "", true, "", $is_mobile);
            }
        }
    }

    /**
     * カートの中のキャンペーン商品のチェック
     * @param integer $campaign_id キャンペーンID
     * @return boolean True:キャンペーン商品有り False:キャンペーン商品無し
     */
    function chkCampaign($campaign_id){
        $max = $this->getMax();
        for($i = 0; $i <= $max; $i++) {
            if($_SESSION[$this->key][$i]['is_campaign'] and $_SESSION[$this->key][$i]['campaign_id'] == $campaign_id) return true;
        }

        return false;
    }

}
?>
