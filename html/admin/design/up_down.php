<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once '../require.php';
require_once DATA_PATH . "module/Tar.php";
require_once DATA_PATH . 'include/file_manager.inc';

// ����������å�
$objSession = new SC_Session();
sfIsSuccess($objSession);

class LC_Page {
    var $tpl_mainpage = 'design/up_down.tpl';
    var $tpl_subnavi  = 'design/subnavi.tpl';
    var $tpl_subno    = 'up_down';
    var $tpl_mainno   = "design";
    var $tpl_subtitle = '���åץ���/���������';

    var $arrErr  = array();
    var $arrForm = array();
}

$objPage = new LC_Page();
$objPage->now_template = lfGetNowTemplate();

// uniqid��ƥ�ץ졼�Ȥ�������
$objPage->uniqid = $objSession->getUniqId();

switch(lfGetMode()) {

// ��������ɥܥ��󲡲����ν���
case 'download':
    // �������ܤ������������å�
    if (!sfIsValidTransition($objSession)) {
        sfDispError('');
    }
    lfDownloadCreatedFiles();
    exit;
    break;

// ���åץ��ɥܥ��󲡲����ν���
case 'upload':
    // �������ܤ������������å�
    if (!sfIsValidTransition($objSession)) {
        sfDispError('');
    }
    // �ե�����ѥ�᡼�������
    $objForm = lfInitUpload();
    // ���顼�����å�
    if ($arrErr = lfValidateUpload($objForm)) {
        $objPage->arrErr  = $arrErr;
        $objPage->arrForm = $objForm->getFormParamList();
        break;
    }
    // ���åץ��ɥե���������
    $objUpFile = lfInitUploadFile($objForm);
    // ����ե��������¸
    $errMsg = $objUpFile->makeTempFile('template_file', false);
    // �񤭹��ߥ��顼�����å�
    if(isset($errMsg)) {
        $objPage->arrErr['template_file'] = $errMsg;
        $objPage->arrForm = $objForm->getFormParamList();
        break;
    }
    lfAddTemplates($objForm, $objUpFile);
    $objPage->tpl_onload = "alert('�ƥ�ץ졼�ȥե�����򥢥åץ��ɤ��ޤ�����');";
    break;

// ���ɽ��
default:
    break;
}

// ���̤�ɽ��
$objView = new SC_AdminView();
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
/**
 * SC_UploadFile���饹�ν����.
 *
 * @param object $objForm SC_FormParam�Υ��󥹥���
 * @return object SC_UploadFile�Υ��󥹥���
 */
function lfInitUploadFile($objForm) {
    $pkg_dir = TPL_PKG_PATH . $objForm->getValue('template_code');
    $objUpFile = new SC_UploadFile(TEMPLATE_TEMP_DIR, $pkg_dir);
    $objUpFile->addFile("�ƥ�ץ졼�ȥե�����", 'template_file', array(), TEMPLATE_SIZE, true, 0, 0, false);

    return $objUpFile;
}
/**
 * SC_FormParam���饹�ν����.
 *
 * @param void
 * @retrun object SC_FormParam�Υ��󥹥���
 */
function lfInitUpload() {
    $objForm = new SC_FormParam;

    $objForm->addParam("�ƥ�ץ졼�ȥ�����", "template_code", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "ALNUM_CHECK"));
    $objForm->addParam("�ƥ�ץ졼��̾", "template_name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
    $objForm->setParam($_POST);

    return $objForm;
}
/**
 * upload�⡼�ɤΥѥ�᡼�����ڤ�Ԥ�.
 *
 * @param object $objForm SC_FormParam�Υ��󥹥���
 * @return array ���顼������Ǽ����Ϣ������, ���顼��̵�����(¿ʬ)null���֤�
 */
function lfValidateUpload($objForm) {
    $arrErr = $objForm->checkError();
    if (!empty($arrErr)) {
        return $arrErr;
    }

    $arrForm = $objForm->getHashArray();

    // Ʊ̾�Υե������¸�ߤ�����ϥ��顼
    if(file_exists(USER_TEMPLATE_PATH . $arrForm['template_code'])) {
        $arrErr['template_code'] = "�� Ʊ̾�Υե����뤬���Ǥ�¸�ߤ��ޤ���<br/>";
    }

    // ��Ͽ�ԲĤ�ʸ��������å�
    $arrIgnoreCode = array(
        'admin', 'mobile', 'default'
    );
    if(in_array($arrForm['template_code'], $arrIgnoreCode)) {
        $arrErr['template_code'] = "�� ���Υƥ�ץ졼�ȥ����ɤϻ��ѤǤ��ޤ���<br/>";
    }

    // DB�ˤ��Ǥ���Ͽ����Ƥ��ʤ��������å�
    $objQuery = new SC_Query();
    $ret = $objQuery->count("dtb_templates", "template_code = ?", array($arrForm['template_code']));
    if(!empty($ret)) {
        $arrErr['template_code'] = "�� ���Ǥ���Ͽ����Ƥ���ƥ�ץ졼�ȥ����ɤǤ���<br/>";
    }

    // �ե�����γ�ĥ�ҥ����å�(.tar/tar.gz�Τߵ���)
    $errFlag = true;
    $array_ext = explode(".", $_FILES['template_file']['name']);
    $ext = $array_ext[ count ( $array_ext ) - 1 ];
    $ext = strtolower($ext);
    // .tar�����å�
    if ($ext == 'tar') {
        $errFlag = false;
    }
    $ext = $array_ext[ count ( $array_ext ) - 2 ].".".$ext;
    $ext = strtolower($ext);
    // .tar.gz�����å�
    if ($ext== 'tar.gz') {
        $errFlag = false;
    }

    if($errFlag) {
        $arrErr['template_file'] = "�� ���åץ��ɤ���ƥ�ץ졼�ȥե�����ǵ��Ĥ���Ƥ�������ϡ�tar/tar.gz�Ǥ���<br />";
    }

    return $arrErr;
}
/**
 * DB�����TPL_PKG_PATH�˥ƥ�ץ졼�ȥѥå��������ɲä���.
 *
 * @param object $objForm SC_FormParam�Υ��󥹥���
 * @param object $objUpFile SC_UploadFile�Υ��󥹥���
 * @return void
 */
