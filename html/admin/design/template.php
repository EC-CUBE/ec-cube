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

// ǧ�ڲ��ݤ�Ƚ��
$objSession = new SC_Session();
sfIsSuccess($objSession);

class LC_Page {
    var $tpl_mainpage = 'design/template.tpl';
    var $tpl_subnavi  = 'design/subnavi.tpl';
    var $tpl_subno    = 'template';
    var $tpl_mainno   = "design";
    var $tpl_subtitle = '�ƥ�ץ졼������';

    var $arrErr  = array();
    var $arrForm = array();
}

$objPage = new LC_Page();

// uniqid��ƥ�ץ졼�Ȥ�������
$objPage->uniqid = $objSession->getUniqId();

$objView = new SC_AdminView();

switch(lfGetMode()) {

// ��Ͽ�ܥ��󲡲���
case 'register':
    // �������ܤ������������å�
    if (!sfIsValidTransition($objSession)) {
        sfDispError('');
    }
    // �ѥ�᡼������
    $objForm = lfInitRegister();
    if ($objForm->checkError()) {
        sfDispError('');
    }

    $template_code = $objForm->getValue('template_code');

    if ($template_code == 'default') {
        lfRegisterTemplate('');
        $objPage->tpl_onload="alert('��Ͽ����λ���ޤ�����');";
        break;
    }

    // DB�ػ��Ѥ���ƥ�ץ졼�Ȥ���Ͽ
    lfRegisterTemplate($template_code);

    // �ƥ�ץ졼�Ȥξ��
    lfChangeTemplate($template_code);

    // XXX ����ѥ���ե�����Υ��ꥢ������Ԥ�
    $objView->_smarty->clear_compiled_tpl();

    // ��λ��å�����
    $objPage->tpl_onload="alert('��Ͽ����λ���ޤ�����');";
    break;

// ����ܥ��󲡲���
case 'delete':
    // �������ܤ������������å�
    if (!sfIsValidTransition($objSession)) {
        sfDispError('');
    }
    // �ѥ�᡼������
    $objForm = lfInitDelete();
    if ($objForm->checkError()) {
        sfDispError('');
    }

    $template_code = $objForm->getValue('template_code_delete');
    if ($template_code == lfGetNowTemplate()) {
        $objPage->tpl_onload = "alert('������Υƥ�ץ졼�ȤϺ������ޤ���');";
        break;
    }

    lfDeleteTemplate($template_code);
    break;

// �ץ�ӥ塼�ܥ��󲡲���
case 'preview':
    break;

default:
    break;
}

// default�ѥ�᡼���Υ��å�
$objPage->templates = lfGetAllTemplates();
$objPage->now_template = lfGetNowtemplate();

// ���̤�ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

/**
 * POST�����mode�ѥ�᡼�����������.
 *
 * @param void
 * @return string mode�ѥ�᡼��, ̵�����null
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
 * ����Ŭ�Ѥ��Ƥ���ƥ�ץ졼�ȥѥå�����̾���������.
 *
 * @param void
 * @return string �ƥ�ץ졼�ȥѥå�����̾
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
 * ���Ѥ���ƥ�ץ졼�Ȥ�DB����Ͽ����
 */
function lfRegisterTemplate($template_code) {
    $objQuery = new SC_Query();
    $objQuery->update(
        'dtb_baseinfo',
        array('top_tpl'=> $template_code)
    );
}
/**
 * �ƥ�ץ졼�Ȥ��񤭥��ԡ�����.
 */
function lfChangeTemplate($template_code){
    $from = TPL_PKG_PATH . $template_code . '/user_edit/';

    if (!file_exists($from)) {
        $mess = $from . '��¸�ߤ��ޤ���';
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
