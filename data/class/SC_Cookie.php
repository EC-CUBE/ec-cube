<?php
/*　日時表示用クラス */
class SC_Cookie {
	
	var $expire;
	
	// コンストラクタ
	function SC_Cookie($day = 365) {
		// 有効期限
		$this->expire = time() + ($day * 24 * 3600);
	}
	
	// クッキー書き込み
	function setCookie($key, $val) {
		setcookie($key, $val, $this->expire, "/", DOMAIN_NAME);
	}
	
	// クッキー取得
	function getCookie($key) {
		return $_COOKIE[$key];
	}
}
?>