<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
$INSTALL_DIR = realpath(dirname( __FILE__));
require_once("../" . HTML2DATA_DIR . "module/Request.php");
define("INSTALL_LOG", "./temp/install.log");

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
	//$objPage = lfDispAgreement($objPage);
	$objPage = lfDispStep0($objPage);
	//$objPage->tpl_onload .= "fnChangeVisible('agreement_yes', 'next');";
	break;

/* ������α��

// ���ѵ���������Ʊ��
case 'agreement':
	$objPage = lfDispStep0($objPage);
	break;
*/	
	
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
	$objPage->arrErr = lfAddTable("dtb_session", $dsn);			// ���å��������ơ��֥�
	$objPage->arrErr = lfAddTable("dtb_module", $dsn);			// �⥸�塼������ơ��֥�
	$objPage->arrErr = lfAddTable("dtb_campaign_order", $dsn);	// �����ڡ������ơ��֥�
	$objPage->arrErr = lfAddTable("dtb_mobile_kara_mail", $dsn);	// ���᡼������ơ��֥�
	$objPage->arrErr = lfAddTable("dtb_mobile_ext_session_id", $dsn);	// ���å����ID�����ơ��֥�
	$objPage->arrErr = lfAddTable("dtb_site_control", $dsn);	// �����Ⱦ�������ơ��֥�
	$objPage->arrErr = lfAddTable("dtb_trackback", $dsn);	// �ȥ�å��Хå������ơ��֥�
    $objPage->arrErr = lfAddTable("dtb_blayn", $dsn);   // �֥쥤��IP�����ơ��֥�	
	
	// �������ɲ�
	lfAddColumn($dsn);

	// �ǡ������ɲ�
	lfAddData($dsn);
	
	if(count($objPage->arrErr) == 0) {
		// �����åפ�����ˤϼ����̤�����
		$skip = $_POST["db_skip"];
		if ($skip == "on") {
			// ����ե����������
			lfMakeConfigFile();
			$objPage = lfDispComplete($objPage);
			//$objPage = lfDispStep4($objPage);
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
		$objPage->tpl_mode = 'step4';
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
	
	// �ɲåơ��֥뤬����к�����롣
	lfDropTable("dtb_module", $dsn);
	lfDropTable("dtb_session", $dsn);
	lfDropTable("dtb_campaign_order", $dsn);
	lfDropTable("dtb_mobile_ext_session_id", $dsn);
	lfDropTable("dtb_mobile_kara_mail", $dsn);
	lfDropTable("dtb_site_control", $dsn);
	lfDropTable("dtb_trackback", $dsn);
    lfDropTable("dtb_blayn", $dsn);
			
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

	// ��������Ͽ
	$login_id = $objWebParam->getValue('login_id');
	$login_pass = sha1($objWebParam->getValue('login_pass') . ":" . AUTH_MAGIC);
	
	$sql = "DELETE FROM dtb_member WHERE login_id = ?";
	$objQuery->query($sql, array($login_id));	

	$sql = "INSERT INTO dtb_member (name, login_id, password, creator_id, authority, work, del_flg, rank, create_date, update_date)
			VALUES ('������',?,?,0,0,1,0,1, now(), now());";
	
	$objQuery->query($sql, array($login_id, $login_pass));		
	
	global $GLOBAL_ERR;
	$GLOBAL_ERR = "";
	$objPage = lfDispComplete($objPage);
	
	// �����Ⱦ��������
	$req = new HTTP_Request("http://www.ec-cube.net/mall/use_site.php");
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	
	$arrSendData = array();
	foreach($_POST as $key => $val){
		if (ereg("^senddata_*", $key)){
			$arrSendDataTmp = array(str_replace("senddata_", "", $key) => $val);
			$arrSendData = array_merge($arrSendData, $arrSendDataTmp);
		}
	}
	
	$req->addPostDataArray($arrSendData);
	
	if (!PEAR::isError($req->sendRequest())) {
		$response1 = $req->getResponseBody();
	} else {
		$response1 = "";
	}
	$req->clearPostData();
	
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
case 'return_agreement':
	$objPage = lfDispAgreement($objPage);
	$objPage->tpl_onload .= "fnChangeVisible('agreement_yes', 'next');";
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
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
	$objPage->tpl_mainpage = 'welcome.tpl';
	$objPage->tpl_mode = 'welcome';
	return $objPage;
}

// ���ѵ���������ɽ��
function lfDispAgreement($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->arrHidden['agreement'] = $_POST['agreement'];	
	$objPage->tpl_mainpage = 'agreement.tpl';
	$objPage->tpl_mode = 'agreement';
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
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
	$objPage->tpl_mainpage = 'step0.tpl';
	$objPage->tpl_mode = 'step0';
	
	// �ץ����ǽ���ߤ����ե����롦�ǥ��쥯�ȥ�
	$arrWriteFile = array(
		".." . HTML2DATA_DIR . "install.php",
		"../user_data",
		"../cp",
		"../upload",
		".." . HTML2DATA_DIR . "Smarty/templates_c",
		".." . HTML2DATA_DIR . "downloads",
		".." . HTML2DATA_DIR . "logs"
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
		$path = ".." . HTML2DATA_DIR . "Smarty/templates_c/admin";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = ".." . HTML2DATA_DIR . "Smarty/templates_c/mobile";
		if(!file_exists($path)) {
			mkdir($path); 
		}
		$path = "../upload/temp_template";
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
        $path = "../upload/mobile_image";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = "../upload/csv";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = ".." . HTML2DATA_DIR . "downloads/module";
		if(!file_exists($path)) {
			mkdir($path);
		}
		$path = ".." . HTML2DATA_DIR . "downloads/update";
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
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
	$objPage->tpl_mainpage = 'step0_1.tpl';
	$objPage->tpl_mode = 'step0_1';
	// �ե����륳�ԡ�
	$objPage->copy_mess = sfCopyDir("./user_data/", "../user_data/", $objPage->copy_mess);
	$objPage->copy_mess = sfCopyDir("./save_image/", "../upload/save_image/", $objPage->copy_mess);	
	return $objPage;
}

// STEP0_2���̤�ɽ��(�ե�����Υ��ԡ�) 
function lfDispStep0_2($objPage) {
	global $objWebParam;
	global $objDBParam;
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = $objWebParam->getHashArray();
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	$objPage->arrHidden['db_skip'] = $_POST['db_skip'];
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
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
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
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
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
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
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
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
	$objPage->arrHidden = array_merge($objPage->arrHidden, $objDBParam->getHashArray());
	// hidden�������ͤ��ݻ�
	$objPage->arrHidden['agreement'] = $_POST['agreement'];
	
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
	$objPage->tpl_db_skip = $_POST['db_skip'];
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
	$objWebParam->addParam("�����ԡ��᡼�륢�ɥ쥹", "admin_mail", MTEXT_LEN, "", array("EXIST_CHECK","EMAIL_CHECK","EMAIL_CHAR_CHECK","MAX_LENGTH_CHECK"), $admin_mail);
	$objWebParam->addParam("�����ԡ�������ID", "login_id", MTEXT_LEN, "", array("EXIST_CHECK","EXIST_CHECK", "ALNUM_CHECK"));
	$objWebParam->addParam("�����ԡ��ѥ����", "login_pass", MTEXT_LEN, "", array("EXIST_CHECK","EXIST_CHECK", "ALNUM_CHECK"));
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
	
	// ������ID�����å�
	$objErr->doFunc(array("�����ԡ�������ID",'login_id',ID_MIN_LEN , ID_MAX_LEN) ,array("NUM_RANGE_CHECK"));
	
	// �ѥ���ɤΥ����å�
	$objErr->doFunc( array("�����ԡ��ѥ����",'login_pass',4 ,15 ) ,array( "NUM_RANGE_CHECK" ) );	
	
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
			gfPrintLog($objDB->userinfo, INSTALL_LOG);
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
						gfPrintLog($ret->userinfo, INSTALL_LOG);
					}
				}
			}			
		} else {
			$arrErr['all'] = ">> " . $objDB->message;
			gfPrintLog($objDB->userinfo, INSTALL_LOG);
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
	
	$data_path = sfRmDupSlash($root_dir . HTML2DATA_DIR);
    $data_path = realpath($data_path);
    // ������'/'��Ĥ���
	if (!ereg("/$", $data_path)) {
		$data_path = $data_path . "/";
	}
	$filepath = $data_path . "install.php";
	
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
    "    define ('MOBILE_HTML_PATH', HTML_PATH . 'mobile/');\n" .
    "    define ('MOBILE_SITE_URL', SITE_URL . 'mobile/');\n" .
    "    define ('MOBILE_SSL_URL', SSL_URL . 'mobile/');\n" .
    "    define ('MOBILE_URL_DIR', URL_DIR . 'mobile/');\n" .
	"?>";
	
	if($fp = fopen($filepath,"w")) {
		fwrite($fp, $config_data);
		fclose($fp);
	}
}

