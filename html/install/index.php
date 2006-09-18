<?php
require_once("../require.php");
$INSTALL_DIR = realpath(dirname( __FILE__));

class LC_Page {
	function LC_Page() {
		$this->arrDB_TYPE = array(
			'pgsql' => 'PostgreSQL',
			'mysql' => 'mySQL'	
		);
	}
}

$objPage = new LC_Page();

// �ƥ�ץ졼�ȥ���ѥ���ǥ��쥯�ȥ�ν���߸��¥����å�
$temp_dir = $INSTALL_DIR . '/temp';
$mode = lfGetFileMode($temp_dir);

if($mode != '777') {
	sfErrorHeader($temp_dir . "�˥桼������߸���(777)����Ϳ���Ʋ�������", true);
	exit;
}

$objView = new SC_InstallView($INSTALL_DIR . '/templates', $INSTALL_DIR . '/temp');

// �ѥ�᡼���������饹
$objWebParam = new SC_FormParam();
$objDBParam = new SC_FormParam();
// �ѥ�᡼������ν����
$objWebParam = lfInitWebParam($objWebParam);
$objDBParam = lfInitDBParam($objDBParam);

//�ե���������μ���
$objWebParam->setParam($_POST);
$objDBParam->setParam($_POST);

switch($_POST['mode']) {
// �褦����
case 'welcome':
	$objPage = lfDispStep0($objPage);
	break;
// �����������¤Υ����å�
case 'step0':
	$objPage = lfDispStep0_1($objPage);
	break;	
// �ե�����Υ��ԡ�
case 'step0_1':
	$objPage = lfDispStep1($objPage);
	break;	
// WEB�����Ȥ�����
case 'step1':
	//�����ͤΥ��顼�����å�
	$objPage->arrErr = lfCheckWEBError($objWebParam);
	if(count($objPage->arrErr) == 0) {
		$objPage = lfDispStep2($objPage);
	} else {
		$objPage = lfDispStep1($objPage);
	}
	break;
// �ǡ����١���������
case 'step2':
	//�����ͤΥ��顼�����å�
	if ($_POST['db_type'] == 'pgsql') {
		$_POST['db_port'] = "";
	}else{
		$_POST['db_port'] = ":".$_POST['db_port'];
		$objDBParam->setValue("db_port", $_POST['db_port']);
	}
	
	$objPage->arrErr = lfCheckDBError($objDBParam);
	
	if(count($objPage->arrErr) == 0) {
		$objPage = lfDispStep3($objPage);
	} else {
		$objPage = lfDispStep2($objPage);
	}
	break;
// �ơ��֥�κ���
case 'step3':
	// ���ϥǡ������Ϥ���
	$arrRet =  $objDBParam->getHashArray();
	// �ơ��֥�κ���
	$objPage->arrErr = lfExecuteSQL("./create_table_mysql.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port']); 
	if(count($objPage->arrErr) == 0) {
		$objPage->tpl_message.="�����ơ��֥�κ������������ޤ�����<br>";
	} else {
		$objPage->tpl_message.="�ߡ��ơ��֥�κ����˼��Ԥ��ޤ�����<br>";		
	}

	// �ӥ塼�κ���
	if(count($objPage->arrErr) == 0 and $arrRet['db_type'] == 'pgsql') {
		// �ӥ塼�κ���
		$objPage->arrErr = lfExecuteSQL("./create_view.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port']); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="�����ӥ塼�κ������������ޤ�����<br>";
		} else {
			$objPage->tpl_message.="�ߡ��ӥ塼�κ����˼��Ԥ��ޤ�����<br>";		
		}
	}	
	
	// ����ǡ����κ���
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./insert_data.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port']); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="��������ǡ����κ������������ޤ�����<br>";
		} else {
			$objPage->tpl_message.="�ߡ�����ǡ����κ����˼��Ԥ��ޤ�����<br>";		
		}
	}	
	
	// ����ॳ���Ȥν����
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./column_comment.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port']); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="��������ॳ���Ȥν���ߤ��������ޤ�����<br>";
		} else {
			$objPage->tpl_message.="�ߡ�����ॳ���Ȥν���ߤ˼��Ԥ��ޤ�����<br>";		
		}
	}	
	
	// �ơ��֥륳���Ȥν����
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./table_comment.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port']); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="�����ơ��֥륳���Ȥν���ߤ��������ޤ�����<br>";
		} else {
			$objPage->tpl_message.="�ߡ��ơ��֥륳���Ȥν���ߤ˼��Ԥ��ޤ�����<br>";		
		}
	}
	
	if(count($objPage->arrErr) == 0) {
		// ����ե����������
		lfMakeConfigFile();
		$objPage = lfDispStep3($objPage);
		$objPage->tpl_mode = 'complete';
	} else {
		$objPage = lfDispStep3($objPage);
	}
	break;
