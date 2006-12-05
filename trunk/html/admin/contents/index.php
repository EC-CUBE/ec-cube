<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

//---- ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

//---- �ڡ���ɽ�����饹
class LC_Page {
	
	var $arrSession;
	var $tpl_mainpage;
	var $sub_navipage;
	var $regist_data;
	var $arrYear;
	var $arrMonth;
	var $arrDate;
	var $selected_year;
	var $selected_month;
	var $selected_day;
	var $list_data;
	var $max_rank;
	var $edit_mode;
	var $news_title;
	var $news_date_unix;
	var $news_url;
	var $link_method;
	
	function LC_Page() {
		$this->tpl_mainpage = 'contents/index.tpl';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = "index";
		$this->tpl_mainno = 'contents';
		$this->selected_year = date("Y");
		$this->selected_month = date("n");
		$this->selected_day = date("j");
		$this->tpl_subtitle = '����������';
	}
}


//---- �ڡ����������
$conn = new SC_DbConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objDate = new SC_Date(ADMIN_NEWS_STARTYEAR);

//----�����եץ����������
$objPage->arrYear = $objDate->getYear();
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

//----��������Ͽ/�Խ���Ͽ
if ( $_POST['mode'] == 'regist'){
	$_POST = lfConvData($_POST);

	if ($objPage->arrErr = lfErrorCheck()) {		// ���ϥ��顼�Υ����å�
		foreach($_POST as $key => $val) {
			$objPage->$key = $val;
		}
		$objPage->selected_year = $_POST["year"];
		$objPage->selected_month = $_POST["month"];
		$objPage->selected_day = $_POST["day"];

	} else {
		
		if (isset($_POST['link_method']) == ""){
			$_POST['link_method'] = 1;
		}
		
		$registDate = $_POST['year'] ."/". $_POST['month'] ."/". $_POST['day'];

		//-- �Խ���Ͽ
		if (strlen($_POST["news_id"]) > 0 && is_numeric($_POST["news_id"])) {

			lfNewsUpdate();

		//--��������Ͽ
		} else {
			lfNewsInsert();
		}

		$objPage->tpl_onload = "window.alert('�Խ�����λ���ޤ���');";
	}
}

//----���Խ��ǡ�������
if ($_POST["mode"] == "search" && is_numeric($_POST["news_id"])) {
	$sql = "SELECT *, cast(substring(news_date,1, 10) as date) as cast_news_date FROM dtb_news WHERE news_id = ? ";
	$result = $conn->getAll($sql, array($_POST["news_id"]));
	foreach($result[0] as $key => $val ){
		$objPage->$key = $val;
	}
	$arrData = split("-",$result[0]["cast_news_date"]);
	
	$objPage->selected_year = $arrData[0];
	$objPage->selected_month =$arrData[1];
	$objPage->selected_day =  $arrData[2];

	$objPage->edit_mode = "on";
}

//----���ǡ������
if ( $_POST['mode'] == 'delete' && is_numeric($_POST["news_id"])) {
	
	// rank�����
	$pre_rank = $conn->getone(" SELECT rank FROM dtb_news WHERE del_flg = 0 AND news_id = ? ", array( $_POST['news_id']  ));

	//-- ������뿷�����ʹߤ�rank��1�ķ���夲�Ƥ���
	$conn->query("BEGIN");
	$sql = "UPDATE dtb_news SET rank = rank - 1, update_date = NOW() WHERE del_flg = 0 AND rank > ?";
	$conn->query( $sql, array( $pre_rank  ) );

	$sql = "UPDATE dtb_news SET rank = 0, del_flg = 1, update_date = NOW() WHERE news_id = ?";
	$conn->query( $sql, array( $_POST['news_id'] ) );
	$conn->query("COMMIT");

	sfReload();				//��ʬ�˥�����쥯�ȡʺ��ɹ��ˤ���ư���ɻߡ�
}

//----��ɽ����̰�ư

if ( $_POST['mode'] == 'move' && is_numeric($_POST["news_id"]) ) {
	if ($_POST["term"] == "up") {
		sfRankUp("dtb_news", "news_id", $_POST["news_id"]);
	} else if ($_POST["term"] == "down") {
		sfRankDown("dtb_news", "news_id", $_POST["news_id"]);	
	}
	//sf_rebuildIndex($conn);
	sfReload();
}

//----������ɽ����̰�ư
if ($_POST['mode'] == 'moveRankSet') {
	$key = "pos-".$_POST['news_id'];
	$input_pos = mb_convert_kana($_POST[$key], "n");
	if(sfIsInt($input_pos)) {
		sfMoveRank("dtb_news", "news_id", $_POST['news_id'], $input_pos);
		sfReload();
	}
}


//---- ���ǡ�������
$sql = "SELECT *, cast(substring(news_date,1, 10) as date) as cast_news_date FROM dtb_news WHERE del_flg = '0' ORDER BY rank DESC";
$objPage->list_data = $conn->getAll($sql);
$objPage->line_max = count($objPage->list_data);
$sql = "SELECT MAX(rank) FROM dtb_news WHERE del_flg = '0'";		// rank�κ����ͤ����
$objPage->max_rank = $conn->getOne($sql);

$objPage->arrForm['news_select'] = 0;

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


//function --------------------------------------------------------------------------------------------- 

