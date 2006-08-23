<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->arrDB_TYPE = array(
			1 => 'PostgreSQL',
			// 2 => 'mySQL'	// 未対応
		);
		
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

// パラメータ管理クラス
$objWebParam = new SC_FormParam();
$objDBParam = new SC_FormParam();
// パラメータ情報の初期化
$objWebParam = lfInitWebParam($objWebParam);
$objDBParam = lfInitDBParam($objDBParam);

//フォーム配列の取得
$objWebParam->setParam($_POST);
$objDBParam->setParam($_POST);

switch($_POST['mode']) {
// ようこそ
case 'welcome':
	$objPage = lfDispStep0($objPage);
	break;
// アクセス権限のチェック
case 'step0':
	$objPage = lfDispStep0_1($objPage);
	break;	
// ファイルのコピー
case 'step0_1':
	$objPage = lfDispStep1($objPage);
	break;	
// WEBサイトの設定
case 'step1':
	//入力値のエラーチェック
	$objPage->arrErr = lfCheckWEBError($objWebParam);
	if(count($objPage->arrErr) == 0) {
		$objPage = lfDispStep2($objPage);
	} else {
		$objPage = lfDispStep1($objPage);
	}
	break;
// データベースの設定
case 'step2':
	//入力値のエラーチェック
	$objPage->arrErr = lfCheckDBError($objDBParam);
	if(count($objPage->arrErr) == 0) {
		$objPage = lfDispStep3($objPage);
	} else {
		$objPage = lfDispStep2($objPage);
	}
	break;
// テーブルの作成
case 'step3':
	// 入力データを渡す。
	$arrRet =  $objDBParam->getHashArray();
	// テーブルの作成
	$objPage->arrErr = lfExecuteSQL("./create_table.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name']); 
	if(count($objPage->arrErr) == 0) {
		$objPage->tpl_message.="○：テーブルの作成に成功しました。<br>";
	} else {
		$objPage->tpl_message.="×：テーブルの作成に失敗しました。<br>";		
	}

	// ビューの作成
	if(count($objPage->arrErr) == 0) {
		// ビューの作成
		$objPage->arrErr = lfExecuteSQL("./create_view.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name']); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：ビューの作成に成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：ビューの作成に失敗しました。<br>";		
		}
	}	
	
	// 初期データの作成
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./insert_data.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name']); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：初期データの作成に成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：初期データの作成に失敗しました。<br>";		
		}
	}	
	
	// カラムコメントの書込み
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./column_comment.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name']); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：カラムコメントの書込みに成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：カラムコメントの書込みに失敗しました。<br>";		
		}
	}	
	
	// テーブルコメントの書込み
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./table_comment.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name']); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：テーブルコメントの書込みに成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：テーブルコメントの書込みに失敗しました。<br>";		
		}
	}
	
	if(count($objPage->arrErr) == 0) {
		lfMakeConfigFile();
		$objPage = lfDispComplete($objPage);
	} else {
		$objPage = lfDispStep3($objPage);
	}
	break;
case 'return_step0':
	$objPage = lfDispStep0($objPage);
	break;	
case 'return_step1':
	$objPage = lfDispStep1($objPage);
	break;
case 'return_step2':
	$objPage = lfDispStep2($objPage);
	break;
case 'return_welcome':
default:
	$objPage = lfDispWelcome($objPage);
	break;
}

//フォーム用のパラメータを返す
$objPage->arrForm = $objWebParam->getFormParamList();
$objPage->arrForm = array_merge($objPage->arrForm, $objDBParam->getFormParamList());

$objView->assignobj($objPage);
$objView->display('install/install_frame.tpl');
//-----------------------------------------------------------------------------------------------------------------------------------
// ようこそ画面の表示
function lfDispWelcome($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hiddenに入力値を保持
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hiddenに入力値を保持
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->tpl_mainpage = 'install/welcome.tpl';
	$objPage->tpl_mode = 'welcome';
	return $objPage;
}