// �ơ��֥�����
case 'drop':
	// ���ϥǡ������Ϥ���
	$arrRet =  $objDBParam->getHashArray();
	
	if ($arrRet['db_type'] == 'pgsql'){
		// �ӥ塼�κ��
		$objPage->arrErr = lfExecuteSQL("./drop_view.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port'], false); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="�����ӥ塼�κ�����������ޤ�����<br>";
		} else {
			$objPage->tpl_message.="�ߡ��ӥ塼�κ���˼��Ԥ��ޤ�����<br>";		
		}
	}


	// �ơ��֥�κ��
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./drop_table.sql", $arrRet['db_user'], $arrRet['db_password'], $arrRet['db_server'], $arrRet['db_name'], $arrRet['db_type'], $arrRet['db_port'], false); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="�����ơ��֥�κ�����������ޤ�����<br>";
		} else {
			$objPage->tpl_message.="�ߡ��ơ��֥�κ���˼��Ԥ��ޤ�����<br>";		
		}
	}
	$objPage = lfDispStep3($objPage);
	break;
// ��λ����
case 'complete':
	// ����åץޥ�������ν񤭹���
	$arrRet =  $objDBParam->getHashArray();
	$dsn = "pgsql://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server']."/".$arrRet['db_name'];
	$sqlval['shop_name'] = $objWebParam->getValue('shop_name');
	$sqlval['email01'] = $objWebParam->getValue('admin_mail');
	$sqlval['email02'] = $objWebParam->getValue('admin_mail');
	$sqlval['email03'] = $objWebParam->getValue('admin_mail');
	$sqlval['email04'] = $objWebParam->getValue('admin_mail');
	$sqlval['email05'] = $objWebParam->getValue('admin_mail');
	$sqlval['top_tpl'] = "default1";
	$sqlval['product_tpl'] = "default1";
	$sqlval['detail_tpl'] = "default1";
	$sqlval['mypage_tpl'] = "default1";
	$objQuery = new SC_Query($dsn);
	$cnt = $objQuery->count("dtb_baseinfo");
	if($cnt > 0) {
		$objQuery->update("dtb_baseinfo", $sqlval);
	} else {
		$objQuery->insert("dtb_baseinfo", $sqlval);		
	}
	global $GLOBAL_ERR;
	$GLOBAL_ERR = "";
	$objPage = lfDispComplete($objPage);
	break;
case 'return_step0':
	$objPage = lfDispStep0($objPage);
	break;	
case 'return_step1':
	$objPage = lfDispStep1($objPage);
	break;
case 'return_step2':
	$objPage = lfDispStep2($objPage);
	break;
case 'return_welcome':
default:
	$objPage = lfDispWelcome($objPage);
	break;
}

//�ե������ѤΥѥ�᡼�����֤�
$objPage->arrForm = $objWebParam->getFormParamList();
$objPage->arrForm = array_merge($objPage->arrForm, $objDBParam->getFormParamList());

// SiteInfo���ɤ߹��ޤʤ�
$objView->assignobj($objPage);
$objView->display('install_frame.tpl');
//-----------------------------------------------------------------------------------------------------------------------------------
// �褦�������̤�ɽ��
function lfDispWelcome($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->tpl_mainpage = 'welcome.tpl';
	$objPage->tpl_mode = 'welcome';
	return $objPage;
}

// STEP0���̤�ɽ��(�ե����븢�¥����å�) 
function lfDispStep0($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->tpl_mainpage = 'step0.tpl';
	$objPage->tpl_mode = 'step0';
	
	// �ץ����ǽ���ߤ����ե����롦�ǥ��쥯�ȥ�
	$arrWriteFile = array(
		"html/install.inc",
		"html/user_data",
		"html/upload",
		"data/Smarty/templates_c",		
		"data/update",
		"data/logs",
	);
	
	$mess = "";
	$err_file = false;
	foreach($arrWriteFile as $val) {
		$path = "../../" . $val;		
		if(file_exists($path)) {
			$mode = lfGetFileMode("../../" . $val);
			
			// �ǥ��쥯�ȥ�ξ��
			if(is_dir($path)) {
				if($mode == "777") {
					$mess.= ">> ����$val($mode) �����ꤢ��ޤ���<br>";					
				} else {
					$mess.= ">> �ߡ�$val($mode) �˥桼������߸���(777)����Ϳ���Ʋ�������<br>";
					$err_file = true;										
				}
			} else {
				if($mode == "666") {
					$mess.= ">> ����$val($mode) �����ꤢ��ޤ���<br>";					
				} else {
					$mess.= ">> �ߡ�$val($mode) �˥桼������߸���(666)����Ϳ���Ʋ�������<br>";
					$err_file = true;							
				}
			}	
			
		} else {
			$mess.= ">> �ߡ�$val �����Ĥ���ޤ���<br>";
			$err_file = true;
		}
	}
	
	// ���¥��顼����ȯ�����Ƥ��ʤ����
	if(!$err_file) {
		$path = "../../data/Smarty/templates_c/admin";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../../html/upload/save_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../../html/upload/temp_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../../html/upload/graph_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../../html/upload/csv";
		if(!file_exists($path)) {
			mkdir($path);
		}
	}
	
	$objPage->mess = $mess;
	$objPage->err_file = $err_file;

	return $objPage;
}


