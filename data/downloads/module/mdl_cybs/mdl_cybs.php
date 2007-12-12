<?php
require_once 'mdl_cybs.inc';
require_once 'class/mdl_cybs_config.php';

class LC_Page {
    //コンストラクタ
    function LC_Page() {
        //メインテンプレートの指定
        $this->tpl_mainpage = MODULE_PATH . 'mdl_cybs/mdl_cybs.tpl';
        $this->tpl_subtitle = 'サイバーソース決済モジュール';
        $this->extension_installed = lfIsInstalledCybsExt();
    }
}

$objPage = new LC_Page;
$objView = new SC_AdminView;

$objForm = lfInitParam();
$objPage->arrForm = $objForm->getFormParamList();

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
    $objPage->tpl_onload = 'alert("登録完了しました。"); window.close();';
    break;

// モジュールの削除
case 'module_del':
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
function lfInitParam($arrParam = null) {
    $objForm = new SC_FormParam;
    $objForm->addParam('リクエスト先', 'cybs_request_url', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('マーチャントID', 'cybs_merchant_id', MTEXT_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('鍵・証明書パス', 'cybs_key_path', MTEXT_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), MDL_CYBS_KEY_PATH);
    $objForm->addParam('サブスクリプションサービス', 'cybs_subs_use', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

    if (empty($arrParam)) {
        $arrParam = $_POST;
    }
    $objForm->setParam($arrParam);
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

    $mid = $objForm->getValue('cybs_merchant_id');
    $basePath = $objForm->getValue('cybs_key_path');
    $arrFileTypes = array('crt', 'pvt', 'pwd');

    // 鍵・証明書の存在チェック
    $notFound = false;
    foreach ($arrFileTypes as $fileType) {
        // /opt/CyberSource/SDK/keys/***.crt
        $path = $basePath . $mid . '.' . $fileType;
        if (!file_exists($path)) {
            $notFound = true;
            break;
        }
    }
    if ($notFound) {
        return array('cybs_key_path' => $path . "が見つかりません。<br>");
    }
    return null;
}

/**
 * mod_cybsがインストール済みかチェックする.
 *
 * @return boolean
 */
function lfIsInstalledCybsExt() {
    if (!extension_loaded(MDL_CYBS_EXT)) {
        if (!dl(MDL_CYBS_EXT)) {
            return false;
        }
    }
    return true;
}
?>
