<?php 

require_once("../require.php");

class LC_Page {
    var $arrSession;
    var $tpl_mode;
    var $tpl_login_email;
    function LC_Page() {
        $this->tpl_mainpage = 'nonmember/index.tpl';
        global $arrPref;
        $this->arrPref = $arrPref;
        global $arrSex;
        $this->arrSex = $arrSex;
        global $arrJob;
        $this->arrJob = $arrJob;
        $this->tpl_onload = 'fnCheckInputDeliv();';
        
        /*
         session_start����no-cache�إå������������뤳�Ȥ�
         �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
         private-no-expire:���饤����ȤΥ���å������Ĥ��롣
        */
        session_cache_limiter('private-no-expire');             
    }
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_MobileView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objCustomer = new SC_Customer();
$objCookie = new SC_Cookie();
$objFormParam = new SC_FormParam();         // �ե�������
lfInitParam();                              // �ѥ�᡼������ν����
$objFormParam->setParam($_POST);            // POST�ͤμ���

// �桼����ˡ���ID�μ����ȹ������֤�������������å�
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

// ��ˡ���ID������Ѥ�
$objPage->tpl_uniqid = $uniqid;


//���Υڡ�����������ܤˤ�ä�ʬ��
if(!empty($_POST["mode2"]) || $_SESSION['user_info'] ){
    if ($_POST["mode2"] == "deliv" || $_SESSION['user_info']["mode2"] == "deliv") {
            
           
            $objFormParam = new SC_FormParam();
            // �ѥ�᡼������ν����
           
            // POST�ͤμ���
            $objFormParam->setParam($_SESSION['user_info']);
            $arrRet = $objFormParam->getHashArray();
            $sqlval = $objFormParam->getDbArray();
            
            // �����ͤμ���
            $objPage->arrForm = $objFormParam->getFormParamList();
            $objPage->arrErr = $arrErr;
            
           foreach($_SESSION['user_info'] as $key => $value){
               $objPage->arrAddr[0][$key] = $value;
           }
            
            //�ǡ����١����ΰ����¸�ѥơ��֥�dtb_order_temp�˥ǡ������Ǽ����
            lfRegistDataTemp($objPage->arrAddr[0]['uniqid'],$objPage->arrAddr[0]); 
            
            lfCopyDeliv($objPage->tpl_uniqid, $_SESSION['user_info']);
           
            $objPage->tpl_mainpage = 'nonmember/nonmember_deliv.tpl';
            $objPage->tpl_title = '���Ϥ������';
            //objPage�ξ����objView�˳�Ǽ
            $objView->assignobj($objPage);
            $objView->display(SITE_FRAME);
        }
        
        if ($_POST["mode2"] == "customer_addr") {

            if ($_POST['deli'] != "") {
                header("Location:" . gfAddSessionId("./payment.php"));
            exit;
            }else{
                // ���顼���֤�
                $arrErr['deli'] = '�� ���Ϥ�������򤷤Ƥ���������';
            }
        }     
//���ܥ����ѽ���   
}elseif($_POST["mode"]!="nonmember"){  
    //uniqid����ǡ����١����Υǡ������ɤ߹���ɽ������
    $objQuery = new SC_Query();
    $where = "order_temp_id = ?";
    $arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($objPage->tpl_uniqid));
    $objFormParam->setParam($arrRet[0]);
    $objPage->arrForm = $objFormParam->getFormParamList();        
    
         foreach($objPage->arrForm as $key => $value){
           $objPage->arrAddr[0][str_replace("order_","",$key)] = $value['value'];
       }
    $objPage->tpl_mainpage = 'nonmember/nonmember_deliv.tpl';
    $objPage->tpl_title = '���Ϥ������';
    $objView->assignobj($objPage);
    $objView->display(SITE_FRAME);
}


