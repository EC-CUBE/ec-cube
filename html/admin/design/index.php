<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");
require_once(DATA_PATH . "include/page_layout.inc");

// ǧ��Ƚ��
sfIsSuccess(new SC_Session());

class LC_Page {
    var $arrForm;
    var $arrHidden;

    function LC_Page() {
        $this->tpl_mainpage = 'design/index.tpl';
        $this->tpl_subnavi = 'design/subnavi.tpl';
        $this->tpl_subno = "layout";
        $this->tpl_mainno = "design";
        $this->tpl_subtitle = '�쥤�������Խ�';
    }
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

// �ڡ���ID�����
if (isset($_GET['page_id'])) {
    $page_id = $_GET['page_id'];
}else if ($_POST['page_id']){
    $page_id = $_POST['page_id'];
}else{
    $page_id = 1;
}

// �Խ���ǽ�ڡ��������
$objPage->arrEditPage = lfgetPageData();

// �֥�å������ѥǡ��������
$sel   = ", pos.target_id, pos.bloc_id, pos.bloc_row ";
$from  = ", dtb_blocposition AS pos";
$where = " where ";
$where .= " lay.page_id = ? AND ";
$where .= "lay.page_id = pos.page_id AND exists (select bloc_id from dtb_bloc as blc where pos.bloc_id = blc.bloc_id) ORDER BY lay.page_id,pos.target_id, pos.bloc_row, pos.bloc_id ";
$arrData = array($page_id);
$arrBlocPos = lfgetLayoutData($sel, $from, $where, $arrData );

// �ǡ�����¸�ߥ����å���Ԥ�
$arrPageData = lfgetPageData("page_id = ?", array($page_id));
if (count($arrPageData) <= 0) {
    $exists_page = 0;
}else{
    $exists_page = 1;
}
$objPage->exists_page = $exists_page;

// ��å�����ɽ��
if ($_GET['msg'] == "on") {
    $objPage->complate_msg="alert('��Ͽ����λ���ޤ�����');";
}

// �֥�å������
$arrBloc = lfgetBlocData();

// �����֥�å�����
if ($_POST['mode'] == 'new_bloc') {
    header("location: ./bloc.php");
}

// �����ڡ�������
if ($_POST['mode'] == 'new_page') {
    header("location: ./main_edit.php");
}

// �ǡ�����Ͽ����
if ($_POST['mode'] == 'confirm' or $_POST['mode'] == 'preview') {

    $arrPageData = array();
    if ($_POST['mode'] == 'preview') {
        $arrPageData = lfgetPageData(" page_id = ? " , array($page_id));
        $page_id = "0";
        $_POST['page_id'] = "0";
    }

    // �����Ѥ˥ǡ�����������
    $arrUpdBlocData = array();
    $arrTargetFlip = array_flip($arrTarget);

    $upd_cnt = 1;
    $arrUpdData[$upd_cnt]['page_id'] = $_POST['page_id'];

    // POST�Υǡ�����Ȥ��䤹���褦�˽���
    for($upd_cnt = 1; $upd_cnt <= $_POST['bloc_cnt']; $upd_cnt++){
        if (!isset($_POST['id_'.$upd_cnt])) {
            break;
        }
        $arrUpdBlocData[$upd_cnt]['name'] 		= $_POST['name_'.$upd_cnt];							// �֥�å�̾��
        $arrUpdBlocData[$upd_cnt]['id']	  		= $_POST['id_'.$upd_cnt];							// �֥�å�ID
        $arrUpdBlocData[$upd_cnt]['target_id'] 	= $arrTargetFlip[$_POST['target_id_'.$upd_cnt]];	// �������å�ID
        $arrUpdBlocData[$upd_cnt]['top'] 		= $_POST['top_'.$upd_cnt];							// TOP��ɸ
        $arrUpdBlocData[$upd_cnt]['update_url']	= $_SERVER['HTTP_REFERER'];							// ����URL
    }

    // �ǡ����ι�����Ԥ�
    $objDBConn = new SC_DbConn;		// DB���֥�������
    $arrRet = array();				// �ǡ���������

    // delete�¹�
    $del_sql = "";
    $del_sql .= "DELETE FROM dtb_blocposition WHERE page_id = ? ";
    $arrRet = $objDBConn->query($del_sql,array($page_id));

    // �֥�å��ν�����������������Ԥ�
    foreach($arrUpdBlocData as $key => $val){
        // �֥�å��ν�������
        $bloc_row = lfGetRowID($arrUpdBlocData, $val);
        $arrUpdBlocData[$key]['bloc_row'] = $bloc_row;
        $arrUpdBlocData[$key]['page_id'] 	= $_POST['page_id'];	// �ڡ���ID

        if ($arrUpdBlocData[$key]['target_id'] == 5) {
            $arrUpdBlocData[$key]['bloc_row'] = "0";
        }

        // insertʸ����
        $ins_sql = "";
        $ins_sql .= "INSERT INTO dtb_blocposition ";
        $ins_sql .= " values ( ";
        $ins_sql .= "	?  ";			// �ڡ���ID
        $ins_sql .= "	,? ";			// �������å�ID
        $ins_sql .= "	,? ";			// �֥�å�ID
        $ins_sql .= "	,? ";			// �֥�å����¤ӽ��
        $ins_sql .= "	,(SELECT filename FROM dtb_bloc WHERE bloc_id = ?) ";			// �ե�����̾��
        $ins_sql .= "	)  ";

        // insert�ǡ�������
        $arrInsData = array($page_id,
                             $arrUpdBlocData[$key]['target_id'],
                             $arrUpdBlocData[$key]['id'],
                             $arrUpdBlocData[$key]['bloc_row'],
                             $arrUpdBlocData[$key]['id']
                            );
        // SQL�¹�
        $arrRet = $objDBConn->query($ins_sql,$arrInsData);
    }

    // �ץ�ӥ塼����
    if ($_POST['mode'] == 'preview') {
        if ($page_id === "") {
            header("location: ./index.php");
        }
        lfSetPreData($arrPageData);

        $_SESSION['preview'] = "ON";
        header("Location: ". URL_DIR . "preview/index.php");
    }else{
        header("Location: ./index.php?page_id=$page_id&msg=on");
    }
}

// �ǡ���������� �١����ǡ����Ǥʤ���Хե��������
if ($_POST['mode'] == 'delete' and 	!lfCheckBaseData($page_id)) {
    lfDelPageData($page_id);
}

// �֥�å��������������Ѥ��Խ�
$tpl_arrBloc = array();
$cnt = 0;
// ���Ѥ���Ƥ���֥�å��ǡ���������
foreach($arrBlocPos as $key => $val){
    if ($val['page_id'] == $page_id) {
        $tpl_arrBloc = lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt);
        $cnt++;
    }
}

