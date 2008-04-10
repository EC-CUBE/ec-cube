<?php
/**
 * モジュールバージョン表記
 * @version CVS: $Id$
 */
require_once 'mdl_cybs.inc';
require_once 'class/mdl_cybs_config.php';

class LC_Page {
    //コンストラクタ
    function LC_Page() {
        //メインテンプレートの指定
        $this->tpl_mainpage = MODULE_PATH . 'mdl_cybs/mdl_cybs.tpl';
        $this->tpl_subtitle = 'サイバーソース決済モジュール';
        $this->extension_installed = sfCybsLoadModCybs();
    }
}

$objPage = new LC_Page;
$objView = new SC_AdminView;

$objForm = lfInitParam($_POST);
$objPage->arrForm = $objForm->getFormParamList();

sfAlterMemo(); // dtb_memoにmemoカラムを追加する

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
switch($mode) {
// 入力項目の登録
case 'edit':
    if ($arrErr = lfCheckError($objForm)) {
        $objPage->arrErr = $arrErr;
        break;
    }

    $objConfig =& Mdl_Cybs_Config::getInstanse();
    $objConfig->registerConfig($objConfig->createSqlArray($objForm));
    lfCreateBatchIdTable();
    $objPage->tpl_onload = 'alert("登録完了しました。\n基本情報＞支払方法設定より詳細設定をしてください。"); window.close();';
    break;

// 通常表示
default:
    // DBの登録値を取得する.
    $objConfig =& Mdl_Cybs_Config::getInstanse();
    $arrConfig = $objConfig->getConfig();

    // DBに値が登録されていればその値を表示させる
    if (!empty($arrConfig)) {
        $objForm = lfInitParam($arrConfig);
        $objPage->arrForm = $objForm->getFormParamList();
    }
}

$objView->assignObj($objPage);
$objView->display($objPage->tpl_mainpage);
//sfPrintR($objView->_smarty->get_template_vars());

/**
 * パラメータの初期化
 *
 * @param array
 * @return SC_FormParam
 */
function lfInitParam($arrParam) {
    $objForm = new SC_FormParam;
    $objForm->addParam('リクエスト先', 'cybs_request_url', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('マーチャントID', 'cybs_merchant_id', MTEXT_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('オンデマンド課金', 'cybs_ondemand_use', 1, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('3Dセキュア認証', 'cybs_3d_use', 1, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->setParam($arrParam);
    $objForm->convParam();
    return $objForm;
}
/**
 * エラーチェックを行う
 *
 * @param SC_FormParam $objForm
 * @return array|null
 */
function lfCheckError($objForm) {
    $arrErr = $objForm->checkError();
    if ($arrErr) return $arrErr;

    return null;
}

/**
 * バッチID取得用のテーブルを作成する
 *
 */
function lfCreateBatchIdTable() {
    $objQuery = new SC_Query();
    if (sfTabaleExists('dtb_cybs_batch_id')) {
        return;
    }
    $sql_mysql = "create table dtb_cybs_batch_id(batch_id int auto_increment primary key NOT NULL) TYPE=InnoDB;";
    $sql_pgsql = "create table dtb_cybs_batch_id(batch_id serial NOT NULL);";

    $sql = '';
    if (DB_TYPE == 'pgsql') {
        $sql = $sql_pgsql;
    } elseif (DB_TYPE == 'mysql') {
        $sql = $sql_mysql;
    }
    $objQuery->query($sql);
}

?>
