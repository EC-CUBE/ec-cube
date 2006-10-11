<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
$INSTALL_DIR = realpath(dirname( __FILE__));

class LC_Page {
	function LC_Page() {
		$this->arrDB_TYPE = array(
			'pgsql' => 'PostgreSQL',
			'mysql' => 'MySQL'	
		);
		$this->arrDB_PORT = array(
			'pgsql' => '5432',
			'mysql' => '3306'	
		);
	}
}

$objPage = new LC_Page();

// テンプレートコンパイルディレクトリの書込み権限チェック
$temp_dir = $INSTALL_DIR . '/temp';
$mode = lfGetFileMode($temp_dir);

if($mode != '777') {
	sfErrorHeader($temp_dir . "にユーザ書込み権限(777)を付与して下さい。", true);
	exit;
}

$objView = new SC_InstallView($INSTALL_DIR . '/templates', $INSTALL_DIR . '/temp');

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
	
	$skip = $_POST["db_skip"];

	// スキップする場合には完了画面へ遷移
	if ($skip == "on") {
		// 設定ファイルの生成
		lfMakeConfigFile();
		$objPage = lfDispComplete($objPage);
		break;
	}
	
	// テーブルの作成
	$objPage->arrErr = lfExecuteSQL("./create_table_".$arrRet['db_type'].".sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port']); 
	if(count($objPage->arrErr) == 0) {
		$objPage->tpl_message.="○：テーブルの作成に成功しました。<br>";
	} else {
		$objPage->tpl_message.="×：テーブルの作成に失敗しました。<br>";		
	}

	// ビューの作成
	if(count($objPage->arrErr) == 0 and $arrRet['db_type'] == 'pgsql') {
		// ビューの作成
		$objPage->arrErr = lfExecuteSQL("./create_view.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port']); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：ビューの作成に成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：ビューの作成に失敗しました。<br>";		
		}
	}	
	
	// 初期データの作成
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./insert_data.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port']); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：初期データの作成に成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：初期データの作成に失敗しました。<br>";		
		}
	}	
	
	// カラムコメントの書込み
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./column_comment.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port']); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：カラムコメントの書込みに成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：カラムコメントの書込みに失敗しました。<br>";		
		}
	}	
	
	// テーブルコメントの書込み
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./table_comment.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port']); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：テーブルコメントの書込みに成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：テーブルコメントの書込みに失敗しました。<br>";		
		}
	}


	if(count($objPage->arrErr) == 0) {
		// 設定ファイルの生成
		lfMakeConfigFile();
		$objPage = lfDispStep3($objPage);
		$objPage->tpl_mode = 'complete';
	} else {
		$objPage = lfDispStep3($objPage);
	}
	break;
// テーブル類削除
case 'drop':
	// 入力データを渡す。
	$arrRet =  $objDBParam->getHashArray();
	
	if ($arrRet['db_type'] == 'pgsql'){
		// ビューの削除
		$objPage->arrErr = lfExecuteSQL("./drop_view.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port'], false); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：ビューの削除に成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：ビューの削除に失敗しました。<br>";		
		}
	}


	// テーブルの削除
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./drop_table.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port'], false); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：テーブルの削除に成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：テーブルの削除に失敗しました。<br>";		
		}
	}
	$objPage = lfDispStep3($objPage);
	break;
// 完了画面
case 'complete':

	// ショップマスタ情報の書き込み
	$arrRet =  $objDBParam->getHashArray();
	
	$dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].$port."/".$arrRet['db_name'];
	$sqlval['shop_name'] = $objWebParam->getValue('shop_name');
	$sqlval['email01'] = $objWebParam->getValue('admin_mail');
	$sqlval['email02'] = $objWebParam->getValue('admin_mail');
	$sqlval['email03'] = $objWebParam->getValue('admin_mail');
	$sqlval['email04'] = $objWebParam->getValue('admin_mail');
	$sqlval['email05'] = $objWebParam->getValue('admin_mail');
	$sqlval['top_tpl'] = "default1";
	$sqlval['product_tpl'] = "default1";
	$sqlval['detail_tpl'] = "default1";
	$sqlval['mypage_tpl'] = "default1";
	$objQuery = new SC_Query($dsn);
	$cnt = $objQuery->count("dtb_baseinfo");
	if($cnt > 0) {
		$objQuery->update("dtb_baseinfo", $sqlval);
	} else {		
		$objQuery->insert("dtb_baseinfo", $sqlval);		
	}
	global $GLOBAL_ERR;
	$GLOBAL_ERR = "";
	$objPage = lfDispComplete($objPage);
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

