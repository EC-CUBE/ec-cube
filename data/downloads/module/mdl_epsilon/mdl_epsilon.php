<?php
/**
 * 
 * @copyright	2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id$
 * @link		http://www.lockon.co.jp/
 *
 */
 
require_once("../../require.php");
require_once(MODULE_PATH . "mdl_epsilon/mdl_epsilon.inc");

define("MDL_EPSILON_ID", 4);

$arrPayment = array(
	1 => '���쥸�å�',
	2 => '����ӥ�'
);

$arrCredit = array(
	1 => 'VISA, MASTER',
	2 => 'JCB, AMEX'
);

//�ڡ����������饹
class LC_Page {
	//���󥹥ȥ饯��
	function LC_Page() {
		//�ᥤ��ƥ�ץ졼�Ȥλ���
		$this->tpl_mainpage = MODULE_PATH . 'mdl_epsilon/mdl_epsilon.tpl';
		$this->tpl_subtitle = '���ץ�����ѥ⥸�塼��';
		global $arrPayment;
		$this->arrPayment = $arrPayment;
		global $arrCredit;
		$this->arrCredit = $arrCredit;
		global $arrConvenience;
		$this->arrConvenience = $arrConvenience;
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
	
	if(count($objPage->arrErr) == 0) {
		// ���ѥ���ӥˤ˥����å������äƤ�����ˤϡ��ϥ��ե���ڤ���Խ�����
		$convCnt = count($_POST["convenience"]);
		if($convCnt > 0){
			$convenience = $_POST["convenience"][0];
			for($i = 1 ; $i < $convCnt ; $i++){
				$convenience .= "-" . $_POST["convenience"][$i];
			}
		}
		
		// del_flg�����ˤ��Ƥ���
		$del_sql = "UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ? ";
		$arrDel = array(MDL_EPSILON_ID);
		$objQuery->query($del_sql, $arrDel);
		
		// �ǡ�����Ͽ
		foreach($_POST["payment"] as $key => $val){
			// ��󥯤κ����ͤ��������
			$max_rank = $objQuery->getone("SELECT max(rank) FROM dtb_payment");
			
			// ���쥸�åȤ˥����å������äƤ���Х��쥸�åȤ���Ͽ����
			if($val == 1){
				(in_array(1, $_POST["credit"])) ? $visa = "1" : $visa = "0";
				(in_array(2, $_POST["credit"])) ? $jcb = "1" : $jcb = "0";
				$arrData = array(			
					"payment_method" => "Epsilon���쥸�å�"
					,"fix" => 3
					,"creator_id" => $objSess->member_id
					,"create_date" => "now()"
					,"update_date" => "now()"
					,"upper_rule" => 500000
					,"module_id" => MDL_EPSILON_ID
					,"module_path" => MODULE_PATH . "mdl_epsilon/card.php"
					,"memo01" => $_POST["code"]
					,"memo02" => $_POST["url"]
					,"memo03" => $val
					,"memo04" => $visa . $jcb . "000-0000-00000"
					,"del_flg" => "0"
				);
			}

			// ����ӥˤ˥����å������äƤ���Х���ӥˤ���Ͽ����
			if($val == 2){
				$arrData = array(			
					"payment_method" => "Epsilon����ӥ�"
					,"fix" => 3
					,"creator_id" => $objSess->member_id
					,"create_date" => "now()"
					,"update_date" => "now()"
					,"upper_rule" => 500000
					,"module_id" => MDL_EPSILON_ID
					,"module_path" => MODULE_PATH . "mdl_epsilon/convenience.php"
					,"memo01" => $_POST["code"]
					,"memo02" => $_POST["url"]
					,"memo03" => $val
					,"memo04" => "00100-0000-00000"
					,"memo05" => $convenience
					,"del_flg" => "0"
				);
			}
			
			// �ǡ�����¸�ߤ��Ƥ����UPDATE��̵�����INSERT
			$arrPaymentData = lfGetPaymentDB("AND memo03 = ?", array($val));
			if(count($arrPaymentData) > 0){
				$objQuery->update("dtb_payment", $arrData, " module_id = '" . MDL_EPSILON_ID . "' AND memo03 = '" . $val ."'");
			}else{
				$arrData["rank"] = $max_rank + 1;
				$objQuery->insert("dtb_payment", $arrData);
			}
		}
		
		// javascript�¹�
		$objPage->tpl_onload = 'alert("��Ͽ��λ���ޤ�����\n���ܾ�����ʧ��ˡ������ܺ�����򤷤Ƥ���������"); window.close();';
	}
	break;
case 'module_del':
	// ���ѹ��ܤ�¸�ߥ����å�
	if(sfColumnExists("dtb_payment", "memo01")){
		// �ǡ����κ���ե饰�򤿤Ƥ�
		$objQuery->query("UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ?", array(MDL_EPSILON_ID));
	}
	break;
default:
	// �ǡ����Υ���
	lfLoadData();	
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();

$objView->assignobj($objPage);					//�ѿ���ƥ�ץ졼�Ȥ˥������󤹤�
$objView->display($objPage->tpl_mainpage);		//�ƥ�ץ졼�Ȥν���
//-------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam($objFormParam) {
	$objFormParam->addParam("���󥳡���", "code", INT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��³��URL", "url", URL_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "URL_CHECK"));
	$objFormParam->addParam("���ѷ��", "payment", "", "", array("EXIST_CHECK"));
	$objFormParam->addParam("���ѥ��쥸�å�", "credit");	
	$objFormParam->addParam("���ѥ���ӥ�", "convenience");	
	return $objFormParam;
}

// ���顼�����å���Ԥ�
function lfCheckError(){
	global $objFormParam;
	
	$arrErr = $objFormParam->checkError();
	
	// ���ѥ��쥸�åȡ����ѥ���ӥˤΥ��顼�����å�
	$arrChkPay = $_POST["payment"];
	foreach((array)$arrChkPay as $key => $val){
		// ���ѥ��쥸�å�
		if($val == 1 and count($_POST["credit"]) <= 0){
			$arrErr["credit"] = "���ѥ��쥸�åȤ����򤵤�Ƥ��ޤ���<br />";
		}
		// ���ѥ���ӥ�
		if($val == 2 and count($_POST["convenience"]) <= 0){
			$arrErr["convenience"] = "���ѥ���ӥˤ����򤵤�Ƥ��ޤ���<br />";
		}
	}
	
	// ssl�б�Ƚ��
	if(extension_loaded('openssl')){
		$arrErr["url"] = "���Υ����С���SSL���б����Ƥ��ޤ���http����³���Ƥ���������";
	}

	
	// ��³�����å���Ԥ�
	if(count($arrErr) == 0) $arrErr = lfChkConnect();

	return $arrErr;
}

// ��³�����å���Ԥ�
function lfChkConnect(){
	global $objQuery;
	global $objPage;
	
	$arrRet = array();
	
	// �᡼�륢�ɥ쥹����
	$email = $objQuery->getone("SELECT email03 FROM dtb_baseinfo");

	// ���󥳡���	
	(in_array(1, (array)$_POST["payment"])) ? $cre = "1" : $cre = "0";
	(in_array(2, (array)$_POST["payment"])) ? $con = "1" : $con = "0";
	$st_code = $cre . "0" . $con . "00-0000-00000";
	
	// �����ǡ�������
	$arrSendData = array(
		'contract_code' => $_POST["code"],		// ���󥳡���
		'user_id' => "connect_test",			// �桼��ID
		'user_name' => "��³�ƥ���",			// �桼��̾
		'user_mail_add' => $email,				// �᡼�륢�ɥ쥹
		'st_code' => $st_code,					// ��Ѷ�ʬ
		'process_code' => '3',					// ������ʬ(����)
		'xml' => '1',							// ��������(����)
	);
	
	// �ǡ�������
	$arrXML = sfPostPaymentData($_POST["url"], $arrSendData, false);
	if($arrXML == "") {
		$arrRet["url"] = "��³�Ǥ��ޤ���Ǥ�����<br>";
		return $arrRet;	
	}
	
	// ���顼�����뤫�����å�����
	$err_code = sfGetXMLValue($arrXML,'RESULT','ERR_CODE');
	switch ($err_code) {
		case "":
			break;
		case "607":
			$arrRet["code"] = "���󥳡��ɤ��㤤�ޤ���<br>";
			return $arrRet;
		default :
			$arrRet["service"] = sfGetXMLValue($arrXML,'RESULT','ERR_DETAIL');
			return $arrRet;
	}

	// ����ӥ˻��꤬����Х���ӥ�ʬ�롼�פ��������å���Ԥ�
	if(count($_POST["convenience"]) > 0){
		foreach($_POST["convenience"] as $key => $val){
			// �����ǡ�������
			$arrSendData['conveni_code'] = $val;			// ����ӥ˥�����
			$arrSendData['user_tel'] = "0300000000";		// �����ֹ�
			$arrSendData['user_name_kana'] = "�����ƥ���";	// ��̾(����)
			$arrSendData['haraikomi_mail'] = 0;				// ʧ���᡼��(�������ʤ�)
			
			// �ǡ�������
			$arrXML = sfPostPaymentData($_POST["url"], $arrSendData, false);
			if($arrXML == "") {
				$arrRet["url"] = "��³�Ǥ��ޤ���Ǥ�����<br>";
				return $arrRet;	
			}
			
			// ���顼�����뤫�����å�����
			$err_code = sfGetXMLValue($arrXML,'RESULT','ERR_CODE');
			if($err_code != ""){
				$arrRet["service"] = sfGetXMLValue($arrXML,'RESULT','ERR_DETAIL');
				return $arrRet;
			}
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
	foreach($arrRet as $key => $val){
		// ���ѷ�Ѥ�ɽ���Ѥ��Ѵ�
		$arrDisp["payment"][$key] = $val["payment"];
		
		// ���쥸�åȤη�Ѷ�ʬ�����
		if($val["payment"] == 1) $credit = $val["payment_code"];
		
		// ����ӥ�
		if($val["payment"] == 2) $arrDisp["convenience"] = $val["convenience"];
	}
	$objFormParam->setParam($arrDisp);
	$objFormParam->splitParamCheckBoxes("convenience");
	
	// ���쥸�å�
	if(substr($credit, 0, 1)) $arrCredit["credit"][] = 1;
	if(substr($credit, 1, 1)) $arrCredit["credit"][] = 2;
	$objFormParam->setParam($arrCredit);
}

// DB����ǡ������������
function lfGetPaymentDB($where = "", $arrWhereVal = array()){
	global $objQuery;
	
	$arrVal = array(MDL_EPSILON_ID);
	$arrVal = array_merge($arrVal, $arrWhereVal);
	
	$arrRet = array();
	$sql = "SELECT 
				module_id, 
				memo01 as code, 
				memo02 as url, 
				memo03 as payment,
				memo04 as payment_code, 
				memo05 as convenience
			FROM dtb_payment WHERE module_id = ? " . $where;
	$arrRet = $objQuery->getall($sql, $arrVal);

	return $arrRet;
}

?>