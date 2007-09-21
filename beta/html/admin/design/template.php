<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once '../require.php';
require_once DATA_PATH . "module/Tar.php";
require_once DATA_PATH . "module/SearchReplace.php";
require_once DATA_PATH . "include/file_manager.inc";

// 認証可否の判定
$objSession = new SC_Session();
sfIsSuccess($objSession);

class LC_Page {
    var $tpl_mainpage = 'design/template.tpl';
    var $tpl_subnavi  = 'design/subnavi.tpl';
    var $tpl_subno    = 'template';
    var $tpl_mainno   = "design";
    var $tpl_subtitle = 'テンプレート設定';

    var $arrErr  = array();
    var $arrForm = array();
}

$objPage = new LC_Page();

// uniqidをテンプレートへ埋め込み
$objPage->uniqid = $objSession->getUniqId();

$objView = new SC_AdminView();

switch(lfGetMode()) {

// 登録ボタン押下時
case 'register':
    // 画面遷移の正当性チェック
    if (!sfIsValidTransition($objSession)) {
        sfDispError('');
    }
    // パラメータ検証
    $objForm = lfInitRegister();
    if ($objForm->checkError()) {
        sfDispError('');
    }

    $template_code = $objForm->getValue('template_code');

    if ($template_code == 'default') {
        lfRegisterTemplate('');
        $objPage->tpl_onload="alert('登録が完了しました。');";
        break;
    }

    // DBへ使用するテンプレートを登録
    lfRegisterTemplate($template_code);

    // テンプレートの上書き
    lfChangeTemplate($template_code);

    // XXX コンパイルファイルのクリア処理を行う
    $objView->_smarty->clear_compiled_tpl();

    // 完了メッセージ
    $objPage->tpl_onload="alert('登録が完了しました。');";
    break;

// 削除ボタン押下時
case 'delete':
    // 画面遷移の正当性チェック
    if (!sfIsValidTransition($objSession)) {
        sfDispError('');
    }
    // パラメータ検証
    $objForm = lfInitDelete();
    if ($objForm->checkError()) {
        sfDispError('');
    }

    $template_code = $objForm->getValue('template_code_delete');
    if ($template_code == lfGetNowTemplate()) {
        $objPage->tpl_onload = "alert('選択中のテンプレートは削除出来ません');";
        break;
    }

    lfDeleteTemplate($template_code);
    break;

// プレビューボタン押下時
case 'preview':
    break;

default:
    break;
}

// defaultパラメータのセット
$objPage->templates = lfGetAllTemplates();
$objPage->now_template = lfGetNowtemplate();

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

/**
 * POSTされるmodeパラメータを取得する.
 *
 * @param void
 * @return string modeパラメータ, 無ければnull
 */
function lfGetMode(){
    if (isset($_POST['mode'])) return $_POST['mode'];
}

function lfInitRegister() {
    $objForm = new SC_FormParam();
    $objForm->addParam(
        'template_code', 'template_code', STEXT_LEN, '',
        array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "ALNUM_CHECK")
    );
    $objForm->setParam($_POST);

    return $objForm;
}

function lfInitDelete() {
    $objForm = new SC_FormParam();
    $objForm->addParam(
        'template_code_delete', 'template_code_delete', STEXT_LEN, '',
        array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "ALNUM_CHECK")
    );
    $objForm->setParam($_POST);

    return $objForm;
}

/**
 * 現在適用しているテンプレートパッケージ名を取得する.
 *
 * @param void
 * @return string テンプレートパッケージ名
 */
function lfGetNowTemplate() {
    $objQuery = new SC_Query();
    $arrRet = $objQuery->select('top_tpl', 'dtb_baseinfo');
    if (isset($arrRet[0]['top_tpl'])) {
        return $arrRet[0]['top_tpl'];
    }
    return null;
}

/**
 * 使用するテンプレートをDBへ登録する
 */
function lfRegisterTemplate($template_code) {
    $objQuery = new SC_Query();
    $objQuery->update(
        'dtb_baseinfo',
        array('top_tpl'=> $template_code)
    );
}
/**
 * テンプレートを上書きコピーする.
 */
function lfChangeTemplate($template_code){
    $from = TPL_PKG_PATH . $template_code . '/user_edit/';

    if (!file_exists($from)) {
        $mess = $from . 'は存在しません';
    } else {
        $to = USER_PATH;
        $mess = sfCopyDir($from, $to, '', true);
    }
    return $mess;
}

function lfGetAllTemplates() {
    $objQuery = new SC_Query();
    $arrRet = $objQuery->select('*', 'dtb_templates');
    if (empty($arrRet)) return array();

    return $arrRet;
}

function lfDeleteTemplate($template_code) {
    $objQuery = new SC_Query();
    $objQuery->delete('dtb_templates', 'template_code = ?', array($template_code));

    sfDelFile(TPL_PKG_PATH . $template_code);
}
?>
