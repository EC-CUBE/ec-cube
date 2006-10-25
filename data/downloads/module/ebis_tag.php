<?php
/**
 * 
 * @copyright	2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id$
 * @link		http://www.lockon.co.jp/
 *
 */

require_once("../../require.php");

//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage = MODULE_PATH . 'ebis_tag.tpl';
		$this->tpl_subtitle = 'EBiSタグ埋め機能';
	}
}
$objPage = new LC_Page();
$objView = new SC_AdminView();

sfPrintR($_POST);
sfPrintR($_SESSION);

$objView->assignobj($objPage);		//変数をテンプレートにアサインする
$objView->display($objPage->tpl_mainpage);		//テンプレートの出力
//-------------------------------------------------------------------------------------------------------
?>