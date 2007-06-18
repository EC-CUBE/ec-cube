<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 * ��������ɲ�
 */
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = 'nonmember/deliv_addr.tpl';
		$this->tpl_title = "¾�Τ��Ϥ������Ͽ";
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView(false);
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();
$objConn = new SC_DBConn();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();

$objPage->arrForm = $_POST;
$objPage->arrPref = $arrPref;

//�̤Τ��Ϥ���ģ���Ͽ�ѥ��������
$arrRegistColumn = array(
                             array(  "column" => "name01",      "convert" => "aKV" ),
                             array(  "column" => "name02",      "convert" => "aKV" ),
                             array(  "column" => "kana01",      "convert" => "CKV" ),
                             array(  "column" => "kana02",      "convert" => "CKV" ),
                             array(  "column" => "zip01",       "convert" => "n" ),
                             array(  "column" => "zip02",       "convert" => "n" ),
                             array(  "column" => "pref",        "convert" => "n" ),
                             array(  "column" => "addr01",      "convert" => "aKV" ),
                             array(  "column" => "addr02",      "convert" => "aKV" ),
                             array(  "column" => "tel01",       "convert" => "n" ),
                             array(  "column" => "tel02",       "convert" => "n" ),
                             array(  "column" => "tel03",       "convert" => "n" ),
                        );

//-- �ǡ�������
foreach($_POST as $key => $val) {
	if ($key != "mode" && $key != "return" && $key != "submit" && $key != session_name()) {
		$objPage->list_data[ $key ] = $val;
	}
}

// �桼����ˡ���ID�μ����ȹ������֤�������������å�
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

                      
    //-- ���ϥǡ������Ѵ�
    $objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);                        


if($_SESSION['deliv_info']){
    
    foreach($_SESSION['deliv_info'] as $key => $val){
        $objPage->arrForm[$key] = $val;
    }
    
}

// ���ܥ����ѽ���
if (!empty($_POST["return"])) {
	switch ($_POST["mode"]) {
	case 'complete':
		$_POST["mode"] = "set2";
		break;
	case 'set2':
		$_POST["mode"] = "set1";
		break;
	default:
        header("Location: " . gfAddSessionId('deliv.php'));
        exit;

	}
}

