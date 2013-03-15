<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
define('HTML_REALDIR', rtrim(realpath(rtrim(realpath(dirname(__FILE__)), '/\\') . '/../'), '/\\') . '/');

require_once HTML_REALDIR . 'define.php';
define('INSTALL_FUNCTION', true);
define('INSTALL_INFO_URL', 'http://www.ec-cube.net/install_info/index.php');
if (ob_get_level() > 0 && ob_get_length() > 0) {
    while (ob_end_clean());
}
require_once HTML_REALDIR . HTML2DATA_DIR . 'require_base.php';
ob_start();
// ▲require.php 相当

$ownDir = realpath(dirname(__FILE__)) . '/';

if (!defined('ADMIN_DIR')) {
    define('ADMIN_DIR', 'admin/');
}

define('INSTALL_LOG', './temp/install.log');
ini_set('max_execution_time', 300);

$objPage = new StdClass;
$objPage->arrDB_TYPE = array(
    'pgsql' => 'PostgreSQL',
    'mysql' => 'MySQL',
);
$objPage->arrDB_PORT = array(
    'pgsql' => '',
    'mysql' => '',
);
$objPage->arrMailBackend = array('mail' => 'mail',
                                 'smtp' => 'SMTP',
                                 'sendmail' => 'sendmail');

$objDb = new SC_Helper_DB_Ex();

// テンプレートコンパイルディレクトリの書込み権限チェック
$temp_dir = $ownDir . 'temp';

if (!is_writable($temp_dir)) {
    SC_Utils_Ex::sfErrorHeader(t('c_Please grant user write access (777, 707, etc.) for T_ARG1_01', array('T_ARG1' => $temp_dir)), true);
    exit;
}

$objView = new SC_InstallView_Ex($ownDir . 'templates', $ownDir . 'temp');

// パラメーター管理クラス
$objWebParam = new SC_FormParam();
$objDBParam = new SC_FormParam();
// パラメーター情報の初期化
$objWebParam = lfInitWebParam($objWebParam);
$objDBParam = lfInitDBParam($objDBParam);

//フォーム配列の取得
$objWebParam->setParam($_POST);
$objDBParam->setParam($_POST);

$mode = isset($_POST['mode_overwrite']) ? $_POST['mode_overwrite'] : $_POST['mode'];

