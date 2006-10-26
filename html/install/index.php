<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
$INSTALL_DIR = realpath(dirname( __FILE__));

class LC_Page {
	function LC_Page() {
		$this->arrDB_TYPE = array(
			'pgsql' => 'PostgreSQL',
			'mysql' => 'MySQL'	
		);
		$this->arrDB_PORT = array(
			'pgsql' => '',
			'mysql' => ''	
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
	$dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].":".$arrRet['db_port']."/".$arrRet['db_name'];
	
	/*
		lfAddTable�ϡ��С�����󥢥å������ɲåơ��֥뤬ȯ���������˼¹Ԥ��롣
		�ʣģ¹����β��̸ߴ��Τ��᥹���å׻��⶯����
	*/
	// �ơ��֥뤬¸�ߤ��ʤ������ɲä���롣
	$objPage->arrErr = lfAddTable("dtb_session", $dsn);	// ���å��������ơ��֥�
		
	if(count($objPage->arrErr) == 0) {
		// �����åפ�����ˤϼ����̤�����
		$skip = $_POST["db_skip"];
		if ($skip == "on") {
			// ����ե����������
			lfMakeConfigFile();
			//$objPage = lfDispComplete($objPage);
			$objPage = lfDispStep4($objPage);
			break;
		}
	}
	
	// �ơ��֥�κ���
	$objPage->arrErr = lfExecuteSQL("./sql/create_table_".$arrRet['db_type'].".sql", $dsn); 
	if(count($objPage->arrErr) == 0) {
		$objPage->tpl_message.="�����ơ��֥�κ������������ޤ�����<br>";
	} else {
		$objPage->tpl_message.="�ߡ��ơ��֥�κ����˼��Ԥ��ޤ�����<br>";		
	}

	// �ӥ塼�κ���
	if(count($objPage->arrErr) == 0 and $arrRet['db_type'] == 'pgsql') {
		// �ӥ塼�κ���
		$objPage->arrErr = lfExecuteSQL("./sql/create_view.sql", $dsn); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="�����ӥ塼�κ������������ޤ�����<br>";
		} else {
			$objPage->tpl_message.="�ߡ��ӥ塼�κ����˼��Ԥ��ޤ�����<br>";		
		}
	}	
	
	// ����ǡ����κ���
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./sql/insert_data.sql", $dsn); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="��������ǡ����κ������������ޤ�����<br>";
		} else {
			$objPage->tpl_message.="�ߡ�����ǡ����κ����˼��Ԥ��ޤ�����<br>";		
		}
	}	
	
	// ����ॳ���Ȥν����
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./sql/column_comment.sql", $dsn); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="��������ॳ���Ȥν���ߤ��������ޤ�����<br>";
		} else {
			$objPage->tpl_message.="�ߡ�����ॳ���Ȥν���ߤ˼��Ԥ��ޤ�����<br>";		
		}
	}	
	
	// �ơ��֥륳���Ȥν����
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./sql/table_comment.sql", $dsn); 
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
case 'step4':
	$objPage = lfDispStep4($objPage);
	break;
	
