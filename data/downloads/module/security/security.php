<?php
/**
 * 
 * @copyright	2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: ebis_tag.php,v 1.0 2006/10/26 04:02:40 naka Exp $
 * @link		http://www.lockon.co.jp/
 *
 */
 
//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage = MODULE_PATH . 'security/security.tpl';
		$this->tpl_subtitle = 'セキュリティチェック';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

$arrList[] = sfCheckDataPath();

$objPage->arrList = $arrList;

$objView->assignobj($objPage);					//変数をテンプレートにアサインする
$objView->display($objPage->tpl_mainpage);		//テンプレートの出力
//-------------------------------------------------------------------------------------------------------
// 設定ファイル(data)のパスが公開パスでないか確認する
function sfCheckDataPath() {
    // ドキュメントルートのパスを推測する。
    $doc_root = ereg_replace(URL_DIR . "$","/",HTML_PATH);
    $arrResult['title'] = "設定ファイルの保存パス";
    
    $data_path = realpath(DATA_PATH);
    
    $arrResult['result'] = $data_path;
    
    
    return $arrResult;
}

?>