// �ơ��֥���ɲáʴ��˥ơ��֥뤬¸�ߤ�����Ϻ������ʤ���
function lfAddTable($table_name, $dsn) {
    global $objPage;
	$arrErr = array();
	if(!sfTabaleExists($table_name, $dsn)) {
		list($db_type) = split(":", $dsn);
		$sql_path = "./sql/add/". $table_name . "_" .$db_type .".sql";
		$arrErr = lfExecuteSQL($sql_path, $dsn);
		if(count($arrErr) == 0) {
			$objPage->tpl_message.="�����ɲåơ��֥�($table_name)�κ������������ޤ�����<br>";
		} else {
			$objPage->tpl_message.="�ߡ��ɲåơ��֥�($table_name)�κ����˼��Ԥ��ޤ�����<br>";		
		}
	} else {
		$objPage->tpl_message.="�����ɲåơ��֥�($table_name)����ǧ����ޤ�����<br>";        
    }
    
	return $arrErr;
}

// �ơ��֥�κ���ʴ��˥ơ��֥뤬¸�ߤ�����Τߺ�������
function lfDropTable($table_name, $dsn) {
	$arrErr = array();
	if(sfTabaleExists($table_name, $dsn)) {
		// Debug�⡼�ɻ���
		$options['debug'] = PEAR_DB_DEBUG;
		$objDB = DB::connect($dsn, $options);
		// ��³����
		if(!PEAR::isError($objDB)) {
			$objDB->query("DROP TABLE " . $table_name);
		} else {
			$arrErr['all'] = ">> " . $objDB->message . "<br>";
			// ���顼ʸ���������
			ereg("\[(.*)\]", $objDB->userinfo, $arrKey);
			$arrErr['all'].= $arrKey[0] . "<br>";
			gfPrintLog($objDB->userinfo, INSTALL_LOG);
		}
	}
	return $arrErr;
}

