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
	}
}
$objPage = new LC_Page();
$objView = new SC_AdminView();

$objView->assignobj($objPage);		//変数をテンプレートにアサインする
$objView->display($this->tpl_mainpage);		//テンプレートの出力
//-------------------------------------------------------------------------------------------------------
?>