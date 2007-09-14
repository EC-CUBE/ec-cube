<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 */

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

//-------------------------------------��NONMEMBER----------------------------------------------
//---- �ڡ����������

$CONF = sf_getBasisData();                  // Ź�޴��ܾ���
$objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
$objPage->arrPref = $arrPref;
$objPage->arrJob = $arrJob;
$objPage->arrReminder = $arrReminder;
$objPage->arrYear = $objDate->getYear('', 1950);    //�����եץ����������
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

//SSLURLȽ��
if (SSLURL_CHECK == 1){
    $ssl_url= sfRmDupSlash(MOBILE_SSL_URL.$_SERVER['REQUEST_URI']);
    if (!ereg("^https://", $non_ssl_url)){
        sfDispSiteError(URL_ERROR, "", false, "", true);
    }
}

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

// �桼����ˡ���ID�μ����ȹ������֤�������������å�
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

$objPage->tpl_uniqid = $uniqid;

switch($_POST['mode']) {
// ���Υڡ��������
case 'return':
    // ��ǧ�ڡ����ذ�ư
    header("Location: " . gfAddSessionId(MOBILE_URL_CART_TOP));
    exit;
    break;
case 'nonmember':
    $objPage = lfSetNonMember($objPage);
    // ��break�ʤ�
default:
    if($_GET['from'] == 'nonmember') {
        $objPage = lfSetNonMember($objPage);
    }
    // �桼����ˡ���ID�μ���
    $uniqid = $objSiteSess->getUniqId();
//    $objQuery = new SC_Query();
//    $where = "order_temp_id = ?";
//    $arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($uniqid));
//    sfprintr($arrRet);
//    // DB�ͤμ���
//    $objFormParam->setParam($arrRet[0]);
//    $objFormParam->setValue('order_email_check', $arrRet[0]['order_email']);
//    $objFormParam->setDBDate($arrRet[0]['order_birth']);
    break;
}

// ���å���Ƚ��
$objPage->tpl_login_email = $objCookie->getCookie('login_email');
if($objPage->tpl_login_email != "") {
    $objPage->tpl_login_memory = "1";
}

// ���������դμ���
$objDate = new SC_Date(START_BIRTH_YEAR);
$objPage->arrYear = $objDate->getYear('', 1950);    //�����եץ����������
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

