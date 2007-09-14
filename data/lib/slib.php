<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

//---���Υե�����Υѥ������
$INC_PATH = realpath( dirname( __FILE__) );
require_once( $INC_PATH ."/../conf/conf.php" );
require_once( $INC_PATH ."/../class/SC_DbConn.php" );
require_once( $INC_PATH ."/../class/SC_Query.php" );
require_once( $INC_PATH ."/../class/SC_CampaignSession.php" );
require_once( $INC_PATH ."/../include/session.inc" );

// ���ڡ������̥��顼
$GLOBAL_ERR = "";
// ���󥹥ȡ���������
sfInitInstall();

/* �ǡ����١����ΥС��������� */
function sfGetDBVersion($dsn = "") {
	if($dsn == "") {
		if(defined('DEFAULT_DSN')) {
			$dsn = DEFAULT_DSN;
		} else {
			return;
		}
	}
	
	$objQuery = new SC_Query($dsn, true, true);
	list($db_type) = split(":", $dsn);
	if($db_type == 'mysql') {
		$val = $objQuery->getOne("select version()");
		$version = "MySQL " . $val;
	}	
	if($db_type == 'pgsql') {
		$val = $objQuery->getOne("select version()");
		$arrLine = split(" " , $val);
		$version = $arrLine[0] . " " . $arrLine[1];
	}
	return $version;
}

/* �ơ��֥��¸�ߥ����å� */
function sfTabaleExists($table_name, $dsn = "") {
	if($dsn == "") {
		if(defined('DEFAULT_DSN')) {
			$dsn = DEFAULT_DSN;
		} else {
			return;
		}
	}
	
	$objQuery = new SC_Query($dsn, true, true);
	// �������³����Ƥ�����
	if(!$objQuery->isError()) {
		list($db_type) = split(":", $dsn);
		// postgresql��mysql�Ȥǽ�����ʬ����
		if ($db_type == "pgsql") {
			$sql = "SELECT
						relname
					FROM
					    pg_class
					WHERE
						(relkind = 'r' OR relkind = 'v') AND 
					    relname = ? 
					GROUP BY
						relname";
			$arrRet = $objQuery->getAll($sql, array($table_name));
			if(count($arrRet) > 0) {
				return true;
			}
		}else if ($db_type == "mysql") {
			$sql = "SHOW TABLE STATUS LIKE ?";
			$arrRet = $objQuery->getAll($sql, array($table_name));
			if(count($arrRet) > 0) {
				return true;
			}
		}
	}
	return false;
}

// ������¸�ߥ����å��Ⱥ���
function sfColumnExists($table_name, $col_name, $col_type = "", $dsn = "", $add = false) {
	if($dsn == "") {
		if(defined('DEFAULT_DSN')) {
			$dsn = DEFAULT_DSN;
		} else {
			return;
		}
	}

	// �ơ��֥뤬̵����Х��顼
	if(!sfTabaleExists($table_name, $dsn)) return false;
	
	$objQuery = new SC_Query($dsn, true, true);
	// �������³����Ƥ�����
	if(!$objQuery->isError()) {
		list($db_type) = split(":", $dsn);
		
		// �����ꥹ�Ȥ����
		$arrRet = sfGetColumnList($table_name, $objQuery, $db_type);
		if(count($arrRet) > 0) {
			if(in_array($col_name, $arrRet)){
				return true;
			}
		}
	}
	
	// �������ɲä���
	if($add){
		$objQuery->query("ALTER TABLE $table_name ADD $col_name $col_type ");
		return true;
	}
	
	return false;
}

// ����ǥå�����¸�ߥ����å��Ⱥ���
function sfIndexExists($table_name, $col_name, $index_name, $length = "", $dsn = "", $add = false) {
	if($dsn == "") {
		if(defined('DEFAULT_DSN')) {
			$dsn = DEFAULT_DSN;
		} else {
			return;
		}
	}

	// �ơ��֥뤬̵����Х��顼
	if(!sfTabaleExists($table_name, $dsn)) return false;
	
	$objQuery = new SC_Query($dsn, true, true);
	// �������³����Ƥ�����
	if(!$objQuery->isError()) {
		list($db_type) = split(":", $dsn);		
		switch($db_type) {
		case 'pgsql':
			// ����ǥå�����¸�߳�ǧ
			$arrRet = $objQuery->getAll("SELECT relname FROM pg_class WHERE relname = ?", array($index_name));
			break;
		case 'mysql':
			// ����ǥå�����¸�߳�ǧ
			$arrRet = $objQuery->getAll("SHOW INDEX FROM ? WHERE Key_name = ?", array($table_name, $index_name));			
			break;
		default:
			return false;
		}
		// ���Ǥ˥���ǥå�����¸�ߤ�����
		if(count($arrRet) > 0) {
			return true;
		}
	}
	
	// ����ǥå������������
	if($add){
		switch($db_type) {
		case 'pgsql':
			$objQuery->query("CREATE INDEX ? ON ? (?)", array($index_name, $table_name, $col_name));
			break;
		case 'mysql':
			$objQuery->query("CREATE INDEX ? ON ? (?(?))", array($index_name, $table_name, $col_name, $length));			
			break;
		default:
			return false;
		}
		return true;
	}	
	return false;
}

// �ǡ�����¸�ߥ����å�
function sfDataExists($table_name, $where, $arrval, $dsn = "", $sql = "", $add = false) {
	if($dsn == "") {
		if(defined('DEFAULT_DSN')) {
			$dsn = DEFAULT_DSN;
		} else {
			return;
		}
	}
	$objQuery = new SC_Query($dsn, true, true);
	$count = $objQuery->count($table_name, $where, $arrval);

	if($count > 0) {
		$ret = true;
	} else {
		$ret = false;
	}
	// �ǡ������ɲä���
	if(!$ret && $add) {
		$objQuery->exec($sql);
	}
	
	return $ret;
}

/*
 * �����ȴ������󤫤��ͤ�������롣
 * �ǡ�����¸�ߤ����硢ɬ��1�ʾ�ο��ͤ����ꤵ��Ƥ��롣
 * 0���֤������ϡ��ƤӽФ������б����뤳�ȡ�
 * 
 * @param $control_id ����ID
 * @param $dsn DataSource
 * @return $control_flg �ե饰
 */
function sfGetSiteControlFlg($control_id, $dsn = "") {

	// �ǡ���������
	if($dsn == "") {
		if(defined('DEFAULT_DSN')) {
			$dsn = DEFAULT_DSN;
		} else {
			return;
		}
	}

	// ����������
	$target_column = "control_flg";
	$table_name = "dtb_site_control";
	$where = "control_id = ?";
	$arrval = array($control_id);
	$control_flg = 0;

	// ������ȯ��
	$objQuery = new SC_Query($dsn, true, true);
	$arrSiteControl = $objQuery->select($target_column, $table_name, $where, $arrval);

	// �ǡ�����¸�ߤ���Хե饰���������
	if (count($arrSiteControl) > 0) {
		$control_flg = $arrSiteControl[0]["control_flg"];
	}
	
	return $control_flg;
}

// �ơ��֥�Υ����������������
function sfGetColumnList($table_name, $objQuery = "", $db_type = DB_TYPE){
	if($objQuery == "") $objQuery = new SC_Query();
	$arrRet = array();
	
	// postgresql��mysql�Ȥǽ�����ʬ����
	if ($db_type == "pgsql") {
		$sql = "SELECT a.attname FROM pg_class c, pg_attribute a WHERE c.relname=? AND c.oid=a.attrelid AND a.attnum > 0 AND not a.attname like '........pg.dropped.%........' ORDER BY a.attnum";
		$arrColList = $objQuery->getAll($sql, array($table_name));
		$arrColList = sfswaparray($arrColList);
		$arrRet = $arrColList["attname"];
	}else if ($db_type == "mysql") {
		$sql = "SHOW COLUMNS FROM $table_name";
		$arrColList = $objQuery->getAll($sql);
		$arrColList = sfswaparray($arrColList);
		$arrRet = $arrColList["Field"];
	}
	return $arrRet;
}

// ���󥹥ȡ���������
function sfInitInstall() {
	// ���󥹥ȡ���Ѥߤ��������Ƥ��ʤ���
	if(!defined('ECCUBE_INSTALL')) {
		if(!ereg("/install/", $_SERVER['PHP_SELF'])) {
			header("Location: ./install/");
		}
	} else {
		$path = HTML_PATH . "install/index.php";
		if(file_exists($path)) {
			sfErrorHeader(">> /install/index.php�ϡ����󥹥ȡ��봰λ��˥ե�����������Ƥ���������");
		}
		
		// ��С�������install.php�Υ����å�
		$path = HTML_PATH . "install.php";
		if(file_exists($path)) {
			sfErrorHeader(">> /install.php�ϥ������ƥ����ۡ���Ȥʤ�ޤ���������Ƥ���������");
		}		
	}
}

// ���åץǡ��Ȥ��������줿PHP���ɤ߽Ф�
function sfLoadUpdateModule() {
	// URL����ǥ��쥯�ȥ����
	$main_php = ereg_replace(URL_DIR, "", $_SERVER['PHP_SELF']);
	$extern_php = UPDATE_PATH . $main_php;
	if(file_exists($extern_php)) {
		require_once($extern_php);
	}
}

function sf_getBasisData() {
	//DB���������������
	$objConn = new SC_DbConn(DEFAULT_DSN);
	$result = $objConn->getAll("SELECT * FROM dtb_baseinfo");
	if(is_array($result[0])) {
		foreach ( $result[0] as $key=>$value ){
			$CONF["$key"] = $value;
		}
	}
	return $CONF;
}

// �����դ����顼��å�������ɽ��
function sfErrorHeader($mess, $print = false) {
	global $GLOBAL_ERR;
	if($GLOBAL_ERR == "") {
		$GLOBAL_ERR = "<meta http-equiv='Content-Type' content='text/html; charset=" . CHAR_CODE . "'>\n";
	}
	$GLOBAL_ERR.= "<table width='100%' border='0' cellspacing='0' cellpadding='0' summary=' '>\n";
	$GLOBAL_ERR.= "<tr>\n";
	$GLOBAL_ERR.= "<td bgcolor='#ffeebb' height='25' colspan='2' align='center'>\n";
	$GLOBAL_ERR.= "<SPAN style='color:red; font-size:12px'><strong>" . $mess . "</strong></span>\n";
	$GLOBAL_ERR.= "</td>\n";
	$GLOBAL_ERR.= "	</tr>\n";
	$GLOBAL_ERR.= "</table>\n";
	
	if($print) {
		print($GLOBAL_ERR);
	}
}

/* ���顼�ڡ�����ɽ�� */
function sfDispError($type) {
	
	class LC_ErrorPage {
		function LC_ErrorPage() {
			$this->tpl_mainpage = 'login_error.tpl';
			$this->tpl_title = '���顼';
		}
	}

	$objPage = new LC_ErrorPage();
	$objView = new SC_AdminView();
	
	switch ($type) {
	    case LOGIN_ERROR:
			$objPage->tpl_error="�ɣĤޤ��ϥѥ���ɤ�����������ޤ���<br />�⤦���٤���ǧ�Τ������������Ϥ��Ƥ���������";
	    	break;
		case ACCESS_ERROR:
			$objPage->tpl_error="������ǧ�ڤ�ͭ�������ڤ�β�ǽ��������ޤ���<br />�⤦���٤���ǧ�Τ��������٥����󤷤Ƥ���������";
			break;
		case AUTH_ERROR:
			$objPage->tpl_error="���Υե�����ˤϥ����������¤�����ޤ���<br />�⤦���٤���ǧ�Τ��������٥����󤷤Ƥ���������";
			break;
		case INVALID_MOVE_ERRORR:
			$objPage->tpl_error="�����ʥڡ�����ư�Ǥ���<br />�⤦���٤���ǧ�Τ������������Ϥ��Ƥ���������";
			break;
		default:
	    	$objPage->tpl_error="���顼��ȯ�����ޤ�����<br />�⤦���٤���ǧ�Τ��������٥����󤷤Ƥ���������";
			break;
	}
	
	$objView->assignobj($objPage);
	$objView->display(LOGIN_FRAME);
	
	exit;
}