function lfAddTemplates($objForm, $objUpFile) {
    $template_code = $objForm->getValue('template_code');
    $template_dir = TPL_PKG_PATH . $objForm->getValue('template_code');
    $compile_dir  = COMPILE_DIR . "/$template_code";
    // �ե��������
    mkdir($template_dir);
    mkdir($compile_dir);
    // ����ե����������¸�ǥ��쥯�ȥ�ذ�ư
    $objUpFile->moveTempFile();
    // ����
    lfUnpacking($template_dir, $_FILES['template_file']['name']);
    // DB�˥ƥ�ץ졼�Ⱦ������¸
    lfRegisterTemplates($objForm->getHashArray());
}
/**
 * ���åץ��ɤ��줿tar���������֤���ह��.
 *
 * TODO �������狼��ˤ����Τ�ľ��,
 * $file_name��$objUpFile�ν��������TPL_PKG_PATH����¸��ˤʤäƤ��뤿��ɬ��
 *
 * @param string $dir ������ǥ��쥯�ȥ�
 * @param strin $file_name ���������֤Υե�����̾
 * @return string Archive_Tar::extractModify()�Υ��顼
 */
function lfUnpacking($dir, $file_name) {

    // ���̥ե饰TRUE��gzip����򤪤��ʤ�
    $tar = new Archive_Tar("$dir/$file_name", true);

    // ��ĥ�Ҥ��ڤ���
    $unpacking_name = preg_replace("/(\.tar|\.tar\.gz)$/", "", $file_name);

    // ���ꤵ�줿�ե������˲��ह��
    $err = $tar->extractModify("$dir/", $unpacking_name);

    // �ե�������
    @sfDelFile("$dir/$unpacking_name");
    // ���̥ե�������
    @unlink("$dir/$file_name");

    return $err;
}
/**
 * dtb_templates���������Ƥ���Ͽ����.
 *
 * @param array $arrForm POST���줿�ѥ�᡼��
 * @return void
 */
function lfRegisterTemplates($arrForm) {
    $objQuery = new SC_Query();
    $objQuery->insert('dtb_templates', $arrForm);
}
/**
 * �桼�������������ե�����򥢡������֤���������ɤ�����
 * TODO �ץ�ե��������
 * @param void
 * @return void
 */
function lfDownloadCreatedFiles() {
    $dlFileName = 'tpl_package_' . date('YmdHis') . '.tar.gz';
    $tmpDir = TEMPLATE_TEMP_DIR . time() . '/';
    $tmpUserEditDir = $tmpDir . 'user_edit/';

    if (!mkdir($tmpDir)) return ;
    if (!mkdir($tmpDir . 'templates')) return ;
    if (!mkdir($tmpUserEditDir)) return ;

    lfCopyTplPackage($tmpDir);
    lfCopyUserEdit($tmpUserEditDir);

    // �ե������������
    $arrFileHash = sfGetFileList($tmpDir);
    foreach($arrFileHash as $val) {
        $arrFileList[] = $val['file_name'];
    }

    // �ǥ��쥯�ȥ���ư
    chdir($tmpDir);
    // ���̤򤪤��ʤ�
    $tar = new Archive_Tar($dlFileName, true);
    $tar->create($arrFileList);

    // �����������HTTP�إå�����
    header("Content-disposition: attachment; filename=${dlFileName}");
    header("Content-type: application/octet-stream; name=${dlFileName}");
    header("Content-Length: " . filesize($dlFileName));
    readfile($dlFileName);

    // ���̥ե�������
    unlink($dlFileName);
    // ����ե�������
    sfDelFile($tmpDir);
}
/**
 * �ǥ���������Ǻ������줿�ե������upload/temp_template/�ʲ��˥��ԡ�����
 *
 * @param string $to
 * @return void
 */
function lfCopyUserEdit($to) {
    $arrDirs = array(
        'css',
        'include',
        'templates'
    );

    foreach ($arrDirs as $dir) {
        $from = USER_PATH .  $dir;
        sfCopyDir($from, $to, '', true);
    }
}
/**
 * �������򤷤Ƥ���ƥ�ץ졼�ȥѥå�������upload/temp_template/�ʲ��˥��ԡ�����
 *
 * @param string $to ��¸��ѥ�
 * @return void
 */
function lfCopyTplPackage($to) {
    $nowTpl = lfGetNowTemplate();
    if (!$nowTpl) return;

    $from = TPL_PKG_PATH . $nowTpl . '/';
    sfCopyDir($from, $to, '');
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

?>
