<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
// ▼require.php 相当
// rtrim は PHP バージョン依存対策
define('HTML_PATH', rtrim(realpath(rtrim(realpath(dirname(__FILE__)), '/\\') . '/../'), '/\\') . '/');

require_once HTML_PATH . 'define.php';
define('INSTALL_FUNCTION', true);
require_once HTML_PATH . HTML2DATA_DIR . 'require_base.php';
// ▲require.php 相当

$INSTALL_DIR = realpath(dirname( __FILE__));
require_once(DATA_PATH . "module/Request.php");

define("INSTALL_LOG", "./temp/install.log");
ini_set("max_execution_time", 300);

$objPage = new StdClass;
$objPage->arrDB_TYPE = array(
    'pgsql' => 'PostgreSQL',
    'mysql' => 'MySQL'
);
$objPage->arrDB_PORT = array(
    'pgsql' => '',
    'mysql' => ''
);

$objDb = new SC_Helper_DB_Ex();

// テンプレートコンパイルディレクトリの書込み権限チェック
$temp_dir = $INSTALL_DIR . '/temp';

if(!is_writable($temp_dir)) {
    SC_Utils_Ex::sfErrorHeader($temp_dir . "にユーザ書込み権限(777, 707等)を付与して下さい。", true);
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

$mode = isset($_POST['mode_overwrite']) ? $_POST['mode_overwrite'] : $_POST['mode'];

switch($mode) {
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
        // 設定ファイルの生成
        lfMakeConfigFile();
        $objPage = lfDispStep3($objPage);
    } else {
        $objPage = lfDispStep2($objPage);
    }
    break;
// テーブルの作成
case 'step3':
    // 入力データを渡す。
    $arrRet =  $objDBParam->getHashArray();
    define("DB_TYPE", $arrRet['db_type']);
    $dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].":".$arrRet['db_port']."/".$arrRet['db_name'];

    if(count($objPage->arrErr) == 0) {
        // スキップする場合には次画面へ遷移
        $skip = $_POST["db_skip"];
        if ($skip == "on") {
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

    // シーケンスの作成
    if (count($objPage->arrErr) == 0) {
        $objPage->arrErr = lfCreateSequence(getSequences(), $dsn);
        if(count($objPage->arrErr) == 0) {
            $objPage->tpl_message.="○：シーケンスの作成に成功しました。<br>";
        } else {
            $objPage->tpl_message.="×：シーケンスの作成に失敗しました。<br>";
        }
    }

    if(count($objPage->arrErr) == 0) {
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
    if (!defined("DB_TYPE")) {
        define("DB_TYPE", $arrRet['db_type']);
    }
    $dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].":".$arrRet['db_port']."/".$arrRet['db_name'];

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

    // シーケンスの削除
    if(count($objPage->arrErr) == 0) {
        $objPage->arrErr = lfDropSequence(getSequences(), $dsn);
        if(count($objPage->arrErr) == 0) {
            $objPage->tpl_message.="○：シーケンスの削除に成功しました。<br>";
        } else {
            $objPage->tpl_message.="×：シーケンスの削除に失敗しました。<br>";
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

    $sql = "INSERT INTO dtb_member (member_id, name, login_id, password, creator_id, authority, work, del_flg, rank, create_date, update_date)
            VALUES (?,'管理者',?,?,0,0,1,0,1, now(), now());";
    $member_id = $objQuery->nextVal("dtb_member_member_id");
    $objQuery->query($sql, array($member_id, $login_id, $login_pass));

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

// STEP0画面の表示(チェック)
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

    // プログラムで書込みされるファイル・ディレクトリ
    $arrWriteFile = array(
        DATA_PATH . "install.php",
        HTML_PATH . "user_data",
        HTML_PATH . "cp",
        HTML_PATH . "upload",
        DATA_PATH . "cache/",
        DATA_PATH . "class/",
        DATA_PATH . "Smarty/",
        DATA_PATH . "logs/",
        DATA_PATH . "downloads/",
        DATA_PATH . "upload/",
    );

    $mess = "";
    $hasErr = false;
    foreach($arrWriteFile as $val) {
        // listdirsの保持データを初期化
        initdirs();
        if (is_dir($val)) {
           $arrDirs = listdirs($val);
        } else {
            $arrDirs = array($val);
        }

        foreach ($arrDirs as $path) {
            if(file_exists($path)) {
                $filemode = lfGetFileMode($path);
                $real_path = realpath($path);

                // ディレクトリの場合
                if(is_dir($path)) {
                    if(!is_writable($path)) {
                        $mess.= ">> ×：$real_path($filemode) <br>ユーザ書込み権限(777, 707等)を付与して下さい。<br>";
                        $hasErr = true;
                    } else {
                        GC_Utils_Ex::gfPrintLog("WRITABLE：".$path, INSTALL_LOG);
                    }
                } else {
                    if(!is_writable($path)) {
                        $mess.= ">> ×：$real_path($filemode) <br>ユーザ書込み権限(666, 606等)を付与して下さい。<br>";
                        $hasErr = true;
                    } else {
                        GC_Utils_Ex::gfPrintLog("WRITABLE：".$path, INSTALL_LOG);
                    }
                }
            } else {
                $mess.= ">> ×：$path が見つかりません。<br>";
                $hasErr = true;
            }
        }
    }

    if (ini_get('safe_mode')) {
        $mess .= ">> ×：PHPのセーフモードが有効になっています。<br>";
        $hasErr = true;
    }

    // 問題点を検出している場合
    if ($hasErr) {
        $objPage->tpl_mode = 'return_step0';
    }
    // 問題点を検出していない場合
    else {
        $objPage->tpl_mode = 'step0';
        umask(0);
        $path = HTML_PATH . "upload/temp_template";
        if(!file_exists($path)) {
            mkdir($path);
        }
        $path = HTML_PATH . "upload/save_image";
        if(!file_exists($path)) {
            mkdir($path);
        }
        $path = HTML_PATH . "upload/temp_image";
        if(!file_exists($path)) {
            mkdir($path);
        }
        $path = HTML_PATH . "upload/graph_image";
        if(!file_exists($path)) {
            mkdir($path);
        }
        $path = HTML_PATH . "upload/mobile_image";
        if(!file_exists($path)) {
            mkdir($path);
        }
        $path = DATA_PATH . "downloads/module";
        if(!file_exists($path)) {
            mkdir($path);
        }
        $path = DATA_PATH . "downloads/update";
        if(!file_exists($path)) {
            mkdir($path);
        }
        $path = DATA_PATH . "upload/csv";
        if(!file_exists($path)) {
            mkdir($path);
        }
        $mess.= ">> ○：アクセス権限は正常です。<br>";
    }

    $objPage->mess = $mess;
    $objPage->hasErr = $hasErr;

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
    $objPage->copy_mess = SC_Utils_Ex::sfCopyDir("./user_data/", HTML_PATH . "user_data/", $objPage->copy_mess);
    $objPage->copy_mess = SC_Utils_Ex::sfCopyDir("./save_image/", HTML_PATH . "upload/save_image/", $objPage->copy_mess);
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
    global $objDb;

    // hiddenに入力値を保持
    $objPage->arrHidden = $objWebParam->getHashArray();
    $objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
    // hiddenに入力値を保持
    $objPage->arrHidden['agreement'] = $_POST['agreement'];

    $normal_url = $objWebParam->getValue('normal_url');
    // 語尾に'/'をつける
    if (!ereg("/$", $normal_url)) $normal_url = $normal_url . "/";

    $arrDbParam = $objDBParam->getHashArray();
    if (!defined("DB_TYPE")) {
        define("DB_TYPE", $arrDbParam['db_type']);
    }
    $dsn = $arrDbParam['db_type']."://".$arrDbParam['db_user'].":".$arrDbParam['db_password']."@".$arrDbParam['db_server'].":".$arrDbParam['db_port']."/".$arrDbParam['db_name'];

    $objPage->tpl_site_url = $normal_url;
    $objPage->tpl_shop_name = $objWebParam->getValue('shop_name');
    $objPage->tpl_cube_ver = ECCUBE_VERSION;
    $objPage->tpl_php_ver = phpversion();
    $objPage->tpl_db_ver = $objDb->sfGetDBVersion($dsn);
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
    global $objDb;

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
        $objQuery = new SC_Query();
        $tables = $objQuery->listTables();

        if(!PEAR::isError($tables) && in_array("dtb_baseinfo", $tables)) {
            $arrRet = $objQuery->select("shop_name, email01", "dtb_baseinfo");
            $shop_name = $arrRet[0]['shop_name'];
            $admin_mail = $arrRet[0]['email01'];
        }
    }

    $objWebParam->addParam("店名", "shop_name", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $shop_name);
    $objWebParam->addParam("管理者：メールアドレス", "admin_mail", MTEXT_LEN, "", array("EXIST_CHECK","EMAIL_CHECK","EMAIL_CHAR_CHECK","MAX_LENGTH_CHECK"), $admin_mail);
    $objWebParam->addParam("管理者：ログインID", "login_id", ID_MAX_LEN, "", array("EXIST_CHECK","SPTAB_CHECK", "ALNUM_CHECK"));
    $objWebParam->addParam("管理者：パスワード", "login_pass", ID_MAX_LEN, "", array("EXIST_CHECK","SPTAB_CHECK", "ALNUM_CHECK"));
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
    $objErr->doFunc(array("管理者：ログインID",'login_id',ID_MIN_LEN , ID_MAX_LEN) ,array("SPTAB_CHECK" ,"NUM_RANGE_CHECK"));

    // パスワードのチェック
    $objErr->doFunc( array("管理者：パスワード",'login_pass',ID_MIN_LEN ,ID_MAX_LEN ) ,array("SPTAB_CHECK" ,"NUM_RANGE_CHECK" ));

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
        $objDB = MDB2::connect($dsn, $options);
        // 接続成功
        if(!PEAR::isError($objDB)) {
            $dbFactory = SC_DB_DBFactory_Ex::getInstance();
            // データベースバージョン情報の取得
            $objPage->tpl_db_version = $dbFactory->sfGetDBVersion($dsn);
        } else {
            $objErr->arrErr['all'] = ">> " . $objDB->message . "<br>";
            // エラー文を取得する
            ereg("\[(.*)\]", $objDB->userinfo, $arrKey);
            $objErr->arrErr['all'].= $arrKey[0] . "<br>";
            GC_Utils_Ex::gfPrintLog($objDB->userinfo, INSTALL_LOG);
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
        $objDB = MDB2::connect($dsn, $options);
        // 接続エラー
        if(!PEAR::isError($objDB)) {
            $sql_split = split(";",$sql);
            foreach($sql_split as $key => $val){
                SC_Utils::sfFlush();
                if (trim($val) != "") {
                    $ret = $objDB->query($val);
                    if(PEAR::isError($ret) && $disp_err) {
                        $arrErr['all'] = ">> " . $ret->message . "<br>";
                        // エラー文を取得する
                        ereg("\[(.*)\]", $ret->userinfo, $arrKey);
                        $arrErr['all'].= $arrKey[0] . "<br>";
                        $objPage->update_mess.=">> テーブル構成の変更に失敗しました。<br>";
                        GC_Utils_Ex::gfPrintLog($ret->userinfo, INSTALL_LOG);
                    } else {
                        GC_Utils_Ex::gfPrintLog("OK:". $val, INSTALL_LOG);
                    }
                }
            }
        } else {
            $arrErr['all'] = ">> " . $objDB->message;
            GC_Utils_Ex::gfPrintLog($objDB->userinfo, INSTALL_LOG);
        }
    }
    return $arrErr;
}

/**
 * シーケンスを削除する.
 *
 * @param array $arrSequences シーケンスのテーブル名, カラム名の配列
 * @param string $dsn データソース名
 * @return array エラーが発生した場合はエラーメッセージの配列
 */
function lfDropSequence($arrSequences, $dsn) {
    $arrErr = array();

    // Debugモード指定
    $options['debug'] = PEAR_DB_DEBUG;
    $objDB = MDB2::connect($dsn, $options);
    $objDB->loadModule('Manager');

    // 接続エラー
    if (!PEAR::isError($objDB)) {

        $exists = $objDB->listSequences();
        foreach ($arrSequences as $seq) {
            $seq_name = $seq[0] . "_" . $seq[1];
            if (in_array($seq_name, $exists)) {
                $result = $objDB->dropSequence($seq_name);
                if (PEAR::isError($result)) {
                    $arrErr['all'] = ">> " . $result->message . "<br />";
                    GC_Utils_Ex::gfPrintLog($result->userinfo, INSTALL_LOG);
                } else {
                    GC_Utils_Ex::gfPrintLog("OK:". $seq_name, INSTALL_LOG);
                }
            }
        }
    } else {
        $arrErr['all'] = ">> " . $objDB->message;
        GC_Utils_Ex::gfPrintLog($objDB->userinfo, INSTALL_LOG);
    }
    return $arrErr;
}

/**
 * シーケンスを生成する.
 *
 * @param array $arrSequences シーケンスのテーブル名, カラム名の配列
 * @param string $dsn データソース名
 * @return array エラーが発生した場合はエラーメッセージの配列
 */
function lfCreateSequence($arrSequences, $dsn) {
   $arrErr = array();

    // Debugモード指定
    $options['debug'] = PEAR_DB_DEBUG;
    $objDB = MDB2::connect($dsn, $options);
    $objDB->loadModule('Manager');

    // 接続エラー
    if (!PEAR::isError($objDB)) {

        $exists = $objDB->listSequences();
        foreach ($arrSequences as $seq) {
            $res = $objDB->query("SELECT max(" . $seq[1] . ") FROM ". $seq[0]);
            if (PEAR::isError($res)) {
                $arrErr['all'] = ">> " . $res->userinfo . "<br />";
                GC_Utils_Ex::gfPrintLog($res->userinfo, INSTALL_LOG);
                return $arrErr;
            }
            $max = $res->fetchOne();

            $seq_name = $seq[0] . "_" . $seq[1];
            $result = $objDB->createSequence($seq_name, $max + 1);
            if (PEAR::isError($result)) {
                $arrErr['all'] = ">> " . $result->message . "<br />";
                GC_Utils_Ex::gfPrintLog($result->userinfo, INSTALL_LOG);
            } else {
                GC_Utils_Ex::gfPrintLog("OK:". $seq_name, INSTALL_LOG);
            }
        }
    } else {
        $arrErr['all'] = ">> " . $objDB->message;
        GC_Utils_Ex::gfPrintLog($objDB->userinfo, INSTALL_LOG);
    }
    return $arrErr;
}

// 設定ファイルの作成
function lfMakeConfigFile() {
    global $objWebParam;
    global $objDBParam;
    
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
    $url_dir = ereg_replace("^https?://[a-zA-Z0-9_:~=&\?\.\-]+", "", $normal_url);

    $filepath = DATA_PATH . "install.php";

    $config_data =
    "<?php\n".
    "    define ('ECCUBE_INSTALL', 'ON');\n" .
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
    "    define ('MOBILE_HTML_PATH', HTML_PATH . 'mobile/');\n" .
    "    define ('MOBILE_SITE_URL', SITE_URL . 'mobile/');\n" .
    "    define ('MOBILE_SSL_URL', SSL_URL . 'mobile/');\n" .
    "    define ('MOBILE_URL_DIR', URL_DIR . 'mobile/');\n" .
    "?>";

    if($fp = fopen($filepath,"w")) {
        fwrite($fp, $config_data);
        fclose($fp);
    }
}

/**
 * $dir を再帰的に辿ってパス名を配列で返す.
 *
 * @param string 任意のパス名
 * @return array $dir より下層に存在するパス名の配列
 * @see http://www.php.net/glob
 */
$alldirs = array();
function listdirs($dir) {
    global $alldirs;
    $dirs = glob($dir . '/*');
    if (is_array($dirs) && count($dirs) > 0) {
        foreach ($dirs as $d) {
            $alldirs[] = $d;
            listdirs($d);
        }
    }
    return $alldirs;
}

/**
 * 保持したスタティック変数をクリアする。
 */
function initdirs() {
    global $alldirs;
    $alldirs = array();
}

/**
 * シーケンスを使用するテーブル名とカラム名の配列を返す.
 *
 * @return array シーケンスを使用するテーブル名とカラム名の配列
 */
function getSequences() {
    return array(array("dtb_best_products","best_id"),
                 array("dtb_bloc", "bloc_id"),
                 array("dtb_category", "category_id"),
                 array("dtb_class", "class_id"),
                 array("dtb_classcategory", "classcategory_id"),
                 array("dtb_csv", "no"),
                 array("dtb_csv_sql", "sql_id"),
                 array("dtb_customer", "customer_id"),
                 array("dtb_deliv", "deliv_id"),
                 array("dtb_delivfee", "fee_id"),
                 array("dtb_holiday", "holiday_id"),
                 array("dtb_kiyaku", "kiyaku_id"),
                 array("dtb_mail_history", "send_id"),
                 array("dtb_maker", "maker_id"),
                 array("dtb_member", "member_id"),
                 array("dtb_mobile_kara_mail", "kara_mail_id"),
                 array("dtb_module_update_logs", "log_id"),
                 array("dtb_news", "news_id"),
                 array("dtb_order", "order_id"),
                 array("dtb_other_deliv", "other_deliv_id"),
                 array("dtb_pagelayout", "page_id"),
                 array("dtb_payment", "payment_id"),
                 array("dtb_products_class", "product_class_id"),
                 array("dtb_products", "product_id"),
                 array("dtb_class_combination", "class_combination_id"),
                 array("dtb_review", "review_id"),
                 array("dtb_send_history", "send_id"),
                 array("dtb_site_control", "control_id"),
                 array("dtb_table_comment", "id"),
                 array("dtb_trackback", "trackback_id"));
}
?>
