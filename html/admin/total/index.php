<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./index_sub.php");
require_once("../batch/daily.php");

require_once("./class/SC_GraphPie.php");
require_once("./class/SC_GraphLine.php");
require_once("./class/SC_GraphBar.php");

// GD�饤�֥��Υ��󥹥ȡ���Ƚ��
$install_GD = (function_exists("gd_info"))?true:false;

class LC_Page {
	var $arrResults;
	var $keyname;
	var $tpl_image;
	var $arrTitle;
	function LC_Page() {
		$this->tpl_mainpage = 'total/index.tpl';
		$this->tpl_subnavi = 'total/subnavi.tpl';
		$this->tpl_graphsubtitle = 'total/subtitle.tpl';
		$this->tpl_titleimage = URL_DIR.'img/title/title_sale.jpg';
		$this->tpl_mainno = 'total';
		global $arrWDAY;
		$this->arrWDAY = $arrWDAY;
		// �ڡ��������ȥ�
		$this->arrTitle[''] = "�����̽���";
		$this->arrTitle['term'] = "�����̽���";
		$this->arrTitle['products'] = "�����̽���";
		$this->arrTitle['age'] = "ǯ���̽���";
		$this->arrTitle['job'] = "�����̽���";
		$this->arrTitle['member'] = "����̽���";
		
		// ����å������Τ�������դ��Ϥ�
		$this->cashtime = time();
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// ���ϴ��֤򥻥å����˵�Ͽ����
lfSaveDateSession();

if($_GET['draw_image'] != ""){
	define(DRAW_IMAGE , true);
}else{
	define(DRAW_IMAGE , false);
}

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
$objFormParam->setParam($_POST);
$objFormParam->setParam($_GET);

// ������ɤΰ����Ѥ�
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		$objPage->arrHidden[$key] = $val;		
	}
}

$mode = $objFormParam->getValue('mode');
switch($mode) {
case 'pdf':
case 'csv':
case 'search':
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	$arrRet = $objFormParam->getHashArray();
	
	// ���ϥ��顼�ʤ�
	if (count($objPage->arrErr) == 0) {
		foreach ($arrRet as $key => $val) {
			if($val == "") {
				continue;
			}
			switch ($key) {
			case 'search_startyear':
				$sdate = $objFormParam->getValue('search_startyear') . "/" . $objFormParam->getValue('search_startmonth') . "/" . $objFormParam->getValue('search_startday');
				break;
			case 'search_endyear':
				$edate = $objFormParam->getValue('search_endyear') . "/" . $objFormParam->getValue('search_endmonth') . "/" . $objFormParam->getValue('search_endday');
				break;
			case 'search_startyear_m':
				list($sdate, $edate) = sfTermMonth($objFormParam->getValue('search_startyear_m'), $objFormParam->getValue('search_startmonth_m'), CLOSE_DAY);
				break;
			default:
				break;
			}
		}

		if($objFormParam->getValue('type') != "") {
			$type = $objFormParam->getValue('type');
		}
				
		$page = $objFormParam->getValue('page');
        
		switch($page) {
		// �����̽���
		case 'products':
			if($type == "") {
				$type = 'all';
			}
			$objPage->tpl_page_type = "total/page_products.tpl";
			// ̤���ץǡ����ν��פ�Ԥ�
			if(!DAILY_BATCH_MODE) {
				lfRealTimeDailyTotal($sdate, $edate);
			}
			// ������̤μ���
			$objPage = lfGetOrderProducts($type, $sdate, $edate, $objPage, $install_GD, $mode);
			break;
		// �����̽���
		case 'job':
			if($type == "") {
				$type = 'all';
			}
			$objPage->tpl_page_type = "total/page_job.tpl";
			// ̤���ץǡ����ν��פ�Ԥ�
			if(!DAILY_BATCH_MODE) {
				lfRealTimeDailyTotal($sdate, $edate);
			}
			// ������̤μ���
			$objPage = lfGetOrderJob($type, $sdate, $edate, $objPage, $install_GD);
			break;
		// ����̽���
		case 'member':
			if($type == "") {
				$type = 'all';
			}
			$objPage->tpl_page_type = "total/page_member.tpl";
			// ̤���ץǡ����ν��פ�Ԥ�
			if(!DAILY_BATCH_MODE) {
				lfRealTimeDailyTotal($sdate, $edate);
			}
			// ������̤μ���
			$objPage = lfGetOrderMember($type, $sdate, $edate, $objPage, $install_GD);
			break;
		// ǯ���̽���
		case 'age':
			if($type == "") {
				$type = 'all';
			}
			
			$objPage->tpl_page_type = "total/page_age.tpl";
			// ̤���ץǡ����ν��פ�Ԥ�
			if(!DAILY_BATCH_MODE) {
				lfRealTimeDailyTotal($sdate, $edate);
			}
			// ������̤μ���
			$objPage = lfGetOrderAge($type, $sdate, $edate, $objPage, $install_GD);
			break;
		// �����̽���
		default:
			if($type == "") {
				$type = 'day';
			}
			$objPage->tpl_page_type = "total/page_term.tpl";
			// ̤���ץǡ����ν��פ�Ԥ�
			if(!DAILY_BATCH_MODE) {
				lfRealTimeDailyTotal($sdate, $edate);
			}
			// ������̤μ���
			$objPage = lfGetOrderTerm($type, $sdate, $edate, $objPage, $install_GD);
			
			break;
		}

		if($mode == 'csv') {
			// CSV���ϥ����ȥ�Ԥμ���
			list($arrTitleCol, $arrDataCol) = lfGetCSVColum($page, $objPage->keyname);
			$head = sfGetCSVList($arrTitleCol);
			$data = lfGetDataColCSV($objPage->arrResults, $arrDataCol);
			// CSV���������롣
			sfCSVDownload($head.$data, $page."_".$type);
			exit;
		}
		
		if($mode == 'pdf') {
			// CSV���ϥ����ȥ�Ԥμ���
			list($arrTitleCol, $arrDataCol, $arrColSize, $arrAlign, $title) = lfGetPDFColum($page, $type, $objPage->keyname);
			$head = sfGetPDFList($arrTitleCol);
			$data = lfGetDataColPDF($objPage->arrResults, $arrDataCol, 40);
			// PDF������
			$graph_name = basename($objPage->tpl_image);
			lfPDFDownload($graph_name, $head . $data, $arrColSize, $arrAlign, $sdate, $edate, $title, $page);
			exit;
		}
	}
	break;
default:
	if(count($_GET) == 0) {
		// �Хå��⡼�ɤξ��Τ߼¹Ԥ���������ν��פ�Ԥ������
		if(DAILY_BATCH_MODE) {
			// 3�����ޤǤν���
			lfStartDailyTotal(3,0);
		}
	}
	break;
}