/* �����ȥ��顼�ڡ�����ɽ�� */
function sfDispSiteError($type, $objSiteSess = "", $return_top = false, $err_msg = "", $is_mobile = false) {
	global $objCampaignSess;
	
	if ($objSiteSess != "") {
		$objSiteSess->setNowPage('error');
	}
	
	class LC_ErrorPage {
		function LC_ErrorPage() {
			$this->tpl_mainpage = 'error.tpl';
			$this->tpl_css = URL_DIR.'css/layout/error.css';
			$this->tpl_title = '���顼';
		}
	}
	
	$objPage = new LC_ErrorPage();
	
	if($is_mobile === true) {
		$objView = new SC_MobileView();		
	} else {
		$objView = new SC_SiteView();
	}
	
	switch ($type) {
	    case PRODUCT_NOT_FOUND:
			$objPage->tpl_error="������Υڡ����Ϥ������ޤ���";
			break;
		case PAGE_ERROR:
			$objPage->tpl_error="�����ʥڡ�����ư�Ǥ���";
			break;
		case CART_EMPTY:
			$objPage->tpl_error="�����Ȥ˾��ʤ�������ޤ���";
			break;
	    case CART_ADD_ERROR:
			$objPage->tpl_error="����������ϡ������Ȥ˾��ʤ��ɲä��뤳�ȤϤǤ��ޤ���";
			break;
		case CANCEL_PURCHASE:
			$objPage->tpl_error="���μ�³����̵���Ȥʤ�ޤ������ʲ����װ����ͤ����ޤ���<br />�����å��������ͭ�����¤��ڤ�Ƥ���<br />��������³����˿�����������³����¹Ԥ������<br />�����Ǥ˹�����³����λ���Ƥ�����";
			break;
		case CATEGORY_NOT_FOUND:
			$objPage->tpl_error="������Υ��ƥ����¸�ߤ��ޤ���";
			break;
		case SITE_LOGIN_ERROR:
			$objPage->tpl_error="�᡼�륢�ɥ쥹�⤷���ϥѥ���ɤ�����������ޤ���";
			break;
		case TEMP_LOGIN_ERROR:
			$objPage->tpl_error="�᡼�륢�ɥ쥹�⤷���ϥѥ���ɤ�����������ޤ���<br />����Ͽ�����ѤߤǤʤ����ϡ�����Ͽ�᡼��˵��ܤ���Ƥ���<br />URL�������Ͽ��ԤäƤ���������";
			break;
		case CUSTOMER_ERROR:
			$objPage->tpl_error="�����ʥ��������Ǥ���";
			break;
		case SOLD_OUT:
			$objPage->tpl_error="�������������ޤ��󤬡���������ľ��������ڤ줿���ʤ�����ޤ������μ�³����̵���Ȥʤ�ޤ�����";
			break;
		case CART_NOT_FOUND:
			$objPage->tpl_error="�������������ޤ��󤬡���������ξ��ʾ���μ����˼��Ԥ��ޤ��������μ�³����̵���Ȥʤ�ޤ�����";
			break;
		case LACK_POINT:
			$objPage->tpl_error="�������������ޤ��󤬡��ݥ���Ȥ���­���Ƥ���ޤ������μ�³����̵���Ȥʤ�ޤ�����";
			break;
		case FAVORITE_ERROR:
			$objPage->tpl_error="���ˤ�����������ɲä���Ƥ��뾦�ʤǤ���";
			break;
		case EXTRACT_ERROR:
			$objPage->tpl_error="�ե�����β���˼��Ԥ��ޤ�����\n����Υǥ��쥯�ȥ�˽񤭹��߸��¤�Ϳ�����Ƥ��ʤ���ǽ��������ޤ���";
			break;
		case FTP_DOWNLOAD_ERROR:
			$objPage->tpl_error="�ե������FTP��������ɤ˼��Ԥ��ޤ�����";
			break;
		case FTP_LOGIN_ERROR:
			$objPage->tpl_error="FTP������˼��Ԥ��ޤ�����";
			break;
		case FTP_CONNECT_ERROR:
			$objPage->tpl_error="FTP������˼��Ԥ��ޤ�����";
			break;
		case CREATE_DB_ERROR:
			$objPage->tpl_error="DB�κ����˼��Ԥ��ޤ�����\n����Υ桼�����ˤϡ�DB�����θ��¤�Ϳ�����Ƥ��ʤ���ǽ��������ޤ���";
			break;
		case DB_IMPORT_ERROR:
			$objPage->tpl_error="�ǡ����١�����¤�Υ���ݡ��Ȥ˼��Ԥ��ޤ�����\nsql�ե����뤬����Ƥ����ǽ��������ޤ���";
			break;
		case FILE_NOT_FOUND:
			$objPage->tpl_error="����Υѥ��ˡ�����ե����뤬¸�ߤ��ޤ���";
			break;
		case WRITE_FILE_ERROR:
			$objPage->tpl_error="����ե�����˽񤭹���ޤ���\n����ե�����˽񤭹��߸��¤�Ϳ���Ƥ���������";
			break;
		case FREE_ERROR_MSG:
			$objPage->tpl_error=$err_msg;
			break;
 		default:
	    	$objPage->tpl_error="���顼��ȯ�����ޤ�����";
			break;
	}
	
	$objPage->return_top = $return_top;
	
	$objView->assignobj($objPage);
	
	if(is_object($objCampaignSess)) {
		// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
		$objCampaignSess->pageView($objView);
	} else {
		$objView->display(SITE_FRAME);
	}	
	exit;
}

/* ǧ�ڤβ���Ƚ�� */
function sfIsSuccess($objSess, $disp_error = true) {
	$ret = $objSess->IsSuccess();
	if($ret != SUCCESS) {
		if($disp_error) {
			// ���顼�ڡ�����ɽ��
			sfDispError($ret);
		}
		return false;
	}
    // ��ե��顼�����å�(CSRF�λ���Ū���к�)
    // �֥�ե���̵�� �ξ��ϥ��롼
    // �֥�ե���ͭ�� ���� �ִ������̤�������ܤǤʤ��� ���˥��顼���̤�ɽ������
    if ( empty($_SERVER['HTTP_REFERER']) ) {
        // �ٹ�ɽ�������롩
        // sfErrorHeader('>> referrer��̵���ˤʤäƤ��ޤ���');
    } else {
        $domain  = sfIsHTTPS() ? SSL_URL : SITE_URL;
        $pattern = sprintf('|^%s.*|', $domain);
        $referer = $_SERVER['HTTP_REFERER'];

        // �������̤���ʳ������ܤξ��ϥ��顼���̤�ɽ��
        if (!preg_match($pattern, $referer)) {
            if ($disp_error) sfDispError(INVALID_MOVE_ERRORR);
            return false;
        }
    }
    return true;
}

/**
 * HTTPS���ɤ�����Ƚ��
 * 
 * @return bool
 */
function sfIsHTTPS () {
    // HTTPS���ˤ�$_SERVER['HTTPS']�ˤ϶��Ǥʤ��ͤ�����
    // $_SERVER['HTTPS'] != 'off' ��IIS��
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
        return true;
    } else {
        return false;
    }
}

/**
 *  ���������ܤ�����Ƥ��뤫��Ƚ��
 *  �����̤�uniqid��������Ǥ���ɬ�פ�����
 *  @param  obj  SC_Session, SC_SiteSession
 *  @return bool
 */
function sfIsValidTransition($objSess) {
    // �����̤���POST�����uniqid����������Τ��ɤ���������å�
    $uniqid = $objSess->getUniqId();
    if ( !empty($_POST['uniqid']) && ($_POST['uniqid'] === $uniqid) ) {
        return true;
    } else {
        return false;
    }
}

/* ���Υڡ�������������Ͽ���Ԥ�줿��Ƚ�� */
function sfIsPrePage($objSiteSess, $is_mobile = false) {
	$ret = $objSiteSess->isPrePage();
	if($ret != true) {
		// ���顼�ڡ�����ɽ��
		sfDispSiteError(PAGE_ERROR, $objSiteSess, false, "", $is_mobile);			
	}
}

function sfCheckNormalAccess($objSiteSess, $objCartSess) {
	// �桼����ˡ���ID�μ���
	$uniqid = $objSiteSess->getUniqId();
	// �����ܥ���򲡤������Υ��������Ƥ����ԡ�����Ƥ��ʤ����Τߥ��ԡ����롣
	$objCartSess->saveCurrentCart($uniqid);
	// POST�Υ�ˡ���ID�ȥ��å����Υ�ˡ���ID�����(��ˡ���ID��POST����Ƥ��ʤ����ϥ��롼)
	$ret = $objSiteSess->checkUniqId();
	
	if($ret != true) {
		// ���顼�ڡ�����ɽ��
		sfDispSiteError(CANCEL_PURCHASE, $objSiteSess);
	}
	
	// �������⤬���Ǥʤ��� || �����ܥ���򲡤��Ƥ����Ѳ����ʤ���
	$quantity = $objCartSess->getTotalQuantity();
	$ret = $objCartSess->checkChangeCart();
	if($ret == true || !($quantity > 0)) {
		// �����Ⱦ���ɽ���˶�����ư����
		header("Location: ".URL_CART_TOP);
		exit;
	}
	return $uniqid;
}

/* DB������ʸ������� */
function sfGetTimestamp($year, $month, $day, $last = false) {
	if($year != "" && $month != "" && $day != "") {	
		if($last) {
			$time = "23:59:59";
		} else {
			$time = "00:00:00";
		}
		$date = $year."-".$month."-".$day." ".$time;
	} else {
		$date = "";
	}
	return 	$date;
}

// INT���ο��ͥ����å�
function sfIsInt($value) {
	if($value != "" && strlen($value) <= INT_LEN && is_numeric($value)) {
		return true;
	}
	return false;
}

function sfCSVDownload($data, $prefix = ""){
	
	if($prefix == "") {
		$dir_name = sfUpDirName();
		$file_name = $dir_name . date("ymdHis") .".csv";
	} else {
		$file_name = $prefix . date("ymdHis") .".csv";
	}
	
	/* HTTP�إå��ν��� */
	Header("Content-disposition: attachment; filename=${file_name}");
	Header("Content-type: application/octet-stream; name=${file_name}");
	Header("Cache-Control: ");
	Header("Pragma: ");
	
	/* i18n��� ���������ư��ʤ����ᡢmb��� ���ѹ�
	if (i18n_discover_encoding($data) == CHAR_CODE){
		$data = i18n_convert($data,'SJIS',CHAR_CODE);
	}
	*/
	if (mb_internal_encoding() == CHAR_CODE){
		$data = mb_convert_encoding($data,'SJIS',CHAR_CODE);
	}
	
	/* �ǡ�������� */
	echo $data;
}

/* 1���ؾ�Υǥ��쥯�ȥ�̾��������� */
function sfUpDirName() {
	$path = $_SERVER['PHP_SELF'];
	$arrVal = split("/", $path);
	$cnt = count($arrVal);
	return $arrVal[($cnt - 2)];
}

// ���ߤΥ����Ȥ򹹿��ʤ������ݥ��ȤϹԤ�ʤ���
function sfReload($get = "") {
	if ($_SERVER["SERVER_PORT"] == "443" ){
		$url = ereg_replace(URL_DIR . "$", "", SSL_URL);
	} else {
		$url = ereg_replace(URL_DIR . "$", "", SITE_URL);
	}
	
	if($get != "") {
		header("Location: ". $url . $_SERVER['PHP_SELF'] . "?" . $get);
	} else {
		header("Location: ". $url . $_SERVER['PHP_SELF']);
	}
	exit;
}

// ��󥭥󥰤�夲�롣
function sfRankUp($table, $colname, $id, $andwhere = "") {
	$objQuery = new SC_Query();
	$objQuery->begin();
	$where = "$colname = ?";
	if($andwhere != "") {
		$where.= " AND $andwhere";
	}
	// �оݹ��ܤΥ�󥯤����
	$rank = $objQuery->get($table, "rank", $where, array($id));
	// ��󥯤κ����ͤ����
	$maxrank = $objQuery->max($table, "rank", $andwhere);
	// ��󥯤������ͤ��⾮�������˼¹Ԥ��롣
	if($rank < $maxrank) {
		// ��󥯤���ľ��ID��������롣
		$where = "rank = ?";
		if($andwhere != "") {
			$where.= " AND $andwhere";
		}
		$uprank = $rank + 1;
		$up_id = $objQuery->get($table, $colname, $where, array($uprank));
		// ��������ؤ��μ¹�
		$sqlup = "UPDATE $table SET rank = ?, update_date = Now() WHERE $colname = ?";
		$objQuery->exec($sqlup, array($rank + 1, $id));
		$objQuery->exec($sqlup, array($rank, $up_id));
	}
	$objQuery->commit();
}

// ��󥭥󥰤򲼤��롣
function sfRankDown($table, $colname, $id, $andwhere = "") {
	$objQuery = new SC_Query();
	$objQuery->begin();
	$where = "$colname = ?";
	if($andwhere != "") {
		$where.= " AND $andwhere";
	}
	// �оݹ��ܤΥ�󥯤����
	$rank = $objQuery->get($table, "rank", $where, array($id));
		
	// ��󥯤�1(�Ǿ���)�����礭�����˼¹Ԥ��롣
	if($rank > 1) {
		// ��󥯤���Ĳ���ID��������롣
		$where = "rank = ?";
		if($andwhere != "") {
			$where.= " AND $andwhere";
		}
		$downrank = $rank - 1;
		$down_id = $objQuery->get($table, $colname, $where, array($downrank));
		// ��������ؤ��μ¹�
		$sqlup = "UPDATE $table SET rank = ?, update_date = Now() WHERE $colname = ?";
		$objQuery->exec($sqlup, array($rank - 1, $id));
		$objQuery->exec($sqlup, array($rank, $down_id));
	}
	$objQuery->commit();
}

//----�������̤ذ�ư
function sfMoveRank($tableName, $keyIdColumn, $keyId, $pos, $where = "") {
	$objQuery = new SC_Query();
	$objQuery->begin();
		
	// ���ȤΥ�󥯤��������
	$rank = $objQuery->get($tableName, "rank", "$keyIdColumn = ?", array($keyId));	
	$max = $objQuery->max($tableName, "rank", $where);
		
	// �ͤ�Ĵ���ʵս��
	if($pos > $max) {
		$position = 1;
	} else if($pos < 1) {
		$position = $max;
	} else {
		$position = $max - $pos + 1;
	}
	
	if( $position > $rank ) $term = "rank - 1";	//�����ؤ���ν�̤����촹�����ν�̤���礭�����
	if( $position < $rank ) $term = "rank + 1";	//�����ؤ���ν�̤����촹�����ν�̤�꾮�������

	//--�����ꤷ����̤ξ��ʤ����ư�����뾦�ʤޤǤ�rank�򣱤Ĥ��餹
	$sql = "UPDATE $tableName SET rank = $term, update_date = NOW() WHERE rank BETWEEN ? AND ? AND del_flg = 0";
	if($where != "") {
		$sql.= " AND $where";
	}
	
	if( $position > $rank ) $objQuery->exec( $sql, array( $rank + 1, $position ));
	if( $position < $rank ) $objQuery->exec( $sql, array( $position, $rank - 1 ));

	//-- ���ꤷ����̤�rank��񤭴����롣
	$sql  = "UPDATE $tableName SET rank = ?, update_date = NOW() WHERE $keyIdColumn = ? AND del_flg = 0 ";
	if($where != "") {
		$sql.= " AND $where";
	}
	
	$objQuery->exec( $sql, array( $position, $keyId ) );
	$objQuery->commit();
}

