<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage = 'upgrade/index.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$con = ftp_connect("localhost");
$res = ftp_login($con, "osuser", "password");
if($con != false && $res != false) {
	//ファイルのダウンロード
	if($_POST['filename'] != "") {
		
		$download_file = DATA_PATH . "module/upload/" . $_POST['filename'];
		
		if(ftp_get($con, $download_file, $_POST['filename'], FTP_BINARY)) {
			ftp_quit($con);
			//ファイル所有者変更
			echo "ファイルのダウンロードに成功しました。";
			sfPrintR(exec("tar zxvf " . $download_file . " ./", $arrRes));
		} else
			echo 'ファイルのダウンロードに失敗しました。';
		}
	}
	
	//ディレクトリ内の全てのファイルを取得
	$arrRet = ftp_nlist($con, ".");
	$i = 0;
	//必要なファイル情報を取得
	foreach($arrRet as $val) {
		if(!ereg("^\.|^\..", $val)) {
			$arrFile[$i]['filename'] = $val;
			$arrFile[$i]['date'] = date("Y年m月d日", ftp_mdtm($con, $val));
			$arrFile[$i]['filesize'] = number_format(ftp_size($con, $val))."Byte";
			$i++;
		}
	}
	$objPage->arrFile = $arrFile;
}

$objView->assignobj($objPage);		//変数をテンプレートにアサインする
$objView->display(SITE_FRAME);		//テンプレートの出力

//-------------------------------------------------------------------------------------------------------



?>