//---- ����ʸ����������
function lfConvData( $data ){
	
	 // ʸ������Ѵ���mb_convert_kana���Ѵ����ץ�����							
	$arrFlag = array(
					  "year" => "n"
					 ,"month" => "n"
					 ,"day" => "n"
					 ,"url" => "a"
					 ,"news_title" => "aKV"
					 ,"news_comment" => "aKV"
					 ,"link_method" => "n"
					);
		
	if ( is_array($data) ){
		foreach ($arrFlag as $key=>$line) {
			$data[$key] = mb_convert_kana($data[$key], $line);
		}
	}

	return $data;
}

//----�������̤ذ�ư
function sf_setRankPosition($conn, $tableName, $keyIdColumn, $keyId, $position) {

	// ���ȤΥ�󥯤��������
	$conn->query("BEGIN");
	$rank = $conn->getOne("SELECT rank FROM $tableName WHERE $keyIdColumn = ?", array($keyId));	

	if( $position > $rank ) $term = "- 1";	//�����ؤ���ν�̤����촹�����ν�̤���礭�����
	if( $position < $rank ) $term = "+ 1";	//�����ؤ���ν�̤����촹�����ν�̤�꾮�������

	//--�����ꤷ����̤ξ��ʤ����ư�����뾦�ʤޤǤ�rank�򣱤Ĥ��餹
	$sql = "UPDATE $tableName SET rank = rank $term, update_date = NOW() WHERE rank BETWEEN ? AND ? AND del_flg = 0";
	if( $position > $rank ) $conn->query( $sql, array( $rank + 1, $position ) );
	if( $position < $rank ) $conn->query( $sql, array( $position, $rank - 1 ) );

	//-- ���ꤷ����̤�rank��񤭴����롣
	$sql  = "UPDATE $tableName SET rank = ?, update_date = NOW() WHERE $keyIdColumn = ? AND del_flg = 0 ";
	$conn->query( $sql, array( $position, $keyId ) );
	$conn->query("COMMIT");
}

//---- ���ϥ��顼�����å��ʽ�̰�ư�ѡ�
function sf_errorCheckPosition($conn, $tableName, $position, $keyIdColumn, $keyId) {

	$objErr = new SC_CheckError();
	$objErr->doFunc( array("��ư���", "moveposition", 4 ), array( "ZERO_CHECK", "NUM_CHECK", "EXIST_CHECK", "MAX_LENGTH_CHECK" ) );

	// ���ȤΥ�󥯤�������롣
	$rank = $conn->getOne("SELECT rank FROM $tableName WHERE $keyIdColumn = ?", array($keyId));
	if ($rank == $position ) $objErr->arrErr["moveposition"] .= "�� ���ꤷ����ư��̤ϸ��ߤν�̤Ǥ���";
	
	// rank�κ����Ͱʾ�����Ϥ���Ƥ��ʤ�											 
	if( ! $objErr->arrErr["position"] ) {								 
		$sql = "SELECT MAX( rank ) FROM " .$tableName. " WHERE del_flg = 0";
		$result = $conn->getOne($sql);
		if( $position > $result ) $objErr->arrErr["moveposition"] .= "�� ���Ϥ��줿��̤ϡ���Ͽ���κ����ͤ�Ķ���Ƥ��ޤ���";
	}

	return $objErr->arrErr;
}

//---- ���ϥ��顼�����å�
function lfErrorCheck(){

	$objErr = new SC_CheckError();

	$objErr->doFunc(array("����(ǯ)", "year"), array("EXIST_CHECK"));
	$objErr->doFunc(array("����(��)", "month"), array("EXIST_CHECK"));
	$objErr->doFunc(array("����(��)", "day"), array("EXIST_CHECK"));
	$objErr->doFunc(array("����", "year", "month", "day"), array("CHECK_DATE"));
	$objErr->doFunc(array("�����ȥ�", 'news_title', MTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��ʸ", 'url', URL_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��ʸ", 'news_comment', LTEXT_LEN), array("MAX_LENGTH_CHECK"));

	return $objErr->arrErr;
}

//INSERTʸ
function lfNewsInsert(){
	global $conn;
	global $registDate;
	
	if ($_POST["link_method"] == "") {
		$_POST["link_method"] = 1;
	}
	
	//rank�κ���+1���������
	$rank_max = $conn->getone("SELECT MAX(rank) + 1 FROM dtb_news WHERE del_flg = '0'");

	$sql = "INSERT INTO dtb_news (news_date, news_title, creator_id, news_url, link_method, news_comment, rank, create_date, update_date)
			VALUES ( ?,?,?,?,?,?,?,now(),now())";
	$arrRegist = array($registDate, $_POST["news_title"], $_SESSION['member_id'],  $_POST["news_url"], $_POST["link_method"], $_POST["news_comment"], $rank_max);

	$conn->query($sql, $arrRegist);
	
	// �ǽ��1���ܤ���Ͽ��rank��NULL������Τ��к�
	$sql = "UPDATE dtb_news SET rank = 1 WHERE del_flg = 0 AND rank IS NULL";
	$conn->query($sql);
}

function lfNewsUpdate(){
	global $conn;
	global $registDate;

	if ($_POST["link_method"] == "") {
		$_POST["link_method"] = 1;
	}	

	$sql = "UPDATE dtb_news SET news_date = ?, news_title = ?, creator_id = ?, update_date = NOW(),  news_url = ?, link_method = ?, news_comment = ? WHERE news_id = ?";
	$arrRegist = array($registDate, $_POST['news_title'], $_SESSION['member_id'], $_POST['news_url'], $_POST["link_method"], $_POST['news_comment'], $_POST['news_id']);
		
	$conn->query($sql, $arrRegist);	
}
?>