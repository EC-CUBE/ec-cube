<?php

require_once("../../require.php");
require_once(ROOT_DIR."data/include/csv_output.inc");

class LC_Page {
	var $arrForm;
	var $arrHidden;

	function LC_Page() {
		$this->tpl_mainpage = 'contents/csv_sql.tpl';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = 'csv';
		$this->tpl_subno_csv = 'csv_sql';
		$this->tpl_mainno = "contents";
		$this->tpl_subtitle = 'CSV��������';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

$objPage->arrSubnavi = $arrSubnavi;
$objPage->arrSubnaviName = $arrSubnaviName;

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// SQL_ID�μ���
if ($_POST['sql_id'] != "") {
	$sql_id = $_POST['sql_id'];
}elseif($_GET['sql_id'] != ""){
	$sql_id = $_GET['sql_id'];
}else{
	$sql_id = "";
}

$mode = $_POST['mode'];

switch($_POST['mode']) {
	// �ǡ�������Ͽ
	case "confirm":
		//$code = mb_internal_encoding();
		//sfprintr($code);

		// ���顼�����å�
		$objPage->arrErr = lfCheckError($_POST);
		
		if (count($objPage->arrErr) <= 0){
			// �ǡ����ι���
			$sql_id = lfUpdData($sql_id, $_POST);
			// ��λ��å�����ɽ��
			$objPage->tpl_onload = "alert('��Ͽ����λ���ޤ�����');";
		}
		break;
	
	// ��ǧ����
	case "preview":
		// SQLʸɽ��
		$sql = "SELECT \n" . $_POST['sql'];
		$objPage->sql = $sql;
		
		// ���顼ɽ��
		$objErrMsg = lfCheckSQL($_POST);
		if ($errMsg != "") {
			$errMsg = $objErrMsg->message . "\n" . $objErrMsg->userinfo;
		}
		$objPage->sqlerr = $errMsg;

		$objPage->objView = $objView;
		
		// ���̤�ɽ��
		$objView->assignobj($objPage);
		$objView->display('contents/csv_sql_view.tpl');
		exit;
		break;

	// ��������
	case "new_page":
		header("location: ./csv_sql.php");
		break;
		
	// �ǡ������
	case "delete":
		lfDelData($sql_id);
		header("location: ./csv_sql.php");
		break;
		
	case "csv_output":
		// CSV���ϥǡ�������
		$arrCsvData = lfGetSqlList(" WHERE sql_id = ?", array($_POST['csv_output_id']));
		
		$objQuery = new SC_Query();
		$arrCsvOutputData = $objQuery->getall("SELECT " . $arrCsvData[0]['sql']);
		
		if (count($arrCsvOutputData) > 0) {
			
			$arrKey = array_keys(sfSwapArray($arrCsvOutputData));
			foreach($arrKey as $data) {
				if ($i != 0) $header .= ", ";
				$header .= $data;
				$i ++;
			}
			$header .= "\n";

			$data = lfGetCSVData($arrCsvOutputData, $arrKey);
			// CSV����
			sfCSVDownload($header.$data);
			exit;
		break;
		}else{
			$objPage->tpl_onload = "alert('���ϥǡ���������ޤ���');";
			$sql_id = "";
			$_POST="";
		}
		break;
}

// mode �� confirm �ʳ��ΤȤ��ϴ�λ��å������Ͻ��Ϥ��ʤ�
if ($mode != "confirm" and $mode != "csv_output") {
	$objPage->tpl_onload = "";
}

// ��Ͽ�Ѥ�SQL��������
$arrSqlList = lfGetSqlList();

// �Խ���SQL�ǡ����μ���
if ($sql_id != "") {
	$arrSqlData = lfGetSqlList(" WHERE sql_id = ?", array($sql_id));
}

// �ơ��֥�������������
$arrTableList = lfGetTableList();
$arrTableList = sfSwapArray($arrTableList);

// �������򤵤�Ƥ���ơ��֥���������
if ($_POST['selectTable'] == ""){
	$selectTable = $arrTableList['relname'][0];
}else{
	$selectTable = $_POST['selectTable'];
}

// �����������������
$arrColList = lfGetColumnList($selectTable);
$arrColList =  sfSwapArray($arrColList);

// �ƥ�ץ졼�Ȥ˽��Ϥ���ǡ����򥻥å�
$objPage->arrSqlList = $arrSqlList;																// SQL����
$objPage->arrTableList = sfarrCombine($arrTableList['relname'], $arrTableList['description']);	// �ơ��֥����
$objPage->arrColList = sfarrCombine($arrColList['attname'],$arrColList['description']);			// ��������
$objPage->selectTable = $selectTable;															// ���򤵤�Ƥ���ơ��֥�
$objPage->sql_id = $sql_id;																		// ���򤵤�Ƥ���SQL

// POST���줿�ǡ����򥻥åȤ���
if (count($_POST) > 0) {
	$arrSqlData[0]['name'] = $_POST['name'];
	$arrSqlData[0]['sql'] = $_POST['sql'];
}
$objPage->arrSqlData = $arrSqlData[0];															// ���򤵤�Ƥ���SQL�ǡ���

// ���̤�ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**************************************************************************************************************
 * �ؿ�̾	��lfGetTableList
 * ��������	���ơ��֥�������������
 * ����		���ʤ�
 * ����� �����������
 **************************************************************************************************************/
function lfGetTableList(){
	$objQuery = new SC_Query();
	$arrRet = array();		// ��̼�����
	
	$sql = "";
	$sql .= " SELECT";
	$sql .= "     c.relname ,";
	$sql .= "     c.relname || ':' ||obj_description(c.oid) as description";
	$sql .= " FROM";
	$sql .= "     pg_class c,";
	$sql .= "     pg_user u";
	$sql .= " WHERE";
	$sql .= "     c.relowner = u.usesysid AND";
	$sql .= "     relname IN (SELECT";
	$sql .= "                     tablename";
	$sql .= "                 FROM";
	$sql .= "                     pg_tables";
	$sql .= "                 WHERE";
	$sql .= "                     tableowner=? ";
	$sql .= "                 )";
	$sql .= " ORDER BY c.relname ";
	$arrRet = $objQuery->getAll($sql, array(DB_USER));
	
	return $arrRet;
}


/**************************************************************************************************************
 * �ؿ�̾	��lfGetColunmList
 * ��������	���ơ��֥�Υ����������������
 * ����		��$selectTable���ơ��֥�̾��
 * ����� �����������
 **************************************************************************************************************/
function lfGetColumnList($selectTable){
	$objQuery = new SC_Query();
	$arrRet = array();		// ��̼�����
	
	$sql = "";
	$sql .= " SELECT";
	$sql .= "     a.attname,";
	$sql .= "     a.attnum as fldnum, ";
	$sql .= "     (select case count(description) when 0 then a.attname else (select a.attname || ':' || description from pg_description where a.attrelid=objoid and a.attnum=objsubid ) end from pg_description where a.attrelid=objoid and a.attnum=objsubid ) as description ";
	$sql .= " FROM";
	$sql .= "     pg_class c,";
	$sql .= "         pg_attribute a left join pg_description e on (a.attrelid=e.objoid and a.attnum=e.objsubid) ";
	$sql .= " ";
	$sql .= " WHERE";
	$sql .= "     (c.relname=?) AND";
	$sql .= "     (c.oid=a.attrelid) AND";
	$sql .= "     a.attnum > 0";
	$sql .= " ORDER BY";
	$sql .= "     fldnum";
	$sql .= " ";
	$arrRet = $objQuery->getAll($sql, array($selectTable));	

	return $arrRet;
	
}

/**************************************************************************************************************
 * �ؿ�̾	��lfGetSqlList
 * ��������	����Ͽ�Ѥ�SQL�������������
 * ����1	��$where��Where��
 * ����2	��$arrData���ʤ���ߥǡ���
 * ����� �����������
 **************************************************************************************************************/
function lfGetSqlList($where = "" , $arrData = array()){
	$objQuery = new SC_Query();
	$arrRet = array();		// ��̼�����
	
	$sql = "";
	$sql .= " SELECT";
	$sql .= "     sql_id,";
	$sql .= "     name,";
	$sql .= "     sql,";
	$sql .= "     update_date,";
	$sql .= "     create_date";
	$sql .= " FROM";
	$sql .= "     dtb_csv_sql";
	
	// Where��λ��꤬����з�礹��
	if ($where != "") {
		$sql .= " $where ";
	}else{
		$sql .= " ORDER BY sql_id ";
	}
	$sql .= " ";

	// �ǡ�����������Ϥ���Ƥ�����ˤϥ��åȤ���
	if (count($arrData) > 0) {
		$arrRet = $objQuery->getall($sql, $arrData);
	}else{
		$arrRet = $objQuery->getall($sql);
	}

	return $arrRet;
	
}

/**************************************************************************************************************
 * �ؿ�̾	��lfUpdCsvOutput
 * ��������	�����Ϲ��ܤΥ��顼�����å���Ԥ�
 * ����		��POST�ǡ���
 * ����		�����顼����
 **************************************************************************************************************/
function lfCheckError($data){
	$objErr = new SC_CheckError();
	$objErr->doFunc( array("̾��", "name"), array("EXIST_CHECK") );
	$objErr->doFunc( array("SQLʸ", "sql"), array("EXIST_CHECK") );
	
	// SQL�������������å�
	if ($objErr->arrErr['sql'] == "") {
		$objsqlErr = lfCheckSQL($data);
		if ($objsqlErr != "") {
			$objErr->arrErr["sql"] = "SQLʸ�������Ǥ���SQLʸ��ľ���Ƥ�������";
		}
	}
	
	return $objErr->arrErr;

}

/**************************************************************************************************************
 * �ؿ�̾	��lfCheckSQL
 * ��������	�����Ϥ��줿SQLʸ���������������å���Ԥ�
 * ����		��POST�ǡ���
 * ����		�����顼����
 **************************************************************************************************************/
function lfCheckSQL($data){
	$err = "";
	$objDbConn = new SC_DbConn();
	$sql = "SELECT " . $data['sql'] . " ";
	$ret = $objDbConn->conn->query($sql);
	if ($objDbConn->conn->isError($ret)){
		$err = $ret;
	}

	return $err;
}

function lfprintr($data){
	print_r($data);
}

/**************************************************************************************************************
 * �ؿ�̾	��lfUpdData
 * ��������	��DB�˥ǡ�������¸����
 * ����1	��$sql_id��������������ǡ�����SQL_ID
 * ����2	��$arrData�����������ǡ���
 * �����	��$sql_id:SQL_ID���֤�
 **************************************************************************************************************/
function lfUpdData($sql_id = "", $arrData = array()){
	$objQuery = new SC_Query();		// DB���֥�������
	$sql = "";						// �ǡ�������SQL������
	$arrRet = array();				// �ǡ���������(����Ƚ��)
	$arrVal = array();				// �ǡ�������

	// sql_id �����ꤵ��Ƥ�����ˤ�UPDATE
	if ($sql_id != "") {
		// ¸�ߥ����å�
		$arrSqlData = lfGetSqlList(" WHERE sql_id = ?", array($sql_id));
		if (count($arrSqlData) > 0) {
			// �ǡ�������
			$sql = "UPDATE dtb_csv_sql SET name = ?, sql = ?, update_date = now() WHERE sql_id = ? ";
			$arrVal= array($arrData['name'], $arrData['sql'], $sql_id);
		}else{
			// �ǡ����ο�������
			$sql_id = "";
			$sql = "INSERT INTO dtb_csv_sql (name, sql) values (?, ?) ";
			$arrVal= array($arrData['name'], $arrData['sql']);
			
		}
	}else{
		// �ǡ����ο�������
		$sql = "INSERT INTO dtb_csv_sql (name, sql) values (?, ?) ";
		$arrVal= array($arrData['name'], $arrData['sql']);
	}
	// SQL�¹�	
	$arrRet = $objQuery->query($sql,$arrVal);
	
	// ������������$sql_id�����
	if ($sql_id == "") {
		$arrNewData = lfGetSqlList(" ORDER BY create_date DESC");
		$sql_id = $arrNewData[0]['sql_id'];
	}
	
	return $sql_id;
}


/**************************************************************************************************************
 * �ؿ�̾	��lfDelData
 * ��������	���ǡ�����������
 * ����1	��$sql_id�������������ǡ�����SQL_ID
 * �����	���¹Է�̡�TRUE������ FALSE������
 **************************************************************************************************************/
function lfDelData($sql_id = ""){
	$objQuery = new SC_Query();		// DB���֥�������
	$sql = "";						// �ǡ�������SQL������
	$Ret = false;					// �¹Է��

	// sql_id �����ꤵ��Ƥ�����Τ߼¹�
	if ($sql_id != "") {
		// �ǡ����κ��
		$sql = "DELETE FROM dtb_csv_sql WHERE sql_id = ? ";
		// SQL�¹�	
		$ret = $objQuery->query($sql,array($sql_id));
	}else{
		$ret = false;
	}

	// ��̤��֤�
	return $ret;
}


//---- CSV�����ѥǡ�������
function lfGetCSVData( $array, $arrayIndex){	
	for ($i=0; $i<count($array); $i++){
		for ($j=0; $j<count($array[$i]); $j++ ){
			if ( $j > 0 ) $return .= ",";
			$return .= "\"";			
			if ( $arrayIndex ){
				$return .= mb_ereg_replace("<","��",mb_ereg_replace( "\"","\"\"",$array[$i][$arrayIndex[$j]] )) ."\"";	
			} else {
				$return .= mb_ereg_replace("<","��",mb_ereg_replace( "\"","\"\"",$array[$i][$j] )) ."\"";
			}
		}
		$return .= "\n";			
	}
	
	return $return;
}

