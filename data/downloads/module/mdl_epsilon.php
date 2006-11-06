<?php
/**
 * 
 * @copyright	2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id$
 * @link		http://www.lockon.co.jp/
 *
 */
 
require_once("../../require.php");

define("MDL_EPSILON_ID", 4);

$arrPayment = array(
	1 => '���쥸�å�',
	2 => '����ӥ�'
);

$arrCredit = array(
	1 => 'VISA, MASTER',
	2 => 'JCB, AMEX'
);

$arrConvenience = array(
	11 => '���֥󥤥�֥�'
	,21 => '�ե��ߥ꡼�ޡ���'
	,31 => 'LAWSON'
	,32 => '���������ޡ���'
	,33 => '�ߥ˥��ȥå�'
	,34 => '�ǥ��꡼��ޥ���'
);

//�ڡ����������饹
class LC_Page {
	//���󥹥ȥ饯��
	function LC_Page() {
		//�ᥤ��ƥ�ץ졼�Ȥλ���
		$this->tpl_mainpage = MODULE_PATH . 'mdl_epsilon.tpl';
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

// ǧ�ڳ�ǧ
$objSess = new SC_Session();
sfIsSuccess($objSess);

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
$objFormParam = lfInitParam($objFormParam);
// POST�ͤμ���
$objFormParam->setParam($_POST);

$objQuery = new SC_Query();

// ���ѹ��ܤ��ɲ�
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
		
		// DEL/INS����Ͽ���롣
		$delsql = "DELETE FROM dtb_payment WHERE memo01 = ?";
		$objQuery->query($delsql, array(MDL_EPSILON_ID));
		
		foreach($_POST["payment"] as $key => $val){
			
			// ��󥯤κ����ͤ��������
			$max_rank = $objQuery->getone("SELECT max(rank) FROM dtb_payment");
			
			// ���쥸�åȤ˥����å������äƤ���Х��쥸�åȤ���Ͽ����
			if($val == 1){
				(in_array(1, $_POST["credit"])) ? $visa = "1" : $visa = "0";
				(in_array(2, $_POST["credit"])) ? $jcb = "1" : $jcb = "0";
				
				$arrData = array(			
					"payment_method" => "���쥸�å�(���ץ����)"
					,"rule" => "0"
					,"deliv_id" =>0
					,"rank" => $max_rank + 1
					,"fix" => 3
					,"creator_id" => $objSess->member_id
					,"create_date" => "now()"
					,"update_date" => "now()"
					,"upper_rule" => 500000
					,"memo01" => MDL_EPSILON_ID
					,"memo02" => $_POST["code"]
					,"memo03" => $_POST["url"]
					,"memo04" => $val
					,"memo05" => $visa . $jcb . "000-0000-00000"
				);
			}

			// ����ӥˤ˥����å������äƤ���Х���ӥˤ���Ͽ����
			if($val == 2){
				$arrData = array(			
					"payment_method" => "����ӥ�(���ץ����)"
					,"rule" => "0"
					,"deliv_id" =>0
					,"rank" => $max_rank + 1
					,"fix" => 3
					,"creator_id" => $objSess->member_id
					,"create_date" => "now()"
					,"update_date" => "now()"
					,"upper_rule" => 500000
					,"memo01" => MDL_EPSILON_ID
					,"memo02" => $_POST["code"]
					,"memo03" => $_POST["url"]
					,"memo04" => $val
					,"memo05" => "00100-0000-00000"
					,"memo06" => $convenience
				);
			}
			
			$objQuery->insert("dtb_payment", $arrData);
			
		}
	
		// javascript�¹�
		$objPage->tpl_onload = 'alert("��Ͽ��λ���ޤ�����\n���ܾ�����ʧ��ˡ������ܺ�����򤷤Ƥ���������"); window.close();';
	}
	break;
case 'module_del':
	// ���ѹ��ܤ�¸�ߥ����å�
	if(!sfColumnExists("dtb_payment", "memo01")){
		// �ǡ����κ��
		$objQuery->query("DELETE FROM dtb_payment WHERE memo01 = ?", array(MDL_EPSILON_ID));
	}
	break;
default:
	// �ǡ����Υ���
	lfLoadData();	
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();

$objView->assignobj($objPage);					//�ѿ���ƥ�ץ졼�Ȥ˥������󤹤�aaaaa
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
	foreach($arrChkPay as $key => $val){
		// ���ѥ��쥸�å�
		if($val == 1 and count($_POST["credit"]) <= 0){
			$arrErr["credit"] = "���ѥ��쥸�åȤ����򤵤�Ƥ��ޤ���<br />";
		}
		
		// ���ѥ���ӥ�
		if($val == 2 and count($_POST["convenience"]) <= 0){
			$arrErr["convenience"] = "���ѥ���ӥˤ����򤵤�Ƥ��ޤ���<br />";
		}	}

	return $arrErr;
}

// ��Ͽ�ǡ������ɤ߹���
function lfLoadData(){
	global $objQuery;
	global $objFormParam;
	
	$sql = "SELECT 
				memo01, 
				memo02 as code, 
				memo03 as url, 
				memo04 as payment,
				memo05 as payment_code, 
				memo06 as convenience
			FROM dtb_payment WHERE memo01 = ?";
	$arrRet = $objQuery->getall($sql, array(MDL_EPSILON_ID));
	
	$objFormParam->setParam($arrRet[0]);
	$objFormParam->splitParamCheckBoxes("convenience");

	// ����ɽ���Ѥ˥ǡ������Ѵ�
	$arrDisp = array();
	foreach($arrRet as $key => $val){
		// ���ѷ�Ѥ�ɽ���Ѥ��Ѵ�
		$arrDisp["payment"][$key] = $val["payment"];
		
		// ���쥸�åȤη�Ѷ�ʬ�����
		if($val["payment"] == 1) $credit = $val["payment_code"];
	}
	$objFormParam->setParam($arrDisp);
	
	// ���쥸�å�
	if(substr($credit, 0, 1)) $arrCredit["credit"][] = 1;
	if(substr($credit, 1, 1)) $arrCredit["credit"][] = 2;
	$objFormParam->setParam($arrCredit);
	
}

?>