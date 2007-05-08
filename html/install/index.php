<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
$INSTALL_DIR = realpath(dirname( __FILE__));
require_once("../" . HTML2DATA_DIR . "module/Request.php");

define("INSTALL_LOG", "./temp/install.log");

class LC_Page {
	function LC_Page() {
		$this->arrDB_TYPE = array(
			'pgsql' => 'PostgreSQL',
			'mysql' => 'MySQL'	
		);
		$this->arrDB_PORT = array(
			'pgsql' => '',
			'mysql' => ''	
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
	//$objPage = lfDispAgreement($objPage);
	$objPage = lfDispStep0($objPage);
	//$objPage->tpl_onload .= "fnChangeVisible('agreement_yes', 'next');";
	break;

/* 現在保留中

// 使用許諾契約書の同意
case 'agreement':
	$objPage = lfDispStep0($objPage);
	break;
*/	
	
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
	$dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].":".$arrRet['db_port']."/".$arrRet['db_name'];
	
	/*
		lfAddTableは、バージョンアップ等で追加テーブルが発生した場合に実行する。
		（ＤＢ構成の下位互換のためスキップ時も強制）
	*/
	// テーブルが存在しない場合に追加される。
	$objPage->arrErr = lfAddTable("dtb_session", $dsn);			// セッション管理テーブル
	$objPage->arrErr = lfAddTable("dtb_module", $dsn);			// モジュール管理テーブル
	$objPage->arrErr = lfAddTable("dtb_campaign_order", $dsn);	// キャンペーン受注テーブル
	$objPage->arrErr = lfAddTable("dtb_mobile_kara_mail", $dsn);	// 空メール管理テーブル
	$objPage->arrErr = lfAddTable("dtb_mobile_ext_session_id", $dsn);	// セッションID管理テーブル
	$objPage->arrErr = lfAddTable("dtb_site_control", $dsn);	// サイト情報管理テーブル
	$objPage->arrErr = lfAddTable("dtb_trackback", $dsn);	// トラックバック管理テーブル
	
	
	// カラムを追加
	lfAddColumn($dsn);

	// データを追加
	lfAddData($dsn);
	
	if(count($objPage->arrErr) == 0) {
		// スキップする場合には次画面へ遷移
		$skip = $_POST["db_skip"];
		if ($skip == "on") {
			// 設定ファイルの生成
			lfMakeConfigFile();
			$objPage = lfDispComplete($objPage);
			//$objPage = lfDispStep4($objPage);
			break;
		}
	}
	
	// テーブルの作成
	$objPage->arrErr = lfExecuteSQL("./sql/create_table_".$arrRet['db_type'].".sql", $dsn); 
	if(count($objPage->arrErr) == 0) {
		$objPage->tpl_message.="○：テーブルの作成に成功しました。<br>";
	} else {
		$objPage->tpl_message.="×：テーブルの作成に失敗しました。<br>";		
	}

	// ビューの作成
	if(count($objPage->arrErr) == 0 and $arrRet['db_type'] == 'pgsql') {
		// ビューの作成
		$objPage->arrErr = lfExecuteSQL("./sql/create_view.sql", $dsn); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：ビューの作成に成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：ビューの作成に失敗しました。<br>";		
		}
	}	
	
	// 初期データの作成
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./sql/insert_data.sql", $dsn); 
		
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：初期データの作成に成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：初期データの作成に失敗しました。<br>";		
		}
	}	
	
	// カラムコメントの書込み
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./sql/column_comment.sql", $dsn); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：カラムコメントの書込みに成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：カラムコメントの書込みに失敗しました。<br>";		
		}
	}	
	
	// テーブルコメントの書込み
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./sql/table_comment.sql", $dsn); 
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
		$objPage->tpl_mode = 'step4';
	} else {
		$objPage = lfDispStep3($objPage);
	}
	break;
case 'step4':
	$objPage = lfDispStep4($objPage);
	break;
	
