<?php
$now_dir = realpath(dirname(__FILE__));
require_once($now_dir . "/../../../data/lib/slib.php");	
require_once($now_dir . "/../../../data/conf/core_os.php");
require_once($now_dir . "/../../../data/conf/conf_os.php");
require_once($now_dir . "/../../../data/class/SC_View.php");
require_once($now_dir . "/../../../data/class/SC_Query.php");
require_once($now_dir . "/../../../data/class/SC_CheckError.php");
require_once($now_dir . "/../../../data/class/SC_FormParam.php");
require_once($now_dir . "/../../../data/class/SC_Customer.php");
require_once($now_dir . "/../../../data/class/SC_Cookie.php");
require_once($now_dir . "/../../../data/module/Archive/Tar.php");

//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage = 'test/iketest/test.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();

rmdir("test");

$objView->assignobj($objPage);		//変数をテンプレートにアサインする
$objView->display(SITE_FRAME);		//テンプレートの出力

//-------------------------------------------------------------------------------------------------------

//解凍ファイルにより、ローカルファイルを上書きする
function lfSetExtractFile($extract_file, $extract_top_file, $install_success) {
	//ディレクトリでなければ
	if(!is_dir($extract_file)) {
		return false;
	}

	//ディレクトリを開く
	if($handle = opendir($extract_file)) {
		//ディレクトリの中身を読み込む
		while($file = readdir($handle)) {
			//'.'と'..'ファイルは除外
			if($file != "." && $file != "..") {
				//ディレクトリである
				if(is_dir($extract_file . "/" . $file)) {
					//再帰呼び出し
					lfSetExtractFile($extract_file . "/" . $file, $extract_top_file, $install_success);
				} else {
					//解凍ファイルのパスを指定
					$replace_file = ereg_replace("^" . $extract_top_file . "/", "", $extract_file);
					//ファイルをコピー(上書き)する
					if(!copy($extract_file . "/" . $file, ROOT_DIR . $replace_file . "/" . $file)) {
						$install_success = false;
					}
				}
			}
		}
		//ディレクトリを閉じる
		closedir($handle);
	} else {
		$install_success = false;
	}
	return $install_success;
	
}	

?>