// STEP0_1���̤�ɽ��(�ե�����Υ��ԡ�) 
function lfDispStep0_1($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->tpl_mainpage = 'step0_1.tpl';
	$objPage->tpl_mode = 'step0_1';
	// �ե����륳�ԡ�
	$objPage->copy_mess = lfCopyDir("./user_data/", "../../html/user_data/", $objPage->copy_mess);
	$objPage->copy_mess = lfCopyDir("./save_image/", "../../html/upload/save_image/", $objPage->copy_mess);	
	return $objPage;
}

function lfGetFileMode($path) {
	$mode = substr(sprintf('%o', fileperms($path)), -3);
	return $mode;
}

// STEP1���̤�ɽ��
function lfDispStep1($objPage) {
	global $objDBParam;
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = $objDBParam->getHashArray();
	$objPage->tpl_mainpage = 'step1.tpl';
	$objPage->tpl_mode = 'step1';
	return $objPage;
}

// STEP2���̤�ɽ��
function lfDispStep2($objPage) {
	global $objWebParam;
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = $objWebParam->getHashArray();
	$objPage->tpl_mainpage = 'step2.tpl';
	$objPage->tpl_mode = 'step2';
	return $objPage;
}

// STEP3���̤�ɽ��
function lfDispStep3($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->tpl_mainpage = 'step3.tpl';
	$objPage->tpl_mode = 'step3';
	return $objPage;
}

// ��λ���̤�ɽ��
function lfDispComplete($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->tpl_mainpage = 'complete.tpl';
	$objPage->tpl_mode = 'complete';
	return $objPage;
}