// STEP0画面の表示(ファイル権限チェック) 
function lfDispStep0($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hiddenに入力値を保持
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hiddenに入力値を保持
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->tpl_mainpage = 'install/step0.tpl';
	$objPage->tpl_mode = 'step0';
	
	// プログラムで書込みされるファイル・ディレクトリ
	$arrWriteFile = array(
		"html/install.inc",
		"html/user_data",
		"html/upload",
		"data/upload/save_image",
		"data/upload/temp_image",
		"data/upload/graph_image",		
		"data/upload/csv",
		"data/Smarty/templates_c",
		"data/Smarty/templates_c/admin",		
		"data/update",
		"data/logs",
	);
	
	$mess = "";
	$err_file = false;
	foreach($arrWriteFile as $val) {
		$path = "../../" . $val;		
		if(file_exists($path)) {
			$mode = lfGetFileMode("../../" . $val);
			
			// ディレクトリの場合
			if(is_dir($path)) {
				if($mode == "777") {
					$mess.= ">> ○：$val($mode) は問題ありません。<br>";					
				} else {
					$mess.= ">> ×：$val($mode) にユーザ書込み権限を付与して下さい。<br>";
					$err_file = true;										
				}
			} else {
				if($mode == "666") {
					$mess.= ">> ○：$val($mode) は問題ありません。<br>";					
				} else {
					$mess.= ">> ×：$val($mode) にユーザ書込み権限を付与して下さい。<br>";
					$err_file = true;							
				}
			}	
			
		} else {
			$mess.= ">> ×：$val が見つかりません。<br>";
			$err_file = true;
		}
	}
	
	$objPage->mess = $mess;
	$objPage->err_file = $err_file;

	return $objPage;
}


// STEP0_1画面の表示(ファイルのコピー) 
function lfDispStep0_1($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hiddenに入力値を保持
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hiddenに入力値を保持
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->tpl_mainpage = 'install/step0_1.tpl';
	$objPage->tpl_mode = 'step0_1';	
	$objPage->copy_mess = lfCopyDir("./user_data/", "../../html/user_data/", $objPage->copy_mess);
	return $objPage;
}

function lfGetFileMode($path) {
	$mode = substr(sprintf('%o', fileperms($path)), -3);
	return $mode;
}

// STEP1画面の表示
function lfDispStep1($objPage) {
	global $objDBParam;
	// hiddenに入力値を保持
	$objPage->arrHidden = $objDBParam->getHashArray();
	$objPage->tpl_mainpage = 'install/step1.tpl';
	$objPage->tpl_mode = 'step1';
	return $objPage;
}

// STEP2画面の表示
function lfDispStep2($objPage) {
	global $objWebParam;
	// hiddenに入力値を保持
	$objPage->arrHidden = $objWebParam->getHashArray();
	$objPage->tpl_mainpage = 'install/step2.tpl';
	$objPage->tpl_mode = 'step2';
	return $objPage;
}

// STEP3画面の表示
function lfDispStep3($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hiddenに入力値を保持
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hiddenに入力値を保持
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->tpl_mainpage = 'install/step3.tpl';
	$objPage->tpl_mode = 'step3';
	return $objPage;
}

// 完了画面の表示
function lfDispComplete($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hiddenに入力値を保持
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hiddenに入力値を保持
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->tpl_mainpage = 'install/complete.tpl';
	$objPage->tpl_mode = 'complete';
	return $objPage;
}

// WEBパラメータ情報の初期化
function lfInitWebParam($objWebParam) {
	
	$install_dir = ereg_replace("html/", "", $_SERVER['DOCUMENT_ROOT']);
	$normal_url = "http://" . $_SERVER['HTTP_HOST'] . "/";
	$secure_url = "https://" . $_SERVER['HTTP_HOST'] . "/";
	$domain = ereg_replace("^[a-zA-Z0-9_~=&\?\/-]+\.", "", $_SERVER['HTTP_HOST']);
		
	$objWebParam->addParam("インストールディレクトリ", "install_dir", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $install_dir);
	$objWebParam->addParam("URL(通常)", "normal_url", MTEXT_LEN, "", array("EXIST_CHECK","URL_CHECK","MAX_LENGTH_CHECK"), $normal_url);
	$objWebParam->addParam("URL(セキュア)", "secure_url", MTEXT_LEN, "", array("EXIST_CHECK","URL_CHECK","MAX_LENGTH_CHECK"), $secure_url);
	$objWebParam->addParam("ドメイン", "domain", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $domain);	
	
	return $objWebParam;
}

