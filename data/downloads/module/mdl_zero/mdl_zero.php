<?php
/**
 * 
 * @copyright    2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version CVS: $Id: 1.0 2006-06-04 06:38:01Z kakinaka $ 
 * @link        http://www.lockon.co.jp/
 *
 */
require_once(MODULE_PATH . "mdl_zero/mdl_zero.inc");

//�ڡ����������饹
class LC_Page {
    //���󥹥ȥ饯��
    function LC_Page() {
        //�ᥤ��ƥ�ץ졼�Ȥλ���
        $this->tpl_mainpage = MODULE_PATH . 'mdl_zero/mdl_zero.tpl';
        $this->tpl_subtitle = '�����ѥ⥸�塼��';
    }
}
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// ���쥸�åȥ����å�
lfZeroCheck();

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
        lfUpdPaymentDB();
        
        // javascript�¹�
        $objPage->tpl_onload = 'alert("��Ͽ��λ���ޤ�����\n���ܾ�����ʧ��ˡ������ܺ�����򤷤Ƥ���������"); window.close();';
    }
    break;
case 'module_del':
    // ���ѹ��ܤ�¸�ߥ����å�
    if(sfColumnExists("dtb_payment", "memo01")){
        // �ǡ����κ���ե饰�򤿤Ƥ�
        $objQuery->query("UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ?", array(MDL_ZERO_ID));
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
    $objFormParam->addParam("PC��", "pc", INT_LEN, "n");
    $objFormParam->addParam("����Ź������", "pc_send", SEND_LEN, "KVa", array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
    $objFormParam->addParam("���ȥ����� ", "pc_clientip", CLIENTIP_LEN, "KVa", array("MAX_LENGTH_CHECK", "NUM_CHECK"));

    $objFormParam->addParam("������", "mobile", INT_LEN, "n");
    $objFormParam->addParam("����Ź������", "mobile_send", SEND_LEN, "KVa", array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
    $objFormParam->addParam("���ȥ����� ", "mobile_clientip", CLIENTIP_LEN, "KVa", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    
    return $objFormParam;
}

// ���顼�����å���Ԥ�
function lfCheckError(){
    global $objFormParam;
    
    $arrErr = $objFormParam->checkError();
    
    if($_POST["pc"]){
        if(empty($_POST["pc_send"])) $arrErr["pc_send"] = "�� ����Ź������ �����Ϥ���Ƥ��ޤ���<br>";
        if(empty($_POST["pc_clientip"])) $arrErr["pc_clientip"] = "�� ���ȥ����� �����Ϥ���Ƥ��ޤ���<br>";
    }

    if($_POST["mobile"]){
        if(empty($_POST["mobile_send"])) $arrErr["mobile_send"] = "�� ����Ź������ �����Ϥ���Ƥ��ޤ���<br>";
        if(empty($_POST["mobile_clientip"])) $arrErr["mobile_clientip"] = "�� ���ȥ����� �����Ϥ���Ƥ��ޤ���<br>";
    }
    
    // ��³�����å���Ԥ�
    if(count($arrErr) == 0) $arrErr = lfChkConnect();
    
    return $arrErr;
}

// ��³�����å���Ԥ�
function lfChkConnect(){
    $arrRet = array();
    
    // PC�Ǥ���³��ǧ
    if($_POST["pc"]){
        // �����ǡ�������
        $arrSendData = array(
            'clientip' => $_POST["pc_clientip"],    // ���ȥ�����
            'custom' => SEND_PARAM_CUSTOM,            // yes����
            'send' => $_POST["pc_send"],            // ����Ź������
            'money' => 0                            // ���
        );
        
        // �ǡ�������
        $arrResponse = sfPostPaymentData(SEND_PARAM_PC_URL, $arrSendData, false);
        
        // ���顼�����뤫�����å�����
        if(!ereg("^<HTML>",$arrResponse )){
            $arrRet["pc_clientip"] = "���ϥǡ���������������ޤ���<br>";
        }
    }

    // �����Ǥ���³��ǧ
    if($_POST["mobile"]){
        // �����ǡ�������
        $arrSendData = array(
            'clientip' => $_POST["mobile_clientip"],    // ���ȥ�����
            'act' => SEND_PARAM_ACT,                    // imode����
            'money' => 0                                // ���
        );
         
        // �ǡ�������
        $arrResponse = sfPostPaymentData(SEND_PARAM_MOBILE_URL, $arrSendData, false);
        
        // ���顼�����뤫�����å�����
        if(!ereg("^<HTML>",$arrResponse )){
            $arrRet["mobile_clientip"] = "���ϥǡ���������������ޤ���<br>";
        }
    }
    
    return $arrRet;    
}

// ��Ͽ�ǡ������ɤ߹���
function lfLoadData(){
    global $objFormParam;
    
    //�ǡ��������
    $arrRet = lfGetPaymentDB(" AND del_flg = '0'");
    
    // �ͤ򥻥å�
    $objFormParam->setParam($arrRet[0]);

    // ����ɽ���Ѥ˥ǡ������Ѵ�
    $arrDisp = array();
    $arrDisp = $arrRet[0];
    if (!empty($arrDisp["pc_send"])) $arrDisp["pc"] = 1;
    if (!empty($arrDisp["mobile_send"])) $arrDisp["mobile"] = 1;
    $objFormParam->setParam($arrDisp);
}

// DB����ǡ������������
function lfGetPaymentDB($where = "", $arrWhereVal = array()){
    global $objQuery;
    
    $arrVal = array(MDL_ZERO_ID);
    $arrVal = array_merge($arrVal, $arrWhereVal);
    
    $arrRet = array();
    $sql = "SELECT 
                module_id, 
                memo01 as pc_send, 
                memo02 as pc_clientip,
                memo04 as mobile_send, 
                memo05 as mobile_clientip
            FROM dtb_payment WHERE module_id = ? " . $where;
    $arrRet = $objQuery->getall($sql, $arrVal);

    return $arrRet;
}


// �ǡ����ι�������
function lfUpdPaymentDB(){
    global $objQuery;
    global $objSess;
    $arrData = array();

    // del_flg�����ˤ��Ƥ���
    $del_sql = "UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ? ";
    $arrDel = array(MDL_ZERO_ID);
    $objQuery->query($del_sql, $arrDel);

    // PC�ѥǡ�����Ͽ
    if($_POST["pc"]){
		$arrData["payment_method"] = "Zero���쥸�å�";
		$arrData["fix"] = 3;
		$arrData["creator_id"] = $objSess->member_id;
		$arrData["update_date"] = "now()";
		$arrData["module_id"] = MDL_ZERO_ID;
		$arrData["module_path"] = MODULE_PATH . "mdl_zero/card.php";
		$arrData["memo01"] = $_POST["pc_send"];
		$arrData["memo02"] = $_POST["pc_clientip"];
		$arrData["memo03"] = ZERO_CREDIT_ID;
		$arrData["del_flg"] = "0";
    }
    
    // �����ѥǡ�����Ͽ
    if($_POST["mobile"]){
		$arrData["payment_method"] = "Zero���쥸�å�";
		$arrData["fix"] = 3;
		$arrData["creator_id"] = $objSess->member_id;
		$arrData["update_date"] = "now()";
		$arrData["module_id"] = MDL_ZERO_ID;
		$arrData["module_path"] = MODULE_PATH . "mdl_zero/card.php";
		$arrData["memo03"] = ZERO_CREDIT_ID;
		$arrData["memo04"] = $_POST["mobile_send"];
		$arrData["memo05"] = $_POST["mobile_clientip"];
		$arrData["del_flg"] = "0";
    }
    
    // �����ǡ���������й������롣
    if(count($arrData) > 0){
	    // ��󥯤κ����ͤ��������
	    $max_rank = $objQuery->getone("SELECT max(rank) FROM dtb_payment");
	    
	    // ��ʧ��ˡ�ǡ��������
	    $arrPaymentData = lfGetPaymentDB();
	    
	    // �ǡ�����¸�ߤ��Ƥ����UPDATE��̵�����INSERT
	    if(count($arrPaymentData) > 0){
            $objQuery->update("dtb_payment", array("memo01"=>"","memo02"=>"","memo03"=>"","memo04"=>"","memo05"=>""), " module_id = '" . MDL_ZERO_ID . "'");
	        $objQuery->update("dtb_payment", $arrData, " module_id = '" . MDL_ZERO_ID . "'");
	    }else{
	        $arrData["rank"] = $max_rank + 1;
	        $objQuery->insert("dtb_payment", $arrData);
	    }
    }
}


function lfZeroCheck(){
    if(!empty($_GET["clientip"])){
        global $objPage;
        global $objView;
        global $objQuery;
        require_once(MODULE_PATH . "mdl_zero/recv.php");
        exit();
    }
}


?>