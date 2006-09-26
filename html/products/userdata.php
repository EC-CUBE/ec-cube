<?php
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*　商品詳細ページのHTML開放部分テンプレート表示用ファイル */

$objUserView = new SC_UserView(TEMPLATE_FTP_DIR, COMPILE_FTP_DIR);
//HTML開放用のテンプレートファイル名
$tpl_name = "products_detail_share.tpl";
//ファイル存在チェック
if(file_exists(TEMPLATE_FTP_DIR . $tpl_name)) {
	$objUserView->display($tpl_name);
}

?>