// �ơ��֥�����
case 'drop':
	// ���ϥǡ������Ϥ���
	$arrRet =  $objDBParam->getHashArray();
	$dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].":".$arrRet['db_port']."/".$arrRet['db_name'];
		
	if ($arrRet['db_type'] == 'pgsql'){
		// �ӥ塼�κ��
		$objPage->arrErr = lfExecuteSQL("./sql/drop_view.sql", $dsn, false); 
		if(count($objPage->arrErr) == 0) {
			$objPage->tpl_message.="�����ӥ塼�κ�����������ޤ�����<br>";
		} else {
			$objPage->tpl_message.="�ߡ��ӥ塼�κ���˼��Ԥ��ޤ�����<br>";		
		}
	}

	// �ơ��֥�κ��
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfExecuteSQL("./sql/drop_table.sql", $dsn, false); 
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
	
	$dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].":".$arrRet['db_port']."/".$arrRet['db_name'];
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
case 'return_step3':
	$objPage = lfDispStep3($objPage);
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
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
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
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'step0.tpl';
	$objPage->tpl_mode = 'step0';
	
	// �ץ����ǽ���ߤ����ե����롦�ǥ��쥯�ȥ�
	$arrWriteFile = array(
		"../../data/install.inc",
		"../user_data",
		"../upload",
		"../../data/Smarty/templates_c",		
		"../../data/logs",
	);
	
	$mess = "";
	$err_file = false;
	foreach($arrWriteFile as $val) {
		if(file_exists($val)) {
			$mode = lfGetFileMode($val);
			$real_path = realpath($val);
						
			// �ǥ��쥯�ȥ�ξ��
			if(is_dir($val)) {
				if($mode == "777") {
					$mess.= ">> ����$real_path($mode) <br>�����������¤�����Ǥ���<br>";					
				} else {
					$mess.= ">> �ߡ�$real_path($mode) <br>�桼������߸���(777)����Ϳ���Ʋ�������<br>";
					$err_file = true;										
				}
			} else {
				if($mode == "666") {
					$mess.= ">> ����$real_path($mode) <br>�����������¤�����Ǥ���<br>";					
				} else {
					$mess.= ">> �ߡ�$real_path($mode) <br>�桼������߸���(666)����Ϳ���Ʋ�������<br>";
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
		$path = "../upload/save_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../upload/temp_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../upload/graph_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../upload/csv";
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
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'step0_1.tpl';
	$objPage->tpl_mode = 'step0_1';
	// �ե����륳�ԡ�
	$objPage->copy_mess = sfCopyDir("./user_data/", "../user_data/", $objPage->copy_mess);
	$objPage->copy_mess = sfCopyDir("./save_image/", "../upload/save_image/", $objPage->copy_mess);	
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
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'step1.tpl';
	$objPage->tpl_mode = 'step1';
	return $objPage;
}

// STEP2���̤�ɽ��
function lfDispStep2($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = $objWebParam->getHashArray();
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
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
	$objPage->tpl_db_skip = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'step3.tpl';
	$objPage->tpl_mode = 'step3';
	return $objPage;
}

// STEP4���̤�ɽ��
function lfDispStep4($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	
	sfprintr($objPage->arrHidden);
	
	$normal_url = $objWebParam->getValue('normal_url');
	// ������'/'��Ĥ���
	if (!ereg("/$", $normal_url)) $normal_url = $normal_url . "/";
	
	$arrDbParam = $objDBParam->getHashArray();
	$dsn = $arrDbParam['db_type']."://".$arrDbParam['db_user'].":".$arrDbParam['db_password']."@".$arrDbParam['db_server'].":".$arrDbParam['db_port']."/".$arrDbParam['db_name'];
	
	$objPage->tpl_site_url = $normal_url;
	$objPage->tpl_shop_name = $objWebParam->getValue('shop_name');
	$objPage->tpl_cube_ver = ECCUBE_VERSION;
	$objPage->tpl_php_ver = phpversion();
	$objPage->tpl_db_ver = sfGetDBVersion($dsn);

	$objPage->tpl_mainpage = 'step4.tpl';
	$objPage->tpl_mode = 'complete';
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
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->tpl_mainpage = 'complete.tpl';
	$objPage->tpl_mode = 'complete';
	
	$secure_url = $objWebParam->getValue('secure_url');
	// ������'/'��Ĥ���
	if (!ereg("/$", $secure_url)) {
		$secure_url = $secure_url . "/";
	}
	$objPage->tpl_sslurl = $secure_url;		
	return $objPage;
}

// WEB�ѥ�᡼������ν����
function lfInitWebParam($objWebParam) {
	
	if(defined('HTML_PATH')) {
		$install_dir = HTML_PATH;
	} else {
		$install_dir = realpath(dirname( __FILE__) . "/../") . "/";
	}
	
	if(defined('SITE_URL')) {
		$normal_url = SITE_URL;
	} else {
		$dir = ereg_replace("install/.*$", "", $_SERVER['REQUEST_URI']);
		$normal_url = "http://" . $_SERVER['HTTP_HOST'] . $dir;
	}
	
	if(defined('SSL_URL')) {
		$secure_url = SSL_URL;
	} else {
		$dir = ereg_replace("install/.*$", "", $_SERVER['REQUEST_URI']);
		$secure_url = "http://" . $_SERVER['HTTP_HOST'] . $dir;
	}

	// Ź̾�������ԥ᡼�륢�ɥ쥹��������롣(�ƥ��󥹥ȡ����)
	if(defined('DEFAULT_DSN')) {
		$ret = sfTabaleExists("dtb_baseinfo", DEFAULT_DSN);
		if($ret) {
			$objQuery = new SC_Query();
			$arrRet = $objQuery->select("shop_name, email01", "dtb_baseinfo");
			$shop_name = $arrRet[0]['shop_name'];
			$admin_mail = $arrRet[0]['email01'];
		}
	}

	$objWebParam->addParam("Ź̾", "shop_name", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $shop_name);
	$objWebParam->addParam("�����ԥ᡼�륢�ɥ쥹", "admin_mail", MTEXT_LEN, "", array("EXIST_CHECK","EMAIL_CHECK","EMAIL_CHAR_CHECK","MAX_LENGTH_CHECK"), $admin_mail);
	$objWebParam->addParam("���󥹥ȡ���ǥ��쥯�ȥ�", "install_dir", MTEXT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $install_dir);
	$objWebParam->addParam("URL(�̾�)", "normal_url", MTEXT_LEN, "", array("EXIST_CHECK","URL_CHECK","MAX_LENGTH_CHECK"), $normal_url);
	$objWebParam->addParam("URL(�����奢)", "secure_url", MTEXT_LEN, "", array("EXIST_CHECK","URL_CHECK","MAX_LENGTH_CHECK"), $secure_url);
	$objWebParam->addParam("�ɥᥤ��", "domain", MTEXT_LEN, "", array("MAX_LENGTH_CHECK"));	

	return $objWebParam;
}

// DB�ѥ�᡼������ν����
function lfInitDBParam($objDBParam) {
		
	if(defined('DB_SERVER')) {
		$db_server = DB_SERVER;
	} else {
		$db_server = "127.0.0.1";
	}
	
	if(defined('DB_TYPE')) {
		$db_type = DB_TYPE;
	} else {
		$db_type = "";
	}
	
	if(defined('DB_PORT')) {
		$db_port = DB_PORT;
	} else {
		$db_port = "";
	}
		
	if(defined('DB_NAME')) {
		$db_name = DB_NAME;
	} else {
		$db_name = "eccube_db";
	}
		
	if(defined('DB_USER')) {
		$db_user = DB_USER;
	} else {
		$db_user = "eccube_db_user";				
	}
			
	$objDBParam->addParam("DB�μ���", "db_type", INT_LEN, "", array("EXIST_CHECK","MAX_LENGTH_CHECK"), $db_type);
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
	
	// �ǥ��쥯�ȥ�̾�Τ߼�������
	$normal_dir = ereg_replace("^https?://[a-zA-Z0-9_~=&\?\.\-]+", "", $arrRet['normal_url']);
	$secure_dir = ereg_replace("^https?://[a-zA-Z0-9_~=&\?\.\-]+", "", $arrRet['secure_url']);
	
	if($normal_dir != $secure_dir) {
		$objErr->arrErr['normal_url'] = "URL�˰ۤʤ볬�ؤ���ꤹ�뤳�ȤϤǤ��ޤ���";
		$objErr->arrErr['secure_url'] = "URL�˰ۤʤ볬�ؤ���ꤹ�뤳�ȤϤǤ��ޤ���";		
	}
	
	return $objErr->arrErr;
}

// �������ƤΥ����å�
function lfCheckDBError($objFormParam) {
	global $objPage;
	
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	if(count($objErr->arrErr) == 0) {
		// ��³��ǧ
		$dsn = $arrRet['db_type']."://".$arrRet['db_user'].":".$arrRet['db_password']."@".$arrRet['db_server'].":".$arrRet['db_port']."/".$arrRet['db_name'];
		// Debug�⡼�ɻ���
		$options['debug'] = PEAR_DB_DEBUG;
		$objDB = DB::connect($dsn, $options);
		// ��³����
		if(!PEAR::isError($objDB)) {
			// �ǡ����١����С���������μ���
			$objPage->tpl_db_version = sfGetDBVersion($dsn);			
		} else {
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
function lfExecuteSQL($filepath, $dsn, $disp_err = true) {
	$arrErr = array();
	
	if(!file_exists($filepath)) {
		$arrErr['all'] = ">> ������ץȥե����뤬���Ĥ���ޤ���";
	} else {
  		if($fp = fopen($filepath,"r")) {
			$sql = fread($fp, filesize($filepath));
			fclose($fp);
		}
		// Debug�⡼�ɻ���
		$options['debug'] = PEAR_DB_DEBUG;
		$objDB = DB::connect($dsn, $options);
		// ��³���顼
		if(!PEAR::isError($objDB)) {
			// ���ԡ����֤�1���ڡ������Ѵ�
			$sql = preg_replace("/[\r\n\t]/"," ",$sql);
			$sql_split = split(";",$sql);
			foreach($sql_split as $key => $val){
				if (trim($val) != "") {
					$ret = $objDB->query($val);
					if(PEAR::isError($ret) && $disp_err) {
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
		
	$root_dir = $objWebParam->getValue('install_dir');
	// ������'/'��Ĥ���
	if (!ereg("/$", $root_dir)) {
		$root_dir = $root_dir . "/";
	}
	
	$normal_url = $objWebParam->getValue('normal_url');
	// ������'/'��Ĥ���
	if (!ereg("/$", $normal_url)) {
		$normal_url = $normal_url . "/";
	}
	
	$secure_url = $objWebParam->getValue('secure_url');
	// ������'/'��Ĥ���
	if (!ereg("/$", $secure_url)) {
		$secure_url = $secure_url . "/";
	}
	
	// �ǥ��쥯�ȥ�μ���
	$url_dir = ereg_replace("^https?://[a-zA-Z0-9_~=&\?\.\-]+", "", $normal_url);
	
	$data_path = $root_dir . "../data/";
	$filepath = $data_path . "install.inc";
	
	$config_data = 
	"<?php\n".
	"    define ('ECCUBE_INSTALL', 'ON');\n" .
	"    define ('HTML_PATH', '" . $root_dir . "');\n" .	 
	"    define ('SITE_URL', '" . $normal_url . "');\n" .
	"    define ('SSL_URL', '" . $secure_url . "');\n" .
	"    define ('URL_DIR', '" . $url_dir . "');\n" .	
	"    define ('DOMAIN_NAME', '" . $objWebParam->getValue('domain') . "');\n" .
	"    define ('DB_TYPE', '" . $objDBParam->getValue('db_type') . "');\n" .
	"    define ('DB_USER', '" . $objDBParam->getValue('db_user') . "');\n" . 
	"    define ('DB_PASSWORD', '" . $objDBParam->getValue('db_password') . "');\n" .
	"    define ('DB_SERVER', '" . $objDBParam->getValue('db_server') . "');\n" .
	"    define ('DB_NAME', '" . $objDBParam->getValue('db_name') . "');\n" .
	"    define ('DB_PORT', '" . $objDBParam->getValue('db_port') .  "');\n" .
	"    define ('DATA_PATH', '".$data_path."');\n" .
	"?>";
	
	if($fp = fopen($filepath,"w")) {
		fwrite($fp, $config_data);
		fclose($fp);
	}
}

// �ơ��֥���ɲáʴ��˥ơ��֥뤬¸�ߤ�����Ϻ������ʤ���
function lfAddTable($table_name, $dsn) {
	$arrErr = array();
	if(!sfTabaleExists($table_name, $dsn)) {
		list($db_type) = split(":", $dsn);
		$sql_path = "./sql/add/". $table_name . "_" .$db_type .".sql";
		$arrErr = lfExecuteSQL($sql_path, $dsn);
	}
	return $arrErr;
}
?>