// テーブル類削除
case 'drop':
	// 入力データを渡す。
	$arrRet =  $objDBParam->getHashArray();
	$dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].":".$arrRet['db_port']."/".$arrRet['db_name'];
	
	// 追加テーブルがあれば削除する。
	lfDropTable("dtb_module", $dsn);
	lfDropTable("dtb_session", $dsn);
	lfDropTable("dtb_campaign_order", $dsn);
	lfDropTable("dtb_mobile_ext_session_id", $dsn);
	lfDropTable("dtb_mobile_kara_mail", $dsn);
	lfDropTable("dtb_site_control", $dsn);
	lfDropTable("dtb_trackback", $dsn);
			
	if ($arrRet['db_type'] == 'pgsql'){
		// ビューの削除
		$objPage->arrErr = lfExecuteSQL("./sql/drop_view.sql", $dsn, false); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="○：ビューの削除に成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：ビューの削除に失敗しました。<br>";		
		}
	}

	// テーブルの削除
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./sql/drop_table.sql", $dsn, false); 
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
	
	$dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].":".$arrRet['db_port']."/".$arrRet['db_name'];
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

	// 管理者登録
	$login_id = $objWebParam->getValue('login_id');
	$login_pass = sha1($objWebParam->getValue('login_pass') . ":" . AUTH_MAGIC);
	
	$sql = "DELETE FROM dtb_member WHERE login_id = ?";
	$objQuery->query($sql, array($login_id));	

	$sql = "INSERT INTO dtb_member (name, login_id, password, creator_id, authority, work, del_flg, rank, create_date, update_date)
			VALUES ('管理者',?,?,0,0,1,0,1, now(), now());";
	
	$objQuery->query($sql, array($login_id, $login_pass));		
	
	global $GLOBAL_ERR;
	$GLOBAL_ERR = "";
	$objPage = lfDispComplete($objPage);
	
	// サイト情報を送信
	$req = new HTTP_Request("http://www.ec-cube.net/mall/use_site.php");
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	
	$arrSendData = array();
	foreach($_POST as $key => $val){
		if (ereg("^senddata_*", $key)){
			$arrSendDataTmp = array(str_replace("senddata_", "", $key) => $val);
			$arrSendData = array_merge($arrSendData, $arrSendDataTmp);
		}
	}
	
	$req->addPostDataArray($arrSendData);
	
	if (!PEAR::isError($req->sendRequest())) {
		$response1 = $req->getResponseBody();
	} else {
		$response1 = "";
	}
	$req->clearPostData();
	
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
case 'return_step3':
	$objPage = lfDispStep3($objPage);
	break;
case 'return_agreement':
	$objPage = lfDispAgreement($objPage);
	$objPage->tpl_onload .= "fnChangeVisible('agreement_yes', 'next');";
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
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
	$objPage->tpl_mainpage = 'welcome.tpl';
	$objPage->tpl_mode = 'welcome';
	return $objPage;
}