// WEBパラメータ情報の初期化
function lfInitDBParam($objDBParam) {
	
	$db_server = "127.0.0.1";
	$db_name = "eccube_db";
	$db_user = "eccube_db_user";
	
	$objDBParam->addParam("DBの種類", "db_type", INT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objDBParam->addParam("DBサーバ", "db_server", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_server);
	$objDBParam->addParam("DB名", "db_name", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_name);
	$objDBParam->addParam("DBユーザ", "db_user", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_user);
	$objDBParam->addParam("DBパスワード", "db_password", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"));	
	return $objDBParam;
}

// 入力内容のチェック
function lfCheckWebError($objFormParam) {
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	return $objErr->arrErr;
}

// 入力内容のチェック
function lfCheckDBError($objFormParam) {
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	if(count($objErr->arrErr) == 0) {
		// 接続確認
		$dsn = "pgsql://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server']."/".$arrRet['db_name'];
		$objDB = DB::connect($dsn);
		// 接続エラー
		if(PEAR::isError($objDB)) {
			$objErr->arrErr['all'] = ">> " . $objDB->message;
			gfPrintLog($objDB->userinfo);
		}
	}		
	return $objErr->arrErr;
}

// SQL文の実行
function lfExecuteSQL($filepath, $db_user, $db_password, $db_server, $db_name) {
	$arrErr = array();
	
	if(!file_exists($filepath)) {
		$arrErr['all'] = ">> スクリプトファイルが見つかりません";
	} else {
  		if($fp = fopen($filepath,"r")) {
			$sql = fread($fp, filesize($filepath));
			fclose($fp);
		}
			
		$dsn = "pgsql://".$db_user.":".$db_password."@".$db_server."/".$db_name;
		$objDB = DB::connect($dsn);
		// 接続エラー
		if(!PEAR::isError($objDB)) {
			$ret = $objDB->query($sql);
			if(PEAR::isError($ret)) {
				$arrErr['all'] = ">> " . $ret->message;
				gfPrintLog($ret->userinfo);
			}
		} else {
			$arrErr['all'] = ">> " . $objDB->message;
			gfPrintLog($objDB->userinfo);
		}
	}
	return $arrErr;
}

// 設定ファイルの作成
function lfMakeConfigFile() {
	global $objWebParam;
	global $objDBParam;
	
	$filepath = $objWebParam->getValue('install_dir') . "/html/install.inc";
	$domain = $objWebParam->getValue('domain');
	if(!ereg("^\.", $domain)) {
		$domain = "." . $domain;
	}
	
	$root_dir = $objWebParam->getValue('install_dir');
	if (!ereg("/$", $root_dir)) {
		$root_dir = $root_dir . "/";
	}
	
	$config_data = 
	"<?php\n".
	"    define ('ECCUBE_INSTALL', 'ON');\n" .
	"    define ('ROOT_DIR', '" . $root_dir . "');\n" . 
	"    define ('SITE_URL', '" . $objWebParam->getValue('normal_url') . "');\n" .
	"    define ('SSL_URL', '" . $objWebParam->getValue('secure_url') . "');\n" .
	"    define ('DOMAIN_NAME', '" . $domain . "');\n" .
	"    define ('DB_USER', '" . $objDBParam->getValue('db_user') . "');\n" . 
	"    define ('DB_PASSWORD', '" . $objDBParam->getValue('db_password') . "');\n" .
	"    define ('DB_SERVER', '" . $objDBParam->getValue('db_server') . "');\n" .
	"    define ('DB_NAME', '" . $objDBParam->getValue('db_name') . "');\n" .
	"?>";
	
	if($fp = fopen($filepath,"w")) {
		fwrite($fp, $config_data);
		fclose($fp);
	}
}

// ディレクトリ以下のファイルを再帰的にコピー
function lfCopyDir($src, $des, $mess, $override = false){
	if(!is_dir($src)){
		return false;
	}

	$oldmask = umask(0);
	$mod= stat($src);
	
	// ディレクトリがなければ作成する
	if(!file_exists($des)) {
		mkdir($des, $mod[2]);
	}
	
	$fileArray=glob( $src."*" );
	foreach( $fileArray as $key => $data_ ){
		// CVS管理ファイルはコピーしない
		if(ereg("/CVS/Entries", $data_)) {
			break;
		}
		if(ereg("/CVS/Repository", $data_)) {
			break;
		}
		if(ereg("/CVS/Root", $data_)) {
			break;
		}
		
		mb_ereg("^(.*[\/])(.*)",$data_, $matches);
		$data=$matches[2];
		if( is_dir( $data_ ) ){
			$mess = lfCopyDir( $data_.'/', $des.$data.'/', $mess);
		}else{
			if(!$override && file_exists($des.$data)) {
				$mess.= $des.$data . "：ファイルが存在します\n";
			} else {
				if(copy( $data_, $des.$data)) {
					$mess.= $des.$data . "：コピー成功\n";
				} else {
					$mess.= $des.$data . "：コピー失敗\n";
				}
			}
			$mod=stat($data_ );
		}
	}
	umask($oldmask);
	return $mess;
}
?>
