<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*
 * �ȥ�å��Хå�����
 * 
 * [1]�ʤ�٤�¿���Υ֥����б��Ǥ���褦�ˡ�GET/POST �˴ؤ�餺��������
 * [2]RSS���׵��GET��__mode�ѥ�᡼����rss�ξ��Τ��б�����(���ʾ�����֤�)
 * [3]ʸ�������ɤϻ��꤬�ʤ����auto���б�����
 * [4]���ѥ�ϡ����ꥸ�ʥ�(����)�Υ��르�ꥺ����б��Ǥ���褦�ˤ��Ƥ���
 */

require_once("../require.php");

$objQuery = new SC_Query();
$objFormParam = new SC_FormParam();

// �ȥ�å��Хå���ǽ�β�Ư���������å�
if (sfGetSiteControlFlg(SITE_CONTROL_TRACKBACK) != 1) {
	// NG
	IfResponseNg();
	exit();
}

// �ѥ�᡼������ν����
lfInitParam();

// ���󥳡�������(�����дĶ��ˤ�ä��ѹ�)
$beforeEncode = "auto";
$afterEncode = mb_internal_encoding();

if (isset($_POST["charset"])) {
	$beforeEncode = $_POST["charset"];
} else if (isset($_GET["charset"])) {
	$beforeEncode = $_GET["charset"];
}

// POST�ǡ����μ����ȥ��󥳡����Ѵ�

// �֥�̾
if (isset($_POST["blog_name"])) {
	$arrData["blog_name"] = trim(mb_convert_encoding($_POST["blog_name"], $afterEncode, $beforeEncode));
} else if (isset($_GET["blog_name"])) {
	$arrData["blog_name"] = trim(mb_convert_encoding($_GET["blog_name"], $afterEncode, $beforeEncode));
}

// �֥�����URL
if (isset($_POST["url"])) {
	$arrData["url"] = trim(mb_convert_encoding($_POST["url"], $afterEncode, $beforeEncode));
} else if (isset($_GET["url"])) {
	$arrData["url"] = trim(mb_convert_encoding($_GET["url"], $afterEncode, $beforeEncode));
} else {
	/*
	 * RSS��Ū�ǤϤʤ�GET�ꥯ�����Ȥ�����(livedoor blog)
	 * _rss�ѥ�᡼���Ǥ�GET�ꥯ�����Ȥ�����(Yahoo blog)
	 */
	if (isset($_GET["__mode"]) && isset($_GET["pid"])) {
		if ($_GET["__mode"] == "rss") {
			IfResponseRss($_GET["pid"]);
		}
	}
	exit();
}

// �֥����������ȥ�
if (isset($_POST["title"])) {
	$arrData["title"] = trim(mb_convert_encoding($_POST["title"], $afterEncode, $beforeEncode));
} else if (isset($_GET["title"])) {
	$arrData["title"] = trim(mb_convert_encoding($_GET["title"], $afterEncode, $beforeEncode));
}

// �֥���������
if (isset($_POST["excerpt"])) {
	$arrData["excerpt"] = trim(mb_convert_encoding($_POST["excerpt"], $afterEncode, $beforeEncode));
} else if (isset($_GET["excerpt"])) {
	$arrData["excerpt"] = trim(mb_convert_encoding($_GET["excerpt"], $afterEncode, $beforeEncode));
}

$log_path = DATA_PATH . "logs/tb_result.log";
gfPrintLog("request data start -----", $log_path);
foreach($arrData as $key => $val) {
	gfPrintLog( "\t" . $key . " => " . $val, $log_path);
}
gfPrintLog("request data end   -----", $log_path);

$objFormParam->setParam($arrData);

// ����ʸ�����Ѵ�
$objFormParam->convParam();
$arrData = $objFormParam->getHashArray();

// ���顼�����å�(�ȥ�å��Хå�������Ω���ʤ��Τǡ�URL�ʳ���ɬ�ܤȤ���)
$objPage->arrErr = lfCheckError();

// ���顼���ʤ����ϥǡ����򹹿�
if(count($objPage->arrErr) == 0) {
	
	// ���ʥ����ɤμ���(GET)
	if (isset($_GET["pid"])) {
		$product_id = $_GET["pid"];

		// ���ʥǡ�����¸�߳�ǧ
		$table = "dtb_products";
		$where = "product_id = ?";

		// ���ʥǡ�����¸�ߤ�����ϥȥ�å��Хå��ǡ����ι���
		if (sfDataExists($table, $where, array($product_id))) {
			$arrData["product_id"] = $product_id;
			
			// �ǡ����ι���
			if (lfEntryTrackBack($arrData) == 1) {
				IfResponseOk();
			}
		} else {
			gfPrintLog("--- PRODUCT NOT EXISTS : " . $product_id, $log_path);
		}
	}
}