// ��󥯤�ޤ�쥳���ɤκ��
// �쥳���ɤ��Ⱥ��������ϡ�$delete��true�ˤ��롣
function sfDeleteRankRecord($table, $colname, $id, $andwhere = "", $delete = false) {
	$objQuery = new SC_Query();
	$objQuery->begin();
	// ����쥳���ɤΥ�󥯤�������롣		
	$where = "$colname = ?";
	if($andwhere != "") {
		$where.= " AND $andwhere";
	}
	$rank = $objQuery->get($table, "rank", $where, array($id));

	if(!$delete) {
		// ��󥯤�ǲ��̤ˤ��롢DEL�ե饰ON
		$sqlup = "UPDATE $table SET rank = 0, del_flg = 1, update_date = Now() ";
		$sqlup.= "WHERE $colname = ?";
		// UPDATE�μ¹�
		$objQuery->exec($sqlup, array($id));
	} else {
		$objQuery->delete($table, "$colname = ?", array($id));
	}
	
	// �ɲå쥳���ɤΥ�󥯤���Υ쥳���ɤ��Ĥ��餹��
	$where = "rank > ?";
	if($andwhere != "") {
		$where.= " AND $andwhere";
	}
	$sqlup = "UPDATE $table SET rank = (rank - 1) WHERE $where";
	$objQuery->exec($sqlup, array($rank));
	$objQuery->commit();
}

// �쥳���ɤ�¸�ߥ����å�
function sfIsRecord($table, $col, $arrval, $addwhere = "") {
	$objQuery = new SC_Query();
	$arrCol = split("[, ]", $col);
		
	$where = "del_flg = 0";
	
	if($addwhere != "") {
		$where.= " AND $addwhere";
	}
		
	foreach($arrCol as $val) {
		if($val != "") {
			if($where == "") {
				$where = "$val = ?";
			} else {
				$where.= " AND $val = ?";
			}
		}
	}
	$ret = $objQuery->get($table, $col, $where, $arrval);
	
	if($ret != "") {
		return true;
	}
	return false;
}

// �����å��ܥå������ͤ�ޡ���
function sfMergeCBValue($keyname, $max) {
	$conv = "";
	$cnt = 1;
	for($cnt = 1; $cnt <= $max; $cnt++) {
		if ($_POST[$keyname . $cnt] == "1") {
			$conv.= "1";
		} else {
			$conv.= "0";
		}
	}
	return $conv;
}

// html_checkboxes���ͤ�ޡ�������2�ʿ��������ѹ����롣
function sfMergeCheckBoxes($array, $max) {
	$ret = "";
	if(is_array($array)) {	
		foreach($array as $val) {
			$arrTmp[$val] = "1";
		}
	}
	for($i = 1; $i <= $max; $i++) {	
		if($arrTmp[$i] == "1") {
			$ret.= "1";
		} else {
			$ret.= "0";
		}
	}
	return $ret;
}


// html_checkboxes���ͤ�ޡ������ơ�-�פǤĤʤ��롣
function sfMergeParamCheckBoxes($array) {
    $ret = '';
	if(is_array($array)) {
		foreach($array as $val) {
			if($ret != "") {
				$ret.= "-$val";
			} else {
				$ret = $val;			
			}
		}
	} else {
		$ret = $array;
	}
	return $ret;
}

// html_checkboxes���ͤ�ޡ�������SQL�����Ѥ��ѹ����롣
function sfSearchCheckBoxes($array) {
	$max = 0;
	$ret = "";
	foreach($array as $val) {
		$arrTmp[$val] = "1";
		if($val > $max) {
			$max = $val;
		}
	}
	for($i = 1; $i <= $max; $i++) {	
		if($arrTmp[$i] == "1") {
			$ret.= "1";
		} else {
			$ret.= "_";
		}
	}
	
	if($ret != "") {	
		$ret.= "%";
	}
	return $ret;
}

// 2�ʿ��������ͤ�html_checkboxes�б����ͤ��ڤ��ؤ���
function sfSplitCheckBoxes($val) {
	$len = strlen($val);
	for($i = 0; $i < $len; $i++) {
		if(substr($val, $i, 1) == "1") {
			$arrRet[] = ($i + 1);
		}
	}
	return $arrRet;
}

// �����å��ܥå������ͤ�ޡ���
function sfMergeCBSearchValue($keyname, $max) {
	$conv = "";
	$cnt = 1;
	for($cnt = 1; $cnt <= $max; $cnt++) {
		if ($_POST[$keyname . $cnt] == "1") {
			$conv.= "1";
		} else {
			$conv.= "_";
		}
	}
	return $conv;
}

// �����å��ܥå������ͤ�ʬ��
function sfSplitCBValue($val, $keyname = "") {
	$len = strlen($val);
	$no = 1;
	for ($cnt = 0; $cnt < $len; $cnt++) {
		if($keyname != "") {
			$arr[$keyname . $no] = substr($val, $cnt, 1);
		} else {
			$arr[] = substr($val, $cnt, 1);
		}
		$no++;
	}
	return $arr;
}

// �������ͤ򥻥åȤ�����������
function sfArrKeyValue($arrList, $keyname, $valname, $len_max = "", $keysize = "") {
	
	$max = count($arrList);
	
	if($len_max != "" && $max > $len_max) {
		$max = $len_max;
	}
	
	for($cnt = 0; $cnt < $max; $cnt++) {
		if($keysize != "") {
			$key = sfCutString($arrList[$cnt][$keyname], $keysize);
		} else {
			$key = $arrList[$cnt][$keyname];
		}
		$val = $arrList[$cnt][$valname];
		
		if(!isset($arrRet[$key])) {
			$arrRet[$key] = $val;
		}
		
	}
	return $arrRet;
}

// �������ͤ򥻥åȤ�����������(�ͤ�ʣ���ξ��)
function sfArrKeyValues($arrList, $keyname, $valname, $len_max = "", $keysize = "", $connect = "") {
	
	$max = count($arrList);
	
	if($len_max != "" && $max > $len_max) {
		$max = $len_max;
	}
	
	for($cnt = 0; $cnt < $max; $cnt++) {
		if($keysize != "") {
			$key = sfCutString($arrList[$cnt][$keyname], $keysize);
		} else {
			$key = $arrList[$cnt][$keyname];
		}
		$val = $arrList[$cnt][$valname];
		
		if($connect != "") {
			$arrRet[$key].= "$val".$connect;
		} else {
			$arrRet[$key][] = $val;		
		}
	}
	return $arrRet;
}

// ������ͤ򥫥�޶��ڤ���֤���
function sfGetCommaList($array, $space=true) {
	if (count($array) > 0) {
		$line = "";
		foreach($array as $val) {
			if ($space) {
				$line .= $val . ", ";
			}else{
				$line .= $val . ",";
			}
		}
		if ($space) {
			$line = ereg_replace(", $", "", $line);
		}else{
			$line = ereg_replace(",$", "", $line);
		}
		return $line;
	}else{
		return false;
	}
	
}

/* ��������Ǥ�CSV�ե����ޥåȤǽ��Ϥ��롣*/
function sfGetCSVList($array) {
	if (count($array) > 0) {
		foreach($array as $key => $val) {
			$val = mb_convert_encoding($val, CHAR_CODE, CHAR_CODE);
			$line .= "\"".$val."\",";
		}
		$line = ereg_replace(",$", "\n", $line);
	}else{
		return false;
	}
	return $line;
}

/* ��������Ǥ�PDF�ե����ޥåȤǽ��Ϥ��롣*/
function sfGetPDFList($array) {
	foreach($array as $key => $val) {
		$line .= "\t".$val;
	}
	$line.="\n";
	return $line;
}



/*-----------------------------------------------------------------*/
/*	check_set_term
/*	ǯ�������̤줿2�Ĥδ��֤�������������å������������ȴ��֤��֤�
/*������ (����ǯ,���Ϸ�,������,��λǯ,��λ��,��λ��)
/*������ array(������������
/*  		��������ǯ���� (YYYY/MM/DD 000000)
/*			������λǯ���� (YYYY/MM/DD 235959)
/*			�������顼 ( 0 = OK, 1 = NG )
/*-----------------------------------------------------------------*/
function sfCheckSetTerm ( $start_year, $start_month, $start_day, $end_year, $end_month, $end_day ) {

	// ���ֻ���
	$error = 0;
	if ( $start_month || $start_day || $start_year){
		if ( ! checkdate($start_month, $start_day , $start_year) ) $error = 1;
	} else {
		$error = 1;
	}
	if ( $end_month || $end_day || $end_year){
		if ( ! checkdate($end_month ,$end_day ,$end_year) ) $error = 2;
	}
	if ( ! $error ){
		$date1 = $start_year ."/".sprintf("%02d",$start_month) ."/".sprintf("%02d",$start_day) ." 000000";
		$date2 = $end_year   ."/".sprintf("%02d",$end_month)   ."/".sprintf("%02d",$end_day)   ." 235959";
		if ($date1 > $date2) $error = 3;
	} else {
		$error = 1;
	}
	return array($date1, $date2, $error);
}

// ���顼�ս���طʿ����ѹ����뤿���function SC_View���ɤ߹���
function sfSetErrorStyle(){
	return 'style="background-color:'.ERR_COLOR.'"';
}

/* DB���Ϥ����ͤΥ����å�
 * 10��ʾ�ϥ����С��ե����顼�򵯤����Τǡ�
 */
function sfCheckNumLength( $value ){
	if ( ! is_numeric($value)  ){
		return false;
	} 
	
	if ( strlen($value) > 9 ) {
		return false;
	}
	
	return true;
}

// ���פ����ͤΥ���̾�����
function sfSearchKey($array, $word, $default) {
	foreach($array as $key => $val) {
		if($val == $word) {
			return $key;
		}
	}
	return $default;
}

// ���ƥ���ĥ꡼�μ���($products_check:true������Ͽ�ѤߤΤ�Τ�������)
function sfGetCategoryList($addwhere = "", $products_check = false, $head = CATEGORY_HEAD) {
	$objQuery = new SC_Query();
	$where = "del_flg = 0";
	
	if($addwhere != "") {
		$where.= " AND $addwhere";
	}
		
	$objQuery->setoption("ORDER BY rank DESC");
	
	if($products_check) {
		$col = "T1.category_id, category_name, level";
		$from = "dtb_category AS T1 LEFT JOIN dtb_category_total_count AS T2 ON T1.category_id = T2.category_id";
		$where .= " AND product_count > 0";
	} else {
		$col = "category_id, category_name, level";
		$from = "dtb_category";
	}
	
	$arrRet = $objQuery->select($col, $from, $where);
			
	$max = count($arrRet);
	for($cnt = 0; $cnt < $max; $cnt++) {
		$id = $arrRet[$cnt]['category_id'];
		$name = $arrRet[$cnt]['category_name'];
		$arrList[$id] = "";
		/*
		for($n = 1; $n < $arrRet[$cnt]['level']; $n++) {
			$arrList[$id].= "��";
		}
		*/
		for($cat_cnt = 0; $cat_cnt < $arrRet[$cnt]['level']; $cat_cnt++) {
			$arrList[$id].= $head;
		}
		$arrList[$id].= $name;
	}
	return $arrList;
}

// ���ƥ���ĥ꡼�μ����ʿƥ��ƥ����Value:0)
function sfGetLevelCatList($parent_zero = true) {
	$objQuery = new SC_Query();
	$col = "category_id, category_name, level";
	$where = "del_flg = 0";
	$objQuery->setoption("ORDER BY rank DESC");
	$arrRet = $objQuery->select($col, "dtb_category", $where);
	$max = count($arrRet);
	
	for($cnt = 0; $cnt < $max; $cnt++) {
		if($parent_zero) {
			if($arrRet[$cnt]['level'] == LEVEL_MAX) {
				$arrValue[$cnt] = $arrRet[$cnt]['category_id'];
			} else {
				$arrValue[$cnt] = ""; 
			}
		} else {
			$arrValue[$cnt] = $arrRet[$cnt]['category_id'];
		}
		
		$arrOutput[$cnt] = "";
		/*	 		
		for($n = 1; $n < $arrRet[$cnt]['level']; $n++) {
			$arrOutput[$cnt].= "��";
		}
		*/
		for($cat_cnt = 0; $cat_cnt < $arrRet[$cnt]['level']; $cat_cnt++) {
			$arrOutput[$cnt].= CATEGORY_HEAD;
		}
		$arrOutput[$cnt].= $arrRet[$cnt]['category_name'];
	}
	return array($arrValue, $arrOutput);
}

function sfGetErrorColor($val) {
	if($val != "") {
		return "background-color:" . ERR_COLOR;
	}
	return "";
}


function sfGetEnabled($val) {
	if( ! $val ) {
		return " disabled=\"disabled\"";
	}
	return "";
}

function sfGetChecked($param, $value) {
	if($param == $value) {
		return "checked=\"checked\"";
	}
	return "";
}

// SELECT�ܥå����ѥꥹ�Ȥκ���
function sfGetIDValueList($table, $keyname, $valname) {
	$objQuery = new SC_Query();
	$col = "$keyname, $valname";
	$objQuery->setwhere("del_flg = 0");
	$objQuery->setorder("rank DESC");
	$arrList = $objQuery->select($col, $table);
	$count = count($arrList);
	for($cnt = 0; $cnt < $count; $cnt++) {
		$key = $arrList[$cnt][$keyname];
		$val = $arrList[$cnt][$valname];
		$arrRet[$key] = $val;
	}
	return $arrRet;
}

