<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: mdl_remise.php 1.0 2007-02-05 06:08:28Z inoue $
 * @link		http://www.lockon.co.jp/
 *
 */
require_once(MODULE_PATH . "mdl_remise/mdl_remise.inc");

//�ڡ����������饹
class LC_Page {
	//���󥹥ȥ饯��
	function LC_Page() {
		//�ᥤ��ƥ�ץ졼�Ȥλ���
		$this->tpl_mainpage = MODULE_PATH . 'mdl_remise/mdl_remise.tpl';
		$this->tpl_subtitle = '��ߡ�����ѥ⥸�塼��';
		global $arrPayment;
		$this->arrPayment = $arrPayment;
		global $arrCredit;
		$this->arrCredit = $arrCredit;
		global $arrCreditDivide;
		$this->arrCreditDivide = $arrCreditDivide;
	}
}
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// ��ߡ��������ɥ��쥸�åȷ�ѷ�����ν���
lfRemiseCreditResultCheck();

// ����ӥ���������å�
lfRemiseConveniCheck();

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

$mode = "";
if (isset($_POST['mode'])) {
	$mode = $_POST['mode'];
}

switch($mode) {
	case 'edit':
		// ���ϥ��顼Ƚ��
		$objPage->arrErr = lfCheckError();

		// ���顼�ʤ��ξ��ˤϥǡ����򹹿�	
		if (count($objPage->arrErr) == 0) {
			// �ǡ�������
			lfUpdPaymentDB();
			
			// javascript�¹�
			$objPage->tpl_onload = 'alert("��Ͽ��λ���ޤ�����\n���ܾ�����ʧ��ˡ������ܺ�����򤷤Ƥ���������"); window.close();';
		}
		break;
	case 'module_del':
		// ���ѹ��ܤ�¸�ߥ����å�
		if (sfColumnExists("dtb_payment", "memo01")) {
			// �ǡ����κ���ե饰�򤿤Ƥ�
			$objQuery->query("UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ?", array(MDL_REMISE_ID));
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
	$objFormParam->addParam("����Ź������", "code", INT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ۥ����ֹ�", "host_id", INT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("���쥸�å���³��URL(PC)", "credit_url", URL_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "URL_CHECK"));
	$objFormParam->addParam("���쥸�å���³��URL(��Х���)", "mobile_credit_url");
	$objFormParam->addParam("��ʧ����ˡ", "credit_method");
	$objFormParam->addParam("���ץ����", "payment");
	$objFormParam->addParam("����ӥ���³��URL(PC)", "convenience_url");
	$objFormParam->addParam("����ӥ���³��URL(��Х���)", "mobile_convenience_url");
	return $objFormParam;
}

// ���顼�����å���Ԥ�
function lfCheckError(){
	global $objFormParam;
	
	$arrErr = $objFormParam->checkError();
	
	// ���ѥ��쥸�åȡ����ѥ���ӥˤΥ��顼�����å�
	$arrChkPay = $_POST["payment"];

	// ���쥸�åȤλ�ʧ����ˡ
	if (count($_POST["credit_method"]) <= 0) {
		$arrErr["credit_method"] = "��ʧ����ˡ�����򤵤�Ƥ��ޤ���<br />";
	}

	// ���ѥ���ӥ�
	if (isset($arrChkPay)) {
		if ($_POST["convenience_url"] == "" && $_POST["mobile_convenience_url"] == "") {
			$arrErr["convenience_url"] = "����ӥ���³��URL(PC)�ޤ��ϥ���ӥ���³��URL(��Х���)�����Ϥ���Ƥ��ޤ���<br />";
		}
	}

	return $arrErr;
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
	$arrDisp["payment"][0] = 0;

	foreach($arrRet as $key => $val) {
		// ���쥸�åȤη�Ѷ�ʬ�����
		if($val["payment"] == 1) {
			$credit = $val["payment_code"];
			$arrDisp["credit_url"] = $val["credit_url"];
			$arrDisp["mobile_credit_url"] = $val["mobile_credit_url"];
			$arrDisp["credit_method"] = $val["credit_method"];
		}

		// ����ӥˤη�Ѷ�ʬ�����
		if($val["payment"] == 2) {
			$arrDisp["convenience"] = $val["convenience"];
			$arrDisp["payment"][0] = 1;
			$arrDisp["convenience_url"] = $val["convenience_url"];
			$arrDisp["mobile_convenience_url"] = $val["mobile_convenience_url"];
		}
	}

	$objFormParam->setParam($arrDisp);
	$objFormParam->splitParamCheckBoxes("credit_method");
	
	// ���쥸�åȻ�ʧ����ʬ
	//$objFormParam->splitParamCheckBoxes("credit_method");
}

// DB����ǡ������������
function lfGetPaymentDB($where = "", $arrWhereVal = array()){
	global $objQuery;
	
	$arrVal = array(MDL_REMISE_ID);
	$arrVal = array_merge($arrVal, $arrWhereVal);
	
	$arrRet = array();
	$sql = "SELECT 
				module_id, 
				memo01 as code, 
				memo02 as host_id, 
				memo03 as payment,
				memo04 as credit_url,
				memo05 as convenience_url,
				memo06 as mobile_credit_url,
				memo07 as mobile_convenience_url,
				memo08 as credit_method,
				memo09 as credit_divide
			FROM dtb_payment WHERE module_id = ? " . $where;
	$arrRet = $objQuery->getall($sql, $arrVal);

	return $arrRet;
}


// �ǡ����ι�������
function lfUpdPaymentDB(){
	global $objQuery;
	global $objSess;
	
	// ��ʧ����ˡ�˥����å������äƤ�����ϡ��ϥ��ե���ڤ���Խ�����
	$convCnt = count($_POST["credit_method"]);
	if ($convCnt > 0) {
		$credit_method = $_POST["credit_method"][0];
		for ($i = 1 ; $i < $convCnt ; $i++) {
			$credit_method .= "-" . $_POST["credit_method"][$i];
		}
	}

	// del_flg�����ˤ��Ƥ���
	$del_sql = "UPDATE dtb_payment SET del_flg = 1 WHERE module_id = ? ";
	$arrDel = array(MDL_REMISE_ID);
	$objQuery->query($del_sql, $arrDel);

	$arrEntry = array('1');

	if (count($_POST["payment"]) > 0) {
		$arrEntry[] = '2';
	}

	foreach($arrEntry as $key => $val){
		// ��󥯤κ����ͤ��������
		$max_rank = $objQuery->getone("SELECT max(rank) FROM dtb_payment");

		// ��ʧ��ˡ�ǡ��������			
		$arrPaymentData = lfGetPaymentDB("AND memo03 = ?", array($val));

		// ���쥸�åȷ����Ͽ
		if($val == 1) {

			$arrData = array(
				"payment_method" => "remise���쥸�å�"
				,"fix" => 3
				,"creator_id" => $objSess->member_id
				,"create_date" => "now()"
				,"update_date" => "now()"
				,"upper_rule" => REMISE_CREDIT_UPPER
				,"module_id" => MDL_REMISE_ID
				,"module_path" => MODULE_PATH . "mdl_remise/card.php"
				,"memo01" => $_POST["code"]
				,"memo02" => $_POST["host_id"]
				,"memo03" => $val
				,"memo04" => $_POST["credit_url"]
				,"memo06" => $_POST["mobile_credit_url"]
				,"memo08" => $credit_method
				,"memo09" => REMISE_PAYMENT_DIVIDE_MAX
				,"del_flg" => "0"
				,"charge_flg" => "2"
				,"upper_rule_max" => REMISE_CREDIT_UPPER
			);
		}

		// ����ӥˤ˥����å������äƤ���Х���ӥˤ���Ͽ����
		if($val == 2) {
			
			$arrData = array(
				"payment_method" => "remise����ӥ�"
				,"fix" => 3
				,"creator_id" => $objSess->member_id
				,"create_date" => "now()"
				,"update_date" => "now()"
				,"upper_rule" => REMISE_CONVENIENCE_UPPER
				,"module_id" => MDL_REMISE_ID
				,"module_path" => MODULE_PATH . "mdl_remise/convenience.php"
				,"memo01" => $_POST["code"]
				,"memo02" => $_POST["host_id"]
				,"memo03" => $val
				,"memo05" => $_POST["convenience_url"]
				,"memo07" => $_POST["mobile_convenience_url"]
				,"del_flg" => "0"
				,"charge_flg" => "1"
				,"upper_rule_max" => REMISE_CONVENIENCE_UPPER
				,"rule_min" => REMISE_CONVENIENCE_BOTTOM
			);
		}

		// �ǡ�����¸�ߤ��Ƥ����UPDATE��̵�����INSERT
		if (count($arrPaymentData) > 0) {
			$objQuery->update("dtb_payment", $arrData, " module_id = '" . MDL_REMISE_ID . "' AND memo03 = '" . $val ."'");
		} else {
			$arrData["rank"] = $max_rank + 1;
			$objQuery->insert("dtb_payment", $arrData);
		}
	}
}

// ��ߡ��������ɥ��쥸�åȷ�ѷ�����ν���
function lfRemiseCreditResultCheck(){
	global $objQuery;
	
	$log_path = DATA_PATH . "logs/remise_card_result.log";
	gfPrintLog("remise card result : ".$_POST["X-TRANID"] , $log_path);
	
	// TRAN_ID ����ꤵ��Ƥ��ơ������ɾ��󤬤�����
	if (isset($_POST["X-TRANID"]) && isset($_POST["X-PARTOFCARD"])) {
		
		$errFlg = FALSE;
		
		gfPrintLog("remise card result start----------", $log_path);
		foreach($_POST as $key => $val){
			gfPrintLog( "\t" . $key . " => " . $val, $log_path);
		}
		gfPrintLog("remise credit result end  ----------", $log_path);

		// IP���ɥ쥹���椹����
		if (REMISE_IP_ADDRESS_DENY == 1) {
			gfPrintLog("remise remoto ip address : ".$_SERVER["REMOTE_HOST"]."-".$_SERVER["REMOTE_ADDR"], $log_path);
			if (!isset($_SERVER["REMOTE_ADDR"]) || !lfIpAddressDenyCheck($_SERVER["REMOTE_ADDR"])) {
				print("NOT REMISE SERVER");
				exit;
			}
		}
		
		// �����ֹ�ȶ�ۤμ���
		$order_id = 0;
		$payment_total = 0;
		
		if (isset($_POST["X-S_TORIHIKI_NO"])) {
			$order_id = $_POST["X-S_TORIHIKI_NO"];
		}
		
		if (isset($_POST["X-TOTAL"])) {
			$payment_total = $_POST["X-TOTAL"];
		}
		
		gfPrintLog("order_id : ".$order_id, $log_path);
		gfPrintLog("payment_total : ".$payment_total, $log_path);

		// ��ʸ�ǡ�������
		$arrTempOrder = $objQuery->getall("SELECT payment_total FROM dtb_order_temp WHERE order_id = ? ", array($order_id));

		// ��ۤ����
		if (count($arrTempOrder) > 0) {
			gfPrintLog("ORDER payment_total : ".$arrTempOrder[0]['payment_total'], $log_path);
			if ($arrTempOrder[0]['payment_total'] == $payment_total) {
				$errFlg = TRUE;
			}
		}
		
		if ($errFlg) {
			print(REMISE_PAYMENT_CHARGE_OK);
			exit;
		}
		print("ERROR");
		exit;
	}
}

// ����ӥ������ǧ����
function lfRemiseConveniCheck(){
	global $objQuery;
	
	$log_path = DATA_PATH . "logs/remise_cv_charge.log";
	gfPrintLog("remise conveni result : ".$_POST["JOB_ID"] , $log_path);
	
	// ɬ�פʥǡ�������������Ƥ��ơ���Ǽ���Τμ�ư��������Ĥ��Ƥ�����
	if(isset($_POST["JOB_ID"]) && isset($_POST["REC_FLG"]) && REMISE_CONVENIENCE_RECIVE == 1){
		
		$errFlg = FALSE;
			
		// ��Ǽ�Ѥߤξ��
		if ($_POST["REC_FLG"] == REMISE_CONVENIENCE_CHARGE) {
			// POST�����Ƥ����ƥ���¸
			gfPrintLog("remise conveni charge start----------", $log_path);
			foreach($_POST as $key => $val){
				gfPrintLog( "\t" . $key . " => " . $val, $log_path);
			}
			gfPrintLog("remise conveni charge end  ----------", $log_path);

			// IP���ɥ쥹���椹����
			if (REMISE_IP_ADDRESS_DENY == 1) {
				gfPrintLog("remise remoto ip address : ".$_SERVER["REMOTE_HOST"]."-".$_SERVER["REMOTE_ADDR"], $log_path);
				if (!isset($_SERVER["REMOTE_ADDR"]) || !lfIpAddressDenyCheck($_SERVER["REMOTE_ADDR"])) {
					print("NOT REMISE SERVER");
					exit;
				}
			}
			
			// �����ֹ�ȶ�ۤμ���
			$order_id = 0;
			$payment_total = 0;
			
			if (isset($_POST["S_TORIHIKI_NO"])) {
				$order_id = $_POST["S_TORIHIKI_NO"];
			}
			
			if (isset($_POST["TOTAL"])) {
				$payment_total = $_POST["TOTAL"];
			}
			
			gfPrintLog("order_id : ".$order_id, $log_path);
			gfPrintLog("payment_total : ".$payment_total, $log_path);
			
			// ��ʸ�ǡ�������
			$arrTempOrder = $objQuery->getall("SELECT payment_total FROM dtb_order_temp WHERE order_id = ? ", array($order_id));

			// ��ۤ����
			if (count($arrTempOrder) > 0) {
				gfPrintLog("ORDER payment_total : ".$arrTempOrder[0]['payment_total'], $log_path);
				if ($arrTempOrder[0]['payment_total'] == $payment_total) {
					$errFlg = TRUE;
				}
			}
			
			// JOB_ID�������ֹ档�����ۤ����פ�����Τߡ����ơ�����������Ѥߤ��ѹ�����
			if ($errFlg) {
				$sql = "UPDATE dtb_order SET status = 6, update_date = now() ".
					"WHERE order_id = ? AND memo04 = ? ";
				$objQuery->query($sql, array($order_id, $_POST["JOB_ID"]));
			
				//������̤�ɽ��
				print(REMISE_CONVENIENCE_CHARGE_OK);
				exit;
			}
		}
		print("ERROR");
		exit;
	}
}

/**
 * IP���ɥ쥹�Ӱ�����å�
 * @param $ip IP���ɥ쥹
 * @return boolean
 */
function lfIpAddressDenyCheck($ip) {
	
	// IP���ɥ쥹�ϰϤ����äƤʤ����
	if (ip2long(REMISE_IP_ADDRESS_S) > ip2long($ip) || 
		ip2long(REMISE_IP_ADDRESS_E) < ip2long($ip)) {
		return FALSE;
	}
	return TRUE;
}

?>