<?php
/**
 * 
 * @copyright    2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version CVS: $Id: 1.0 2006-06-04 06:38:01Z kakinaka $ 
 * @link        http://www.lockon.co.jp/
 *
 */
require_once(MODULE_PATH . "mdl_movilink/mdl_movilink.inc");

//�ڡ����������饹
class LC_Page {
    //���󥹥ȥ饯��
    function LC_Page() {
        //�ᥤ��ƥ�ץ졼�Ȥλ���
        $this->tpl_mainpage = MODULE_PATH . 'mdl_movilink/mdl_movilink.tpl';
        $this->tpl_subtitle = MODULE_NAME;
    }
}
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// ǧ�ڳ�ǧ
$objSess = new SC_Session();
sfIsSuccess($objSess);

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
$objFormParam = lfInitParam($objFormParam);
// POST�ͤμ���
$objFormParam->setParam($_POST);

// ���ѹ��ܤ��ɲ�(ɬ�ܡ���)
sfAlterMemo();

switch($_POST['mode']) {
case 'edit':
    // ���ϥ��顼Ƚ��
    $objPage->arrErr = lfCheckError();
	
    // ���顼�ʤ��ξ��ˤϥǡ����򹹿�    
    if(count($objPage->arrErr) == 0) {
        // �ǡ�������
        sfSetModuleDB();
        // ����������
        sfMakeMoviLinkColumn();
        // CSV�쥳���ɤ�����
        sfSetMoviLinkCSV();
        // javascript�¹�
        $objPage->tpl_onload = 'alert("��Ͽ��λ���ޤ�����"); window.close();';
    }
    break;
case 'module_del':
    // ���ѹ��ܤ�¸�ߥ����å�
    if(sfColumnExists("dtb_payment", "memo01")){
        // �ǡ����κ���ե饰�򤿤Ƥ�
        $objQuery->query("UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ?", array(MDL_MOVILINK_ID));
    }
    break;
default:
    // �ǡ����Υ���
    lfLoadData();    
    break;
}

$objPage->arrForm = $objFormParam->getFormParamList();

$objView->assignobj($objPage);                    //�ѿ���ƥ�ץ졼�Ȥ˥������󤹤�
$objView->display($objPage->tpl_mainpage);        //�ƥ�ץ졼�Ȥν���
//-------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam($objFormParam) {
    $objFormParam->addParam("EC������ID", "site_id", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));	
	$objFormParam->addParam("���ơ�����", "status", 1, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
    return $objFormParam;
}

// ���顼�����å���Ԥ�
function lfCheckError(){
    global $objFormParam;
    $arrErr = $objFormParam->checkError();
    return $arrErr;
}

// ��Ͽ�ǡ������ɤ߹���
function lfLoadData(){
    global $objFormParam;
    //�ǡ��������
    $arrRet = sfGetModuleDB();
    // �ͤ򥻥å�
    $objFormParam->setParam($arrRet);
}
?>