// �������ɲáʴ��˥���ब¸�ߤ�����Ϻ������ʤ���
function lfAddColumn($dsn) {
	global $objDBParam;

	// ����ơ��֥�	
	sfColumnExists("dtb_order", "memo01", "text", $dsn, true);	
	sfColumnExists("dtb_order", "memo02", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo03", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo04", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo05", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo06", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo07", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo08", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo09", "text", $dsn, true);
	sfColumnExists("dtb_order", "memo10", "text", $dsn, true);
	sfColumnExists("dtb_order", "campaign_id", "int4", $dsn, true);

	// �������ơ��֥�	
	sfColumnExists("dtb_order_temp", "order_id", "text", $dsn, true);	
	sfColumnExists("dtb_order_temp", "memo01", "text", $dsn, true);	
	sfColumnExists("dtb_order_temp", "memo02", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo03", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo04", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo05", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo06", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo07", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo08", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo09", "text", $dsn, true);
	sfColumnExists("dtb_order_temp", "memo10", "text", $dsn, true);

	// ��ʧ����ơ��֥�
	sfColumnExists("dtb_payment", "charge_flg", "int2 default 1", $dsn, true);	
	sfColumnExists("dtb_payment", "rule_min", "numeric", $dsn, true);	
	sfColumnExists("dtb_payment", "upper_rule_max", "numeric", $dsn, true);	
	sfColumnExists("dtb_payment", "module_id", "int4", $dsn, true);	
	sfColumnExists("dtb_payment", "module_path", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo01", "text", $dsn, true);	
	sfColumnExists("dtb_payment", "memo02", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo03", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo04", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo05", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo06", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo07", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo08", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo09", "text", $dsn, true);
	sfColumnExists("dtb_payment", "memo10", "text", $dsn, true);
	
	// �����ڡ���ơ��֥�
	sfColumnExists("dtb_campaign", "directory_name", "text NOT NULL", $dsn, true);
	sfColumnExists("dtb_campaign", "limit_count", "int4 NOT NULL DEFAULT 0", $dsn, true);
	sfColumnExists("dtb_campaign", "total_count", "int4 NOT NULL DEFAULT 0", $dsn, true);
	sfColumnExists("dtb_campaign", "orverlapping_flg", "int2 NOT NULL DEFAULT 0", $dsn, true);
	sfColumnExists("dtb_campaign", "cart_flg", "int2 NOT NULL DEFAULT 0", $dsn, true);
	sfColumnExists("dtb_campaign", "deliv_free_flg", "int2 NOT NULL DEFAULT 0", $dsn, true);	

	// �ܵ�
	sfColumnExists("dtb_customer", "mailmaga_flg", "int2", $dsn, true);
    
    // ����ǥå����γ�ǧ
	if (!sfColumnExists("dtb_customer", "mobile_phone_id", "text", $dsn, true)) {
		// ����ǥå������ɲ�
		sfIndexExists("dtb_customer", "mobile_phone_id", "dtb_customer_mobile_phone_id_key", 64, $dsn, true);
	}

	// �ܵҥ᡼��
	if ($objDBParam->getValue('db_type') == 'mysql') {
		sfColumnExists("dtb_customer_mail", "secret_key", "varchar(50) unique", $dsn, true);
	} else {
		sfColumnExists("dtb_customer_mail", "secret_key", "text unique", $dsn, true);
	}
}

// �ǡ������ɲáʴ��˥ǡ�����¸�ߤ�����Ϻ������ʤ���
function lfAddData($dsn) {
	// CSV�ơ��֥�
	if(sfTabaleExists('dtb_csv', $dsn)) {
		lfInsertCSVData(1,'category_id','���ƥ���ID',53,'now()','now()', $dsn);		
		lfInsertCSVData(4,'order_id','��ʸID',1,'now()','now()', $dsn);
		lfInsertCSVData(4,'campaign_id','�����ڡ���ID',2,'now()','now()', $dsn);
		lfInsertCSVData(4,'customer_id','�ܵ�ID',3,'now()','now()', $dsn);
		lfInsertCSVData(4,'message','��˾��',4,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_name01','�ܵ�̾1',5,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_name02','�ܵ�̾2',6,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_kana01','�ܵ�̾����1',7,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_kana02','�ܵ�̾����2',8,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_email','�᡼�륢�ɥ쥹',9,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_tel01','�����ֹ�1',10,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_tel02','�����ֹ�2',11,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_tel03','�����ֹ�3',12,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_fax01','FAX1',13,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_fax02','FAX2',14,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_fax03','FAX3',15,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_zip01','͹���ֹ�1',16,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_zip02','͹���ֹ�2',17,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_pref','��ƻ�ܸ�',18,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_addr01','����1',19,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_addr02','����2',20,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_sex','����',21,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_birth','��ǯ����',22,'now()','now()', $dsn);
		lfInsertCSVData(4,'order_job','����',23,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_name01','������̾��',24,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_name02','������̾��',25,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_kana01','�����襫��',26,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_kana02','�����襫��',27,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_tel01','�����ֹ�1',28,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_tel02','�����ֹ�2',29,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_tel03','�����ֹ�3',30,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_fax01','FAX1',31,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_fax02','FAX2',32,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_fax03','FAX3',33,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_zip01','͹���ֹ�1',34,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_zip02','͹���ֹ�2',35,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_pref','��ƻ�ܸ�',36,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_addr01','����1',37,'now()','now()', $dsn);
		lfInsertCSVData(4,'deliv_addr02','����2',38,'now()','now()', $dsn);
		lfInsertCSVData(4,'payment_total','����ʧ�����',39,'now()','now()', $dsn);
	}
}

// CSV�ơ��֥�ؤΥǡ������ɲ�
function lfInsertCSVData($csv_id,$col,$disp_name,$rank,$create_date,$update_date, $dsn) {
	$sql = "insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date) values($csv_id,'$col','$disp_name',$rank,$create_date,$update_date);";
	sfDataExists("dtb_csv", "csv_id = ? AND col = ?", array($csv_id, $col), $dsn, $sql, true);
}
?>