// NG
IfResponseNg();
exit();

//----------------------------------------------------------------------------------------------------

/*
 * �ѥ�᡼������ν����
 * 
 * @param void �ʤ�
 * @return void �ʤ�
 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("URL", "url", URL_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�֥������ȥ�", "blog_name", MTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("���������ȥ�", "title", MTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("��������", "excerpt", MLTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}

/*
 * �������ƤΥ����å�
 * 
 * @param void �ʤ�
 * @return $objErr->arrErr ���顼��å�����
 */
function lfCheckError() {
	global $objFormParam;
	
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	return $objErr->arrErr;
}

/*
 * ��������
 * 
 * @param $arrData �ȥ�å��Хå��ǡ���
 * @return $ret ���
 */
function lfEntryTrackBack($arrData) {
	global $objQuery;

	// ��
	$log_path = DATA_PATH . "logs/tb_result.log";

	// ���ѥ�ե��륿��
	if (lfSpamFilter($arrData)) {
		$arrData["status"] = TRACKBACK_STATUS_NOT_VIEW;
	} else {
		$arrData["status"] = TRACKBACK_STATUS_SPAM;
	}

	$arrData["create_date"] = "now()";
	$arrData["update_date"] = "now()";
    
    if(!isset($arrData['url'])){
        $arrData['url'] = '';
    }elseif(!isset($arrData['excerpt'])){
        $arrData['excerpt'] = '';
    }
    if(!isset($arrData['url'])){
        $arrData['url'] = '';
    }elseif(!isset($arrData['excerpt'])){
        $arrData['excerpt'] = '';
    }
    if(!isset($arrData['url'])){
        $arrData['url'] = '';
    }elseif(!isset($arrData['excerpt'])){
        $arrData['excerpt'] = '';
    }
	// �ǡ�������Ͽ
	$table = "dtb_trackback";
	$ret = $objQuery->insert($table, $arrData);
	return $ret;
}

/*
 * ���ѥ�ե��륿��
 * 
 * @param $arrData �ȥ�å��Хå��ǡ���
 * @param $run �ե��륿���ե饰(true:���Ѥ��� false:���Ѥ��ʤ�)
 * @return $ret ���
 */
function lfSpamFilter($arrData, $run = false) {
	$ret = true;
	
	// �ե��륿������
	if ($run) {
	}
	return $ret;
}

/*
 * OK�쥹�ݥ󥹤��֤�
 * 
 * @param void �ʤ�
 * @return void �ʤ�
 */
function IfResponseOk() {
	header("Content-type: text/xml");
	print("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>");
	print("<response>");
	print("<error>0</error>");
	print("</response>");
	exit();
}

/*
 * NG�쥹�ݥ󥹤��֤�
 * 
 * @param void �ʤ�
 * @return void �ʤ�
 */
function IfResponseNg() {
	header("Content-type: text/xml");
	print("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>");
	print("<response>");
	print("<error>1</error>");
	print("<message>The error message</message>");
	print("</response>");
	exit();
}

/*
 * �ȥ�å��Хå�RSS���֤�
 * 
 * @param $product_id ���ʥ�����
 * @return void �ʤ�
 */
function IfResponseRss($product_id) {
	global $objQuery;
	
	$retProduct = $objQuery->select("*", "dtb_products", "product_id = ?", array($product_id));
	
	if (count($retProduct) > 0) {
		header("Content-type: text/xml");
		print("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>");
		print("<response>");
		print("<error>0</error>");
		print("<rss version=\"0.91\">");
		print("<channel>");
		print("<title>" . $retProduct[0]["name"] . "</title>");
		print("<link>");
		print(SITE_URL . "products/detail.php?product_id=" . $product_id);
		print("</link>");
		print("<description>");
		print($retProduct[0]["main_comment"]);
		print("</description>");
		print("<language>ja-jp</language>");
		print("</channel>");
		print("</rss>");
		print("</response>");
		exit();
	}
}

//-----------------------------------------------------------------------------------------------------------------------------------
?>
