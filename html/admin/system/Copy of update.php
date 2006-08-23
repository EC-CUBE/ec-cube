<?php
$now_dir = realpath(dirname(__FILE__));
require_once($now_dir . "/../../../data/lib/slib.php");	
require_once($now_dir . "/../../../data/class/SC_View.php");
require_once($now_dir . "/../../../data/class/SC_Query.php");
require_once($now_dir . "/../../../data/class/SC_CheckError.php");
require_once($now_dir . "/../../../data/class/SC_FormParam.php");
require_once($now_dir . "/../../../data/class/SC_Customer.php");
require_once($now_dir . "/../../../data/class/SC_Cookie.php");
//require_once($now_dir . "/../../../data/module/Archive/Tar.php");

//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage = 'system/update.tpl';
		$this->tpl_subnavi = 'system/subnavi.tpl';
		$this->tpl_mainno = 'system';		
		$this->tpl_subno = 'update';
		$this->tpl_subtitle = 'アップデート管理';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

/*
//アップデート情報を取得
$fp = fopen("csv.php", "r");
$i = 0;
$j = 0;
$k = 0;
//ファイル取得成功
if($fp) {
	//CSV情報を配列に格納
	while(!feof($fp)) {
		$line = fgetcsv($fp, 40000);
		$arrRet[$i]['update_id'] = $line[0];		//アップデート機能ID
		$arrRet[$i]['file_name'] = $line[1];		//ファイル名
		$arrRet[$i]['func_name'] = $line[2];		//機能名
		$arrRet[$i]['func_explain'] = $line[3];		//機能説明
		$arrRet[$i]['version'] = $line[4];			//バージョン
		$arrRet[$i]['update_date'] = $line[5];		//最終更新日時
		if($i >= 1) {
			$arrval = array($arrRet[$i]['update_id'], $arrRet[$i]['version']);
			//既にインストールされている機能かどうかチェック
			$arrGet = $objQuery->select("*", "dtb_update_list", "update_id = ? AND version = ? ", $arrval);
			//インストール済み
			if(count($arrGet) > 0) {
				$arrInstalled[$k] = $arrGet[0];
				$k++;
			} else {
				$arrUpList[$j] = $arrRet[$i];
				$j++;
			}
		}
		$i++;
	}
	//インストール済み
	$objPage->arrInstalled = $arrInstalled;
	//アップデート可能機能一覧
	$objPage->arrUpList = $arrUpList;
}

//確認画面
if($_POST['mode'] == 'confirm' && sfIsInt($_POST['update_id'])) {
	//テンプレート指定
	$objPage->tpl_mainpage = "system/update_confirm.tpl";
	//アップデート機能ID
	$update_id = $_POST['update_id'];
	//ファイル名
	$comp_file = trim($arrRet[$update_id]['file_name']);
	//FTP接続
	$con = ftp_connect("localhost");
	if($con != false) {
		//FTPログイン成功
		if(ftp_login($con, "osuser", "password")) {
			//ローカルに保存するパス
			$objPage->local_save_dir = ROOT_DIR . "data/install/";
			//ローカルに保存するファイル名をパス指定
			$local_file = $objPage->local_save_dir . $comp_file;
			//FTPダウンロード成功
			if(ftp_get($con, $local_file, $comp_file, FTP_BINARY)) {
				//FTP接続切断
				ftp_quit($con);
				//現在のディレクトリを、インストールフォルダに移行
				$current_dir = getcwd();
				//ディレクトリの移行
				chdir(ROOT_DIR . "data/install/");
				//アーカイブ管理クラス
				$objTar = new Archive_Tar($comp_file);
				//解凍後のファイル名の取得(拡張子を削除したファイル)
				$extract_file = ereg_replace("\.tar\.gz|\.tar|\.gz|\.tgz", "", $comp_file);
				//アーカイブファイルのリストを表示
				$arrRet = $objTar->listcontent();
				foreach($arrRet as $data) {
					//PHPファイルもしくはtplファイルである
					if(ereg("\.php$|\.tpl$", $data['filename'])) {
						$main_file = ereg_replace($extract_file . "/", "", $data['filename']);
						$arrFile[]['main_file'] = ROOT_DIR . $main_file; 
					}
					//sqlファイルである
					if(ereg("\.sql$", $data['filename'])) {
						//ファイル名からDB名を取得する
						$sql_file = ereg_replace($extract_file . "/", "", $data['filename']);
						$db_name = ereg_replace("\.sql$", "", $sql_file);
						$arrFile[]['sql_file'] = $db_name;
					}
				}
				$objPage->arrFile = $arrFile;
				//圧縮ファイルの削除
				unlink($comp_file);
				//現在のディレクトリを元に戻す
				chdir($current_dir);
			} else {
				//FTPダウンロードエラー
				sfDispSiteError(FTP_DOWNLOAD_ERROR);
			}
		} else {
			//FTPログインエラー
			sfDispSiteError(FTP_LOGIN_ERROR);
		}
	} else {
		//FTP接続エラー
		sfDispSiteError(FTP_CONNECT_ERROR);
	}
}



//アップデート機能のインストール
if($_POST['mode'] == 'install' && sfIsInt($_POST['update_id'])) {
	//アップデート機能ID
	$update_id = $_POST['update_id'];
	//ファイル名
	$comp_file = trim($arrRet[$update_id]['file_name']);
	//FTP接続
	$con = ftp_connect("localhost");
	if($con != false) {
		//FTPログイン成功
		if(ftp_login($con, "osuser", "password")) {
			//ローカルに保存するパスを指定
			$objPage->local_save_dir = ROOT_DIR . "data/install/";
			//ローカルに保存するファイル名をパス指定
			$local_file = ROOT_DIR . "data/install/" . $comp_file;
			//FTPダウンロード成功
			if(ftp_get($con, $local_file, $comp_file, FTP_BINARY)) {
				//FTP接続切断
				ftp_quit($con);
				//現在のディレクトリを、インストールフォルダに移行
				$current_dir = getcwd();
				//ディレクトリの移行
				chdir(ROOT_DIR . "data/install/");
				//アーカイブ管理クラス
				$objTar = new Archive_Tar($comp_file, true);
				//エラーの詳細を返す
				//$objTar->setErrorHandling(PEAR_ERROR_PRINT);
				//解凍成功
				if($objTar->extract("./")) {
					//圧縮ファイルを削除
					unlink($comp_file);
					//解凍後のファイル名の取得(拡張子を削除したファイル)
					$extract_file = ereg_replace("\.tar\.gz|\.tar|\.gz|\.tgz", "", $comp_file);
					//解凍後の親ファイルを変数に渡す
					$extract_top_file = $extract_file;
					//インストール成功のフラグ
					$install_flag = true;
					//解凍ファイルにより、ローカルファイルを上書きする
					$flag = lfSetExtractFile($extract_file, $extract_top_file, $install_flag);
					//ファイルの上書きが成功
					if($flag) {
						//格納するキーと値を指定
						$sqlval['update_id'] = $update_id;
						$sqlval['func_name'] = $arrRet[$update_id]['func_name'];
						$sqlval['func_explain'] = $arrRet[$update_id]['func_explain'];
						$sqlval['version'] = $arrRet[$update_id]['version'];
						$sqlval['update_date'] = 'now()';
						
						//アップデート管理テーブルに情報を格納
						$objQuery->insert("dtb_update_list", $sqlval);
						//ファイルの削除
						system("rm -rf ". $extract_file);
						//情報を更新
						sfReload();
					} else {
						sfDispSiteError(WRITE_FILE_ERROR);
					}

				} else {
					//ファイル解凍エラー
					sfDispSiteError(EXTRACT_ERROR);
				}
				//現在のディレクトリを元に戻す
				chdir($current_dir);
				
			} else {
				//FTPダウンロードエラー
				sfDispSiteError(FTP_DOWNLOAD_ERROR);
			}
		} else {
			//FTPログインエラー
			sfDispSiteError(FTP_LOGIN_ERROR);
		}
	} else {
		//FTP接続エラー
		sfDispSiteError(FTP_CONNECT_ERROR);
	}
}
*/
$objView->assignobj($objPage);		//変数をテンプレートにアサインする
$objView->display(MAIN_FRAME);		//テンプレートの出力

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
					//ファイルである
					if(is_file($extract_file . "/" . $file)) {
						//sqlファイルである
						if(ereg("\.sql$", $file)) {
							//ファイルを取り入れる
							require_once($extract_file . "/" . $file);
							//クエリー実行クラス
							$objQuery = new SC_Query;
							//クエリーの実行
							$objQuery->query($sql);
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
