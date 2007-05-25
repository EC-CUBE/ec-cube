<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * モバイルサイト/空メールの返信からの遷移
 */

define('SKIP_MOBILE_INIT', true);
require_once 'require.php';

if (isset($_GET['token'])) {
	$next_url = gfFinishKaraMail($_GET['token']);
}

if (isset($next_url) && $next_url !== false) {
	header("Location: $next_url");
} else {
	header('Location: ' . MOBILE_SITE_URL);
}
?>