switch ($mode) {
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
        $objPage->arrErr = lfCheckWebError($objWebParam);
        if (count($objPage->arrErr) == 0) {
            $objPage = lfDispStep2($objPage);
        } else {
            $objPage = lfDispStep1($objPage);
        }
        break;
    // データベースの設定
    case 'step2':
        //入力値のエラーチェック
        $objPage->arrErr = lfCheckDBError($objDBParam);
        if (count($objPage->arrErr) == 0) {
            if ($err = renameAdminDir($objWebParam->getValue('admin_dir')) !== true) {
                $objPage->arrErr['all'] .= $err;
                $objPage = lfDispStep2($objPage);
            } else {
                $objPage = lfDispStep3($objPage);
            }
        } else {
            $objPage = lfDispStep2($objPage);
        }
        break;
    // テーブルの作成
    case 'step3':
        $arrDsn = getArrayDsn($objDBParam);

        if (count($objPage->arrErr) == 0) {
            // スキップする場合には次画面へ遷移
            $skip = $_POST['db_skip'];
            if ($skip == 'on') {
                $objPage = lfDispStep4($objPage);
                break;
            }
        }

        // テーブルの作成
        $objPage->arrErr = lfExecuteSQL('./sql/create_table_' . $arrDsn['phptype'] . '.sql', $arrDsn);
        if (count($objPage->arrErr) == 0) {
            $objPage->tpl_message .= t('c_○: The table was successfully created.<br />_01');
        } else {
            $objPage->tpl_message .= t('c_×: The table was not created.<br />_01');
        }

        // 初期データの作成
        if (count($objPage->arrErr) == 0) {
            $objPage->arrErr = lfExecuteSQL('./sql/insert_data.sql', $arrDsn);
            if (count($objPage->arrErr) == 0) {
                $objPage->tpl_message .= t('c_○: Initial data was successfully created.<br />_01');
            } else {
                $objPage->tpl_message .= t('c_×: Initial data was not created.<br />_01');
            }
        }

        // シーケンスの作成
        if (count($objPage->arrErr) == 0) {
            $objPage->arrErr = lfCreateSequence(getSequences(), $arrDsn);
            if (count($objPage->arrErr) == 0) {
                $objPage->tpl_message .= t('c_○: The sequence was successfully created.<br />_01');
            } else {
                $objPage->tpl_message .= t('c_×: The sequence was not created.<br />_01');
            }
        }

        if (count($objPage->arrErr) == 0) {
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
        $arrDsn = getArrayDsn($objDBParam);

        // テーブルの削除
        if (count($objPage->arrErr) == 0) {
            $objPage->arrErr = lfExecuteSQL('./sql/drop_table.sql', $arrDsn, false);
            if (count($objPage->arrErr) == 0) {
                $objPage->tpl_message .= t('c_○: The table was successfully deleted.<br />_01');
            } else {
                $objPage->tpl_message .= t('c_×: The table was not deleted.<br />_01');
            }
        }

        // シーケンスの削除
        if (count($objPage->arrErr) == 0) {
            $objPage->arrErr = lfDropSequence(getSequences(), $arrDsn);
            if (count($objPage->arrErr) == 0) {
                $objPage->tpl_message .= t('c_○: The sequence was successfully deleted.<br />_01');
            } else {
                $objPage->tpl_message .= t('c_×: The sequence was not deleted.<br />_01');
            }
        }

        //マスターデータのキャッシュを削除
        $cache_dir = DATA_REALDIR . 'cache/';
        $res_dir = opendir($cache_dir);
        while ($file_name = readdir($res_dir)){
            //dummy以外は削除
            if ($file_name != 'dummy'){
                if (is_file($cache_dir . $file_name)) {
                    unlink($cache_dir . $file_name);
                }
            }
        }
        closedir($res_dir);

        $objPage = lfDispStep3($objPage);
        break;
    // 完了画面
    case 'complete':

        $GLOBAL_ERR = '';
        $objPage = lfDispComplete($objPage);

        if (isset($_POST['send_info']) && $_POST['send_info'] === 'true') {
            // サイト情報を送信
            $req = new HTTP_Request('http://www.ec-cube.net/mall/use_site.php');
            $req->setMethod(HTTP_REQUEST_METHOD_POST);

            $arrSendData = array();
            foreach ($_POST as $key => $val) {
                if (preg_match('/^senddata_*/', $key)) {
                    $arrSendDataTmp = array(str_replace('senddata_', '', $key) => $val);
                    $arrSendData = array_merge($arrSendData, $arrSendDataTmp);
                }
            }

            foreach ($arrSendData as $key => $val) {
                $req->addPostData($key, $val);
            }

            if (!PEAR::isError($req->sendRequest())) {
                $response1 = $req->getResponseBody();
            } else {
                $response1 = '';
            }
            $req->clearPostData();
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

//フォーム用のパラメーターを返す
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
        USER_REALDIR,
        HTML_REALDIR . 'upload/',
        DATA_REALDIR . 'cache/',
        DATA_REALDIR . 'class/',
        DATA_REALDIR . 'Smarty/',
        DATA_REALDIR . 'logs/',
        DATA_REALDIR . 'downloads/',
        DATA_REALDIR . 'upload/',
        HTML_REALDIR,
        DATA_REALDIR . 'config/',
    );

    $mess = '';
    $hasErr = false;
    foreach ($arrWriteFile as $val) {
        // listdirsの保持データを初期化
        initdirs();
        if (is_dir($val) and $val != HTML_REALDIR) {
            $arrDirs = listdirs($val);
        } else {
            $arrDirs = array($val);
        }

        foreach ($arrDirs as $path) {
            if (file_exists($path)) {
                $filemode = lfGetFileMode($path);
                $real_path = realpath($path);

                // ディレクトリの場合
                if (is_dir($path)) {
                    if (!is_writable($path)) {
                        $mess .= t('c_>> ×:T_ARG1(T_ARG2)Please grant user write access (777, 707, etc.)_01', array('T_ARG1' => $real_path, 'T_ARG2' => $filemode));
                        $hasErr = true;
                    } else {
                        GC_Utils_Ex::gfPrintLog('WRITABLE：' . $path, INSTALL_LOG);
                    }
                } else {
                    if (!is_writable($path)) {
                        $mess .= t('c_>> ×:T_ARG1(T_ARG2)Please grant user write access (666, 606, etc.)_01', array('T_ARG1' => $real_path, 'T_ARG2' => $filemode));
                        $hasErr = true;
                    } else {
                        GC_Utils_Ex::gfPrintLog('WRITABLE：' . $path, INSTALL_LOG);
                    }
                }
            } else {
                $mess .= t('c_>> ×:T_ARG1 cannot be found._01', array('T_ARG1' => $path));
                $hasErr = true;
            }
        }
    }

    if (ini_get('safe_mode')) {
        $mess .= t('c_>> ×:PHP safe mode is active._01');
        $hasErr = true;
    }

    if (get_magic_quotes_gpc()) {
        $mess .= t("c_>> ×:PHP settings directive 'magic_quotes_gpc' is active._01");
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
        $path = DATA_REALDIR . 'downloads/plugin';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = HTML_REALDIR . 'plugin';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = HTML_REALDIR . 'upload/temp_plugin';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = DATA_REALDIR . 'downloads/tmp';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = DATA_REALDIR . 'downloads/tmp/plugin_install';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = HTML_REALDIR . 'upload/temp_template';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = HTML_REALDIR . 'upload/save_image';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = HTML_REALDIR . 'upload/temp_image';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = HTML_REALDIR . 'upload/graph_image';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = HTML_REALDIR . 'upload/mobile_image';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = DATA_REALDIR . 'downloads/module';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = DATA_REALDIR . 'downloads/update';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = DATA_REALDIR . 'upload/csv';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $mess .= t('c_>> ○:Access authority is normal._01');
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
    $objPage->copy_mess = SC_Utils_Ex::sfCopyDir('./save_image/', HTML_REALDIR . 'upload/save_image/', $objPage->copy_mess);
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

    // 設定ファイルの生成
    lfMakeConfigFile();

    // hiddenに入力値を保持
    $objPage->arrHidden = $objWebParam->getHashArray();
    $objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
    // hiddenに入力値を保持
    $objPage->arrHidden['agreement'] = $_POST['agreement'];

    $normal_url = $objWebParam->getValue('normal_url');
    // 語尾に'/'をつける
    $normal_url = rtrim($normal_url, '/') . '/';

    $arrDsn = getArrayDsn($objDBParam);

    $objPage->tpl_site_url = $normal_url;
    $objPage->tpl_shop_name = $objWebParam->getValue('shop_name');
    $objPage->tpl_cube_ver = ECCUBE_VERSION;
    $objPage->tpl_php_ver = phpversion();
    $dbFactory = SC_DB_DBFactory_Ex::getInstance($arrDsn['phptype']);
    $objPage->tpl_db_ver = $dbFactory->sfGetDBVersion($arrDsn);
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

    $arrDsn = getArrayDsn($objDBParam);

    $sqlval['id'] = 1;
    $sqlval['shop_name'] = $objWebParam->getValue('shop_name');
    $sqlval['email01'] = $objWebParam->getValue('admin_mail');
    $sqlval['email02'] = $objWebParam->getValue('admin_mail');
    $sqlval['email03'] = $objWebParam->getValue('admin_mail');
    $sqlval['email04'] = $objWebParam->getValue('admin_mail');
    $sqlval['email05'] = $objWebParam->getValue('admin_mail');
    $sqlval['top_tpl'] = 'default1';
    $sqlval['product_tpl'] = 'default1';
    $sqlval['detail_tpl'] = 'default1';
    $sqlval['mypage_tpl'] = 'default1';
    $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
    $objQuery = new SC_Query($arrDsn);
    $cnt = $objQuery->count('dtb_baseinfo');
    if ($cnt > 0) {
        $objQuery->update('dtb_baseinfo', $sqlval);
    } else {
        $objQuery->insert('dtb_baseinfo', $sqlval);
    }

    // 管理者登録
    $login_id = $objWebParam->getValue('login_id');
    $salt = SC_Utils_Ex::sfGetRandomString(10);
    $login_pass = SC_Utils_Ex::sfGetHashString($objWebParam->getValue('login_pass'), $salt);

    $arrVal = array(
        'login_id' => $login_id,
        'password' => $login_pass,
        'salt' => $salt,
        'work' => 1,
        'del_flg' => 0,
        'update_date' => 'CURRENT_TIMESTAMP',
    );

    $member_id = $objQuery->get('member_id', 'dtb_member', 'login_id = ? AND del_flg = 0', array($login_id));

    if (strlen($member_id) == 0) {
        $member_id = $objQuery->nextVal('dtb_member_member_id');
        $arrVal['member_id'] = $member_id;
        $arrVal['name'] = t('c_Administrator_01');
        $arrVal['creator_id'] = 0;
        $arrVal['authority'] = 0;
        $arrVal['rank'] = 1;
        $objQuery->insert('dtb_member', $arrVal);
    } else {
        $objQuery->update('dtb_member', $arrVal, 'member_id = ?', array($member_id));
    }

    $objPage->arrHidden['db_skip'] = $_POST['db_skip'];
    $objPage->tpl_mainpage = 'complete.tpl';
    $objPage->tpl_mode = 'complete';

    $secure_url = $objWebParam->getValue('secure_url');
    // 語尾に'/'をつける
    $secure_url = rtrim($secure_url, '/') . '/';
    $objPage->tpl_sslurl = $secure_url;
    //EC-CUBEオフィシャルサイトからのお知らせURL
    $objPage->install_info_url = INSTALL_INFO_URL;
    return $objPage;
}

// WEBパラメーター情報の初期化
function lfInitWebParam($objWebParam) {
    global $objDb;

    if (defined('HTTP_URL')) {
        $normal_url = HTTP_URL;
    } else {
        $dir = preg_replace('|install/.*$|', '', $_SERVER['REQUEST_URI']);
        $normal_url = 'http://' . $_SERVER['HTTP_HOST'] . $dir;
    }

    if (defined('HTTPS_URL')) {
        $secure_url = HTTPS_URL;
    } else {
        $dir = preg_replace('|install/.*$|', '', $_SERVER['REQUEST_URI']);
        $secure_url = 'http://' . $_SERVER['HTTP_HOST'] . $dir;
    }

    // 店名、管理者メールアドレスを取得する。(再インストール時)
    if (defined('DEFAULT_DSN')) {
        $objQuery = new SC_Query();
        $tables = $objQuery->listTables();

        if (!PEAR::isError($tables) && in_array('dtb_baseinfo', $tables)) {
            $arrRet = $objQuery->select('shop_name, email01', 'dtb_baseinfo');
            $shop_name = $arrRet[0]['shop_name'];
            $admin_mail = $arrRet[0]['email01'];
        }
    }

    // 管理機能のディレクトリ名を取得（再インストール時）
    $oldAdminDir = SC_Utils_Ex::sfTrimURL(ADMIN_DIR);

    if (defined('ADMIN_FORCE_SSL')) {
        $admin_force_ssl = ADMIN_FORCE_SSL;
    } else {
        $admin_force_ssl = '';
    }

    if (defined('ADMIN_ALLOW_HOSTS')) {
        $arrAllowHosts = unserialize(ADMIN_ALLOW_HOSTS);
        $admin_allow_hosts = '';
        foreach ($arrAllowHosts as $val) {
            $admin_allow_hosts .= $val . "\n";
        }

    } else {
        $admin_allow_hosts = '';
    }

    if (defined('MAIL_BACKEND')) {
        $mail_backend = MAIL_BACKEND;
    } else {
        $mail_backend = 'mail';
    }
    if (defined('SMTP_HOST')) {
        $smtp_host = SMTP_HOST;
    }
    if (defined('SMTP_PORT')) {
        $smtp_port = SMTP_PORT;
    }
    if (defined('SMTP_USER')) {
        $smtp_user = SMTP_USER;
    }
    if (defined('SMTP_PASSWORD')) {
        $smtp_password = SMTP_PASSWORD;
    }

    $objWebParam->addParam(t('c_Store name_01'), 'shop_name', MTEXT_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $shop_name);
    $objWebParam->addParam(t('c_Administrator: E-mail address_01'), 'admin_mail', null, '', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'), $admin_mail);
    $objWebParam->addParam(t('c_Administrator: Login ID_01'), 'login_id', ID_MAX_LEN, '', array('EXIST_CHECK', 'SPTAB_CHECK', 'ALNUM_CHECK'));
    $objWebParam->addParam(t('c_Administrator: Password_01'), 'login_pass', ID_MAX_LEN, '', array('EXIST_CHECK', 'SPTAB_CHECK', 'ALNUM_CHECK'));
    $objWebParam->addParam(t('c_Management area: Directory_01'), 'admin_dir', ID_MAX_LEN, 'a', array('EXIST_CHECK', 'SPTAB_CHECK', 'ALNUM_CHECK'), $oldAdminDir);
    $objWebParam->addParam(t('c_Management area: SSL restriction_01'), 'admin_force_ssl', 1, 'n', array('SPTAB_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'), $admin_force_ssl);
    $objWebParam->addParam(t('c_Management area: IP restriction_01'), 'admin_allow_hosts', LTEXT_LEN, 'an', array('IP_CHECK', 'MAX_LENGTH_CHECK'), $admin_allow_hosts);
    $objWebParam->addParam(t('c_URL (normal)_01'), 'normal_url', MTEXT_LEN, '', array('EXIST_CHECK', 'URL_CHECK', 'MAX_LENGTH_CHECK'), $normal_url);
    $objWebParam->addParam(t('c_URL (secure)_01'), 'secure_url', MTEXT_LEN, '', array('EXIST_CHECK', 'URL_CHECK', 'MAX_LENGTH_CHECK'), $secure_url);
    $objWebParam->addParam(t('c_Domain_01'), 'domain', MTEXT_LEN, '', array('MAX_LENGTH_CHECK'));
    $objWebParam->addParam(t('c_Mailer backend_01'), 'mail_backend', STEXT_LEN, 'a', array('MAX_LENGTH_CHECK', 'EXIST_CHECK'), $mail_backend);
    $objWebParam->addParam(t('c_SMTP host_01'), 'smtp_host', STEXT_LEN, 'a', array('MAX_LENGTH_CHECK'), $smtp_host);
    $objWebParam->addParam(t('c_SMTP port_01'), 'smtp_port', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $smtp_port);
    $objWebParam->addParam(t('c_SMTP user_01'), 'smtp_user', STEXT_LEN, 'a', array('MAX_LENGTH_CHECK'), $smtp_user);
    $objWebParam->addParam(t('c_SMTP password_01'), 'smtp_password', STEXT_LEN, 'a', array('MAX_LENGTH_CHECK'), $smtp_password);

    return $objWebParam;
}

// DBパラメーター情報の初期化
function lfInitDBParam($objDBParam) {

    if (defined('DB_SERVER')) {
        $db_server = DB_SERVER;
    } else {
        $db_server = '127.0.0.1';
    }

    if (defined('DB_TYPE')) {
        $db_type = DB_TYPE;
    } else {
        $db_type = '';
    }

    if (defined('DB_PORT')) {
        $db_port = DB_PORT;
    } else {
        $db_port = '';
    }

    if (defined('DB_NAME')) {
        $db_name = DB_NAME;
    } else {
        $db_name = 'eccube_db';
    }

    if (defined('DB_USER')) {
        $db_user = DB_USER;
    } else {
        $db_user = 'eccube_db_user';
    }

    $objDBParam->addParam(t('c_DB type_01'), 'db_type', INT_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $db_type);
    $objDBParam->addParam(t('c_DB server_01'), 'db_server', MTEXT_LEN, '', array('MAX_LENGTH_CHECK'), $db_server);
    $objDBParam->addParam(t('c_DB port_01'), 'db_port', INT_LEN, '', array('MAX_LENGTH_CHECK'), $db_port);
    $objDBParam->addParam(t('c_DB name_01'), 'db_name', MTEXT_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $db_name);
    $objDBParam->addParam(t('c_DB user_01'), 'db_user', MTEXT_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $db_user);
    $objDBParam->addParam(t('c_DB password_01'), 'db_password', MTEXT_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));

    return $objDBParam;
}

// 入力内容のチェック
function lfCheckWebError($objWebParam) {
    // 入力データを渡す。
    $arrRet = $objWebParam->getHashArray();
    $objErr = new SC_CheckError($arrRet);
    $objErr->arrErr = $objWebParam->checkError();

    // ディレクトリ名のみ取得する
    $normal_dir = preg_replace('|^https?://[a-zA-Z0-9_~=&\?\.\-]+|', '', $arrRet['normal_url']);
    $secure_dir = preg_replace('|^https?://[a-zA-Z0-9_~=&\?\.\-]+|', '', $arrRet['secure_url']);

    if ($normal_dir != $secure_dir) {
        $objErr->arrErr['normal_url'] = t('c_* It is not possible to designate a different hierarchy for the URL._01');
        $objErr->arrErr['secure_url'] = t('c_* It is not possible to designate a different hierarchy for the URL._01');
    }

    // ログインIDチェック
    $objErr->doFunc(array(t('c_Administrator: Login ID_01'), 'login_id', ID_MIN_LEN, ID_MAX_LEN), array('SPTAB_CHECK', 'NUM_RANGE_CHECK'));

    // パスワードのチェック
    $objErr->doFunc(array(t('c_Administrator: Password_01'), 'login_pass', ID_MIN_LEN, ID_MAX_LEN), array('SPTAB_CHECK', 'NUM_RANGE_CHECK'));

    // 管理機能ディレクトリのチェック
    $objErr->doFunc(array(t('c_Management area: Directory_01'), 'admin_dir', ID_MIN_LEN, ID_MAX_LEN), array('SPTAB_CHECK', 'NUM_RANGE_CHECK'));

    $oldAdminDir = SC_Utils_Ex::sfTrimURL(ADMIN_DIR);
    $newAdminDir = $objWebParam->getValue('admin_dir');
    if ($oldAdminDir !== $newAdminDir AND file_exists(HTML_REALDIR . $newAdminDir) and $newAdminDir != 'admin') {
        $objErr->arrErr['admin_dir'] = t('c_* The designated management directory already exists. Please use a different name._01');
    }

    return $objErr->arrErr;
}

// 入力内容のチェック
function lfCheckDBError($objDBParam) {
    global $objPage;

    // 入力データを渡す。
    $arrRet = $objDBParam->getHashArray();

    $objErr = new SC_CheckError($arrRet);
    $objErr->arrErr = $objDBParam->checkError();

    if (count($objErr->arrErr) == 0) {
        $arrDsn = getArrayDsn($objDBParam);
        // Debugモード指定
        $options['debug'] = PEAR_DB_DEBUG;
        $objDB = MDB2::connect($arrDsn, $options);
        // 接続成功
        if (!PEAR::isError($objDB)) {
            $dbFactory = SC_DB_DBFactory_Ex::getInstance($arrDsn['phptype']);
            // データベースバージョン情報の取得
            $objPage->tpl_db_version = $dbFactory->sfGetDBVersion($arrDsn);
        } else {
            $objErr->arrErr['all'] = '>> ' . $objDB->message . '<br />';
            // エラー文を取得する
            preg_match('/\[(.*)\]/', $objDB->userinfo, $arrKey);
            $objErr->arrErr['all'] .= $arrKey[0] . '<br />';
            GC_Utils_Ex::gfPrintLog($objDB->userinfo, INSTALL_LOG);
        }
    }
    return $objErr->arrErr;
}

// SQL文の実行
function lfExecuteSQL($filepath, $arrDsn, $disp_err = true) {
    $arrErr = array();

    if (!file_exists($filepath)) {
        $arrErr['all'] = t('c_>> The script file cannot be found_01');
    } else {
        if ($fp = fopen($filepath, 'r')) {
            $sql = fread($fp, filesize($filepath));
            fclose($fp);
        }
        // Debugモード指定
        $options['debug'] = PEAR_DB_DEBUG;
        $objDB = MDB2::connect($arrDsn, $options);
        // 接続エラー
        if (!PEAR::isError($objDB)) {
            $objDB->setCharset('utf8');

            // MySQL 用の初期化
            // XXX SC_Query を使うようにすれば、この処理は不要となる
            if ($arrDsn['phptype'] === 'mysql') {
                $objDB->exec('SET SESSION storage_engine = InnoDB');
                $objDB->exec("SET SESSION sql_mode = 'ANSI'");
            }

            $sql_split = split(';', $sql);
            foreach ($sql_split as $key => $val) {
                SC_Utils::sfFlush(true);
                if (trim($val) != '') {
                    $ret = $objDB->query($val);
                    if (PEAR::isError($ret) && $disp_err) {
                        $arrErr['all'] = '>> ' . $ret->message . '<br />';
                        // エラー文を取得する
                        preg_match('/\[(.*)\]/', $ret->userinfo, $arrKey);
                        $arrErr['all'] .= $arrKey[0] . '<br />';
                        $objPage->update_mess .= t('c_>> Table structure was not changed.<br />_01');
                        GC_Utils_Ex::gfPrintLog($ret->userinfo, INSTALL_LOG);
                        break;
                    } else {
                        GC_Utils_Ex::gfPrintLog('OK:' . $val, INSTALL_LOG);
                    }
                }
            }
        } else {
            $arrErr['all'] = '>> ' . $objDB->message;
            GC_Utils_Ex::gfPrintLog($objDB->userinfo, INSTALL_LOG);
        }
    }
    return $arrErr;
}

/**
 * シーケンスを削除する.
 *
 * @param array $arrSequences シーケンスのテーブル名, カラム名の配列
 * @param array $arrDsn データソース名の配列
 * @return array エラーが発生した場合はエラーメッセージの配列
 */
function lfDropSequence($arrSequences, $arrDsn) {
    $arrErr = array();

    // Debugモード指定
    $options['debug'] = PEAR_DB_DEBUG;
    $objDB = MDB2::connect($arrDsn, $options);
    $objManager =& $objDB->loadModule('Manager');

    // 接続エラー
    if (!PEAR::isError($objDB)) {

        $exists = $objManager->listSequences();
        foreach ($arrSequences as $seq) {
            SC_Utils::sfFlush(true);
            $seq_name = $seq[0] . '_' . $seq[1];
            if (in_array($seq_name, $exists)) {
                $result = $objManager->dropSequence($seq_name);
                if (PEAR::isError($result)) {
                    $arrErr['all'] = '>> ' . $result->message . '<br />';
                    GC_Utils_Ex::gfPrintLog($result->userinfo, INSTALL_LOG);
                } else {
                    GC_Utils_Ex::gfPrintLog('OK:' . $seq_name, INSTALL_LOG);
                }
            }
        }
    } else {
        $arrErr['all'] = '>> ' . $objDB->message;
        GC_Utils_Ex::gfPrintLog($objDB->userinfo, INSTALL_LOG);
    }
    return $arrErr;
}

/**
 * シーケンスを生成する.
 *
 * @param array $arrSequences シーケンスのテーブル名, カラム名の配列
 * @param array $arrDsn データソース名の配列
 * @return array エラーが発生した場合はエラーメッセージの配列
 */
function lfCreateSequence($arrSequences, $arrDsn) {
    $arrErr = array();

    // Debugモード指定
    $options['debug'] = PEAR_DB_DEBUG;
    $objDB = MDB2::connect($arrDsn, $options);
    $objManager =& $objDB->loadModule('Manager');

    // 接続エラー
    if (!PEAR::isError($objDB)) {

        $exists = $objManager->listSequences();
        foreach ($arrSequences as $seq) {
            SC_Utils::sfFlush(true);
            $res = $objDB->query('SELECT max(' . $seq[1] . ') FROM ' . $seq[0]);
            if (PEAR::isError($res)) {
                $arrErr['all'] = '>> ' . $res->userinfo . '<br />';
                GC_Utils_Ex::gfPrintLog($res->userinfo, INSTALL_LOG);
                return $arrErr;
            }
            $max = $res->fetchOne();

            $seq_name = $seq[0] . '_' . $seq[1];
            $result = $objManager->createSequence($seq_name, $max + 1);
            if (PEAR::isError($result)) {
                $arrErr['all'] = '>> ' . $result->message . '<br />';
                GC_Utils_Ex::gfPrintLog($result->userinfo, INSTALL_LOG);
            } else {
                GC_Utils_Ex::gfPrintLog('OK:' . $seq_name, INSTALL_LOG);
            }
        }
    } else {
        $arrErr['all'] = '>> ' . $objDB->message;
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
    $normal_url = rtrim($normal_url, '/') . '/';

    $secure_url = $objWebParam->getValue('secure_url');
    // 語尾に'/'をつける
    $secure_url = rtrim($secure_url, '/') . '/';

    // ディレクトリの取得
    $url_dir = preg_replace('|^https?://[a-zA-Z0-9_:~=&\?\.\-]+|', '', $normal_url);

    //管理機能SSL制限
    if ($objWebParam->getValue('admin_force_ssl') == 1 and strpos($secure_url, 'https://') !== FALSE) {
        $force_ssl = 'TRUE';
    } else {
        $force_ssl = 'FALSE';
    }
    //管理機能IP制限
    $allow_hosts = array();
    $hosts = $objWebParam->getValue('admin_allow_hosts');
    if (!empty($hosts)) {
        $hosts = str_replace("\r", '', $hosts);
        if (strpos($hosts, "\n") === false) {
            $hosts .= "\n";
        }
        $hosts = explode("\n", $hosts);
        foreach ($hosts as $key => $host) {
            $host = trim($host);
            if (strlen($host) >= 8) {
                $allow_hosts[] = $host;
            }
        }
    }
    //パスワード暗号化方式決定
    $arrAlgos = hash_algos();
    if (array_search('sha256', $arrAlgos) !== FALSE) {
        $algos = 'sha256';
    } elseif (array_search('sha1', $arrAlgos) !== FALSE) {
        $algos = 'sha1';
    } elseif (array_search('md5', $arrAlgos) !== FALSE) {
        $algos = 'md5';
    } else {
        $algos = '';
    }
    //MAGICハッシュワード決定
    if ($_POST['db_skip'] && defined('AUTH_MAGIC')) {
        $auth_magic = AUTH_MAGIC;
    } else {
        $auth_magic = SC_Utils_Ex::sfGetRandomString(40);
        define('AUTH_MAGIC', $auth_magic);
    }

    // FIXME 変数出力はエスケープすべき
    $config_data = "<?php\n"
                 . "define('ECCUBE_INSTALL', 'ON');\n"
                 . "define('HTTP_URL', '"              . $normal_url . "');\n"
                 . "define('HTTPS_URL', '"             . $secure_url . "');\n"
                 . "define('ROOT_URLPATH', '"          . $url_dir . "');\n"
                 . "define('DOMAIN_NAME', '"           . $objWebParam->getValue('domain') . "');\n"
                 . "define('DB_TYPE', '"               . $objDBParam->getValue('db_type') . "');\n"
                 . "define('DB_USER', '"               . $objDBParam->getValue('db_user') . "');\n"
                 . "define('DB_PASSWORD', '"           . $objDBParam->getValue('db_password') . "');\n"
                 . "define('DB_SERVER', '"             . $objDBParam->getValue('db_server') . "');\n"
                 . "define('DB_NAME', '"               . $objDBParam->getValue('db_name') . "');\n"
                 . "define('DB_PORT', '"               . $objDBParam->getValue('db_port') . "');\n"
                 . "define('ADMIN_DIR', '"             . $objWebParam->getValue('admin_dir') . "/');\n"
                 . "define('ADMIN_FORCE_SSL', "        . $force_ssl . ");\n"
                 . "define('ADMIN_ALLOW_HOSTS', '"     . serialize($allow_hosts) . "');\n"
                 . "define('AUTH_MAGIC', '"            . $auth_magic . "');\n"
                 . "define('PASSWORD_HASH_ALGOS', '"   . $algos . "');\n"
                 . "define('MAIL_BACKEND', '"          . $objWebParam->getValue('mail_backend') . "');\n"
                 . "define('SMTP_HOST', '"             . $objWebParam->getValue('smtp_host') . "');\n"
                 . "define('SMTP_PORT', '"             . $objWebParam->getValue('smtp_port') . "');\n"
                 . "define('SMTP_USER', '"             . $objWebParam->getValue('smtp_user') . "');\n"
                 . "define('SMTP_PASSWORD', '"         . $objWebParam->getValue('smtp_password') . "');\n";

    if ($fp = fopen(CONFIG_REALFILE, 'w')) {
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
    $alldirs[] = $dir;
    $dirs = glob($dir . '/*');
    if (is_array($dirs) && count($dirs) > 0) {
        foreach ($dirs as $d) {
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
    return array(
        array('dtb_best_products', 'best_id'),
        array('dtb_bloc', 'bloc_id'),
        array('dtb_category', 'category_id'),
        array('dtb_class', 'class_id'),
        array('dtb_classcategory', 'classcategory_id'),
        array('dtb_csv', 'no'),
        array('dtb_csv_sql', 'sql_id'),
        array('dtb_customer', 'customer_id'),
        array('dtb_deliv', 'deliv_id'),
        array('dtb_holiday', 'holiday_id'),
        array('dtb_kiyaku', 'kiyaku_id'),
        array('dtb_mail_history', 'send_id'),
        array('dtb_maker', 'maker_id'),
        array('dtb_member', 'member_id'),
        array('dtb_module_update_logs', 'log_id'),
        array('dtb_news', 'news_id'),
        array('dtb_order', 'order_id'),
        array('dtb_order_detail', 'order_detail_id'),
        array('dtb_other_deliv', 'other_deliv_id'),
        array('dtb_pagelayout', 'page_id'),
        array('dtb_payment', 'payment_id'),
        array('dtb_products_class', 'product_class_id'),
        array('dtb_products', 'product_id'),
        array('dtb_review', 'review_id'),
        array('dtb_send_history', 'send_id'),
        array('dtb_mailmaga_template', 'template_id'),
        array('dtb_plugin', 'plugin_id'),
        array('dtb_plugin_hookpoint', 'plugin_hookpoint_id'),
        array('dtb_api_config', 'api_config_id'),
        array('dtb_api_account', 'api_account_id'),
    );
}


/**
 * 管理機能のディレクトリ名の変更
 *
 * @param string 設定する管理機能のディレクトリ名
 */
function renameAdminDir($adminDir) {
    $oldAdminDir = SC_Utils_Ex::sfTrimURL(ADMIN_DIR);
    if ($adminDir === $oldAdminDir) {
        return true;
    }
    if (file_exists(HTML_REALDIR . $adminDir)) {
        return t('c_* The designated management directory already exists. Please use  a different name._01');
    }
    if (!rename(HTML_REALDIR . $oldAdminDir, HTML_REALDIR . $adminDir)) {
        return  t('c_* Renaming to T_ARG1 failed. Check directory access._01', array('T_ARG1' => HTML_REALDIR . $adminDir));
    }
    return true;
}

function getArrayDsn(SC_FormParam $objDBParam) {
    $arrRet = $objDBParam->getHashArray();

    if (!defined('DB_TYPE')) {
        define('DB_TYPE', $arrRet['db_type']);
    }

    $arrDsn = array(
        'phptype'   => $arrRet['db_type'],
        'username'  => $arrRet['db_user'],
        'password'  => $arrRet['db_password'],
        'database'  => $arrRet['db_name'],
        'port'      => $arrRet['db_port'],
    );

    // 文字列形式の DSN との互換処理
    if (strlen($arrRet['db_server']) >= 1 && $arrRet['db_server'] !== '+') {
        $arrDsn['hostspec'] = $arrRet['db_server'];
    }

    return $arrDsn;
}