// 使用許諾契約書の表示
function lfDispAgreement($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hiddenに入力値を保持
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hiddenに入力値を保持
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->arrHidden['agreement'] = $_POST['agreement'];	
	$objPage->tpl_mainpage = 'agreement.tpl';
	$objPage->tpl_mode = 'agreement';
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
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
	$objPage->tpl_mainpage = 'step0.tpl';
	$objPage->tpl_mode = 'step0';
	
	// プログラムで書込みされるファイル・ディレクトリ
	$arrWriteFile = array(
		".." . HTML2DATA_DIR . "install.php",
		"../user_data",
		"../cp",
		"../upload",
		".." . HTML2DATA_DIR . "Smarty/templates_c",
		".." . HTML2DATA_DIR . "downloads",
		".." . HTML2DATA_DIR . "logs"
	);
	
	$mess = "";
	$err_file = false;
	foreach($arrWriteFile as $val) {
		if(file_exists($val)) {
			$mode = lfGetFileMode($val);
			$real_path = realpath($val);
						
			// ディレクトリの場合
			if(is_dir($val)) {
				if($mode == "777") {
					$mess.= ">> ○：$real_path($mode) <br>アクセス権限は正常です。<br>";					
				} else {
					$mess.= ">> ×：$real_path($mode) <br>ユーザ書込み権限(777)を付与して下さい。<br>";
					$err_file = true;										
				}
			} else {
				if($mode == "666") {
					$mess.= ">> ○：$real_path($mode) <br>アクセス権限は正常です。<br>";					
				} else {
					$mess.= ">> ×：$real_path($mode) <br>ユーザ書込み権限(666)を付与して下さい。<br>";
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
		$path = ".." . HTML2DATA_DIR . "Smarty/templates_c/admin";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../upload/temp_template";
		if(!file_exists($path)) {
			mkdir($path);
		}		
		$path = "../upload/save_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../upload/temp_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../upload/graph_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
        $path = "../upload/mobile_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../upload/csv";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = ".." . HTML2DATA_DIR . "downloads/module";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = ".." . HTML2DATA_DIR . "downloads/update";
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
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
	$objPage->tpl_mainpage = 'step0_1.tpl';
	$objPage->tpl_mode = 'step0_1';
	// ファイルコピー
	$objPage->copy_mess = sfCopyDir("./user_data/", "../user_data/", $objPage->copy_mess);
	$objPage->copy_mess = sfCopyDir("./save_image/", "../upload/save_image/", $objPage->copy_mess);	
	return $objPage;
}

// STEP0_2画面の表示(ファイルのコピー) 
function lfDispStep0_2($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hiddenに入力値を保持
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hiddenに入力値を保持
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
	$objPage->tpl_mainpage = 'step0_1.tpl';
	$objPage->tpl_mode = 'step0_1';
	// ファイルコピー
	$objPage->copy_mess = sfCopyDir("./user_data/", "../user_data/", $objPage->copy_mess);
	$objPage->copy_mess = sfCopyDir("./save_image/", "../upload/save_image/", $objPage->copy_mess);	
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
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
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
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
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
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
	$objPage->tpl_db_skip = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'step3.tpl';
	$objPage->tpl_mode = 'step3';
	return $objPage;
}

// STEP4画面の表示
function lfDispStep4($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hiddenに入力値を保持
	$objPage->arrHidden = $objWebParam->getHashArray();
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	// hiddenに入力値を保持
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
	
	$normal_url = $objWebParam->getValue('normal_url');
	// 語尾に'/'をつける
	if (!ereg("/$", $normal_url)) $normal_url = $normal_url . "/";

	$arrDbParam = $objDBParam->getHashArray();
	$dsn = $arrDbParam['db_type']."://".$arrDbParam['db_user'].":".$arrDbParam['db_password']."@".$arrDbParam['db_server'].":".$arrDbParam['db_port']."/".$arrDbParam['db_name'];

	$objPage->tpl_site_url = $normal_url;
	$objPage->tpl_shop_name = $objWebParam->getValue('shop_name');
	$objPage->tpl_cube_ver = ECCUBE_VERSION;
	$objPage->tpl_php_ver = phpversion();
	$objPage->tpl_db_ver = sfGetDBVersion($dsn);
	$objPage->tpl_db_skip = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'step4.tpl';
	$objPage->tpl_mode = 'complete';
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
	
	$secure_url = $objWebParam->getValue('secure_url');
	// 語尾に'/'をつける
	if (!ereg("/$", $secure_url)) {
		$secure_url = $secure_url . "/";
	}
	$objPage->tpl_sslurl = $secure_url;		
	return $objPage;
}

// WEBパラメータ情報の初期化
function lfInitWebParam($objWebParam) {
	
	if(defined('HTML_PATH')) {
		$install_dir = HTML_PATH;
	} else {
		$install_dir = realpath(dirname( __FILE__) . "/../") . "/";
	}
	
	if(defined('SITE_URL')) {
		$normal_url = SITE_URL;
	} else {
		$dir = ereg_replace("install/.*$", "", $_SERVER['REQUEST_URI']);
		$normal_url = "http://" . $_SERVER['HTTP_HOST'] . $dir;
	}
	
	if(defined('SSL_URL')) {
		$secure_url = SSL_URL;
	} else {
		$dir = ereg_replace("install/.*$", "", $_SERVER['REQUEST_URI']);
		$secure_url = "http://" . $_SERVER['HTTP_HOST'] . $dir;
	}

	// 店名、管理者メールアドレスを取得する。(再インストール時)
	if(defined('DEFAULT_DSN')) {
		$ret = sfTabaleExists("dtb_baseinfo", DEFAULT_DSN);
		if($ret) {
			$objQuery = new SC_Query();
			$arrRet = $objQuery->select("shop_name, email01", "dtb_baseinfo");
			$shop_name = $arrRet[0]['shop_name'];
			$admin_mail = $arrRet[0]['email01'];
		}
	}

	$objWebParam->addParam("店名", "shop_name", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $shop_name);
	$objWebParam->addParam("管理者：メールアドレス", "admin_mail", MTEXT_LEN, "", array("EXIST_CHECK","EMAIL_CHECK","EMAIL_CHAR_CHECK","MAX_LENGTH_CHECK"), $admin_mail);
	$objWebParam->addParam("管理者：ログインID", "login_id", MTEXT_LEN, "", array("EXIST_CHECK","EXIST_CHECK", "ALNUM_CHECK"));
	$objWebParam->addParam("管理者：パスワード", "login_pass", MTEXT_LEN, "", array("EXIST_CHECK","EXIST_CHECK", "ALNUM_CHECK"));
	$objWebParam->addParam("インストールディレクトリ", "install_dir", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $install_dir);
	$objWebParam->addParam("URL(通常)", "normal_url", MTEXT_LEN, "", array("EXIST_CHECK","URL_CHECK","MAX_LENGTH_CHECK"), $normal_url);
	$objWebParam->addParam("URL(セキュア)", "secure_url", MTEXT_LEN, "", array("EXIST_CHECK","URL_CHECK","MAX_LENGTH_CHECK"), $secure_url);
	$objWebParam->addParam("ドメイン", "domain", MTEXT_LEN, "", array("MAX_LENGTH_CHECK"));	
	
	return $objWebParam;
}

// DBパラメータ情報の初期化
function lfInitDBParam($objDBParam) {
		
	if(defined('DB_SERVER')) {
		$db_server = DB_SERVER;
	} else {
		$db_server = "127.0.0.1";
	}
	
	if(defined('DB_TYPE')) {
		$db_type = DB_TYPE;
	} else {
		$db_type = "";
	}
	
	if(defined('DB_PORT')) {
		$db_port = DB_PORT;
	} else {
		$db_port = "";
	}
		
	if(defined('DB_NAME')) {
		$db_name = DB_NAME;
	} else {
		$db_name = "eccube_db";
	}
		
	if(defined('DB_USER')) {
		$db_user = DB_USER;
	} else {
		$db_user = "eccube_db_user";				
	}
			
	$objDBParam->addParam("DBの種類", "db_type", INT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_type);
	$objDBParam->addParam("DBサーバ", "db_server", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_server);
	$objDBParam->addParam("DBポート", "db_port", INT_LEN, "", array("MAX_LENGTH_CHECK"), $db_port);
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
	
	// ディレクトリ名のみ取得する
	$normal_dir = ereg_replace("^https?://[a-zA-Z0-9_~=&\?\.\-]+", "", $arrRet['normal_url']);
	$secure_dir = ereg_replace("^https?://[a-zA-Z0-9_~=&\?\.\-]+", "", $arrRet['secure_url']);
	
	if($normal_dir != $secure_dir) {
		$objErr->arrErr['normal_url'] = "URLに異なる階層を指定することはできません。";
		$objErr->arrErr['secure_url'] = "URLに異なる階層を指定することはできません。";		
	}
	
	// ログインIDチェック
	$objErr->doFunc(array("管理者：ログインID",'login_id',ID_MIN_LEN , ID_MAX_LEN) ,array("NUM_RANGE_CHECK"));
	
	// パスワードのチェック
	$objErr->doFunc( array("管理者：パスワード",'login_pass',4 ,15 ) ,array( "NUM_RANGE_CHECK" ) );	
	
	return $objErr->arrErr;
}

// 入力内容のチェック
function lfCheckDBError($objFormParam) {
	global $objPage;
	
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	if(count($objErr->arrErr) == 0) {
		// 接続確認
		$dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].":".$arrRet['db_port']."/".$arrRet['db_name'];
		// Debugモード指定
		$options['debug'] = PEAR_DB_DEBUG;
		$objDB = DB::connect($dsn, $options);
		// 接続成功
		if(!PEAR::isError($objDB)) {
			// データベースバージョン情報の取得
			$objPage->tpl_db_version = sfGetDBVersion($dsn);			
		} else {
			$objErr->arrErr['all'] = ">> " . $objDB->message . "<br>";
			// エラー文を取得する
			ereg("\[(.*)\]", $objDB->userinfo, $arrKey);
			$objErr->arrErr['all'].= $arrKey[0] . "<br>";
			gfPrintLog($objDB->userinfo, INSTALL_LOG);
		}
	}
	return $objErr->arrErr;
}

// SQL文の実行
function lfExecuteSQL($filepath, $dsn, $disp_err = true) {
	$arrErr = array();
	
	if(!file_exists($filepath)) {
		$arrErr['all'] = ">> スクリプトファイルが見つかりません";
	} else {
  		if($fp = fopen($filepath,"r")) {
			$sql = fread($fp, filesize($filepath));
			fclose($fp);
		}
		// Debugモード指定
		$options['debug'] = PEAR_DB_DEBUG;
		$objDB = DB::connect($dsn, $options);
		// 接続エラー
		if(!PEAR::isError($objDB)) {
			// 改行、タブを1スペースに変換
			$sql = preg_replace("/[\r\n\t]/"," ",$sql);
			$sql_split = split(";",$sql);
			foreach($sql_split as $key => $val){
				if (trim($val) != "") {
					$ret = $objDB->query($val);
					if(PEAR::isError($ret) && $disp_err) {
						$arrErr['all'] = ">> " . $ret->message . "<br>";
						// エラー文を取得する
						ereg("\[(.*)\]", $ret->userinfo, $arrKey);
						$arrErr['all'].= $arrKey[0] . "<br>";
						$objPage->update_mess.=">> テーブル構成の変更に失敗しました。<br>";
						gfPrintLog($ret->userinfo, INSTALL_LOG);
					}
				}
			}			
		} else {
			$arrErr['all'] = ">> " . $objDB->message;
			gfPrintLog($objDB->userinfo, INSTALL_LOG);
		}
	}
	return $arrErr;
}

// 設定ファイルの作成
function lfMakeConfigFile() {
	global $objWebParam;
	global $objDBParam;
	
	$root_dir = $objWebParam->getValue('install_dir');
	// 語尾に'/'をつける
	if (!ereg("/$", $root_dir)) {
		$root_dir = $root_dir . "/";
	}
	
	$normal_url = $objWebParam->getValue('normal_url');
	// 語尾に'/'をつける
	if (!ereg("/$", $normal_url)) {
		$normal_url = $normal_url . "/";
	}
	
	$secure_url = $objWebParam->getValue('secure_url');
	// 語尾に'/'をつける
	if (!ereg("/$", $secure_url)) {
		$secure_url = $secure_url . "/";
	}
	
	// ディレクトリの取得
	$url_dir = ereg_replace("^https?://[a-zA-Z0-9_~=&\?\.\-]+", "", $normal_url);
	
	$data_path = sfRmDupSlash($root_dir . HTML2DATA_DIR);
    $data_path = realpath($data_path);
    // 語尾に'/'をつける
	if (!ereg("/$", $data_path)) {
		$data_path = $data_path . "/";
	}
	$filepath = $data_path . "install.php";
	
	$config_data = 
	"<?php\n".
	"    define ('ECCUBE_INSTALL', 'ON');\n" .
	"    define ('HTML_PATH', '" . $root_dir . "');\n" .	 
	"    define ('SITE_URL', '" . $normal_url . "');\n" .
	"    define ('SSL_URL', '" . $secure_url . "');\n" .
	"    define ('URL_DIR', '" . $url_dir . "');\n" .	
	"    define ('DOMAIN_NAME', '" . $objWebParam->getValue('domain') . "');\n" .
	"    define ('DB_TYPE', '" . $objDBParam->getValue('db_type') . "');\n" .
	"    define ('DB_USER', '" . $objDBParam->getValue('db_user') . "');\n" . 
	"    define ('DB_PASSWORD', '" . $objDBParam->getValue('db_password') . "');\n" .
	"    define ('DB_SERVER', '" . $objDBParam->getValue('db_server') . "');\n" .
	"    define ('DB_NAME', '" . $objDBParam->getValue('db_name') . "');\n" .
	"    define ('DB_PORT', '" . $objDBParam->getValue('db_port') .  "');\n" .
	"    define ('DATA_PATH', '".$data_path."');\n" .
    "    define ('MOBILE_HTML_PATH', HTML_PATH . 'mobile/');\n" .
    "    define ('MOBILE_SITE_URL', SITE_URL . 'mobile/');\n" .
    "    define ('MOBILE_SSL_URL', SSL_URL . 'mobile/');\n" .
    "    define ('MOBILE_URL_DIR', URL_DIR . 'mobile/');\n" .
	"?>";
	
	if($fp = fopen($filepath,"w")) {
		fwrite($fp, $config_data);
		fclose($fp);
	}
/* install_mobile.incは使用しない用に変更

	// モバイル版の設定ファイル install_mobile.inc を作成する。
	$filepath = $data_path . "install_mobile.inc";
	
	$config_data = 
	"<?php\n".
	"    define ('ECCUBE_INSTALL', 'ON');\n" .
	"    define ('HTML_PATH', '" . $root_dir . "mobile/');\n" .	 
	"    define ('PC_HTML_PATH', '" . $root_dir . "');\n" .	 
	"    define ('SITE_URL', '" . $normal_url . "mobile/');\n" .
	"    define ('PC_SITE_URL', '" . $normal_url . "');\n" .
	"    define ('SSL_URL', '" . $secure_url . "mobile/');\n" .
	"    define ('PC_SSL_URL', '" . $secure_url . "');\n" .
	"    define ('URL_DIR', '" . $url_dir . "mobile/');\n" .	
	"    define ('PC_URL_DIR', '" . $url_dir . "');\n" .	
	"    define ('DOMAIN_NAME', '" . $objWebParam->getValue('domain') . "');\n" .
	"    define ('DB_TYPE', '" . $objDBParam->getValue('db_type') . "');\n" .
	"    define ('DB_USER', '" . $objDBParam->getValue('db_user') . "');\n" . 
	"    define ('DB_PASSWORD', '" . $objDBParam->getValue('db_password') . "');\n" .
	"    define ('DB_SERVER', '" . $objDBParam->getValue('db_server') . "');\n" .
	"    define ('DB_NAME', '" . $objDBParam->getValue('db_name') . "');\n" .
	"    define ('DB_PORT', '" . $objDBParam->getValue('db_port') .  "');\n" .
	"    define ('DATA_PATH', '".$data_path."');\n" .
	"?>";
	
	if($fp = fopen($filepath,"w")) {
		fwrite($fp, $config_data);
		fclose($fp);
	}
*/
}

// テーブルの追加（既にテーブルが存在する場合は作成しない）
function lfAddTable($table_name, $dsn) {
    global $objPage;
	$arrErr = array();
	if(!sfTabaleExists($table_name, $dsn)) {
		list($db_type) = split(":", $dsn);
		$sql_path = "./sql/add/". $table_name . "_" .$db_type .".sql";
		$arrErr = lfExecuteSQL($sql_path, $dsn);
		if(count($arrErr) == 0) {
			$objPage->tpl_message.="○：追加テーブル($table_name)の作成に成功しました。<br>";
		} else {
			$objPage->tpl_message.="×：追加テーブル($table_name)の作成に失敗しました。<br>";		
		}
	} else {
		$objPage->tpl_message.="○：追加テーブル($table_name)が確認されました。<br>";        
    }
    
	return $arrErr;
}

// テーブルの削除（既にテーブルが存在する場合のみ削除する）
function lfDropTable($table_name, $dsn) {
	$arrErr = array();
	if(sfTabaleExists($table_name, $dsn)) {
		// Debugモード指定
		$options['debug'] = PEAR_DB_DEBUG;
		$objDB = DB::connect($dsn, $options);
		// 接続成功
		if(!PEAR::isError($objDB)) {
			$objDB->query("DROP TABLE " . $table_name);
		} else {
			$arrErr['all'] = ">> " . $objDB->message . "<br>";
			// エラー文を取得する
			ereg("\[(.*)\]", $objDB->userinfo, $arrKey);
			$arrErr['all'].= $arrKey[0] . "<br>";
			gfPrintLog($objDB->userinfo, INSTALL_LOG);
		}
	}
	return $arrErr;
}

// カラムの追加（既にカラムが存在する場合は作成しない）
function lfAddColumn($dsn) {
	global $objDBParam;

	// 受注テーブル	
	sfColumnExists("dtb_order", "memo01", "text", $dsn, true);	
	sfColumnExists("dtb_order", "memo02", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo03", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo04", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo05", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo06", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo07", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo08", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo09", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo10", "text", $dsn, true);
	sfColumnExists("dtb_order", "campaign_id", "int4", $dsn, true);

	// 受注一時テーブル	
	sfColumnExists("dtb_order_temp", "order_id", "text", $dsn, true);	
	sfColumnExists("dtb_order_temp", "memo01", "text", $dsn, true);	
	sfColumnExists("dtb_order_temp", "memo02", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo03", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo04", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo05", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo06", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo07", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo08", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo09", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo10", "text", $dsn, true);

	// 支払情報テーブル
	sfColumnExists("dtb_payment", "charge_flg", "int2 default 1", $dsn, true);	
	sfColumnExists("dtb_payment", "rule_min", "numeric", $dsn, true);	
	sfColumnExists("dtb_payment", "upper_rule_max", "numeric", $dsn, true);	
	sfColumnExists("dtb_payment", "module_id", "int4", $dsn, true);	
	sfColumnExists("dtb_payment", "module_path", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo01", "text", $dsn, true);	
	sfColumnExists("dtb_payment", "memo02", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo03", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo04", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo05", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo06", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo07", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo08", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo09", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo10", "text", $dsn, true);
	
	// キャンペーンテーブル
	sfColumnExists("dtb_campaign", "directory_name", "text NOT NULL", $dsn, true);
	sfColumnExists("dtb_campaign", "limit_count", "int4 NOT NULL DEFAULT 0", $dsn, true);
	sfColumnExists("dtb_campaign", "total_count", "int4 NOT NULL DEFAULT 0", $dsn, true);
	sfColumnExists("dtb_campaign", "orverlapping_flg", "int2 NOT NULL DEFAULT 0", $dsn, true);
	sfColumnExists("dtb_campaign", "cart_flg", "int2 NOT NULL DEFAULT 0", $dsn, true);
	sfColumnExists("dtb_campaign", "deliv_free_flg", "int2 NOT NULL DEFAULT 0", $dsn, true);	

	// 顧客
	sfColumnExists("dtb_customer", "mailmaga_flg", "int2", $dsn, true);
    
    // インデックスの確認
	if (!sfColumnExists("dtb_customer", "mobile_phone_id", "text", $dsn, true)) {
		// インデックスの追加
		sfIndexExists("dtb_customer", "mobile_phone_id", "dtb_customer_mobile_phone_id_key", 64, $dsn, true);
	}

	// 顧客メール
	if ($objDBParam->getValue('db_type') == 'mysql') {
		sfColumnExists("dtb_customer_mail", "secret_key", "varchar(50) unique", $dsn, true);
	} else {
		sfColumnExists("dtb_customer_mail", "secret_key", "text unique", $dsn, true);
	}
}

// データの追加（既にデータが存在する場合は作成しない）
function lfAddData($dsn) {
	// CSVテーブル
	if(sfTabaleExists('dtb_csv', $dsn)) {
		lfInsertCSVData(1,'category_id','カテゴリID',53,'now()','now()', $dsn);		
		lfInsertCSVData(4,'order_id','注文ID',1,'now()','now()', $dsn);
		lfInsertCSVData(4,'campaign_id','キャンペーンID',2,'now()','now()', $dsn);
		lfInsertCSVData(4,'customer_id','顧客ID',3,'now()','now()', $dsn);
		lfInsertCSVData(4,'message','要望等',4,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_name01','顧客名1',5,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_name02','顧客名2',6,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_kana01','顧客名カナ1',7,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_kana02','顧客名カナ2',8,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_email','メールアドレス',9,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_tel01','電話番号1',10,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_tel02','電話番号2',11,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_tel03','電話番号3',12,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_fax01','FAX1',13,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_fax02','FAX2',14,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_fax03','FAX3',15,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_zip01','郵便番号1',16,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_zip02','郵便番号2',17,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_pref','都道府県',18,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_addr01','住所1',19,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_addr02','住所2',20,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_sex','性別',21,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_birth','生年月日',22,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_job','職種',23,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_name01','配送先名前',24,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_name02','配送先名前',25,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_kana01','配送先カナ',26,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_kana02','配送先カナ',27,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_tel01','電話番号1',28,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_tel02','電話番号2',29,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_tel03','電話番号3',30,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_fax01','FAX1',31,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_fax02','FAX2',32,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_fax03','FAX3',33,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_zip01','郵便番号1',34,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_zip02','郵便番号2',35,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_pref','都道府県',36,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_addr01','住所1',37,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_addr02','住所2',38,'now()','now()', $dsn);
		lfInsertCSVData(4,'payment_total','お支払い合計',39,'now()','now()', $dsn);
	}
}

// CSVテーブルへのデータの追加
function lfInsertCSVData($csv_id,$col,$disp_name,$rank,$create_date,$update_date, $dsn) {
	$sql = "insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date) values($csv_id,'$col','$disp_name',$rank,$create_date,$update_date);";
	sfDataExists("dtb_csv", "csv_id = ? AND col = ?", array($csv_id, $col), $dsn, $sql, true);
}
?>