function sfTrim($str) {
	$ret = ereg_replace("^[�� \n\r]*", "", $str);
	$ret = ereg_replace("[�� \n\r]*$", "", $ret);
	return $ret;
}

/* ��°���뤹�٤Ƥγ��ؤο�ID��������֤� */
function sfGetParents($objQuery, $table, $pid_name, $id_name, $id) {
	$arrRet = sfGetParentsArray($table, $pid_name, $id_name, $id);
	// �������Ƭ1�Ĥ������롣
	array_shift($arrRet);
	return $arrRet;
}


/* ��ID������򸵤�����Υ�����������롣*/
function sfGetParentsCol($objQuery, $table, $id_name, $col_name, $arrId ) {
	$col = $col_name;
	$len = count($arrId);
	$where = "";
	
	for($cnt = 0; $cnt < $len; $cnt++) {
		if($where == "") {
			$where = "$id_name = ?";
		} else {
			$where.= " OR $id_name = ?";
		}
	}
	
	$objQuery->setorder("level");
	$arrRet = $objQuery->select($col, $table, $where, $arrId);
	return $arrRet;	
}

/* ��ID��������֤� */
function sfGetChildsID($table, $pid_name, $id_name, $id) {
	$arrRet = sfGetChildrenArray($table, $pid_name, $id_name, $id);
	return $arrRet;
}

/* ���ƥ����ѹ����ΰ�ư���� */
function sfMoveCatRank($objQuery, $table, $id_name, $cat_name, $old_catid, $new_catid, $id) {
	if ($old_catid == $new_catid) {
		return;
	}
	// �쥫�ƥ���ǤΥ�󥯺������
	// ��ư�쥳���ɤΥ�󥯤�������롣		
	$where = "$id_name = ?";
	$rank = $objQuery->get($table, "rank", $where, array($id));
	// ����쥳���ɤΥ�󥯤���Υ쥳���ɤ��Ĳ��ˤ��餹��
	$where = "rank > ? AND $cat_name = ?";
	$sqlup = "UPDATE $table SET rank = (rank - 1) WHERE $where";
	$objQuery->exec($sqlup, array($rank, $old_catid));
	// �����ƥ���Ǥ���Ͽ����
	// �����ƥ���κ����󥯤�������롣
	$max_rank = $objQuery->max($table, "rank", "$cat_name = ?", array($new_catid)) + 1;
	$where = "$id_name = ?";
	$sqlup = "UPDATE $table SET rank = ? WHERE $where";
	$objQuery->exec($sqlup, array($max_rank, $id));
}

/* �Ƕ�׻� */
function sfTax($price, $tax, $tax_rule) {
	$real_tax = $tax / 100;
	$ret = $price * $real_tax;
	switch($tax_rule) {
	// �ͼθ���
	case 1:
		$ret = round($ret);
		break;
	// �ڤ�Τ�
	case 2:
		$ret = floor($ret);
		break;
	// �ڤ�夲
	case 3:
		$ret = ceil($ret);
		break;
	// �ǥե����:�ڤ�夲
	default:
		$ret = ceil($ret);
		break;
	}
	return $ret;
}

/* �Ƕ���Ϳ */
function sfPreTax($price, $tax, $tax_rule) {
	$real_tax = $tax / 100;
	$ret = $price * (1 + $real_tax);
	
	switch($tax_rule) {
	// �ͼθ���
	case 1:
		$ret = round($ret);
		break;
	// �ڤ�Τ�
	case 2:
		$ret = floor($ret);
		break;
	// �ڤ�夲
	case 3:
		$ret = ceil($ret);
		break;
	// �ǥե����:�ڤ�夲
	default:
		$ret = ceil($ret);
		break;
	}
	return $ret;
}

// �������ꤷ�ƻͼθ���
function sfRound($value, $pow = 0){
	$adjust = pow(10 ,$pow-1);

	// �������0�Фʤ���з�������Ԥ�
	if(sfIsInt($adjust) and $pow > 1){
		$ret = (round($value * $adjust)/$adjust);
	}
	
	$ret = round($ret);

	return $ret;
}

/* �ݥ������Ϳ */
function sfPrePoint($price, $point_rate, $rule = POINT_RULE, $product_id = "") {
	if(sfIsInt($product_id)) {
		$objQuery = new SC_Query();
	    $where = "now() >= cast(start_date as date) AND ";
	    $where .= "now() < cast(end_date as date) AND ";
		
		$where .= "del_flg = 0 AND campaign_id IN (SELECT campaign_id FROM dtb_campaign_detail where product_id = ? )";
		//��Ͽ(����)���ս�
		$objQuery->setorder('update_date DESC');
		//�����ڡ���ݥ���Ȥμ���
		$arrRet = $objQuery->select("campaign_name, campaign_point_rate", "dtb_campaign", $where, array($product_id));
	}
	//ʣ���Υ����ڡ������Ͽ����Ƥ��뾦�ʤϡ��ǿ��Υ����ڡ��󤫤�ݥ���Ȥ����
	if($arrRet[0]['campaign_point_rate'] != "") {
		$campaign_point_rate = $arrRet[0]['campaign_point_rate'];
		$real_point = $campaign_point_rate / 100;
	} else {
		$real_point = $point_rate / 100;
	}
	$ret = $price * $real_point;
	switch($rule) {
	// �ͼθ���
	case 1:
		$ret = round($ret);
		break;
	// �ڤ�Τ�
	case 2:
		$ret = floor($ret);
		break;
	// �ڤ�夲
	case 3:
		$ret = ceil($ret);
		break;
	// �ǥե����:�ڤ�夲
	default:
		$ret = ceil($ret);
		break;
	}
	//�����ڡ����ʤξ��
	if($campaign_point_rate != "") {
		$ret = "(".$arrRet[0]['campaign_name']."�ݥ����Ψ".$campaign_point_rate."%)".$ret;
	}
	return $ret;
}

/* ����ʬ��η������ */
function sfGetClassCatCount() {
	$sql = "select count(dtb_class.class_id) as count, dtb_class.class_id ";
	$sql.= "from dtb_class inner join dtb_classcategory on dtb_class.class_id = dtb_classcategory.class_id ";
	$sql.= "where dtb_class.del_flg = 0 AND dtb_classcategory.del_flg = 0 ";
	$sql.= "group by dtb_class.class_id, dtb_class.name";
	$objQuery = new SC_Query();
	$arrList = $objQuery->getall($sql);
	// �������ͤ򥻥åȤ�����������
	$arrRet = sfArrKeyValue($arrList, 'class_id', 'count');
	
	return $arrRet;
}

/* ���ʤ���Ͽ */
function sfInsertProductClass($objQuery, $arrList, $product_id) {
	// ���Ǥ˵�����Ͽ�����뤫�ɤ���������å����롣
	$where = "product_id = ? AND classcategory_id1 <> 0 AND classcategory_id1 <> 0";
	$count = $objQuery->count("dtb_products_class", $where,  array($product_id));
	
	// ���Ǥ˵�����Ͽ���ʤ����
	if($count == 0) {
		// ��¸���ʤκ��
		$where = "product_id = ?";
		$objQuery->delete("dtb_products_class", $where, array($product_id));
		$sqlval['product_id'] = $product_id;
		$sqlval['classcategory_id1'] = '0';
		$sqlval['classcategory_id2'] = '0';
		$sqlval['product_code'] = $arrList["product_code"];
		$sqlval['stock'] = $arrList["stock"];
		$sqlval['stock_unlimited'] = $arrList["stock_unlimited"];
		$sqlval['price01'] = $arrList['price01'];
		$sqlval['price02'] = $arrList['price02'];
		$sqlval['creator_id'] = $_SESSION['member_id'];
		$sqlval['create_date'] = "now()";
		
		if($_SESSION['member_id'] == "") {
			$sqlval['creator_id'] = '0';
		}
		
		// INSERT�μ¹�
		$objQuery->insert("dtb_products_class", $sqlval);
	}
}

function sfGetProductClassId($product_id, $classcategory_id1, $classcategory_id2) {
	$where = "product_id = ? AND classcategory_id1 = ? AND classcategory_id2 = ?";
	$objQuery = new SC_Query();
	$ret = $objQuery->get("dtb_products_class", "product_class_id", $where, Array($product_id, $classcategory_id1, $classcategory_id2));
	return $ret;
}

/* ʸ���Ρ�/�פ�ʤ��� */
function sfTrimURL($url) {
	$ret = ereg_replace("[/]+$", "", $url);
	return $ret;
}

/* ���ʵ��ʾ���μ��� */
function sfGetProductsClass($arrID) {
	list($product_id, $classcategory_id1, $classcategory_id2) = $arrID;	
	
	if($classcategory_id1 == "") {
		$classcategory_id1 = '0';
	}
	if($classcategory_id2 == "") {
		$classcategory_id2 = '0';
	}
		
	// ���ʵ��ʼ���
	$objQuery = new SC_Query();
	$col = "product_id, deliv_fee, name, product_code, main_list_image, main_image, price01, price02, point_rate, product_class_id, classcategory_id1, classcategory_id2, class_id1, class_id2, stock, stock_unlimited, sale_limit, sale_unlimited";
	$table = "vw_product_class AS prdcls";
	$where = "product_id = ? AND classcategory_id1 = ? AND classcategory_id2 = ?";
	$objQuery->setorder("rank1 DESC, rank2 DESC");
	$arrRet = $objQuery->select($col, $table, $where, array($product_id, $classcategory_id1, $classcategory_id2));
	return $arrRet[0];
}

/* ���׾���򸵤˺ǽ��׻� */
function sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo, $objCustomer = "") {
	// ���ʤι�׸Ŀ�
	$total_quantity = $objCartSess->getTotalQuantity(true);
	
	// �Ƕ�μ���
	$arrData['tax'] = $objPage->tpl_total_tax;
	// ���פμ���
	$arrData['subtotal'] = $objPage->tpl_total_pretax;	
	
	// ��������μ���
	$arrData['deliv_fee'] = 0;
		
	// ���ʤ��Ȥ�������ͭ���ξ��
	if (OPTION_PRODUCT_DELIV_FEE == 1) {
		$arrData['deliv_fee']+= $objCartSess->getAllProductsDelivFee();
	}
	
	// �����ȼԤ�������ͭ���ξ��
	if (OPTION_DELIV_FEE == 1) {
		// �����ι�פ�׻�����
		$arrData['deliv_fee']+= sfGetDelivFee($arrData['deliv_pref'], $arrData['payment_id']);
	}
	
	// ����̵���ι����������ꤵ��Ƥ�����
	if(DELIV_FREE_AMOUNT > 0) {
		if($total_quantity >= DELIV_FREE_AMOUNT) {
			$arrData['deliv_fee'] = 0;
		}	
	}
		
	// ����̵����郎���ꤵ��Ƥ�����
	if($arrInfo['free_rule'] > 0) {
		// ���פ�̵������Ķ���Ƥ�����
		if($arrData['subtotal'] >= $arrInfo['free_rule']) {
			$arrData['deliv_fee'] = 0;
		}
	}

	// ��פη׻�
	$arrData['total'] = $objPage->tpl_total_pretax;	// ���ʹ��
	$arrData['total']+= $arrData['deliv_fee'];		// ����
	$arrData['total']+= $arrData['charge'];			// �����
	// ����ʧ�����
	$arrData['payment_total'] = $arrData['total'] - ($arrData['use_point'] * POINT_VALUE);
	// �û��ݥ���Ȥη׻�
	$arrData['add_point'] = sfGetAddPoint($objPage->tpl_total_point, $arrData['use_point'], $arrInfo);
	
	if($objCustomer != "") {
		// ��������Ǥ��ä����
		if($objCustomer->isBirthMonth()) {
			$arrData['birth_point'] = BIRTH_MONTH_POINT;
			$arrData['add_point'] += $arrData['birth_point'];
		}
	}
	
	if($arrData['add_point'] < 0) {
		$arrData['add_point'] = 0;
	}
	
	return $arrData;
}