// SiteInfoを読み込まない
$objView->assignobj($objPage);
$objView->display('install_frame.tpl');
//-----------------------------------------------------------------------------------------------------------------------------------
// ようこそ画面の表示
function lfDispWelcome($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hiddenに入力値を保持
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hiddenに入力値を保持
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'welcome.tpl';
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
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'step0.tpl';
	$objPage->tpl_mode = 'step0';
	
	// プログラムで書込みされるファイル・ディレクトリ
	$arrWriteFile = array(
		"data/install.inc",
		"html/user_data",
		"html/upload",
		"data/Smarty/templates_c",		
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
					$mess.= ">> ×：$val($mode) にユーザ書込み権限(777)を付与して下さい。<br>";
					$err_file = true;										
				}
			} else {
				if($mode == "666") {
					$mess.= ">> ○：$val($mode) は問題ありません。<br>";					
				} else {
					$mess.= ">> ×：$val($mode) にユーザ書込み権限(666)を付与して下さい。<br>";
					$err_file = true;							
				}
			}	
			
		} else {
			$mess.= ">> ×：$val が見つかりません。<br>";
			$err_file = true;
		}
	}
	
	// 権限エラー等が発生していない場合
	if(!$err_file) {
		$path = "../../data/Smarty/templates_c/admin";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../../html/upload/save_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../../html/upload/temp_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../../html/upload/graph_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../../html/upload/csv";
		if(!file_exists($path)) {
			mkdir($path);
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
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'step0_1.tpl';
	$objPage->tpl_mode = 'step0_1';
	// ファイルコピー
	$objPage->copy_mess = sfCopyDir("./user_data/", "../../html/user_data/", $objPage->copy_mess);
	$objPage->copy_mess = sfCopyDir("./save_image/", "../../html/upload/save_image/", $objPage->copy_mess);	
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
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'step1.tpl';
	$objPage->tpl_mode = 'step1';
	return $objPage;
}

// STEP2画面の表示
function lfDispStep2($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hiddenに入力値を保持
	$objPage->arrHidden = $objWebParam->getHashArray();
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'step2.tpl';
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
	$objPage->tpl_db_skip = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'step3.tpl';
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
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'complete.tpl';
	$objPage->tpl_mode = 'complete';
	return $objPage;
}

// WEBパラメータ情報の初期化
function lfInitWebParam($objWebParam) {
	
	if(defined(ROOT_DIR)) {
		$install_dir = ROOT_DIR;
	} else {
		$install_dir = realpath(dirname( __FILE__) . "/../../") . "/";
	}
	
	if(defined(SITE_URL)) {
		$normal_url = SITE_URL;
	} else {
		$normal_url = "http://" . $_SERVER['HTTP_HOST'] . "/";
	}
	
	if(defined(SSL_URL)) {
		$normal_url = SSL_URL;
	} else {
		$secure_url = "http://" . $_SERVER['HTTP_HOST'] . "/";
	}

	$objWebParam->addParam("店名", "shop_name", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objWebParam->addParam("管理者メールアドレス", "admin_mail", MTEXT_LEN, "", array("EXIST_CHECK","EMAIL_CHECK","EMAIL_CHAR_CHECK","MAX_LENGTH_CHECK"));
	$objWebParam->addParam("インストールディレクトリ", "install_dir", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $install_dir);
	$objWebParam->addParam("URL(通常)", "normal_url", MTEXT_LEN, "", array("EXIST_CHECK","URL_CHECK","MAX_LENGTH_CHECK"), $normal_url);
	$objWebParam->addParam("URL(セキュア)", "secure_url", MTEXT_LEN, "", array("EXIST_CHECK","URL_CHECK","MAX_LENGTH_CHECK"), $secure_url);
	$objWebParam->addParam("ドメイン", "domain", MTEXT_LEN, "", array("MAX_LENGTH_CHECK"));	

	return $objWebParam;
}

// DBパラメータ情報の初期化
function lfInitDBParam($objDBParam) {
	
	if(defined(DB_SERVER)) {
		$db_server = DB_SERVER;
	} else {
		$db_server = "127.0.0.1";
	}
	
	if(defined(DB_PORT)) {
		$db_port = DB_PORT;
	} else {
		$db_port = "";
	}
	
	if(defined(DB_NAME)) {
		$db_name = DB_NAME;
	} else {
		$db_name = "eccube_db";
	}
		
	if(defined(DB_USER)) {
		$db_user = DB_USER;
	} else {
		$db_user = "eccube_db_user";				
	}
	
	if(defined(DB_PASSWORD)) {
		$db_password = DB_PASSWORD;
	} else {
		$db_password = "";				
	}

	print(DB_PASSWORD . $db_password);
	print(DB_PORT . $db_port);
			
	$objDBParam->addParam("DBの種類", "db_type", INT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objDBParam->addParam("DBサーバ", "db_server", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_server);
	$objDBParam->addParam("DBポート", "db_port", INT_LEN, "", array("MAX_LENGTH_CHECK"), $db_port);
	$objDBParam->addParam("DB名", "db_name", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_name);
	$objDBParam->addParam("DBユーザ", "db_user", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_user);
	$objDBParam->addParam("DBパスワード", "db_password", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_password);	
	

	
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
		$dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].":".$arrRet['db_port']."/".$arrRet['db_name'];
		// Debugモード指定
		$options['debug'] = 3;
		$objDB = DB::connect($dsn, $options);
		// 接続エラー
		if(PEAR::isError($objDB)) {
			$objErr->arrErr['all'] = ">> " . $objDB->message . "<br>";
			// エラー文を取得する
			ereg("\[(.*)\]", $objDB->userinfo, $arrKey);
			$objErr->arrErr['all'].= $arrKey[0] . "<br>";
			gfPrintLog($objDB->userinfo, "./temp/install.log");
		}
	}
	return $objErr->arrErr;
}

// SQL文の実行
function lfExecuteSQL($filepath, $db_user, $db_password, $db_server, $db_name, $db_type, $db_port, $disp_err = true) {
	$arrErr = array();

	if(!file_exists($filepath)) {
		$arrErr['all'] = ">> スクリプトファイルが見つかりません";
	} else {
  		if($fp = fopen($filepath,"r")) {
			$sql = fread($fp, filesize($filepath));
			fclose($fp);
		}

		$dsn = $db_type."://".$db_user.":".$db_password."@".$db_server.":".$db_port."/".$db_name;
		
		$objDB = DB::connect($dsn);
		// 接続エラー
		if(!PEAR::isError($objDB)) {
			// 改行、タブを1スペースに変換
			$sql = preg_replace("/[\r\n\t]/"," ",$sql);
			$sql_split = split(";",$sql);
			foreach($sql_split as $key => $val){
				if (trim($val) != "") {
					$ret = $objDB->query($val);
					if(PEAR::isError($ret) and $disp_err) {
						$arrErr['all'] = ">> " . $ret->message . "<br>";
						// エラー文を取得する
						ereg("\[(.*)\]", $ret->userinfo, $arrKey);
						$arrErr['all'].= $arrKey[0] . "<br>";
						$objPage->update_mess.=">> テーブル構成の変更に失敗しました。<br>";
						gfPrintLog($ret->userinfo, "./temp/install.log");
					}
				}
			}
			
		} else {
			$arrErr['all'] = ">> " . $objDB->message;
			gfPrintLog($objDB->userinfo, "./temp/install.log");
		}
	}
	return $arrErr;
}

// 設定ファイルの作成
function lfMakeConfigFile() {
	global $objWebParam;
	global $objDBParam;
	global $port;
	
	$filepath = $objWebParam->getValue('install_dir') . "/data/install.inc";
	$domain = $objWebParam->getValue('domain');
	
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
	"    define ('DB_TYPE', '" . $objDBParam->getValue('db_type') . "');\n" .
	"    define ('DB_USER', '" . $objDBParam->getValue('db_user') . "');\n" . 
	"    define ('DB_PASSWORD', '" . $objDBParam->getValue('db_password') . "');\n" .
	"    define ('DB_SERVER', '" . $objDBParam->getValue('db_server') . "');\n" .
	"    define ('DB_NAME', '" . $objDBParam->getValue('db_name') . "');\n" .
	"    define ('DB_PORT', '" . $objDBParam->getValue('db_port') .  "');\n" .
	"?>";
	
	if($fp = fopen($filepath,"w")) {
		fwrite($fp, $config_data);
		fclose($fp);
	}
}

?>