// ̤���ѤΥ֥�å��ǡ������ɲ�
foreach($arrBloc as $key => $val){
    if (!lfChkBloc($val, $tpl_arrBloc)) {
        $val['target_id'] = 5;	// ̤���Ѥ��ɲä���
        $tpl_arrBloc = lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt);
        $cnt++;
    }
}

$objPage->tpl_arrBloc = $tpl_arrBloc;
$objPage->bloc_cnt = count($tpl_arrBloc);
$objPage->page_id = $page_id;

// �ڡ���̾�Τ����
$arrPageData = lfgetPageData(' page_id = ?', array($page_id));
$objPage->arrPageData = $arrPageData[0];

global $GLOBAL_ERR;
$errCnt = 0;
if ($GLOBAL_ERR != "") {
    $arrGlobalErr = explode("\n",$GLOBAL_ERR);
    $errCnt = count($arrGlobalErr) - 8;
    if ($errCnt < 0 ) {
        $errCnt = 0;
    }
}
$objPage->errCnt = $errCnt;

// ���̤�ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

/**************************************************************************************************************
 * �ؿ�̾	��lfgetLayoutData
 * ��������	���Խ���ǽ�ʥڡ���������������
 * ����1	��$sel    ������ Select��ʸ
 * ����2	��$where  ������ Where��ʸ
 * ����3	��$arrVal ������ Where��ιʹ������
 * �����	���ڡ����쥤�����Ⱦ���
 **************************************************************************************************************/
