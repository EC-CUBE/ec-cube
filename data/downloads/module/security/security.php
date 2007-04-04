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

$arrList[] = sfCheckOpenData();
$arrList[] = sfCheckInstall();

$objPage->arrList = $arrList;

$objView->assignobj($objPage);					//変数をテンプレートにアサインする
$objView->display($objPage->tpl_mainpage);		//テンプレートの出力
//-------------------------------------------------------------------------------------------------------
// 設定ファイル(data)のパスが公開パスでないか確認する
function sfCheckOpenData() {
    // ドキュメントルートのパスを推測する。
    $doc_root = ereg_replace(URL_DIR . "$","/",HTML_PATH);
    $data_path = realpath(DATA_PATH);
    
    // dataのパスがドキュメントルート以下にあるか判定
    if(ereg("^".$doc_root, $data_path)) {
        $arrResult['result'] = "×";
        $arrResult['detail'] = "設定ファイルが、公開されている可能性があります。<br>";
        $arrResult['detail'].= "/data/ディレクトリは、非公開のパスに設置して下さい。";
    } else {
        $arrResult['result'] = "○";
        $arrResult['detail'] = "設定ファイルは、公開パス配下に存在しません。";        
    }
    
    $arrResult['title'] = "設定ファイルの保存パス";
    return $arrResult;
}

// インストールファイルが存在するか確認する
function sfCheckInstall() {
    // インストールファイルの存在チェック
    $inst_path = HTML_PATH . "install/index.php";
    
    if(file_exists($inst_path)) {
        $arrResult['result'] = "×";
        $arrResult['detail'] = "/install/index.phpは、インストール完了後にファイルを削除してください。";            
    } else {
        $arrResult['result'] = "○";
        $arrResult['detail'] = "/install/index.phpは、見つかりませんでした。";    
    }
}
?>