$objPage->install_GD = $install_GD;

// ��Ͽ��������������
$objDate = new SC_Date();
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrYear = $objDate->getYear();
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();
// �����ͤμ���
$objPage->arrForm = $objFormParam->getFormParamList();

$objPage->tpl_subtitle = $objPage->arrTitle[$objFormParam->getValue('page')];

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------
/* PDF���� */
function lfPDFDownload($image, $table, $arrColSize, $arrAlign, $sdate, $edate, $title, $page = "") {
	
	$objPdf = new SC_Pdf();
	$objPdf->setTableColor("CCCCCC", "F0F0F0", "D1DEFE");
			
	// ����Ȥʤ�PDF�ե�����λ���
	$objPdf->setTemplate(PDF_DIR . "total.pdf");

	$disp_sdate = sfDispDBDate($sdate, false);
	$disp_edate = sfDispDBDate($edate, false);
				
	$arrText['title_block'] = $title;
	$arrText['date_block'] = "$disp_sdate-$disp_edate";
	$arrImage['graph_block'] = GRAPH_DIR . $image;
	
	// ʸ����\n��������
	$table = ereg_replace("\n$", "", $table);
	$arrRet = split("\n", $table);
	$page_max = intval((count($arrRet) / 35) + 1);
	
	for($page = 1; $page <= $page_max; $page++) {
		if($page > 1) {
			// 2�ڡ����ʹ�
			$start_no = 35 * ($page - 1) + 1;
		} else {
			// ���ϥڡ���
			$start_no = 1;			
		}
				
		$arrText['page_block'] = $page . " / " . $page_max;
		$objPdf->setTextBlock($arrText);
		$objPdf->setImageBlock($arrImage);
		// �֥�å��ͤ�����
		$objPdf->writeBlock();
		// �ǽ��ڡ����Τߡ������̽��פϹ�פ��ʤ��ΤǺǽ��Ԥο����ѹ����ʤ���
		if($page == $page_max && $page != 'products') {
			$last_color_flg = true;
		} else {
			$last_color_flg = false;
		}	
		$objPdf->writeTableCenter($table, 500, $arrColSize, $arrAlign, 35, $start_no, $last_color_flg);
		$objPdf->closePage();
	}

	// PDF�ν���
	$objPdf->output();	
}