function lfgetLayoutData($sel = '' , $from = '', $where = '', $arrVal = ''){
    $objDBConn = new SC_DbConn;		// DB���֥�������
    $sql = "";						// �ǡ�������SQL������
    $arrRet = array();				// �ǡ���������

    // SQL����

    $sql = "";
    $sql .= " select ";
    $sql .= "     lay.page_id ";
    $sql .= "     ,lay.page_name ";
    $sql .= "     ,lay.url ";
    $sql .= "     ,lay.author ";
    $sql .= "     ,lay.description ";
    $sql .= "     ,lay.keyword ";
    $sql .= "     ,lay.update_url ";
    $sql .= "     ,lay.create_date ";
    $sql .= "     ,lay.update_date ";

    // Select��λ��꤬������ɲ�
    if ($sel != '') {
        $sql .= $sel;
    }

    $sql .= " from dtb_pagelayout AS lay ";
    // From��λ��꤬������ɲ�
    if ($from != '') {
        $sql .= $from;
    }

    // where��λ��꤬������ɲ�
    if ($where != '') {
        $sql .= $where;
    }else{
        $sql .= " ORDER BY lay.page_id ";
    }

    $arrRet = $objDBConn->getAll($sql, $arrVal);

    return $arrRet;
}

/**************************************************************************************************************
 * �ؿ�̾	��lfgetBlocData
 * ��������	���֥�å�������������
 * ����1	��$where  ������ Where��ʸ
 * ����2	��$arrVal ������ Where��ιʹ������
 * �����	���֥�å�����
 **************************************************************************************************************/
function lfgetBlocData($where = '', $arrVal = ''){
    $objDBConn = new SC_DbConn;		// DB���֥�������
    $sql = "";						// �ǡ�������SQL������
    $arrRet = array();				// �ǡ���������

    // SQL����
    $sql = "";
    $sql .= " SELECT ";
    $sql .= "	bloc_id";
    $sql .= "	,bloc_name";
    $sql .= "	,tpl_path";
    $sql .= "	,filename";
    $sql .= " 	,update_date";
    $sql .= " FROM ";
    $sql .= " 	dtb_bloc";

    // where��λ��꤬������ɲ�
    if ($where != '') {
        $sql .= " WHERE " . $where;
    }

    $sql .= " ORDER BY 	bloc_id";

    $arrRet = $objDBConn->getAll($sql, $arrVal);

    return $arrRet;
}

/**************************************************************************************************************
 * �ؿ�̾	��lfSetBlocData
 * ��������	���֥�å�������������������
 * ����1	��$arrBloc    	������ Bloc����
 * ����2	��$tpl_arrBloc	������ �ǡ����򥻥åȤ�������
 * ����3	��$cnt			������ �����ֹ�
 * �����	���ǡ����򥻥åȤ�������
 **************************************************************************************************************/
function lfSetBlocData($arrBloc, $val, $tpl_arrBloc, $cnt) {
    global $arrTarget;

    $tpl_arrBloc[$cnt]['target_id'] = $arrTarget[$val['target_id']];
    $tpl_arrBloc[$cnt]['bloc_id'] = $val['bloc_id'];
    $tpl_arrBloc[$cnt]['bloc_row'] = $val['bloc_row'];

    foreach($arrBloc as $bloc_key => $bloc_val){
        if ($bloc_val['bloc_id'] == $val['bloc_id']) {
            $bloc_name = $bloc_val['bloc_name'];
            break;
        }
    }
    $tpl_arrBloc[$cnt]['name'] = $bloc_name;

    return $tpl_arrBloc;
}