// �����ͤμ���
$objPage->arrForm = $objFormParam->getFormParamList();
sfprintr($_SESSION);
//objPage�ξ����objView�˳�Ǽ
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//--------------------------------------------------------------------------------------------------------------------------
/* �������ϥڡ����Υ��å� */
function lfSetNonMember($objPage) {
    
    $arrRegistColumn = array(
                             array(  "column" => "name01", "convert" => "aKV" ),
                             array(  "column" => "name02", "convert" => "aKV" ),
                             array(  "column" => "kana01", "convert" => "CKV" ),
                             array(  "column" => "kana02", "convert" => "CKV" ),
                             array(  "column" => "zip01", "convert" => "n" ),
                             array(  "column" => "zip02", "convert" => "n" ),
                             array(  "column" => "pref", "convert" => "n" ),
                             array(  "column" => "addr01", "convert" => "aKV" ),
                             array(  "column" => "addr02", "convert" => "aKV" ),
                             array(  "column" => "email", "convert" => "a" ),
                             array(  "column" => "email2", "convert" => "a" ),
                             array(  "column" => "email_mobile", "convert" => "a" ),
                             array(  "column" => "email_mobile2", "convert" => "a" ),
                             array(  "column" => "tel01", "convert" => "n" ),
                             array(  "column" => "tel02", "convert" => "n" ),
                             array(  "column" => "tel03", "convert" => "n" ),
                             array(  "column" => "fax01", "convert" => "n" ),
                             array(  "column" => "fax02", "convert" => "n" ),
                             array(  "column" => "fax03", "convert" => "n" ),
                             array(  "column" => "sex", "convert" => "n" ),
                             array(  "column" => "job", "convert" => "n" ),
                             array(  "column" => "birth", "convert" => "n" ),
                             array(  "column" => "reminder", "convert" => "n" ),
                             array(  "column" => "reminder_answer", "convert" => "aKV"),
                             array(  "column" => "password", "convert" => "a" ),
                             array(  "column" => "password02", "convert" => "a" ),
                             array(  "column" => "mailmaga_flg", "convert" => "n" ),
                         );

//---- ��Ͽ�����ѥ��������
$arrRejectRegistColumn = array("year", "month", "day", "email02", "email_mobile02", "password02");
        
    $objPage->tpl_mainpage = 'nonmember/nonmember_set1.tpl';
    $objPage->tpl_css = array();
    $objPage->tpl_css[] = '/css/layout/login/nonmember.css';
    
    //-- POST�ǡ����ΰ����Ѥ�
    $objPage->arrForm = $_POST;
    
    if($objPage->arrForm['year'] == '----') {
        $objPage->arrForm['year'] = '';
    }
    
    $objPage->arrForm['email'] = strtolower($objPage->arrForm['email']);        // email�Ϥ��٤ƾ�ʸ���ǽ���
    
    //-- ���ϥǡ������Ѵ�
    $objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);

    // ���ܥ����ѽ���
    //return����˸��Υڡ�����̾�������äƤ���
    if (!empty($_POST["return"])) {
        switch ($_POST["mode2"]) {
        case "deliv_date":  
            break;
        case "deliv":
            $_POST["mode2"] = "set3";
            break;
        case "set3":
            $_POST["mode2"] = "set2";
            break;
        default:
            $_POST["mode2"] = "set1";
            break;
        }
    }    

    //--�����ϥ��顼�����å�
    if (!empty($_POST["mode2"])) {
        if ($_POST["mode2"] == "set2") {
            $objPage->arrErr = lfErrorCheck1($objPage->arrForm);
            $objPage->tpl_mainpage = 'nonmember/nonmember_set1.tpl';
            $objPage->tpl_title = '�����;�������(1/3)';
        } elseif ($_POST["mode2"] == "set3") {
            $objPage->arrErr = lfErrorCheck2($objPage->arrForm);
            $objPage->tpl_mainpage = 'nonmember/nonmember_set2.tpl';
            $objPage->tpl_title = '�����;�������(2/3)';
        } elseif ($_POST["mode2"] == "deliv"){
            $objPage->arrErr = lfErrorCheck3($objPage->arrForm);
            $objPage->tpl_mainpage = 'nonmember/nonmember_set3.tpl';
            $objPage->tpl_title = '�����;�������(3/3)';
        }
    
    //�ե�������ͤ�$objPage�Υ����Ȥ����������Ƥ���
   foreach($objPage->arrForm as $key => $val) {
            $objPage->$key = $val;
        }
    }

        // ���ϥ��顼�Υ����å�
    if ($objPage->arrErr || !empty($_POST["return"])) {     

        //-- �ǡ���������
        if ($_POST["mode2"] == "set2") {
            $checkVal = array("email", "name01", "name02", "kana01", "kana02");
        } elseif ($_POST["mode2"] == "set3") {
            $checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
        } else {
            $checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mail_flag");
        }

        foreach($objPage->arrForm as $key => $val) {
            if ($key != "mode2" && $key != "submit" && $key != "return" && $key != session_name() && !in_array($key, $checkVal))
                $objPage->list_data[ $key ] = $val;
        }

    } else {

        //--���ƥ�ץ졼������
        if ($_POST["mode2"] == "set2") {
            $objPage->tpl_mainpage = 'nonmember/nonmember_set2.tpl';
            $objPage->tpl_title = '�����;�������(2/3)';
        } elseif ($_POST["mode2"] == "set3") {
            $objPage->tpl_mainpage = 'nonmember/nonmember_set3.tpl';
            $objPage->tpl_title = '�����;�������(3/3)';

            if (@$objPage->arrForm['pref'] == "" && @$objPage->arrForm['addr01'] == "" && @$objPage->arrForm['addr02'] == "") {
                $address = lfGetAddress($_REQUEST['zip01'].$_REQUEST['zip02']);
                $objPage->pref = @$address[0]['state'];
                $objPage->addr01 = @$address[0]['city'] . @$address[0]['town'];
            }
        }

        //-- �ǡ�������
        unset($objPage->list_data);
        if ($_POST["mode2"] == "set2") {
            $checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
        } elseif ($_POST["mode2"] == "set3") {
            $checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mail_flag");
        } else {
            $checkVal = array();
        }

        //$objPage->arrForm�ե�������ͤ�list�˳�Ǽ
        foreach($objPage->arrForm as $key => $val) {
            if ($key != "mode2" && $key != "submit" && $key != "confirm" && $key != "return" && $key != session_name() && !in_array($key, $checkVal)) {
                $objPage->list_data[ $key ] = $val;
                $_SESSION['user_info'][$key] = $val;
            }
        }
//        sfprintr($_SESSION['user_info']);
       if($_POST["mode2"] == "deliv"){
            $_SESSION['user_info']['mode2'] = "deliv"; 
            header("Location:" . gfAddSessionId("./deliv.php"));
       }
    }
    return $objPage;
}