/* �������⾦�ʤν��׽��� */
function sfTotalCart($objPage, $objCartSess, $arrInfo) {
	// ����̾����
	$arrClassName = sfGetIDValueList("dtb_class", "class_id", "name");
	// ����ʬ��̾����
	$arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
	
	$objPage->tpl_total_pretax = 0;		// ���ѹ��(�ǹ���)
	$objPage->tpl_total_tax = 0;		// �����ǹ��
	$objPage->tpl_total_point = 0;		// �ݥ���ȹ��
	
	// �����������μ���
	$arrCart = $objCartSess->getCartList();
	$max = count($arrCart);
	$cnt = 0;

	for ($i = 0; $i < $max; $i++) {
		// ���ʵ��ʾ���μ���	
		$arrData = sfGetProductsClass($arrCart[$i]['id']);
		$limit = "";
		// DB��¸�ߤ��뾦��
		if (count($arrData) > 0) {
			
			// �������¿�����롣			
			if ($arrData['stock_unlimited'] != '1' && $arrData['sale_unlimited'] != '1') {
				if($arrData['sale_limit'] < $arrData['stock']) {
					$limit = $arrData['sale_limit'];
				} else {
					$limit = $arrData['stock'];
				}
			} else {
				if ($arrData['sale_unlimited'] != '1') {
					$limit = $arrData['sale_limit'];
				}
				if ($arrData['stock_unlimited'] != '1') {
					$limit = $arrData['stock'];
				}
			}
						
			if($limit != "" && $limit < $arrCart[$i]['quantity']) {
				// �������⾦�ʿ������¤˹�碌��
				$objCartSess->setProductValue($arrCart[$i]['id'], 'quantity', $limit);
				$quantity = $limit;
				$objPage->tpl_message = "����" . $arrData['name'] . "�פ��������¤��Ƥ���ޤ������٤ˤ���ʾ�ι����ϤǤ��ޤ���";
			} else {
				$quantity = $arrCart[$i]['quantity'];
			}
			
			$objPage->arrProductsClass[$cnt] = $arrData;
			$objPage->arrProductsClass[$cnt]['quantity'] = $quantity;
			$objPage->arrProductsClass[$cnt]['cart_no'] = $arrCart[$i]['cart_no'];
			$objPage->arrProductsClass[$cnt]['class_name1'] = $arrClassName[$arrData['class_id1']];
			$objPage->arrProductsClass[$cnt]['class_name2'] = $arrClassName[$arrData['class_id2']];
			$objPage->arrProductsClass[$cnt]['classcategory_name1'] = $arrClassCatName[$arrData['classcategory_id1']];
			$objPage->arrProductsClass[$cnt]['classcategory_name2'] = $arrClassCatName[$arrData['classcategory_id2']];
			
			// ����������
			list($image_width, $image_height) = getimagesize(IMAGE_SAVE_DIR . basename($objPage->arrProductsClass[$cnt]["main_image"]));
			$objPage->arrProductsClass[$cnt]["tpl_image_width"] = $image_width + 60;
			$objPage->arrProductsClass[$cnt]["tpl_image_height"] = $image_height + 80;
			
			// ���ʤ���Ͽ
			if ($arrData['price02'] != "") {
				$objCartSess->setProductValue($arrCart[$i]['id'], 'price', $arrData['price02']);
				$objPage->arrProductsClass[$cnt]['uniq_price'] = $arrData['price02'];
			} else {
				$objCartSess->setProductValue($arrCart[$i]['id'], 'price', $arrData['price01']);
				$objPage->arrProductsClass[$cnt]['uniq_price'] = $arrData['price01'];
			}
			// �ݥ������ͿΨ����Ͽ
			$objCartSess->setProductValue($arrCart[$i]['id'], 'point_rate', $arrData['point_rate']);
			// ���ʤ��Ȥι�׶��
			$objPage->arrProductsClass[$cnt]['total_pretax'] = $objCartSess->getProductTotal($arrInfo, $arrCart[$i]['id']);
			// �����ι�פ�׻�����
			$objPage->tpl_total_deliv_fee+= ($arrData['deliv_fee'] * $arrCart[$i]['quantity']);
			$cnt++;
		} else {
			// DB�˾��ʤ����Ĥ���ʤ����ϥ����Ⱦ��ʤκ��
			$objCartSess->delProductKey('id', $arrCart[$i]['id']);
		}
	}
	
	// �����ʹ�׶��(�ǹ���)
	$objPage->tpl_total_pretax = $objCartSess->getAllProductsTotal($arrInfo);
	// �����ʹ�׾�����
	$objPage->tpl_total_tax = $objCartSess->getAllProductsTax($arrInfo);
	// �����ʹ�ץݥ����
	$objPage->tpl_total_point = $objCartSess->getAllProductsPoint();
	
	return $objPage;	
}

/* DB������Ф������դ�ʸ�����Ĵ�����롣*/
function sfDispDBDate($dbdate, $time = true) {
	list($y, $m, $d, $H, $M) = split("[- :]", $dbdate);

	if(strlen($y) > 0 && strlen($m) > 0 && strlen($d) > 0) {
		if ($time) {
			$str = sprintf("%04d/%02d/%02d %02d:%02d", $y, $m, $d, $H, $M);
		} else {
			$str = sprintf("%04d/%02d/%02d", $y, $m, $d, $H, $M);						
		}
	} else {
		$str = "";
	}
	return $str;
}

function sfGetDelivTime($payment_id = "") {
	$objQuery = new SC_Query();
	
	$deliv_id = "";
	
	if($payment_id != "") {
		$where = "del_flg = 0 AND payment_id = ?";
		$arrRet = $objQuery->select("deliv_id", "dtb_payment", $where, array($payment_id));
		$deliv_id = $arrRet[0]['deliv_id'];
	}
	
	if($deliv_id != "") {
		$objQuery->setorder("time_id");
		$where = "deliv_id = ?";
		$arrRet= $objQuery->select("time_id, deliv_time", "dtb_delivtime", $where, array($deliv_id));
	}
	
	return $arrRet;	
}


// ��ƻ�ܸ�����ʧ����ˡ��������������������
function sfGetDelivFee($pref, $payment_id = "") {
	$objQuery = new SC_Query();
	
	$deliv_id = "";
	
	// ��ʧ����ˡ�����ꤵ��Ƥ�����ϡ��б����������ȼԤ��������
	if($payment_id != "") {
		$where = "del_flg = 0 AND payment_id = ?";
		$arrRet = $objQuery->select("deliv_id", "dtb_payment", $where, array($payment_id));
		$deliv_id = $arrRet[0]['deliv_id'];
	// ��ʧ����ˡ�����ꤵ��Ƥ��ʤ����ϡ���Ƭ�������ȼԤ��������
	} else {
		$where = "del_flg = 0";
		$objQuery->setOrder("rank DESC");
		$objQuery->setLimitOffset(1);
		$arrRet = $objQuery->select("deliv_id", "dtb_deliv", $where);
		$deliv_id = $arrRet[0]['deliv_id'];	
	}
	
	// �����ȼԤ��������������
	if($deliv_id != "") {
		
		// ��ƻ�ܸ������ꤵ��Ƥ��ʤ����ϡ�����Ԥ��ֹ����ꤷ�Ƥ���
		if($pref == "") {
			$pref = 13;
		}
		
		$objQuery = new SC_Query();
		$where = "deliv_id = ? AND pref = ?";
		$arrRet= $objQuery->select("fee", "dtb_delivfee", $where, array($deliv_id, $pref));
	}	
	return $arrRet[0]['fee'];	
}

/* ��ʧ����ˡ�μ��� */
function sfGetPayment() {
	$objQuery = new SC_Query();
	// ������ۤ����۰ʲ��ι��ܤ����
	$where = "del_flg = 0";
	$objQuery->setorder("fix, rank DESC");
	$arrRet = $objQuery->select("payment_id, payment_method, rule", "dtb_payment", $where);
	return $arrRet;	
}

/* ����򥭡�̾���Ȥ�������ѹ����� */
function sfSwapArray($array) {
	$max = count($array);
	for($i = 0; $i < $max; $i++) {
		foreach($array[$i] as $key => $val) {
			$arrRet[$key][] = $val;
		}
	}
	return $arrRet;
}

/* �������򤹤��Smarty��) */
function sfMultiply($num1, $num2) {
	return ($num1 * $num2);
}

/* DB����Ͽ���줿�ƥ�ץ졼�ȥ᡼������� */
function sfSendTemplateMail($to, $to_name, $template_id, $objPage) {
	global $arrMAILTPLPATH;
	$objQuery = new SC_Query();
	// �᡼��ƥ�ץ졼�Ⱦ���μ���
	$where = "template_id = ?";
	$arrRet = $objQuery->select("subject, header, footer", "dtb_mailtemplate", $where, array($template_id));
	$objPage->tpl_header = $arrRet[0]['header'];
	$objPage->tpl_footer = $arrRet[0]['footer'];
	$tmp_subject = $arrRet[0]['subject'];
	
	$objSiteInfo = new SC_SiteInfo();
	$arrInfo = $objSiteInfo->data;
	
	$objMailView = new SC_SiteView();
	// �᡼����ʸ�μ���
	$objMailView->assignobj($objPage);
	$body = $objMailView->fetch($arrMAILTPLPATH[$template_id]);
	
	// �᡼����������
	$objSendMail = new GC_SendMail();
	$from = $arrInfo['email03'];
	$error = $arrInfo['email04'];
	$tosubject = $tmp_subject;
	$objSendMail->setItem('', $tosubject, $body, $from, $arrInfo['shop_name'], $from, $error, $error);
	$objSendMail->setTo($to, $to_name);
	$objSendMail->sendMail();	// �᡼������
}

/** ����λ�᡼������
 *  $template_id �� 1�������ѥƥ�ץ졼�ȡ�0��PC�ѥƥ�ץ졼��
 */
function sfSendOrderMail($order_id, $template_id, $subject = "", $body = "", $send = true) {
	global $arrMAILTPLPATH;
	
	$objPage = new LC_Page();
	$objSiteInfo = new SC_SiteInfo();
	$arrInfo = $objSiteInfo->data;
	$objPage->arrInfo = $arrInfo;
	
	$objQuery = new SC_Query();
		
	if($subject == "" && $body == "" ) {
		// �᡼��ƥ�ץ졼�Ⱦ���μ���
		$where = "template_id = ?";
		$arrRet = $objQuery->select("subject, body", "dtb_mailtemplate", $where, array($template_id));
		$objPage->tpl_body = $arrRet[0]['body'];
		$tmp_subject = $arrRet[0]['subject'];
	} else {
		$objPage->tpl_body = $body;
		$tmp_subject = $subject;
	}
	
	// �������μ���
	$where = "order_id = ?";
	$arrRet = $objQuery->select("*", "dtb_order", $where, array($order_id));
	$arrOrder = $arrRet[0];
	$arrOrderDetail = $objQuery->select("*", "dtb_order_detail", $where, array($order_id));
	
	$objPage->Message_tmp = $arrOrder['message'];
		
	// �ܵҾ���μ���
	$customer_id = $arrOrder['customer_id'];
	$arrRet = $objQuery->select("point,name01,name02", "dtb_customer", "customer_id = ?", array($customer_id));
	$arrCustomer = $arrRet[0];

	$objPage->arrCustomer = $arrCustomer;
	$objPage->arrOrder = $arrOrder;
    
	//����¾��Ѿ���
	if($arrOrder['memo02'] != "") {
		$arrOther = unserialize($arrOrder['memo02']);
		
		foreach($arrOther as $other_key => $other_val){
			if(sfTrim($other_val["value"]) == ""){
				$arrOther[$other_key]["value"] = "";
			}
		}		
		$objPage->arrOther = $arrOther;
	}
		
	// ��ƻ�ܸ��Ѵ�
	global $arrPref;
	$objPage->arrOrder['deliv_pref'] = $arrPref[$objPage->arrOrder['deliv_pref']];
	
	$objPage->arrOrderDetail = $arrOrderDetail;
	
	$objCustomer = new SC_Customer();
	$objPage->tpl_user_point = $objCustomer->getValue('point');
	
	$objMailView = new SC_SiteView();
	// �᡼����ʸ�μ���
	$objMailView->assignobj($objPage);
	
    $name = $objPage->arrOrder['order_name01']." ".$objPage->arrOrder['order_name02'];
    $objPage->tpl_body = ereg_replace( "(\{name\})", $name ,  $objPage->tpl_body );
    $tmp_subject = ereg_replace( "(\{name\})", $name ,  $tmp_subject );
	 
    // ����ưŪ��ʬ�����
	$body = $objMailView->fetch($arrMAILTPLPATH[$template_id]);
    $body = ereg_replace( "(\{order\})", $body ,  $objPage->tpl_body );
        
	// �᡼����������
	$objSendMail = new GC_SendMail();
	$bcc = $arrInfo['email01'];
	$from = $arrInfo['email03'];
	$error = $arrInfo['email04'];

	$tosubject = sfMakeSubject($tmp_subject);
	
	$objSendMail->setItem('', $tosubject, $body, $from, $arrInfo['shop_name'], $from, $error, $error, $bcc);
	$objSendMail->setTo($arrOrder["order_email"], $arrOrder["order_name01"] . " ". $arrOrder["order_name02"] ." ��");

	// �����ե饰:true�ξ��ϡ��������롣
	if($send) {
		if ($objSendMail->sendMail()) {
			sfSaveMailHistory($order_id, $template_id, $tosubject, $body);
		}
	}
	return $objSendMail;
}

// �ƥ�ץ졼�Ȥ���Ѥ����᡼�������
function sfSendTplMail($to, $subject, $tplpath, $objPage) {
	$objMailView = new SC_SiteView();
	$objSiteInfo = new SC_SiteInfo();
	$arrInfo = $objSiteInfo->data;
	// �᡼����ʸ�μ���
	$objPage->tpl_shopname=$arrInfo['shop_name'];
	$objPage->tpl_infoemail = $arrInfo['email02'];
	$objMailView->assignobj($objPage);
	$body = $objMailView->fetch($tplpath);
	// �᡼����������
	$objSendMail = new GC_SendMail();
	$to = mb_encode_mimeheader($to);
	$bcc = $arrInfo['email01'];
	$from = $arrInfo['email03'];
	$error = $arrInfo['email04'];
	$objSendMail->setItem($to, $subject, $body, $from, $arrInfo['shop_name'], $from, $error, $error, $bcc);
	$objSendMail->sendMail();	
}

// �̾�Υ᡼������
function sfSendMail($to, $subject, $body) {
	$objSiteInfo = new SC_SiteInfo();
	$arrInfo = $objSiteInfo->data;
	// �᡼����������
	$objSendMail = new GC_SendMail();
	$bcc = $arrInfo['email01'];
	$from = $arrInfo['email03'];
	$error = $arrInfo['email04'];
	$objSendMail->setItem($to, $subject, $body, $from, $arrInfo['shop_name'], $from, $error, $error, $bcc);
	$objSendMail->sendMail();
}