switch ($_POST['mode']){
	case 'set1':
		$objPage->arrErr = lfErrorCheck1($objPage->arrForm);
		if (count($objPage->arrErr) == 0 && empty($_POST["return"])) {
			$objPage->tpl_mainpage = 'nonmember/set1.tpl';

			$checkVal = array("pref", "addr01", "addr02", "addr03", "tel01", "tel02", "tel03");
			foreach($checkVal as $key) {
				unset($objPage->list_data[$key]);
			}

			// ͹���ֹ椫�齻��μ���
			if (@$objPage->arrForm['pref'] == "" && @$objPage->arrForm['addr01'] == "" && @$objPage->arrForm['addr02'] == "") {
				$address = lfGetAddress($_REQUEST['zip01'].$_REQUEST['zip02']);

				$objPage->arrForm['pref'] = @$address[0]['state'];
				$objPage->arrForm['addr01'] = @$address[0]['city'] . @$address[0]['town'];
			}
		} else {
			$checkVal = array("name01", "name02", "kana01", "kana02", "zip01", "zip02");
			foreach($checkVal as $key) {
				unset($objPage->list_data[$key]);
			}
		}
		break;
	case 'set2':
		$objPage->arrErr = lfErrorCheck2($objPage->arrForm);
		if (count($objPage->arrErr) == 0 && empty($_POST["return"])) {
             // ��Ͽ
            $other_deliv_id = lfRegistData($_POST,$arrRegistColumn,$uniqid);

            // ��Ͽ�Ѥߤ��̤Τ��Ϥ����������ơ��֥�˽񤭹���
            lfRegistOtherDelivData($uniqid, $objCustomer, $other_deliv_id);
           
            $_SESSION['deliv_info'] = $_POST;
            // �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
            $objSiteSess->setRegistFlag();
            // ����ʧ����ˡ����ڡ����ذ�ư
            header("Location: " . gfAddSessionId('./payment.php'));
            exit;

//			$objPage->tpl_mainpage = 'nonmember/set2.tpl';
		} else {
			$objPage->tpl_mainpage = 'nonmember/set1.tpl';

			$checkVal = array("pref", "addr01", "addr02", "addr03", "tel01", "tel02", "tel03");
			foreach($checkVal as $key) {
				unset($objPage->list_data[$key]);
			}
		}
		break;
	case 'complete':
		$objPage->arrErr = lfErrorCheck($objPage->arrForm);
		if (count($objPage->arrErr) == 0) {
            // ��Ͽ
			$other_deliv_id = lfRegistData($_POST,$arrRegistColumn,$uniqid);

			// ��Ͽ�Ѥߤ��̤Τ��Ϥ����������ơ��֥�˽񤭹���
			lfRegistOtherDelivData($uniqid, $objCustomer, $other_deliv_id);

			// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
			$objSiteSess->setRegistFlag();
			// ����ʧ����ˡ����ڡ����ذ�ư
			header("Location: " . gfAddSessionId('./payment.php'));
			exit;
		} else {
			sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
		}
		break;
	default:
		$deliv_count = $objQuery->count("dtb_other_deliv", "customer_id=?", array($objCustomer->getValue('customer_id')));
		if ($deliv_count >= DELIV_ADDR_MAX){
			sfDispSiteError(FREE_ERROR_MSG, "", false, "������Ͽ�����Ķ���Ƥ��ޤ���");
		}
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-------------------------------------------------------------------------------------------------------------

//----������ʸ������Ѵ�
function lfConvertParam($array, $arrRegistColumn) {
    /*
     *  ʸ������Ѵ�
     *  K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
     *  C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
     *  V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ� 
     *  n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
     *  a :  ���ѱѿ�����Ⱦ�ѱѿ������Ѵ�����
     */
    // �����̾�ȥ���С��Ⱦ���   
    foreach ($arrRegistColumn as $data) {
        $arrConvList[ $data["column"] ] = $data["convert"];
    }
    
    // ʸ���Ѵ�
    foreach ($arrConvList as $key => $val) {
        // POST����Ƥ����ͤΤ��Ѵ����롣
        if(strlen(($array[$key])) > 0) {
            $array[$key] = mb_convert_kana($array[$key] ,$val);
        }
    }
    return $array;
}

/* ���顼�����å� */
function lfErrorCheck() {
	$objErr = new SC_CheckError();
	
	$objErr->doFunc(array("��̾��������", 'name01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾����̾��", 'name02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾���ʥ���/����", 'kana01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("��̾���ʥ���/̾��", 'kana02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("͹���ֹ�1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("͹���ֹ�2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("͹���ֹ�", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("��ƻ�ܸ�", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�Զ�Į¼", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("����", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�����ֹ�1", 'tel01'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�����ֹ�2", 'tel02'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�����ֹ�3", 'tel03'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�����ֹ�", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
	return $objErr->arrErr;
	
}

/* ���顼�����å� */
function lfErrorCheck1() {
	$objErr = new SC_CheckError();
	
	$objErr->doFunc(array("��̾��������", 'name01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾����̾��", 'name02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾���ʥ���/����", 'kana01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("��̾���ʥ���/̾��", 'kana02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("͹���ֹ�1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("͹���ֹ�2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("͹���ֹ�", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	return $objErr->arrErr;
	
}

/* ���顼�����å� */
function lfErrorCheck2() {
	$objErr = new SC_CheckError();
	
	$objErr->doFunc(array("��ƻ�ܸ�", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�Զ�Į¼", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("����", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�����ֹ�1", 'tel01'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�����ֹ�2", 'tel02'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�����ֹ�3", 'tel03'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("�����ֹ�", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
	return $objErr->arrErr;
	
}



/* ��Ͽ�¹� ���������Ǥ��뤿��˹���Ū����Ͽ�Ϥ��ʤ�*/
function lfRegistData($array, $arrRegistColumn,$uniqid) {
	global $objConn;
	global $objCustomer;
	
    $objQuery = new SC_Query();
    
    $sqlse = "SELECT customer_id FROM dtb_order_temp WHERE order_temp_id = ?";
    $arrRegist['customer_id'] = $objConn->getOne($sqlse, array($uniqid));
    
	foreach ($arrRegistColumn as $data) {
		if (strlen($array[ $data["column"] ]) > 0) {
			$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
		}
	}
	
	//$arrRegist['customer_id'] = $objCustomer->getvalue('customer_id');
    
	//-- �Խ���Ͽ�¹�
	$objConn->query("BEGIN");
	if ($array['other_deliv_id'] != ""){
		$objConn->autoExecute("dtb_other_deliv", $arrRegist, "other_deliv_id='" .addslashes($array["other_deliv_id"]). "'");
	}else{
		$objConn->autoExecute("dtb_other_deliv", $arrRegist);

		$sqlse = "SELECT max(other_deliv_id) FROM dtb_other_deliv WHERE customer_id = ?";
		$array['other_deliv_id'] = $objConn->getOne($sqlse, array($arrRegist['customer_id']));
	}

	$objConn->query("COMMIT");

	return $array['other_deliv_id'];
}



// ͹���ֹ椫�齻��μ���
function lfGetAddress($zipcode) {
	global $arrPref;

	$conn = new SC_DBconn(ZIP_DSN);

	// ͹���ֹ渡��ʸ����
	$zipcode = mb_convert_kana($zipcode ,"n");
	$sqlse = "SELECT state, city, town FROM mtb_zip WHERE zipcode = ?";

	$data_list = $conn->getAll($sqlse, array($zipcode));

	// ����ǥå������ͤ�ȿž�����롣
	$arrREV_PREF = array_flip($arrPref);

	/*
		��̳�ʤ����������ɤ����ǡ����򤽤Τޤޥ���ݡ��Ȥ����
		�ʲ��Τ褦��ʸ�������äƤ���Τ�	�к����롣
		���ʣ����������ܡ�
		���ʲ��˷Ǻܤ��ʤ����
	*/
	$town =  $data_list[0]['town'];
	$town = ereg_replace("��.*��$","",$town);
	$town = ereg_replace("�ʲ��˷Ǻܤ��ʤ����","",$town);
	$data_list[0]['town'] = $town;
	$data_list[0]['state'] = $arrREV_PREF[$data_list[0]['state']];

	return $data_list;
}

/* �̤Τ��Ϥ��轻���������ơ��֥�� */
function lfRegistOtherDelivData($uniqid, $objCustomer, $other_deliv_id) {
	// ��Ͽ�ǡ����κ���
	$sqlval['order_temp_id'] = $uniqid;
	$sqlval['update_date'] = 'Now()';
	$sqlval['customer_id'] = '0';
    //$sqlval['customer_id'] = $objCustomer->getValue('customer_id');
	$sqlval['order_birth'] = $objCustomer->getValue('birth');

	$objQuery = new SC_Query();
	$where = "other_deliv_id = ?";
	$arrRet = $objQuery->select("*", "dtb_other_deliv", $where, array($other_deliv_id));
	
	$sqlval['deliv_check'] = '1';
    $sqlval['deliv_name01'] = $arrRet[0]['name01'];
    $sqlval['deliv_name02'] = $arrRet[0]['name02'];
    $sqlval['deliv_kana01'] = $arrRet[0]['kana01'];
    $sqlval['deliv_kana02'] = $arrRet[0]['kana02'];
    $sqlval['deliv_zip01'] = $arrRet[0]['zip01'];
    $sqlval['deliv_zip02'] = $arrRet[0]['zip02'];
    $sqlval['deliv_pref'] = $arrRet[0]['pref'];
    $sqlval['deliv_addr01'] = $arrRet[0]['addr01'];
    $sqlval['deliv_addr02'] = $arrRet[0]['addr02'];
    $sqlval['deliv_tel01'] = $arrRet[0]['tel01'];
    $sqlval['deliv_tel02'] = $arrRet[0]['tel02'];
	$sqlval['deliv_tel03'] = $arrRet[0]['tel03'];
	sfRegistTempOrder($uniqid, $sqlval);
}

?>
