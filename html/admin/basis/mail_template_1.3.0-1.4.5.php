<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

$message = "";

// カラム作成
if(sfColumnExists("dtb_mailtemplate", "send_type", "int2", DEFAULT_DSN, true)) {
    $message.= "send_type ok<br />\n";
}

// カラム作成
if(sfColumnExists("dtb_mailtemplate", "body", "text", DEFAULT_DSN, true)) {
    $message.= "body ok<br />\n";
}

// カラム作成
if(sfColumnExists("dtb_mailtemplate", "template_name", "text", DEFAULT_DSN, true)) {
    $message.= "template_name ok<br />\n";
}

$objQuery = new SC_Query();

$arrVal['send_type'] = '1';
$arrVal['template_name'] = '受注完了テンプレート(PCサイト用)';

$objQuery->update("dtb_mailtemplate", $arrVal, "template_id = ?", array('1'));

$arrVal['send_type'] = '2';
$arrVal['template_name'] = '受注完了テンプレート(携帯サイト用)';

$objQuery->update("dtb_mailtemplate", $arrVal, "template_id = ?", array('2'));

$arrRet = $objQuery->select("template_id, header, footer, body", "dtb_mailtemplate", "del_flg = 0");

foreach($arrRet as $array) {
    $arrVal = array();
    // 本文が未設定の場合
    if($array['body'] == "") {
	    $arrVal['body'] = $array['header'] . "\n{order}\n" . $array['footer'];
	    $objQuery->update("dtb_mailtemplate", $arrVal, "template_id = ?", array($array['template_id']));
    }
}

$message.= "データを更新しました。<br />\n";
print($message);

?>


$message.= "データを更新しました。<br />\n";
print($message);

?>