/* ���å��������ϴ��֤�Ͽ���� */
function lfSaveDateSession() {
	if($_POST['form'] == 1) {
		$_SESSION['total']['startyear_m'] = $_POST['search_startyear_m'];
		$_SESSION['total']['startmonth_m'] = $_POST['search_startmonth_m'];
	}
	
	if($_POST['form'] == 2) {
		$_SESSION['total']['startyear'] = $_POST['search_startyear'];
		$_SESSION['total']['startmonth'] = $_POST['search_startmonth'];
		$_SESSION['total']['startday'] = $_POST['search_startday'];
		$_SESSION['total']['endyear'] = $_POST['search_endyear'];
		$_SESSION['total']['endmonth'] = $_POST['search_endmonth'];
		$_SESSION['total']['endday'] = $_POST['search_endday'];
	}
}

/* �ǥե�����ͤμ��� */
function lfGetDateDefault() {
	$year = date("Y");
	$month = date("m");
	$day = date("d");
	
	$list = $_SESSION['total'];
	
	// ���å�������˳��Ϸ��٤���¸����Ƥ��ʤ���
	if($_SESSION['total']['startyear_m'] == "") {
		$list['startyear_m'] = $year;
		$list['startmonth_m'] = $month;
	}
	
	// ���å�������˳������ա���λ���դ���¸����Ƥ��ʤ���
	if($_SESSION['total']['startyear'] == "" && $_SESSION['total']['endyear'] == "") {
		$list['startyear'] = $year;
		$list['startmonth'] = $month;
		$list['startday'] = $day;
		$list['endyear'] = $year;
		$list['endmonth'] = $month;
		$list['endday'] = $day;
	}
	
	return $list;	
}