//��̾�˥ƥ�ץ졼�Ȥ��Ѥ���
function sfMakeSubject($subject){
	
	$objQuery = new SC_Query();
	$objMailView = new SC_SiteView();
	$objPage = new LC_Page();
	
	$arrInfo = $objQuery->select("*","dtb_baseinfo");
	$arrInfo = $arrInfo[0];
	$objPage->tpl_shopname=$arrInfo['shop_name'];
	$objPage->tpl_infoemail=$subject;
	$objMailView->assignobj($objPage);
	$mailtitle = $objMailView->fetch('mail_templates/mail_title.tpl');
	$ret = $mailtitle.$subject;
	return $ret; 
}

// �᡼���ۿ�����ؤ���Ͽ
function sfSaveMailHistory($order_id, $template_id, $subject, $body) {
	$sqlval['subject'] = $subject;
	$sqlval['order_id'] = $order_id;
	$sqlval['template_id'] = $template_id;
	$sqlval['send_date'] = "Now()";
	if($_SESSION['member_id'] != "") {
		$sqlval['creator_id'] = $_SESSION['member_id'];
	} else {
		$sqlval['creator_id'] = '0';
	}
	$sqlval['mail_body'] = $body;
	
	$objQuery = new SC_Query();
	$objQuery->insert("dtb_mail_history", $sqlval);
}

/* ��������������ơ��֥�� */
function sfGetCustomerSqlVal($uniqid, $sqlval) {
	$objCustomer = new SC_Customer();
	// ���������Ͽ����
	if ($objCustomer->isLoginSuccess()) {
		// ��Ͽ�ǡ����κ���
		$sqlval['order_temp_id'] = $uniqid;
		$sqlval['update_date'] = 'Now()';
		$sqlval['customer_id'] = $objCustomer->getValue('customer_id');
	    $sqlval['order_name01'] = $objCustomer->getValue('name01');
	    $sqlval['order_name02'] = $objCustomer->getValue('name02');
	    $sqlval['order_kana01'] = $objCustomer->getValue('kana01');
	    $sqlval['order_kana02'] = $objCustomer->getValue('kana02');
	    $sqlval['order_sex'] = $objCustomer->getValue('sex');
	    $sqlval['order_zip01'] = $objCustomer->getValue('zip01');
	    $sqlval['order_zip02'] = $objCustomer->getValue('zip02');
	    $sqlval['order_pref'] = $objCustomer->getValue('pref');
	    $sqlval['order_addr01'] = $objCustomer->getValue('addr01');
	    $sqlval['order_addr02'] = $objCustomer->getValue('addr02');
	    $sqlval['order_tel01'] = $objCustomer->getValue('tel01');
	    $sqlval['order_tel02'] = $objCustomer->getValue('tel02');
		$sqlval['order_tel03'] = $objCustomer->getValue('tel03');
		if (defined('MOBILE_SITE')) {
			$sqlval['order_email'] = $objCustomer->getValue('email_mobile');
		} else {
			$sqlval['order_email'] = $objCustomer->getValue('email');
		}
		$sqlval['order_job'] = $objCustomer->getValue('job');
		$sqlval['order_birth'] = $objCustomer->getValue('birth');
	}
	return $sqlval;
}

// �������ơ��֥�ؤν񤭹��߽���
function sfRegistTempOrder($uniqid, $sqlval) {
	if($uniqid != "") {
		// ��¸�ǡ����Υ����å�
		$objQuery = new SC_Query();
		$where = "order_temp_id = ?";
		$cnt = $objQuery->count("dtb_order_temp", $where, array($uniqid));
		// ��¸�ǡ������ʤ����
		if ($cnt == 0) {
			// ���񤭹��߻��˲������Ͽ�Ѥ߾���������
			$sqlval = sfGetCustomerSqlVal($uniqid, $sqlval);
			$sqlval['create_date'] = "now()";
			$objQuery->insert("dtb_order_temp", $sqlval);
		} else {
			$objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
		}
	}
}

/* ����Υ��ޥ���Ͽ�����뤫�ɤ����Υ����å�(�������ޤޤʤ�) */
function sfCheckCustomerMailMaga($email) {
	$col = "email, mailmaga_flg, customer_id";
	$from = "dtb_customer";
	$where = "email = ? AND status = 2";
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select($col, $from, $where, array($email));
	// ����Υ᡼�륢�ɥ쥹����Ͽ����Ƥ���
	if($arrRet[0]['customer_id'] != "") {
		return true;
	}
	return false;
}

// �����ɤν�����̤��֤�
function sfGetAuthonlyResult($dir, $file_name, $name01, $name02, $card_no, $card_exp, $amount, $order_id, $jpo_info = "10"){

	$path = $dir .$file_name;		// cgi�ե�����Υե�ѥ�����
	$now_dir = getcwd();			// require�����ޤ������ʤ��Τǡ�cgi�¹ԥǥ��쥯�ȥ�˰�ư����
	chdir($dir);
	
	// �ѥ����Ϥ��ǥ��ޥ�ɥ饤�󤫤�cgi��ư
	$cmd = "$path card_no=$card_no name01=$name01 name02=$name02 card_exp=$card_exp amount=$amount order_id=$order_id jpo_info=$jpo_info";

	$tmpResult = popen($cmd, "r");
	
	// ��̼���
	while( ! FEOF ( $tmpResult ) ) {
		$result .= FGETS($tmpResult);
	}
	pclose($tmpResult);				// 	�ѥ��פ��Ĥ���
	chdir($now_dir);				//�����ˤ����ǥ��쥯�ȥ�˵���
	
	// ��̤�Ϣ������س�Ǽ
	$result = ereg_replace("&$", "", $result);
	foreach (explode("&",$result) as $data) {
		list($key, $val) = explode("=", $data, 2);
		$return[$key] = $val;
	}
	
	return $return;
}

// �������ơ��֥뤫�������������
function sfGetOrderTemp($order_temp_id) {
	$objQuery = new SC_Query();
	$where = "order_temp_id = ?";
	$arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($order_temp_id));
	return $arrRet[0];
}

// ���ƥ���ID����Ƚ���ѤΥ����Х��ѿ�(���ټ�������Ƥ�����Ƽ������ʤ��褦�ˤ���)
$g_category_on = false;
$g_category_id = "";

/* ������Υ��ƥ����������� */
function sfGetCategoryId($product_id, $category_id) {
	global $g_category_on;
	global $g_category_id;
	if(!$g_category_on)	{
		$g_category_on = true;
		$category_id = (int) $category_id;
		$product_id = (int) $product_id;
		if(sfIsInt($category_id) && sfIsRecord("dtb_category","category_id", $category_id)) {
			$g_category_id = $category_id;
		} else if (sfIsInt($product_id) && sfIsRecord("dtb_products","product_id", $product_id, "status = 1")) {
			$objQuery = new SC_Query();
			$where = "product_id = ?";
			$category_id = $objQuery->get("dtb_products", "category_id", $where, array($product_id));
			$g_category_id = $category_id;
		} else {
			// �����ʾ��ϡ�0���֤���
			$g_category_id = 0;
		}
	}
	return $g_category_id;
}

// ROOTID����Ƚ���ѤΥ����Х��ѿ�(���ټ�������Ƥ�����Ƽ������ʤ��褦�ˤ���)
$g_root_on = false;
$g_root_id = "";

/* ������Υ����ƥ�Υ롼�ȥ��ƥ���ID��������� */
function sfGetRootId() {
	global $g_root_on;
	global $g_root_id;
	if(!$g_root_on)	{
		$g_root_on = true;
		$objQuery = new SC_Query();
		if($_GET['product_id'] != "" || $_GET['category_id'] != "") {
			// ������Υ��ƥ���ID��Ƚ�ꤹ��
			$category_id = sfGetCategoryId($_GET['product_id'], $_GET['category_id']);
			// ROOT���ƥ���ID�μ���
			 $arrRet = sfGetParents($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $category_id);
			 $root_id = $arrRet[0];
		} else {
			// ROOT���ƥ���ID��ʤ������ꤹ��
			$root_id = "";
		}
		$g_root_id = $root_id;
	}
	return $g_root_id;
}

/* ���ƥ��꤫�龦�ʤ򸡺��������WHEREʸ���ͤ��֤� */
function sfGetCatWhere($category_id) {
	// �ҥ��ƥ���ID�μ���
	$arrRet = sfGetChildsID("dtb_category", "parent_category_id", "category_id", $category_id);
	$tmp_where = "";
	foreach ($arrRet as $val) {
		if($tmp_where == "") {
			$tmp_where.= " category_id IN ( ?";
		} else {
			$tmp_where.= ",? ";
		}
		$arrval[] = $val;
	}
	$tmp_where.= " ) ";
	return array($tmp_where, $arrval);
}

/* �û��ݥ���Ȥη׻��� */
function sfGetAddPoint($totalpoint, $use_point, $arrInfo) {
	// �������ʤι�ץݥ���Ȥ������Ѥ����ݥ���ȤΥݥ���ȴ������ͤ��������
	$add_point = $totalpoint - intval($use_point * ($arrInfo['point_rate'] / 100));
	
	if($add_point < 0) {
		$add_point = '0';
	}
	return $add_point;
}

/* ��դ���ͽ¬����ˤ���ID */
function sfGetUniqRandomId($head = "") {
	// ͽ¬����ʤ��褦�˥�����ʸ�������Ϳ���롣
	$random = gfMakePassword(8);
	// Ʊ��ۥ�����ǰ�դ�ID������
	$id = uniqid($head);
	return ($id . $random);
}

// ���ƥ����̥��������ʤμ���
function sfGetBestProducts( $conn, $category_id = 0){
	// ������Ͽ����Ƥ������Ƥ��������
	$sql = "SELECT name, main_image, main_list_image, price01_min, price01_max, price02_min, price02_max, point_rate,
			 A.product_id, A.comment FROM dtb_best_products as A LEFT JOIN vw_products_allclass AS allcls 
			USING (product_id) WHERE A.category_id = ? AND A.del_flg = 0 AND status = 1 ORDER BY A.rank";
	$arrItems = $conn->getAll($sql, array($category_id));

	return $arrItems;
}

// �ü�����ʸ���μ�ư����������
function sfManualEscape($data) {
	// ����Ǥʤ����
	if(!is_array($data)) {
		if (DB_TYPE == "pgsql") {
			$ret = pg_escape_string($data);
		}else if(DB_TYPE == "mysql"){
			$ret = mysql_real_escape_string($data);
		}
		$ret = ereg_replace("%", "\\%", $ret);
		$ret = ereg_replace("_", "\\_", $ret);
		return $ret;
	}
	
	// ����ξ��
	foreach($data as $val) {
		if (DB_TYPE == "pgsql") {
			$ret = pg_escape_string($val);
		}else if(DB_TYPE == "mysql"){
			$ret = mysql_real_escape_string($val);
		}

		$ret = ereg_replace("%", "\\%", $ret);
		$ret = ereg_replace("_", "\\_", $ret);
		$arrRet[] = $ret;
	}

	return $arrRet;
}

// �����ֹ桢���ѥݥ���ȡ��û��ݥ���Ȥ���ǽ��ݥ���Ȥ����
function sfGetCustomerPoint($order_id, $use_point, $add_point) {
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("customer_id", "dtb_order", "order_id = ?", array($order_id));
	$customer_id = $arrRet[0]['customer_id'];
	if($customer_id != "" && $customer_id >= 1) {
		$arrRet = $objQuery->select("point", "dtb_customer", "customer_id = ?", array($customer_id));
		$point = $arrRet[0]['point'];
		$total_point = $arrRet[0]['point'] - $use_point + $add_point;
	} else {
		$total_point = "";
		$point = "";
	}
	return array($point, $total_point);
}

/* �ɥᥤ��֤�ͭ���ʥ��å����Υ������� */
function sfDomainSessionStart() {
	$ret = session_id();
/*
	�إå������������Ƥ��Ƥ�session_start()��ɬ�פʥڡ���������Τ�
	�����ȥ����Ȥ��Ƥ���
	if($ret == "" && !headers_sent()) {
*/
	if($ret == "") {
		/* ���å����ѥ�᡼���λ���
		 ���֥饦�����Ĥ���ޤ�ͭ��
		 �����٤ƤΥѥ���ͭ��
		 ��Ʊ���ɥᥤ��֤Ƕ�ͭ */
		session_set_cookie_params (0, "/", DOMAIN_NAME);

		if(!ini_get("session.auto_start")){
			// ���å���󳫻�
			session_start();
		}
	}
}

/* ʸ����˶���Ū�˲��Ԥ������ */
function sfPutBR($str, $size) {
	$i = 0;
	$cnt = 0;
	$line = array();
	$ret = "";
	
	while($str[$i] != "") {
		$line[$cnt].=$str[$i];
		$i++;
		if(strlen($line[$cnt]) > $size) {
			$line[$cnt].="<br />";
			$cnt++;
		}
	}
	
	foreach($line as $val) {
		$ret.=$val;
	}
	return $ret;
}

// ���ʾ巫���֤���Ƥ��륹��å���[/]���Ĥ��Ѵ����롣
function sfRmDupSlash($istr){
	if(ereg("^http://", $istr)) {
		$str = substr($istr, 7);
		$head = "http://";
	} else if(ereg("^https://", $istr)) {
		$str = substr($istr, 8);
		$head = "https://";
	} else {
		$str = $istr;
	}
	$str = ereg_replace("[/]+", "/", $str);
	$ret = $head . $str;
	return $ret;	
}

function sfEncodeFile($filepath, $enc_type, $out_dir) {
	$ifp = fopen($filepath, "r");
	
	$basename = basename($filepath);
	$outpath = $out_dir . "enc_" . $basename;
	
	$ofp = fopen($outpath, "w+");
	
	while(!feof($ifp)) {
		$line = fgets($ifp);
		$line = mb_convert_encoding($line, $enc_type, "auto");
		fwrite($ofp,  $line);
	}
	
	fclose($ofp);
	fclose($ifp);
	
	return 	$outpath;
}

