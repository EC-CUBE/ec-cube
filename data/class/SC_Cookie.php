<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*������ɽ���ѥ��饹 */
class SC_Cookie {
	
	var $expire;
	
	// ���󥹥ȥ饯��
	function SC_Cookie($day = 365) {
		// ͭ������
		$this->expire = time() + ($day * 24 * 3600);
	}
	
	// ���å����񤭹���
	function setCookie($key, $val) {
		setcookie($key, $val, $this->expire, "/", DOMAIN_NAME);
	}
	
	// ���å�������
	function getCookie($key) {
		return $_COOKIE[$key];
	}
}
?>