// WEB�ѥ�᡼������ν����
function lfInitWebParam($objWebParam) {
	
	$install_dir = realpath(dirname( __FILE__) . "/../../") . "/";
	$normal_url = "http://" . $_SERVER['HTTP_HOST'] . "/";
	$secure_url = "http://" . $_SERVER['HTTP_HOST'] . "/";
	$domain = ereg_replace("^[a-zA-Z0-9_~=&\?\/-]+\.", "", $_SERVER['HTTP_HOST']);
	$objWebParam->addParam("Ź̾", "shop_name", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objWebParam->addParam("�����ԥ᡼�륢�ɥ쥹", "admin_mail", MTEXT_LEN, "", array("EXIST_CHECK","EMAIL_CHECK","EMAIL_CHAR_CHECK","MAX_LENGTH_CHECK"));
	$objWebParam->addParam("���󥹥ȡ���ǥ��쥯�ȥ�", "install_dir", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $install_dir);
	$objWebParam->addParam("URL(�̾�)", "normal_url", MTEXT_LEN, "", array("EXIST_CHECK","URL_CHECK","MAX_LENGTH_CHECK"), $normal_url);
	$objWebParam->addParam("URL(�����奢)", "secure_url", MTEXT_LEN, "", array("EXIST_CHECK","URL_CHECK","MAX_LENGTH_CHECK"), $secure_url);
	$objWebParam->addParam("�ɥᥤ��", "domain", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $domain);	
	
	return $objWebParam;
}

// WEB�ѥ�᡼������ν����
function lfInitDBParam($objDBParam) {
	
	$db_server = "127.0.0.1";
	$db_port = "3306";
	$db_name = "eccube_db";
	$db_user = "eccube_db_user";
	
	$objDBParam->addParam("DB�μ���", "db_type", INT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objDBParam->addParam("DB������", "db_server", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_server);
	$objDBParam->addParam("DB�ݡ���", "db_port", INT_LEN, "", array("MAX_LENGTH_CHECK"), $db_port);
	$objDBParam->addParam("DB̾", "db_name", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_name);
	$objDBParam->addParam("DB�桼��", "db_user", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_user);
	$objDBParam->addParam("DB�ѥ����", "db_password", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"));	
	return $objDBParam;
}

// �������ƤΥ����å�
function lfCheckWebError($objFormParam) {
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	return $objErr->arrErr;
}

// �������ƤΥ����å�
function lfCheckDBError($objFormParam) {
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	if(count($objErr->arrErr) == 0) {
		// ��³��ǧ
		$dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].$arrRet['db_port']."/".$arrRet['db_name'];
		// Debug�⡼�ɻ���
		$options['debug'] = 3;
		$objDB = DB::connect($dsn, $options);
		// ��³���顼
		if(PEAR::isError($objDB)) {
			$objErr->arrErr['all'] = ">> " . $objDB->message . "<br>";
			// ���顼ʸ���������
			ereg("\[(.*)\]", $objDB->userinfo, $arrKey);
			$objErr->arrErr['all'].= $arrKey[0] . "<br>";
			gfPrintLog($objDB->userinfo, "./temp/install.log");
		}
	}
	return $objErr->arrErr;
}

// SQLʸ�μ¹�
function lfExecuteSQL($filepath, $db_user, $db_password, $db_server, $db_name, $db_type, $db_port, $disp_err = true) {
	$arrErr = array();

	if(!file_exists($filepath)) {
		$arrErr['all'] = ">> ������ץȥե����뤬���Ĥ���ޤ���";
	} else {
  		if($fp = fopen($filepath,"r")) {
			$sql = fread($fp, filesize($filepath));
			fclose($fp);
		}

		$dsn = $db_type."://".$db_user.":".$db_password."@".$db_server.$db_port."/".$db_name;
		
		print($dsn);

		$objDB = DB::connect($dsn);
		// ��³���顼
		if(!PEAR::isError($objDB)) {
			// ���ԡ����֤�1���ڡ������Ѵ�
			$sql = preg_replace("/[\r\n\t]/"," ",$sql);
			$sql_split = split(";",$sql);
			foreach($sql_split as $key => $val){
				if ($val != "") {
					$ret = $objDB->query($val);
					if(PEAR::isError($ret) and $disp_err) {
						$arrErr['all'] = ">> " . $ret->message . "<br>";
						// ���顼ʸ���������
						ereg("\[(.*)\]", $ret->userinfo, $arrKey);
						$arrErr['all'].= $arrKey[0] . "<br>";
						$objPage->update_mess.=">> �ơ��֥빽�����ѹ��˼��Ԥ��ޤ�����<br>";
						gfPrintLog($ret->userinfo, "./temp/install.log");
					}
				}
			}
			
		} else {
			$arrErr['all'] = ">> " . $objDB->message;
			gfPrintLog($objDB->userinfo, "./temp/install.log");
		}
	}
	return $arrErr;
}

// ����ե�����κ���
function lfMakeConfigFile() {
	global $objWebParam;
	global $objDBParam;
	
	$filepath = $objWebParam->getValue('install_dir') . "/html/install.inc";
	$domain = $objWebParam->getValue('domain');
	if(!ereg("^\.", $domain)) {
		$domain = "." . $domain;
	}
	
	$root_dir = $objWebParam->getValue('install_dir');
	if (!ereg("/$", $root_dir)) {
		$root_dir = $root_dir . "/";
	}
	
	$config_data = 
	"<?php\n".
	"    define ('ECCUBE_INSTALL', 'ON');\n" .
	"    define ('ROOT_DIR', '" . $root_dir . "');\n" . 
	"    define ('SITE_URL', '" . $objWebParam->getValue('normal_url') . "');\n" .
	"    define ('SSL_URL', '" . $objWebParam->getValue('secure_url') . "');\n" .
	"    define ('DOMAIN_NAME', '" . $domain . "');\n" .
	"    define ('DB_USER', '" . $objDBParam->getValue('db_user') . "');\n" . 
	"    define ('DB_PASSWORD', '" . $objDBParam->getValue('db_password') . "');\n" .
	"    define ('DB_SERVER', '" . $objDBParam->getValue('db_server') . "');\n" .
	"    define ('DB_NAME', '" . $objDBParam->getValue('db_name') . "');\n" .
	"?>";
	
	if($fp = fopen($filepath,"w")) {
		fwrite($fp, $config_data);
		fclose($fp);
	}
}

// �ǥ��쥯�ȥ�ʲ��Υե������Ƶ�Ū�˥��ԡ�
function lfCopyDir($src, $des, $mess, $override = false){
	if(!is_dir($src)){
		return false;
	}

	$oldmask = umask(0);
	$mod= stat($src);
	
	// �ǥ��쥯�ȥ꤬�ʤ���к�������
	if(!file_exists($des)) {
		mkdir($des, $mod[2]);
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
			$mess = lfCopyDir( $data_.'/', $des.$data.'/', $mess);
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
?>