function sfCutString($str, $len, $byte = true, $commadisp = true) {
	if($byte) {
		if(strlen($str) > ($len + 2)) {
			$ret =substr($str, 0, $len);
			$cut = substr($str, $len);
		} else {
			$ret = $str;
			$commadisp = false;
		}
	} else {
		if(mb_strlen($str) > ($len + 1)) {
			$ret = mb_substr($str, 0, $len);
			$cut = mb_substr($str, $len);
		} else {
			$ret = $str;
			$commadisp = false;
		}
	}

	// ��ʸ�������������ʬ�Ǥ���ʤ��褦�ˤ��롣
	if (isset($cut)) {
		// ʬ����֤�����κǸ�� [ �ʹߤ�������롣
		$head = strrchr($ret, '[');

		// ʬ����֤���κǽ�� ] ������������롣
		$tail_pos = strpos($cut, ']');
		if ($tail_pos !== false) {
			$tail = substr($cut, 0, $tail_pos + 1);
		}

		// ʬ����֤������ [����� ] �����Ĥ��ä����ϡ�[ ���� ] �ޤǤ�
		// ��³���Ƴ�ʸ������1��ʬ�ˤʤ뤫�ɤ���������å����롣
		if ($head !== false && $tail_pos !== false) {
			$subject = $head . $tail;
			if (preg_match('/^\[emoji:e?\d+\]$/', $subject)) {
				// ��ʸ�����������Ĥ��ä��ΤǺ�����롣
				$ret = substr($ret, 0, -strlen($head));
			}
		}
	}

	if($commadisp){
		$ret = $ret . "...";
	}
	return $ret;
}

// ǯ������������顢����������+1�����������������롣
function sfTermMonth($year, $month, $close_day) {
	$end_year = $year;
	$end_month = $month;
	
	// ���Ϸ��λ���Ʊ�����ݤ�
	$same_month = false;
	
	// ���������������롣
	$end_last_day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));
	
	// �����������������꾯�ʤ����
	if($end_last_day < $close_day) {
		// ��������������˹�碌��
		$end_day = $end_last_day;
	} else {
		$end_day = $close_day;
	}
	
	// ����μ���
	$tmp_year = date("Y", mktime(0, 0, 0, $month, 0, $year));
	$tmp_month = date("m", mktime(0, 0, 0, $month, 0, $year));
	// �������������롣
	$start_last_day = date("d", mktime(0, 0, 0, $month, 0, $year));
	
	// �������������������꾯�ʤ����
	if ($start_last_day < $close_day) {
		// �������˹�碌��
		$tmp_day = $start_last_day;
	} else {
		$tmp_day = $close_day;
	}
	
	// �����������������������
	$start_year = date("Y", mktime(0, 0, 0, $tmp_month, $tmp_day + 1, $tmp_year));
	$start_month = date("m", mktime(0, 0, 0, $tmp_month, $tmp_day + 1, $tmp_year));
	$start_day = date("d", mktime(0, 0, 0, $tmp_month, $tmp_day + 1, $tmp_year));
	
	// ���դκ���
	$start_date = sprintf("%d/%d/%d 00:00:00", $start_year, $start_month, $start_day);
	$end_date = sprintf("%d/%d/%d 23:59:59", $end_year, $end_month, $end_day);
	
	return array($start_date, $end_date);
}

// PDF�Ѥ�RGB���顼���֤�
function sfGetPdfRgb($hexrgb) {
	$hex = substr($hexrgb, 0, 2);
	$r = hexdec($hex) / 255;
	
	$hex = substr($hexrgb, 2, 2);
	$g = hexdec($hex) / 255;
	
	$hex = substr($hexrgb, 4, 2);
	$b = hexdec($hex) / 255;
	
	return array($r, $g, $b);	
}

//���ޥ�����Ͽ�ȥ᡼���ۿ�
function sfRegistTmpMailData($mail_flag, $email){
	$objQuery = new SC_Query();
	$objConn = new SC_DBConn();
	$objPage = new LC_Page();
	
	$random_id = sfGetUniqRandomId();
	$arrRegistMailMagazine["mail_flag"] = $mail_flag;
	$arrRegistMailMagazine["email"] = $email;
	$arrRegistMailMagazine["temp_id"] =$random_id;
	$arrRegistMailMagazine["end_flag"]='0';
	$arrRegistMailMagazine["update_date"] = 'now()';
	
	//���ޥ�����Ͽ�ѥե饰
	$flag = $objQuery->count("dtb_customer_mail_temp", "email=?", array($email));
	$objConn->query("BEGIN");
	switch ($flag){
		case '0':
		$objConn->autoExecute("dtb_customer_mail_temp",$arrRegistMailMagazine);
		break;
	
		case '1':
		$objConn->autoExecute("dtb_customer_mail_temp",$arrRegistMailMagazine, "email = '" .addslashes($email). "'");
		break;
	}
	$objConn->query("COMMIT");
	$subject = sfMakeSubject('���ޥ�����Ͽ����λ���ޤ�����');
	$objPage->tpl_url = SSL_URL."mailmagazine/regist.php?temp_id=".$arrRegistMailMagazine['temp_id'];
	switch ($mail_flag){
		case '1':
		$objPage->tpl_name = "��Ͽ";
		$objPage->tpl_kindname = "HTML";
		break;
		
		case '2':
		$objPage->tpl_name = "��Ͽ";
		$objPage->tpl_kindname = "�ƥ�����";
		break;
		
		case '3':
		$objPage->tpl_name = "���";
		break;
	}
		$objPage->tpl_email = $email;
	sfSendTplMail($email, $subject, 'mail_templates/mailmagazine_temp.tpl', $objPage);
}

// �Ƶ�Ū��¿������򸡺����ư켡������(Hidden���Ϥ�������)���Ѵ����롣
function sfMakeHiddenArray($arrSrc, $arrDst = array(), $parent_key = "") {
	if(is_array($arrSrc)) {
		foreach($arrSrc as $key => $val) {
			if($parent_key != "") {
				$keyname = $parent_key . "[". $key . "]";
			} else {
				$keyname = $key;
			}
			if(is_array($val)) {
				$arrDst = sfMakeHiddenArray($val, $arrDst, $keyname);
			} else {
				$arrDst[$keyname] = $val;
			}
		}
	}
	return $arrDst;
}

// DB���������򥿥�����Ѵ�
function sfDBDatetoTime($db_date) {
	$date = ereg_replace("\..*$","",$db_date);
	$time = strtotime($date);
	return $time;
}

// ���Ϥκݤ˥ƥ�ץ졼�Ȥ��ڤ��ؤ�����
/*
	index.php?tpl=test.tpl
*/
function sfCustomDisplay($objPage, $is_mobile = false) {
	$basename = basename($_SERVER["REQUEST_URI"]);

	if($basename == "") {
		$path = $_SERVER["REQUEST_URI"] . "index.php";
	} else {
		$path = $_SERVER["REQUEST_URI"];
	}	

	if($_GET['tpl'] != "") {
		$tpl_name = $_GET['tpl'];
	} else {
		$tpl_name = ereg_replace("^/", "", $path);
		$tpl_name = ereg_replace("/", "_", $tpl_name);
		$tpl_name = ereg_replace("(\.php$|\.html$)", ".tpl", $tpl_name);
	}

	$template_path = TEMPLATE_FTP_DIR . $tpl_name;

	if($is_mobile === true) {
		$objView = new SC_MobileView();			
		$objView->assignobj($objPage);
		$objView->display(SITE_FRAME);		
	} else if(file_exists($template_path)) {
		$objView = new SC_UserView(TEMPLATE_FTP_DIR, COMPILE_FTP_DIR);
		$objView->assignobj($objPage);
		$objView->display($tpl_name);
	} else {
		$objView = new SC_SiteView();
		$objView->assignobj($objPage);
		$objView->display(SITE_FRAME);
	}
}

//����Խ���Ͽ����
function sfEditCustomerData($array, $arrRegistColumn) {
	$objQuery = new SC_Query();
	
	foreach ($arrRegistColumn as $data) {
		if ($data["column"] != "password") {
			if($array[ $data['column'] ] != "") {
				$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
			} else {
				$arrRegist[ $data['column'] ] = NULL;
			}
		}
	}
	if (strlen($array["year"]) > 0 && strlen($array["month"]) > 0 && strlen($array["day"]) > 0) {
		$arrRegist["birth"] = $array["year"] ."/". $array["month"] ."/". $array["day"] ." 00:00:00";
	} else {
		$arrRegist["birth"] = NULL;
	}

	//-- �ѥ���ɤι�����������ϰŹ沽���ʹ������ʤ�����UPDATEʸ�������ʤ���
	if ($array["password"] != DEFAULT_PASSWORD) $arrRegist["password"] = sha1($array["password"] . ":" . AUTH_MAGIC); 
	$arrRegist["update_date"] = "NOW()";
	
	//-- �Խ���Ͽ�¹�
	if (defined('MOBILE_SITE')) {
		$arrRegist['email_mobile'] = $arrRegist['email'];
		unset($arrRegist['email']);
	}
	$objQuery->begin();
	$objQuery->update("dtb_customer", $arrRegist, "customer_id = ? ", array($array['customer_id']));
	$objQuery->commit();
}

// PHP��mb_convert_encoding�ؿ���Smarty�Ǥ�Ȥ���褦�ˤ���
function sf_mb_convert_encoding($str, $encode = 'CHAR_CODE') {
	return  mb_convert_encoding($str, $encode);
}	

// PHP��mktime�ؿ���Smarty�Ǥ�Ȥ���褦�ˤ���
function sf_mktime($format, $hour=0, $minute=0, $second=0, $month=1, $day=1, $year=1999) {
	return  date($format,mktime($hour, $minute, $second, $month, $day, $year));
}	

// PHP��date�ؿ���Smarty�Ǥ�Ȥ���褦�ˤ���
function sf_date($format, $timestamp = '') {
	return  date( $format, $timestamp);
}

// �����å��ܥå����η����Ѵ�����
function sfChangeCheckBox($data , $tpl = false){
	if ($tpl) {
		if ($data == 1){
			return 'checked';
		}else{
			return "";
		}
	}else{
		if ($data == "on"){
			return 1;
		}else{
			return 2;
		}
	}
}

function sfCategory_Count($objQuery){
	$sql = "";
	
	//�ơ��֥����Ƥκ��
	$objQuery->query("DELETE FROM dtb_category_count");
	$objQuery->query("DELETE FROM dtb_category_total_count");
	
	//�ƥ��ƥ�����ξ��ʿ�������Ƴ�Ǽ
	$sql = " INSERT INTO dtb_category_count(category_id, product_count, create_date) ";
	$sql .= " SELECT T1.category_id, count(T2.category_id), now() FROM dtb_category AS T1 LEFT JOIN dtb_products AS T2 ";
	$sql .= " ON T1.category_id = T2.category_id  ";
	$sql .= " WHERE T2.del_flg = 0 AND T2.status = 1 ";
	$sql .= " GROUP BY T1.category_id, T2.category_id ";
	$objQuery->query($sql);
	
	//�ҥ��ƥ�����ξ��ʿ��򽸷פ���
	$arrCat = $objQuery->getAll("SELECT * FROM dtb_category");
	
	$sql = "";
	foreach($arrCat as $key => $val){
		
		// ��ID���������
		$arrRet = sfGetChildrenArray('dtb_category', 'parent_category_id', 'category_id', $val['category_id']);	
		$line = sfGetCommaList($arrRet);
		
		$sql = " INSERT INTO dtb_category_total_count(category_id, product_count, create_date) ";
		$sql .= " SELECT ?, SUM(product_count), now() FROM dtb_category_count ";
		$sql .= " WHERE category_id IN (" . $line . ")";
				
		$objQuery->query($sql, array($val['category_id']));
	}
}

// 2�Ĥ�������Ѥ���Ϣ��������������
function sfarrCombine($arrKeys, $arrValues) {

	if(count($arrKeys) <= 0 and count($arrValues) <= 0) return array();
	
    $keys = array_values($arrKeys);
    $vals = array_values($arrValues); 
	
    $max = max( count( $keys ), count( $vals ) ); 
    $combine_ary = array(); 
    for($i=0; $i<$max; $i++) { 
        $combine_ary[$keys[$i]] = $vals[$i]; 
    } 
    if(is_array($combine_ary)) return $combine_ary; 
    
	return false; 
}

/* ���ع�¤�Υơ��֥뤫���ID������������ */
function sfGetChildrenArray($table, $pid_name, $id_name, $id) {
	$objQuery = new SC_Query();
	$col = $pid_name . "," . $id_name;
 	$arrData = $objQuery->select($col, $table);
	
	$arrPID = array();
	$arrPID[] = $id;
	$arrChildren = array();
	$arrChildren[] = $id;
	
	$arrRet = sfGetChildrenArraySub($arrData, $pid_name, $id_name, $arrPID);
	
	while(count($arrRet) > 0) {
		$arrChildren = array_merge($arrChildren, $arrRet);
		$arrRet = sfGetChildrenArraySub($arrData, $pid_name, $id_name, $arrRet);
	}
	
	return $arrChildren;
}

/* ��IDľ���λ�ID�򤹤٤Ƽ������� */
function sfGetChildrenArraySub($arrData, $pid_name, $id_name, $arrPID) {
	$arrChildren = array();
	$max = count($arrData);
	
	for($i = 0; $i < $max; $i++) {
		foreach($arrPID as $val) {
			if($arrData[$i][$pid_name] == $val) {
				$arrChildren[] = $arrData[$i][$id_name];
			}
		}
	}	
	return $arrChildren;
}


