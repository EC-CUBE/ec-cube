<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/* �����ȥ��å����������饹 */
class SC_CartSession {
	var $key;
	var $key_tmp;	// ��ˡ���ID����ꤹ�롣
	
	/* ���󥹥ȥ饯�� */
	function SC_CartSession($key = 'cart') {
		sfDomainSessionStart();
		
		if($key == "") $key = "cart";
		$this->key = $key;
	}
	
	// ���ʹ���������Υ�å�
	function saveCurrentCart($key_tmp) {
		$this->key_tmp = "savecart_" . $key_tmp;
		// ���Ǥ˾��󤬤ʤ���и����Υ����Ⱦ����Ͽ���Ƥ���
		if(count($_SESSION[$this->key_tmp]) == 0) {
			$_SESSION[$this->key_tmp] = $_SESSION[$this->key];
		}
		// 1����Ť����ԡ�����ϡ�������Ƥ���
		foreach($_SESSION as $key => $val) {
			if($key != $this->key_tmp && ereg("^savecart_", $key)) {
				unset($_SESSION[$key]);
			}
		}
	}

	// ���ʹ�������ѹ������ä���������å����롣
	function getCancelPurchase() {
		$ret = $_SESSION[$this->key]['cancel_purchase'];
		$_SESSION[$this->key]['cancel_purchase'] = false;
		return $ret;
	}
	
	// ����������˾��ʤ��ѹ����ʤ��ä�����Ƚ��
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
			// ��������ȤΥ��ꥢ
			unset($_SESSION[$this->key_tmp]);
			$_SESSION[$this->key]['cancel_purchase'] = true;
		} else {
			$_SESSION[$this->key]['cancel_purchase'] = false;
		}
		return $_SESSION[$this->key]['cancel_purchase'];
	}
	
	// ���˳�����Ƥ륫���Ȥ�ID���������
	function getNextCartID() {
        foreach($_SESSION[$this->key] as $key => $val){
            $arrRet[] = $_SESSION[$this->key][$key]['cart_no'];
        }
		return (max($arrRet) + 1);		
	}
			
	// ���ʤ��Ȥι�ײ���
	function getProductTotal($arrInfo, $id) {
		$max = $this->getMax();
		for($i = 0; $i <= $max; $i++) {
			if($_SESSION[$this->key][$i]['id'] == $id) {
				// �ǹ��߹��
				$price = $_SESSION[$this->key][$i]['price'];
				$quantity = $_SESSION[$this->key][$i]['quantity'];
				$pre_tax = sfPreTax($price, $arrInfo['tax'], $arrInfo['tax_rule']);
				$total = $pre_tax * $quantity;
				return $total;
			}
		}
		return 0;
	}
	
	// �ͤΥ��å�
	function setProductValue($id, $key, $val) {
		$max = $this->getMax();
		for($i = 0; $i <= $max; $i++) {
			if($_SESSION[$this->key][$i]['id'] == $id) {
				$_SESSION[$this->key][$i][$key] = $val;
			}
		}
	}
		
	// �������⾦�ʤκ��������ֹ��������롣
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
	
	// �������⾦�ʿ��ι��
	function getTotalQuantity() {
		$total = 0;
		$max = $this->getMax();
		for($i = 0; $i <= $max; $i++) {
			$total+= $_SESSION[$this->key][$i]['quantity'];
		}
		return $total;
	}
	

	// �����ʤι�ײ���
	function getAllProductsTotal($arrInfo) {
		// �ǹ��߹��
		$total = 0;
		$max = $this->getMax();
		for($i = 0; $i <= $max; $i++) {
			$price = $_SESSION[$this->key][$i]['price'];
			$quantity = $_SESSION[$this->key][$i]['quantity'];
			$pre_tax = sfPreTax($price, $arrInfo['tax'], $arrInfo['tax_rule']);
			$total+= ($pre_tax * $quantity);
		}
		return $total;
	}

	// �����ʤι���Ƕ�
	function getAllProductsTax($arrInfo) {
		// �ǹ��
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
	
	// �����ʤι�ץݥ����
	function getAllProductsPoint() {
		// �ݥ���ȹ��
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
	
	// �����Ȥؤξ����ɲ�
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
	
	// ���Ǥ�URL��Ͽ���Ƥ���
	function setPrevURL($url) {
		// ���ǤȤ��Ƶ�Ͽ���ʤ��ڡ�������ꤹ�롣
		$arrExclude = array(
			"detail_image.php",
			"/shopping/"
		);
		$exclude = false;
		// �ڡ��������å���Ԥ���
		foreach($arrExclude as $val) {
			if(ereg($val, $url)) {
				$exclude = true;
				break;
			}
		}
		// �����ڡ����Ǥʤ����ϡ����ǤȤ��Ƶ�Ͽ���롣
		if(!$exclude) {		
			$_SESSION[$this->key]['prev_url'] = $url;
		}
	}
	
	// ���Ǥ�URL���������
	function getPrevURL() {
		return $_SESSION[$this->key]['prev_url'];
	}

	// ���������פ������ʤκ��
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
	
	// ��������ˤ��뾦�ʣɣĤ����Ƽ�������
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
	
	// ���ʤκ��
	function delProduct($cart_no) {
		$max = $this->getMax();
		for($i = 0; $i <= $max; $i++) {
			if($_SESSION[$this->key][$i]['cart_no'] == $cart_no) {
				unset($_SESSION[$this->key][$i]);
			}
		}
	}
	
	// �Ŀ�������
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
	
	// �Ŀ��θ���
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
	
	// �����ʤι������
	function getAllProductsDelivFee() {
		// �ݥ���ȹ��
		$total = 0;
		$max = $this->getMax();
		for($i = 0; $i <= $max; $i++) {
			$deliv_fee = $_SESSION[$this->key][$i]['deliv_fee'];
			$quantity = $_SESSION[$this->key][$i]['quantity'];
			$total+= ($deliv_fee * $quantity);
		}
		return $total;
	}
	
	// �����Ȥ��������ڤ�����å�
	function chkSoldOut($arrCartList, $is_mobile = false){
		foreach($arrCartList as $key => $val){
			if($val['quantity'] == 0){
				// ����ڤ쾦�ʤ򥫡��Ȥ���������
				$this->delProduct($val['cart_no']);
				sfDispSiteError(SOLD_OUT, "", true, "", $is_mobile);
			}
		}
	}
    
    /**
     * �����Ȥ���Υ����ڡ����ʤΥ����å�
     * @param integer $campaign_id �����ڡ���ID
     * @return boolean True:�����ڡ�����ͭ�� False:�����ڡ�����̵��
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