/**
 *���Ϥ��줿�����ǡ����١���dtb_order_temp�˳�Ǽ����
 * 
 * @param string $uniqid unique id
 * @param array $array 
 * 
*/
function lfRegistDataTemp($uniqid,$array) {
    global $objFormParam;
    $arrRet = $objFormParam->getHashArray();
    $sqlval = $objFormParam->getDbArray();
        
    // ��Ͽ�ǡ����κ���
    $sqlval['order_temp_id'] = $uniqid;
    $sqlval['order_birth'] = sfGetTimestamp($arrRet['year'], $arrRet['month'], $arrRet['day']);
    $sqlval['update_date'] = 'Now()';
    $sqlval['customer_id'] = '0';
    
    $sqlval['order_name01'] = $array['name01'];
    $sqlval['order_name02'] = $array['name02'];
    $sqlval['order_kana01'] = $array['kana01'];
    $sqlval['order_kana02'] = $array['kana02'];
    $sqlval['order_zip01'] = $array['zip01'];
    $sqlval['order_zip02'] = $array['zip02'];
    $sqlval['order_pref'] = $array['pref'];
    $sqlval['order_addr01'] = $array['addr01'];
    $sqlval['order_addr02'] = $array['addr02'];
    $sqlval['order_tel01'] = $array['tel01'];
    $sqlval['order_tel02'] = $array['tel02'];
    $sqlval['order_tel03'] = $array['tel03'];
    $sqlval['order_email'] = $array['email'];
    $sqlval['order_sex'] = $array['sex'];
          
    // ��¸�ǡ����Υ����å�
    $objQuery = new SC_Query();
    $where = "order_temp_id = ?";
    $cnt = $objQuery->count("dtb_order_temp", $where, array($uniqid));
    // ��¸�ǡ������ʤ����
    if ($cnt == 0) {
        $sqlval['create_date'] = 'Now()';
        $objQuery->insert("dtb_order_temp", $sqlval);
    } else {
        $objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
    }
}

/* �ѥ�᡼������ν���� */
function lfInitParam() {
    global $objFormParam;
    $objFormParam->addParam("��̾��������", "order_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("��̾����̾��", "order_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("�եꥬ�ʡʥ�����", "order_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("�եꥬ�ʡʥᥤ��", "order_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("͹���ֹ�1", "order_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
    $objFormParam->addParam("͹���ֹ�2", "order_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
    $objFormParam->addParam("��ƻ�ܸ�", "order_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("����1", "order_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("����2", "order_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("�����ֹ�1", "order_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("�����ֹ�2", "order_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("�����ֹ�3", "order_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("FAX�ֹ�1", "order_fax01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("FAX�ֹ�2", "order_fax02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("FAX�ֹ�3", "order_fax03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("�᡼�륢�ɥ쥹", "order_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"));
    $objFormParam->addParam("�᡼�륢�ɥ쥹�ʳ�ǧ��", "order_email_check", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"), "", false);
    $objFormParam->addParam("ǯ", "year", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
    $objFormParam->addParam("��", "month", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
    $objFormParam->addParam("��", "day", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
    $objFormParam->addParam("����", "order_sex", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("����", "order_job", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("�̤Τ��Ϥ���", "deliv_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("��̾��������", "deliv_name01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("��̾����̾��", "deliv_name02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("�եꥬ�ʡʥ�����", "deliv_kana01", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("�եꥬ�ʡʥᥤ��", "deliv_kana02", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("͹���ֹ�1", "deliv_zip01", ZIP01_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
    $objFormParam->addParam("͹���ֹ�2", "deliv_zip02", ZIP02_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
    $objFormParam->addParam("��ƻ�ܸ�", "deliv_pref", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("����1", "deliv_addr01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("����2", "deliv_addr02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("�����ֹ�1", "deliv_tel01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("�����ֹ�2", "deliv_tel02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("�����ֹ�3", "deliv_tel03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("�᡼��ޥ�����", "mail_flag", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
}

/* DB�إǡ�������Ͽ */


// �������ơ��֥�Τ��Ϥ���򥳥ԡ�����
function lfCopyDeliv($uniqid, $arrData) {
    $objQuery = new SC_Query();
    
    // �̤Τ��Ϥ������ꤷ�Ƥ��ʤ���硢���������Ͽ����򥳥ԡ����롣
    if($arrData["deliv_check"] != "1") {
        $sqlval['deliv_name01'] = $arrData['order_name01'];
        $sqlval['deliv_name02'] = $arrData['order_name02'];
        $sqlval['deliv_kana01'] = $arrData['order_kana01'];
        $sqlval['deliv_kana02'] = $arrData['order_kana02'];
        $sqlval['deliv_pref'] = $arrData['order_pref'];
        $sqlval['deliv_zip01'] = $arrData['order_zip01'];
        $sqlval['deliv_zip02'] = $arrData['order_zip02'];
        $sqlval['deliv_addr01'] = $arrData['order_addr01'];
        $sqlval['deliv_addr02'] = $arrData['order_addr02'];
        $sqlval['deliv_tel01'] = $arrData['order_tel01'];
        $sqlval['deliv_tel02'] = $arrData['order_tel02'];
        $sqlval['deliv_tel03'] = $arrData['order_tel03'];
        $where = "order_temp_id = ?";
        $objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
    }
}

?>