/* ���ع�¤�Υơ��֥뤫���ID������������ */
function sfGetParentsArray($table, $pid_name, $id_name, $id) {
	$objQuery = new SC_Query();
	$col = $pid_name . "," . $id_name;
 	$arrData = $objQuery->select($col, $table);
	
	$arrParents = array();
	$arrParents[] = $id;
	$child = $id;
	
	$ret = sfGetParentsArraySub($arrData, $pid_name, $id_name, $child);

	while($ret != "") {
		$arrParents[] = $ret;
		$ret = sfGetParentsArraySub($arrData, $pid_name, $id_name, $ret);
	}
	
	$arrParents = array_reverse($arrParents);
	
	return $arrParents;
}

/* ��ID��°�����ID��������� */
function sfGetParentsArraySub($arrData, $pid_name, $id_name, $child) {
	$max = count($arrData);
	$parent = "";
	for($i = 0; $i < $max; $i++) {
		if($arrData[$i][$id_name] == $child) {
			$parent = $arrData[$i][$pid_name];
			break;
		}
	}
	return $parent;
}

/* ���ع�¤�Υơ��֥뤫��Ϳ����줿ID�η����������� */
function sfGetBrothersArray($arrData, $pid_name, $id_name, $arrPID) {
	$max = count($arrData);
	
	$arrBrothers = array();
	foreach($arrPID as $id) {
		// ��ID�򸡺�����
		for($i = 0; $i < $max; $i++) {
			if($arrData[$i][$id_name] == $id) {
				$parent = $arrData[$i][$pid_name];
				break;
			}
		}
		// ����ID�򸡺�����
		for($i = 0; $i < $max; $i++) {
			if($arrData[$i][$pid_name] == $parent) {
				$arrBrothers[] = $arrData[$i][$id_name];
			}
		}					
	}
	return $arrBrothers;
}

/* ���ع�¤�Υơ��֥뤫��Ϳ����줿ID��ľ°�λҤ�������� */
function sfGetUnderChildrenArray($arrData, $pid_name, $id_name, $parent) {
	$max = count($arrData);
	
	$arrChildren = array();
	// ��ID�򸡺�����
	for($i = 0; $i < $max; $i++) {
		if($arrData[$i][$pid_name] == $parent) {
			$arrChildren[] = $arrData[$i][$id_name];
		}
	}					
	return $arrChildren;
}


// ���ƥ���ĥ꡼�μ���
function sfGetCatTree($parent_category_id, $count_check = false) {
	$objQuery = new SC_Query();
	$col = "";
	$col .= " cat.category_id,";
	$col .= " cat.category_name,";
	$col .= " cat.parent_category_id,";
	$col .= " cat.level,";
	$col .= " cat.rank,";
	$col .= " cat.creator_id,";
	$col .= " cat.create_date,";
	$col .= " cat.update_date,";
	$col .= " cat.del_flg, ";
	$col .= " ttl.product_count";	
	$from = "dtb_category as cat left join dtb_category_total_count as ttl on ttl.category_id = cat.category_id";
	// ��Ͽ���ʿ��Υ����å�
	if($count_check) {
		$where = "del_flg = 0 AND product_count > 0";
	} else {
		$where = "del_flg = 0";
	}
	$objQuery->setoption("ORDER BY rank DESC");
	$arrRet = $objQuery->select($col, $from, $where);
	
	$arrParentID = sfGetParents($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $parent_category_id);
	
	foreach($arrRet as $key => $array) {
		foreach($arrParentID as $val) {
			if($array['category_id'] == $val) {
				$arrRet[$key]['display'] = 1;
				break;
			}
		}
	}

	return $arrRet;
}

// �ƥ��ƥ��꡼��Ϣ�뤷��ʸ������������
function sfGetCatCombName($category_id){
	// ���ʤ�°���륫�ƥ���ID��Ĥ˼���
	$objQuery = new SC_Query();
	$arrCatID = sfGetParents($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);	
	$ConbName = "";
	
	// ���ƥ��꡼̾�Τ��������
	foreach($arrCatID as $key => $val){
		$sql = "SELECT category_name FROM dtb_category WHERE category_id = ?";
		$arrVal = array($val);
		$CatName = $objQuery->getOne($sql,$arrVal);
		$ConbName .= $CatName . ' | ';
	}
	// �Ǹ�� �� �򥫥åȤ���
	$ConbName = substr_replace($ConbName, "", strlen($ConbName) - 2, 2);
	
	return $ConbName;
}

// ���ꤷ�����ƥ��꡼ID���祫�ƥ��꡼���������
function sfGetFirstCat($category_id){
	// ���ʤ�°���륫�ƥ���ID��Ĥ˼���
	$objQuery = new SC_Query();
	$arrRet = array();
	$arrCatID = sfGetParents($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);	
	$arrRet['id'] = $arrCatID[0];
	
	// ���ƥ��꡼̾�Τ��������
	$sql = "SELECT category_name FROM dtb_category WHERE category_id = ?";
	$arrVal = array($arrRet['id']);
	$arrRet['name'] = $objQuery->getOne($sql,$arrVal);
	
	return $arrRet;
}

//MySQL�Ѥ�SQLʸ���ѹ�����
function sfChangeMySQL($sql){
	// ���ԡ����֤�1���ڡ������Ѵ�
	$sql = preg_replace("/[\r\n\t]/"," ",$sql);
	
	$sql = sfChangeView($sql);		// viewɽ�򥤥�饤��ӥ塼���Ѵ�����
	$sql = sfChangeILIKE($sql);		// ILIKE������LIKE�������Ѵ�����
	$sql = sfChangeRANDOM($sql);	// RANDOM()��RAND()���Ѵ�����

	return $sql;
}

// SQL�����view��¸�ߤ��Ƥ��뤫�����å���Ԥ���
function sfInArray($sql){
	global $arrView;

	foreach($arrView as $key => $val){
		if (strcasecmp($sql, $val) == 0){
			$changesql = eregi_replace("($key)", "$val", $sql);
			sfInArray($changesql);
		}
	}
	return false;
}

// SQL���󥰥륯�������б�
function sfQuoteSmart($in){
	
    if (is_int($in) || is_double($in)) {
        return $in;
    } elseif (is_bool($in)) {
        return $in ? 1 : 0;
    } elseif (is_null($in)) {
        return 'NULL';
    } else {
        return "'" . str_replace("'", "''", $in) . "'";
    }
}
	
// viewɽ�򥤥�饤��ӥ塼���Ѵ�����
function sfChangeView($sql){
	global $arrView;
	global $arrViewWhere;
	
	$arrViewTmp = $arrView;

	// view��where���Ѵ�
	foreach($arrViewTmp as $key => $val){
		$arrViewTmp[$key] = strtr($arrViewTmp[$key], $arrViewWhere);
	}
	
	// view���Ѵ�
	$changesql = strtr($sql, $arrViewTmp);

	return $changesql;
}

// ILIKE������LIKE�������Ѵ�����
function sfChangeILIKE($sql){
	$changesql = eregi_replace("(ILIKE )", "LIKE BINARY ", $sql);
	return $changesql;
}

// RANDOM()��RAND()���Ѵ�����
function sfChangeRANDOM($sql){
	$changesql = eregi_replace("( RANDOM)", " RAND", $sql);
	return $changesql;
}

// view��where���ִ�����
function sfViewWhere($target, $where = "", $arrval = array(), $option = ""){
	global $arrViewWhere;
	$arrWhere = split("[?]", $where);
	$where_tmp = " WHERE " . $arrWhere[0];
	for($i = 1; $i < count($arrWhere); $i++){
		$where_tmp .= sfQuoteSmart($arrval[$i - 1]) . $arrWhere[$i];
	}
	$arrViewWhere[$target] = $where_tmp . " " . $option;
}

// �ǥ��쥯�ȥ�ʲ��Υե������Ƶ�Ū�˥��ԡ�
function sfCopyDir($src, $des, $mess, $override = false){
	if(!is_dir($src)){
		return false;
	}

	$oldmask = umask(0);
	$mod= stat($src);
	
	// �ǥ��쥯�ȥ꤬�ʤ���к�������
	if(!file_exists($des)) {
		if(!mkdir($des, $mod[2])) {
			print("path:" . $des);
		}
	}
	
	$fileArray=glob( $src."*" );
	foreach( $fileArray as $key => $data_ ){
		// CVS�����ե�����ϥ��ԡ����ʤ�
		if(ereg("/CVS/Entries", $data_)) {
			break;
		}
		if(ereg("/CVS/Repository", $data_)) {
			break;
		}
		if(ereg("/CVS/Root", $data_)) {
			break;
		}
		
		mb_ereg("^(.*[\/])(.*)",$data_, $matches);
		$data=$matches[2];
		if( is_dir( $data_ ) ){
			$mess = sfCopyDir( $data_.'/', $des.$data.'/', $mess);
		}else{
			if(!$override && file_exists($des.$data)) {
				$mess.= $des.$data . "���ե����뤬¸�ߤ��ޤ�\n";
			} else {
				if(@copy( $data_, $des.$data)) {
					$mess.= $des.$data . "�����ԡ�����\n";
				} else {
					$mess.= $des.$data . "�����ԡ�����\n";
				}
			}
			$mod=stat($data_ );
		}
	}
	umask($oldmask);
	return $mess;
}

// ���ꤷ���ե������Υե���������ƺ������
function sfDelFile($dir){
	$dh = opendir($dir);
	// �ե������Υե��������
	while($file = readdir($dh)){
		if ($file == "." or $file == "..") continue;
		$del_file = $dir . "/" . $file;
		if(is_file($del_file)){
			$ret = unlink($dir . "/" . $file);
		}else if (is_dir($del_file)){
			$ret = sfDelFile($del_file);
		}
		
		if(!$ret){
			return $ret;
		}
	}
    
    // �Ĥ���
    closedir($dh);
    
	// �ե��������
	return rmdir($dir);
}

/* 
 * �ؿ�̾��sfWriteFile
 * ����1 ���񤭹���ǡ���
 * ����2 ���ե�����ѥ�
 * ����3 ���񤭹��ߥ�����
 * ����4 ���ѡ��ߥå����
 * ����͡���̥ե饰 �����ʤ� true ���Ԥʤ� false
 * ���������ե�����񤭽Ф�
 */
function sfWriteFile($str, $path, $type, $permission = "") {
	//�ե�����򳫤�
	if (!($file = fopen ($path, $type))) {
		return false;
	}

	//�ե������å�
	flock ($file, LOCK_EX);
	//�ե�����ν񤭹���
	fputs ($file, $str);
	//�ե������å��β��
	flock ($file, LOCK_UN);
	//�ե�������Ĥ���
	fclose ($file);
	// ���¤����
	if($permission != "") {
		chmod($path, $permission);
	}
	
	return true;
}
	
function sfFlush($output = " ", $sleep = 0){
	// �¹Ի��֤����¤��ʤ�
	set_time_limit(0);
	// ���Ϥ�Хåե���󥰤��ʤ�(==���ܸ켫ư�Ѵ��⤷�ʤ�)
	ob_end_clean();
	
	// IE�Τ����256�Х��ȶ�ʸ������
	echo str_pad('',256);
	
	// ���Ϥϥ֥�󥯤����Ǥ⤤���Ȼפ�
	echo $output;
	// ���Ϥ�ե�å��夹��
	flush();
	
	ob_end_flush();
	ob_start();	
	
	// ���֤Τ��������
	sleep($sleep);
}

// @version�ε��ܤ�����ե����뤫��С�������������롣
function sfGetFileVersion($path) {
	if(file_exists($path)) {
		$src_fp = fopen($path, "rb");
		if($src_fp) {
			while (!feof($src_fp)) {
				$line = fgets($src_fp);
				if(ereg("@version", $line)) {
					$arrLine = split(" ", $line);
					$version = $arrLine[5];
				}
			}
			fclose($src_fp);
		}
	}
	return $version;
}

// ���ꤷ��URL���Ф���POST�ǥǡ�������������
function sfSendPostData($url, $arrData, $arrOkCode = array()){
	require_once(DATA_PATH . "module/Request.php");
	
	// �������󥹥�������
	$req = new HTTP_Request($url);
	
	$req->addHeader('User-Agent', 'DoCoMo/2.0��P2101V(c100)');
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	
	// POST�ǡ�������
	$req->addPostDataArray($arrData);
	
	// ���顼��̵����С�����������������
	if (!PEAR::isError($req->sendRequest())) {
		
		// �쥹�ݥ󥹥����ɤ����顼Ƚ��ʤ顢�����֤�
		$res_code = $req->getResponseCode();
		
		if(!in_array($res_code, $arrOkCode)){
			$response = "";
		}else{
			$response = $req->getResponseBody();
		}
		
	} else {
		$response = "";
	}
	
	// POST�ǡ������ꥢ
	$req->clearPostData();	
	
	return $response;
}

/** �ǥХå��Ѵؿ� **/

/**
 *  �Ϥ��줿�ѿ���Dump����
 *
 *  @param  mixed  $obj   Dump�������ѿ�
 *  @param  string $color ���Ϥ��뿧
 *  
 *  @return void �ʤ�
 */
function sfPrintR($obj, $color='green') {
    if ( DEBUG_MODE === false ) {
        return;
    }
    
    $arrColor = array(
        'green' => '#00FF00',
        'red'   => '#FF0000',
        'blue'  => '#0000FF'
    );
    
    if ( empty($arrColor[$color]) ) {
        $color = $arrColor['green'];
    } else {
        $color = $arrColor[$color];
    }
    
    print("<div style='font-size: 12px;color: $color;'>\n");
    print("<strong>**�ǥХå���**</strong><br />\n");
    print("<pre>\n");
    print_r($obj);
    print("</pre>\n");
    print("<strong>**�ǥХå���**</strong></div>\n");
}

?>