/**
 * regist data function
 * 
 * @param string $uniqid this parameter is unique ID of user. 
 * 
 * 
 */
 //�ǡ�������Ͽ��Ԥ�
function lfRegistData($uniqid) {
    global $objFormParam;
    $arrRet = $objFormParam->getHashArray();
    $sqlval = $objFormParam->getDbArray();
    
    // ��Ͽ�ǡ����κ���
    $sqlval['order_temp_id'] = $uniqid;
    $sqlval['order_birth'] = sfGetTimestamp($arrRet['year'], $arrRet['month'], $arrRet['day']);
    $sqlval['update_date'] = 'Now()';
    $sqlval['customer_id'] = '0';
    $sqlval['order_name01'] = $objPage->arrAddr[0]['name01'];
          
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

/* �������ƤΥ����å� */
function lfCheckError() {
    global $objFormParam;
    // ���ϥǡ������Ϥ���
    $arrRet =  $objFormParam->getHashArray();
    $objErr = new SC_CheckError($arrRet);
    $objErr->arrErr = $objFormParam->checkError();
        
    // �̤Τ��Ϥ�������å�
    if($_POST['deliv_check'] == "1") { 
        $objErr->doFunc(array("��̾��������", "deliv_name01"), array("EXIST_CHECK"));
        $objErr->doFunc(array("��̾����̾��", "deliv_name02"), array("EXIST_CHECK"));
        $objErr->doFunc(array("�եꥬ�ʡʥ�����", "deliv_kana01"), array("EXIST_CHECK"));
        $objErr->doFunc(array("�եꥬ�ʡʥᥤ��", "deliv_kana02"), array("EXIST_CHECK"));
        $objErr->doFunc(array("͹���ֹ�1", "deliv_zip01"), array("EXIST_CHECK"));
        $objErr->doFunc(array("͹���ֹ�2", "deliv_zip02"), array("EXIST_CHECK"));
        $objErr->doFunc(array("��ƻ�ܸ�", "deliv_pref"), array("EXIST_CHECK"));
        $objErr->doFunc(array("����1", "deliv_addr01"), array("EXIST_CHECK"));
        $objErr->doFunc(array("����2", "deliv_addr02"), array("EXIST_CHECK"));
        $objErr->doFunc(array("�����ֹ�1", "deliv_tel01"), array("EXIST_CHECK"));
        $objErr->doFunc(array("�����ֹ�2", "deliv_tel02"), array("EXIST_CHECK"));
        $objErr->doFunc(array("�����ֹ�3", "deliv_tel03"), array("EXIST_CHECK"));
    }
    
    // ʣ�����ܥ����å�
    $objErr->doFunc(array("TEL", "order_tel01", "order_tel02", "order_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
    $objErr->doFunc(array("FAX", "order_fax01", "order_fax02", "order_fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
    $objErr->doFunc(array("͹���ֹ�", "order_zip01", "order_zip02"), array("ALL_EXIST_CHECK"));
    $objErr->doFunc(array("TEL", "deliv_tel01", "deliv_tel02", "deliv_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
    $objErr->doFunc(array("FAX", "deliv_fax01", "deliv_fax02", "deliv_fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
    $objErr->doFunc(array("͹���ֹ�", "deliv_zip01", "deliv_zip02"), array("ALL_EXIST_CHECK"));
    $objErr->doFunc(array("��ǯ����", "year", "month", "day"), array("CHECK_DATE"));
    $objErr->doFunc(array("�᡼�륢�ɥ쥹", "�᡼�륢�ɥ쥹�ʳ�ǧ��", "order_email", "order_email_check"), array("EQUAL_CHECK"));
    
    // ���Ǥ˥��ޥ��ơ��֥�˲���Ȥ��ƥ᡼�륢�ɥ쥹����Ͽ����Ƥ�����
    if(sfCheckCustomerMailMaga($arrRet['order_email'])) {
        $objErr->arrErr['order_email'] = "���Υ᡼�륢�ɥ쥹�Ϥ��Ǥ���Ͽ����Ƥ��ޤ���<br>";
    }
        
    return $objErr->arrErr;
}


//-----------------------------NONMEMBER�ؿ�����------------------------------------------------------------------
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


//---- ���ϥ��顼�����å�
function lfErrorCheck1($array) {

    global $objConn;
    $objErr = new SC_CheckError($array);
    
    $objErr->doFunc(array("��̾��������", 'name01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("��̾����̾��", 'name02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("��̾���ʥ���/����", 'kana01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
    $objErr->doFunc(array("��̾���ʥ���/̾��", 'kana02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
    $objErr->doFunc(array('�᡼�륢�ɥ쥹', "email", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));

    return $objErr->arrErr;
}

//---- ���ϥ��顼�����å�
function lfErrorCheck2($array) {

    global $objConn, $objDate;
    $objErr = new SC_CheckError($array);
    
    $objErr->doFunc(array("͹���ֹ�1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
    $objErr->doFunc(array("͹���ֹ�2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK")); 
    $objErr->doFunc(array("͹���ֹ�", "zip01", "zip02"), array("ALL_EXIST_CHECK"));

    $objErr->doFunc(array("����", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
    $objErr->doFunc(array("��ǯ���� (ǯ)", "year", 4), array("EXIST_CHECK", "SPTAB_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
    if (!isset($objErr->arrErr['year'])) {
        $objErr->doFunc(array("��ǯ���� (ǯ)", "year", $objDate->getStartYear()), array("MIN_CHECK"));
        $objErr->doFunc(array("��ǯ���� (ǯ)", "year", $objDate->getEndYear()), array("MAX_CHECK"));
    }
    $objErr->doFunc(array("��ǯ���� (����)", "month", "day"), array("SELECT_CHECK"));
    if (!isset($objErr->arrErr['year']) && !isset($objErr->arrErr['month']) && !isset($objErr->arrErr['day'])) {
        $objErr->doFunc(array("��ǯ����", "year", "month", "day"), array("CHECK_DATE"));
    }
    
    return $objErr->arrErr;
}


//---- ���ϥ��顼�����å�
function lfErrorCheck3($array) {

    global $objConn;
    $objErr = new SC_CheckError($array);
    
    $objErr->doFunc(array("��ƻ�ܸ�", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
    $objErr->doFunc(array("�Զ�Į¼", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("����", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("�����ֹ�1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK" ));
    $objErr->doFunc(array("�����ֹ�2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK" ));
    $objErr->doFunc(array("�����ֹ�3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK" ));
    $objErr->doFunc(array("�����ֹ�", "tel01", "tel02", "tel03",TEL_ITEM_LEN) ,array("TEL_CHECK"));
    
    return $objErr->arrErr;
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
        �ʲ��Τ褦��ʸ�������äƤ���Τ�   �к����롣
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
//NONMEMBER_�ؿ���---------------------------------------------------------------------------------------
?>