/**************************************************************************************************************
 * �ؿ�̾	��lfChkBloc
 * ��������	���֥�å�ID��������ɲä���Ƥ��뤫�Υ����å���Ԥ�
 * ����1	��$arrBloc    ������ Bloc����
 * ����2	��$arrChkData ������ �����å���Ԥ��ǡ�������
 * �����	��True	������ ¸�ߤ���
 * 			��False	������ ¸�ߤ��ʤ�
 **************************************************************************************************************/
function lfChkBloc($arrBloc, $arrChkData) {
    foreach($arrChkData as $key => $val){
        if ($val['bloc_id'] === $arrBloc['bloc_id'] ) {
            // �����¸�ߤ����True���֤�
            return true;
        }
    }

    // �����¸�ߤ��ʤ����Flase���֤�
    return false;
}

/**************************************************************************************************************
 * �ؿ�̾	��lfGetRowID
 * ��������	���֥�å�ID�������ܤ����֤���Ƥ��뤫��Ĵ�٤�
 * ����1	��$arrUpdData   ������ ��������
 * ����2	��$arrObj 		������ �����å���Ԥ��ǡ�������
 * �����	������
 **************************************************************************************************************/
function lfGetRowID($arrUpdData, $arrObj){
    $no = 0; // ��������ѡ�Ʊ���ǡ�����ɬ��1�濫��Τǡ�����ͤ�0��

    // �оݥǡ����������ܤ����֤���Ƥ���Τ���������롣
    foreach ($arrUpdData as $key => $val) {
        if ($val['target_id'] === $arrObj['target_id'] and $val['top'] <= $arrObj['top']){
            $no++;
        }
    }
    // �ֹ���֤�
    return $no;
}

/**************************************************************************************************************
 * �ؿ�̾	��lfGetRowID
 * ��������	���֥�å�ID�������ܤ����֤���Ƥ��뤫��Ĵ�٤�
 * ����1	��$arrUpdData   ������ ��������
 * ����2	��$arrObj 		������ �����å���Ԥ��ǡ�������
 * �����	������
 **************************************************************************************************************/
function lfSetPreData($arrPageData){
    $objDBConn = new SC_DbConn;		// DB���֥�������
    $sql = "";						// �ǡ�������SQL������
    $ret = ""; 						// �ǡ���������̳�Ǽ��
    $arrUpdData = array();			// �����ǡ���������
    $filename = uniqid("");

    $arrPreData = lfgetPageData(" page_id = ? " , array("0"));

    // tpl�ե�����κ��
    $del_tpl = USER_PATH . "templates/" . $arrPreData[0]['filename'] . '.tpl';
    if (file_exists($del_tpl)){
        unlink($del_tpl);
    }

    // �ץ�ӥ塼��tpl�ե�����Υ��ԡ�
    $tplfile = $arrPageData[0]['tpl_dir'] . $arrPageData[0]['filename'];

    if($tplfile == ""){
        // tpl�ե����뤬���ξ��ˤ�MY�ڡ�����Ƚ��
        $tplfile = "user_data/templates/mypage/index";
    }
    copy(HTML_PATH . $tplfile . ".tpl", USER_PATH . "templates/" . $filename . ".tpl");

    // �����ǡ����μ���
    $sql = "select page_name, header_chk, footer_chk from dtb_pagelayout where page_id = ?";
    $ret = $objDBConn->getAll($sql, array($arrPageData[0]['page_id']));

    // db�ǡ����Υ��ԡ�
    $sql = " update dtb_pagelayout set ";
    $sql .= "     page_name = ?";
    $sql .= "     ,header_chk = ?";
    $sql .= "     ,footer_chk = ?";
    $sql .= "     ,url = ?";
    $sql .= "     ,tpl_dir = ?";
    $sql .= "     ,filename = ?";
    $sql .= " where page_id = 0";

    $arrUpdData = array($ret[0]['page_id']
                        ,$ret[0]['page_id']
                        ,$ret[0]['page_id']
                        ,USER_DIR."templates/"
                        ,USER_DIR."templates/"
                        ,$filename
                        );

    $objDBConn->query($sql,$arrUpdData);
}