/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
		
	// �ǥե�����ͤμ���
	$arrList = lfGetDateDefault();
	
	// ���ٽ���
	$objFormParam->addParam("����", "search_startyear_m", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startyear_m']);
	$objFormParam->addParam("����", "search_startmonth_m", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startmonth_m']);
	// ���ֽ���
	$objFormParam->addParam("������", "search_startyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startyear']);
	$objFormParam->addParam("������", "search_startmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startmonth']);
	$objFormParam->addParam("������", "search_startday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startday']);
	$objFormParam->addParam("��λ��", "search_endyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['endyear']);
	$objFormParam->addParam("��λ��", "search_endmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['endmonth']);
	$objFormParam->addParam("��λ��", "search_endday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['endday']);
	
	// hidden�ǡ����μ�����
	$objFormParam->addParam("", "page");
	$objFormParam->addParam("", "type");
	$objFormParam->addParam("", "mode");

}

/* �������ƤΥ����å� */
function lfCheckError() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	// �ü���ܥ����å�
	if($_POST['form'] == 1) {
		$objErr->doFunc(array("����", "search_startyear_m"), array("ONE_EXIST_CHECK"));
	}
	
	if($_POST['form'] == 2) {
		$objErr->doFunc(array("����", "search_startyear", "search_endyear"), array("ONE_EXIST_CHECK"));
	}
			
	$objErr->doFunc(array("����", "search_startyear_m", "search_startmonth_m"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("������", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
	$objErr->doFunc(array("��λ��", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_DATE"));
	$objErr->doFunc(array("������", "��λ��", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_SET_TERM"));
	return $objErr->arrErr;
}

/* �ޤ�������դκ��� */
function lfGetGraphLine($arrResults, $keyname, $type, $xtitle, $ytitle, $sdate, $edate) {
	
	$ret_path = "";
	
	// ��̤�0�԰ʾ夢����Τߥ���դ��������롣
	if(count($arrResults) > 0) {
		
		// ����դ�����
		$arrList = sfArrKeyValue($arrResults, $keyname, "total");

		// ����ե�����̾�μ���
		$pngname = lfGetGraphPng($type);
		
		$path = GRAPH_DIR . $pngname;
		
		// ��٥�ɽ�����󥿡��Х�����
		$interval = intval(count($arrList) / 20);
		if($interval < 1) {
			$interval = 1;
		}
		$objGraphPie = new SC_GraphPie();
		$objGraphLine = new SC_GraphLine();
		
		// �ͤΥ��å�
		$objGraphLine->setData($arrList);
		$objGraphLine->setXLabel(array_keys($arrList));
		
		// ��٥��ž(���ܸ��Բ�)
		if($keyname == "key_day"){
			$objGraphLine->setXLabelAngle(45);
		}

		// �����ȥ륻�å�
		$objGraphLine->setXTitle($xtitle);
		$objGraphLine->setYTitle($ytitle);
		
		// �ᥤ�󥿥��ȥ����
		list($sy, $sm, $sd) = split("[/ ]" , $sdate);
		list($ey, $em, $ed) = split("[/ ]" , $edate);
		$start_date = $sy . "ǯ" . $sm . "��" . $sd . "��";
		$end_date = $ey . "ǯ" . $em . "��" . $ed . "��";
		$objGraphLine->drawTitle("���״��֡�" . $start_date . " - " . $end_date);
		
		// ���������
		$objGraphLine->drawGraph();
		
		// ����դν���
		if(DRAW_IMAGE){
			$objGraphLine->outputGraph();
			exit();
		}

		// �ե�����ѥ����֤�
		$ret_path = GRAPH_URL . $pngname;
	}
	return $ret_path;
}

// �ߥ���դκ��� 
function lfGetGraphPie($arrResults, $keyname, $type, $title = "", $sdate = "", $edate = "") {
	
	$ret_path = "";
	
	// ��̤�0�԰ʾ夢����Τߥ���դ��������롣
	if(count($arrResults) > 0) {
		// ����դ�����
		$arrList = sfArrKeyValue($arrResults, $keyname, "total", GRAPH_PIE_MAX, GRAPH_LABEL_MAX);
		
		// ����ե�����̾�μ���
		$pngname = lfGetGraphPng($type);
		$path = GRAPH_DIR . $pngname;
		
		$objGraphPie = new SC_GraphPie();
		
		/* �ǥХå�ɽ���� by naka
		foreach($arrList as $key => $val) {
			$objGraphPie->debugPrint("key:$key val:$val");
		}
		*/
		
		// �ǡ����򥻥åȤ���
		$objGraphPie->setData($arrList);
		// ����򥻥åȤ���
		$objGraphPie->setLegend(array_keys($arrList));
								
		// �ᥤ�󥿥��ȥ����
		list($sy, $sm, $sd) = split("[/ ]" , $sdate);
		list($ey, $em, $ed) = split("[/ ]" , $edate);
		$start_date = $sy . "ǯ" . $sm . "��" . $sd . "��";
		$end_date = $ey . "ǯ" . $em . "��" . $ed . "��";
		$objGraphPie->drawTitle("���״��֡�" . $start_date . " - " . $end_date);
				
		// �ߥ��������
		$objGraphPie->drawGraph();
		
		// ����դν���
		if(DRAW_IMAGE){
			$objGraphPie->outputGraph();
			exit();
		}

		// �ե�����ѥ����֤�
		$ret_path = GRAPH_URL . $pngname;
	}
	return $ret_path;
}

// ������դκ��� 
function lfGetGraphBar($arrResults, $keyname, $type, $xtitle, $ytitle, $sdate, $edate) {
	$ret_path = "";
	
	// ��̤�0�԰ʾ夢����Τߥ���դ��������롣
	if(count($arrResults) > 0) {
		// ����դ�����
		$arrList = sfArrKeyValue($arrResults, $keyname, "total", GRAPH_PIE_MAX, GRAPH_LABEL_MAX);
		
		// ����ե�����̾�μ���
		$pngname = lfGetGraphPng($type);
		$path = GRAPH_DIR . $pngname;
		
		$objGraphBar = new SC_GraphBar();
		
		foreach(array_keys($arrList) as $val) {
			$arrKey[] = ereg_replace("��", "-", $val);
		}
		
		// ���������
		$objGraphBar->setXLabel($arrKey);
		$objGraphBar->setXTitle($xtitle);
		$objGraphBar->setYTitle($ytitle);
		$objGraphBar->setData($arrList);
		
		// �ᥤ�󥿥��ȥ����
		$arrKey = array_keys($arrList);
		list($sy, $sm, $sd) = split("[/ ]" , $sdate);
		list($ey, $em, $ed) = split("[/ ]" , $edate);
		$start_date = $sy . "ǯ" . $sm . "��" . $sd . "��";
		$end_date = $ey . "ǯ" . $em . "��" . $ed . "��";
		$objGraphBar->drawTitle("���״��֡�" . $start_date . " - " . $end_date);
		
		$objGraphBar->drawGraph();
		
		if(DRAW_IMAGE){
			$objGraphBar->outputGraph();
			exit();
		}
		
		// �ե�����ѥ����֤�
		$ret_path = GRAPH_URL . $pngname;
	}
	return $ret_path;
}

// ������Ѥ�PNG�ե�����̾ 
function lfGetGraphPng($keyname) {
	if($_POST['search_startyear_m'] != "") {
		$pngname = sprintf("%s_%02d%02d.png", $keyname, substr($_POST['search_startyear_m'],2), $_POST['search_startmonth_m']);
	} else {
		$pngname = sprintf("%s_%02d%02d%02d_%02d%02d%02d.png", $keyname, substr($_POST['search_startyear'], 2), $_POST['search_startmonth'], $_POST['search_startday'], substr($_POST['search_endyear'],2), $_POST['search_endmonth'], $_POST['search_endday']);
	}
	return $pngname;
}

// ������������פ�WHEREʬ�κ���
function lfGetWhereMember($col_date, $sdate, $edate, $type, $col_member = "customer_id") {
	// �������դλ���
	if($sdate != "") {
		if ($where != "") {
			$where.= " AND ";
		}			
		$where.= " $col_date >= '". $sdate ."'";
	}
		
	if($edate != "") {
		if ($where != "") {
			$where.= " AND ";
		}
		$edate = date("Y/m/d",strtotime("1 day" ,strtotime($edate)));	
		$where.= " $col_date < date('" . $edate ."')";
	}
	
	// �����������Ƚ��
	switch($type) {
	// ����
	case 'all':
		break;
	case 'member':
		if ($where != "") {
			$where.= " AND ";
		}
		$where.= " $col_member <> 0";
		break;
	case 'nonmember':
		if ($where != "") {
			$where.= " AND ";
		}
		$where.= " $col_member = 0";
		break;
	default:
		break;
	}
	
	return array($where, $arrval);
}

/** ����̽��� **/
function lfGetOrderMember($type, $sdate, $edate, $objPage, $graph = true) {
	global $arrSex;
		
	list($where, $arrval) = lfGetWhereMember('create_date', $sdate, $edate, $type);
	
	// ������פμ���
	$col = "COUNT(*) AS order_count, SUM(total) AS total, (AVG(total)) AS total_average, order_sex";
	$from = "dtb_order";
	$objQuery = new SC_Query();
	$objQuery->setGroupBy("order_sex");
	
	$tmp_where = $where . " AND customer_id <> 0 AND del_flg = 0 ";
	$arrRet = $objQuery->select($col, $from, $tmp_where, $arrval);
	
	// ��������Ǥ��뤳�Ȥ�Ͽ���롣
	$max = count($arrRet);
	for($i = 0; $i < $max; $i++) {
		$arrRet[$i]['member_name'] = '���'.$arrSex[$arrRet[$i]['order_sex']];
	}
	$objPage->arrResults = $arrRet;
	
	// �������פμ���
	$tmp_where = $where . " AND customer_id = 0 AND del_flg = 0 ";
	$arrRet = $objQuery->select($col, $from, $tmp_where, $arrval);
	// ���������Ǥ��뤳�Ȥ�Ͽ���롣
	$max = count($arrRet);
	for($i = 0; $i < $max; $i++) {
		$arrRet[$i]['member_name'] = '����'.$arrSex[$arrRet[$i]['order_sex']];
	}
	
	$objPage->arrResults = array_merge($objPage->arrResults, $arrRet);
	
	// �ߥ���դ�����
	if($graph) {	
		$image_key = "member";
		$objPage->tpl_image = lfGetGraphPie($objPage->arrResults, "member_name", $image_key, "(�����Ψ)", $sdate, $edate);
	}
	
	return $objPage;
}

/** �����̽��� **/
function lfGetOrderProducts($type, $sdate, $edate, $objPage, $graph = true, $mode = "") {
	list($where, $arrval) = lfGetWhereMember('create_date', $sdate, $edate, $type);
    
    $where .= " and del_flg=0 and status <> " . ORDER_CANCEL;
	
	$sql = "SELECT T1.product_id, T1.product_code, T1.product_name as name, T1.products_count, T1.order_count, T1.price, T1.total ";
	$sql.= "FROM ( ";
	$sql.= "SELECT product_id, product_name, product_code, price, ";
	$sql.= "COUNT(*) AS order_count, ";
	$sql.= "SUM(quantity) AS products_count, ";
	$sql.= "(price * sum(quantity)) AS total ";
	$sql.= "FROM dtb_order_detail WHERE order_id IN (SELECT order_id FROM dtb_order WHERE $where ) ";
	$sql.= "GROUP BY product_id, product_name, product_code, price ";
	$sql.= ") AS T1 ";
	$sql.= "ORDER BY T1.total DESC ";
	
	if($mode != "csv") {
		$sql.= "LIMIT " . PRODUCTS_TOTAL_MAX;
	}
	
	$objQuery = new SC_Query();
	$objPage->arrResults = $objQuery->getall($sql, $arrval);
	
	// �ߥ���դ�����
	if($graph) {
		$image_key = "products_" . $type;
		$objPage->tpl_image = lfGetGraphPie($objPage->arrResults, "name", $image_key, "(�����Ψ)", $sdate, $edate);
	}
	
	return $objPage;
}

/** �����̽��� **/
function lfGetOrderJob($type, $sdate, $edate, $objPage, $graph = true) {
	global $arrJob;	
		
	list($where, $arrval) = lfGetWhereMember('T2.create_date', $sdate, $edate, $type);
	
	$sql = "SELECT job, count(*) AS order_count, SUM(total) AS total, (AVG(total)) AS total_average ";
	$sql.= "FROM dtb_customer AS T1 LEFT JOIN dtb_order AS T2 USING ( customer_id ) WHERE $where AND T2.del_flg = 0 and T2.status <> " . ORDER_CANCEL;
	$sql.= " GROUP BY job ORDER BY total DESC";
	
	$objQuery = new SC_Query();
	$objPage->arrResults = $objQuery->getall($sql, $arrval);
    
	$max = count($objPage->arrResults);
	for($i = 0; $i < $max; $i++) {
		$job_key = $objPage->arrResults[$i]['job'];
		if($job_key != "") {
			$objPage->arrResults[$i]['job_name'] = $arrJob[$job_key];
		} else {
			$objPage->arrResults[$i]['job_name'] = "̤����";
		}
	}

	// �ߥ���դ�����	
	if($graph) {
		$image_key = "job_" . $type;
		$objPage->tpl_image = lfGetGraphPie($objPage->arrResults, "job_name", $image_key, "(�����Ψ)", $sdate, $edate);
	}
	
	return $objPage;
}

/** ǯ���̽��� **/
function lfGetOrderAge($type, $sdate, $edate, $objPage, $graph = true) {

	list($where, $arrval) = lfGetWhereMember('order_date', $sdate, $edate, $type, "member");
	
	$sql = "SELECT SUM(order_count) AS order_count, SUM(total) AS total, start_age, end_age ";
	$sql.= "FROM dtb_bat_order_daily_age WHERE $where ";
	$sql.= "GROUP BY start_age, end_age ORDER BY start_age, end_age";

	$objQuery = new SC_Query();
	$objPage->arrResults = $objQuery->getall($sql, $arrval);
	
	$max = count($objPage->arrResults);
	for($i = 0; $i < $max; $i++) {
		if($objPage->arrResults[$i]['order_count'] > 0) {
			$objPage->arrResults[$i]['total_average'] = intval($objPage->arrResults[$i]['total'] / $objPage->arrResults[$i]['order_count']);
		}	
		$start_age = $objPage->arrResults[$i]['start_age'];
		$end_age = $objPage->arrResults[$i]['end_age'];
		if($start_age != "" || $end_age != "") {
			if($end_age != 999) {
				$objPage->arrResults[$i]['age_name'] = $start_age . "��" . $end_age . "��";
			} else {
				$objPage->arrResults[$i]['age_name'] = $start_age . "�С�";
			}
		} else {
			$objPage->arrResults[$i]['age_name'] = "̤����";
		}
	}
	
	// ������դ�����
	if($graph) {
		$image_key = "age_" . $type;
		$xtitle = "(ǯ��)";
		$ytitle = "(�����)";
		$objPage->tpl_image = lfGetGraphBar($objPage->arrResults, "age_name", $image_key, $xtitle, $ytitle, $sdate, $edate);
	}
	
	return $objPage;
}

/** �����̽��� **/
function lfGetOrderTerm($type, $sdate, $edate, $objPage, $graph = true) {
		
		$tmp_col = "sum(total_order) as total_order, sum(men) as men, sum(women) as women,";
		$tmp_col.= "sum(men_member) as men_member, sum(men_nonmember) as men_nonmember,";
		$tmp_col.= "sum(women_member) as women_member, sum(women_nonmember) as women_nonmember,";
		$tmp_col.= "sum(total) as total, (avg(total_average)) as total_average";
		$objQuery = new SC_Query();
		
		switch($type) {
		// ����
		case 'month':
			$col = $tmp_col . ",key_month";
			$objQuery->setgroupby("key_month");
			$objQuery->setOrder("key_month");
			$objPage->keyname = "key_month";
			$objPage->tpl_tail = "��";
			$from = "dtb_bat_order_daily";
			$xtitle = "(����)";
			$ytitle = "(�����)";
			break;
		// ǯ��
		case 'year':
			$col = $tmp_col . ",key_year";
			$objQuery->setgroupby("key_year");
			$objQuery->setOrder("key_year");
			$objPage->keyname = "key_year";
			$objPage->tpl_tail = "ǯ";
			$from = "dtb_bat_order_daily";
			$xtitle = "(ǯ��)";
			$ytitle = "(�����)";
			break;
		// ������
		case 'wday':
			$col = $tmp_col . ",key_wday, wday";
			$objQuery->setgroupby("key_wday, wday");
			$objQuery->setOrder("wday");
			$objPage->keyname = "key_wday";
			$objPage->tpl_tail = "����";
			$from = "dtb_bat_order_daily";
			$xtitle = "(������)";
			$ytitle = "(�����)";
			break;
		// ������
		case 'hour':
			$col = $tmp_col . ",hour";
			$objQuery->setgroupby("hour");
			$objQuery->setOrder("hour");
			$objPage->keyname = "hour";
			$objPage->tpl_tail = "��";
			$from = "dtb_bat_order_daily_hour";
			$xtitle = "(������)";
			$ytitle = "(�����)";
			break;
		default:
			$col = "*";
			$objQuery->setOrder("key_day");
			$objPage->keyname = "key_day";
			$from = "dtb_bat_order_daily";
			$xtitle = "(����)";
			$ytitle = "(�����)";
			break;
		}
		

	// �������դλ���
		if($sdate != "") {
			if ($where != "") {
				$where.= " AND ";
			}			
			$where.= " order_date >= '". $sdate ."'";
		}
		
		if($edate != "") {
			if ($where != "") {
				$where.= " AND ";
			}
			$edate_next = date("Y/m/d",strtotime("1 day" ,strtotime($edate)));
			$where.= " order_date < date('" . $edate_next ."')";
		}
		
		// ������̤μ���
		$objPage->arrResults = $objQuery->select($col, $from, $where, $arrval);
        
		// �ޤ�������դ�����	
		if($graph) {
			$image_key = "term_" . $type;
			$objPage->tpl_image = lfGetGraphLine($objPage->arrResults, $objPage->keyname, $image_key, $xtitle, $ytitle, $sdate, $edate);
		}
		
		// ������̤�0�Ǥʤ����
		if(count($objPage->arrResults) > 0) {
			// �ǽ����׹Լ�������
			$col = $tmp_col;
			$objQuery = new SC_Query();
			$arrRet = $objQuery->select($col, $from, $where, $arrval);
			$arrRet[0][$objPage->keyname] = "���";
			$objPage->arrResults[] = $arrRet[0];
		}

		// ʿ���ͤη׻�
		$max = count($objPage->arrResults);
		for($i = 0; $i < $max; $i++) {
			if($objPage->arrResults[$i]['total_order'] > 0) {
				$objPage->arrResults[$i]['total_average'] = intval($objPage->arrResults[$i]['total'] / $objPage->arrResults[$i]['total_order']);
			}
		}
		
		return $objPage;
}

?>
