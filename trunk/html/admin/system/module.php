<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");

//�ڡ����������饹
class LC_Page {
	//���󥹥ȥ饯��
	function LC_Page() {
		//�ᥤ��ƥ�ץ졼�Ȥλ���
		$this->tpl_mainpage = 'system/module.tpl';
		$this->tpl_subnavi = 'system/subnavi.tpl';
		$this->tpl_mainno = 'system';		
		$this->tpl_subno = 'module';
		$this->tpl_subtitle = '�⥸�塼�����';
	}
}
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// ���å���󥯥饹
$objSess = new SC_Session();
// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

switch($_POST['mode']) {
// ���åץǡ��Ⱦ���ե���������
case 'edit':
	// ���������ǿ��ˤ���
	lfLoadUpdateList();
	break;
// ���󥹥ȡ���
case 'install':
	// ���������ǿ��ˤ���
	lfLoadUpdateList();
	// �⥸�塼�뷴�Υ��󥹥ȡ���
	lfInstallModule();
	break;
// ���󥤥󥹥ȡ���
case 'uninstall':
	// ���������ǿ��ˤ���
	lfLoadUpdateList();
	// �⥸�塼�뷴�Υ��󥹥ȡ���	
	lfUninstallModule();
	break;
default:
	break;
}

$objQuery->setorder("module_id");
$arrUpdate = $objQuery->select("*", "dtb_module");

$objPage->arrUpdate = $arrUpdate;
$objView->assignobj($objPage);		//�ѿ���ƥ�ץ졼�Ȥ˥������󤹤�
$objView->display(MAIN_FRAME);		//�ƥ�ץ졼�Ȥν���
//-------------------------------------------------------------------------------------------------------
// �����ե�����μ���
function lfCopyUpdateFile($file) {
	global $objPage;
	
	$src_path = sfRmDupSlash(UPDATE_HTTP . $file . ".txt");
	$dst_path = sfRmDupSlash(MODULE_PATH . $file);
	$flg_ok = true;	// ����������Ƚ��
	
	$src_fp = @fopen($src_path, "rb");
	
	if(!$src_fp) {
		sfErrorHeader(">> " . $src_path . "�μ����˼��Ԥ��ޤ�����");
		$flg_ok = false;
	} else {
		// �ե�����򤹤٤��ɤ߹���
		$contents = '';
		while (!feof($src_fp)) {
			$contents .= fread($src_fp, 1024);
		}
		fclose($src_fp);
		
		// �ǥ��쥯�ȥ�������ߤ�
		lfMakeDirectory($dst_path);
		// �ե���������		
		$dst_fp = @fopen($dst_path, "wb");
		if(!$dst_fp) {
			sfErrorHeader(">> " . $dst_path . "�򥪡��ץ�Ǥ��ޤ���");
			$flg_ok = false;
		} else {
			fwrite($dst_fp, $contents);
			fclose($dst_fp);
		}
	}
	
	if($flg_ok) {
		$objPage->update_mess.= ">> " . $dst_path . "�����ԡ�����<br>";
	} else {
		$objPage->update_mess.= ">> " . $dst_path . "�����ԡ�����<br>";		
	}
	
	return $flg_ok;
}

// ���٤ƤΥѥ��Υǥ��쥯�ȥ���������
function lfMakeDirectory($path) {
	$pos = 0;
	$cnt = 0;				// ̵�¥롼���к�
	$len = strlen($path);	// ̵�¥롼���к�
	
	while($cnt <= $len) {
		$pos = strpos($path, "/", $pos);
		// �����Ǥ�Ƚ��ϡ�����3�Ĥ����
		if($pos === false) {
			// ����å��夬���Ĥ���ʤ����ϥ롼�פ���ȴ����
			break;
		}
		$pos++; // ʸ��ȯ�����֤��ʸ���ʤ��
		$dir = substr($path, 0, $pos);
		
		// ���Ǥ�¸�ߤ��뤫�ɤ���Ĵ�٤�
		if(!file_exists($dir)) {
			mkdir($dir);
		}
		$cnt++; // ̵�¥롼���к�
	}
}

// ���������ǿ��ˤ���
function lfLoadUpdateList() {
	$objQuery = new SC_Query();
	$path = UPDATE_HTTP . "module.txt";
	$fp = @fopen($path, "rb");
	
	$arrInsID = array();
		
	if(!$fp) {
		sfErrorHeader(">> " . $path . "�μ����˼��Ԥ��ޤ�����");
	} else {
		
		while (!feof($fp)) {
			$arrCSV = fgetcsv($fp, UPDATE_CSV_LINE_MAX);
			
			// ���������׽������ִ�
			foreach($arrCSV as $key => $val){
				$arrCSV[$key] = str_replace('\"', '"', $val);
			}
			
			if(ereg("^#", $arrCSV[0])) {
				continue;
			}
			
			// ������������Ǥ��ä����Τ�
			if(count($arrCSV) == MODULE_CSV_COL_MAX) {
					// insert����module_id������˳�Ǽ
					$arrInsID[] = $arrCSV[0];
				
					// �����������åץǡ��Ⱦ����DB�˽񤭹���
					$sqlval['module_id'] = $arrCSV[0];
					$sqlval['module_name'] = $arrCSV[1];
					$sqlval['latest_version'] = $arrCSV[3];
					$sqlval['module_explain'] = $arrCSV[4];
					$sqlval['main_php'] = $arrCSV[5];
					$sqlval['extern_php'] = $arrCSV[6];
					$sqlval['install_sql'] = $arrCSV[7];
					$sqlval['uninstall_sql'] = $arrCSV[8];				
					$sqlval['other_files'] = $arrCSV[9];
					$sqlval['del_flg'] = $arrCSV[10];
					$sqlval['update_date'] = "now()";
					$sqlval['release_date'] = $arrCSV[12];
					$sqlval['module_x'] = $arrCSV[14];
					$sqlval['module_y'] = $arrCSV[15];
					// �⥸�塼�뤬�б����Ƥ������ΤΥС������
					$sqlval['eccube_version'] = $arrCSV[13];					
					// ��¸�쥳���ɤΥ����å�
					$cnt = $objQuery->count("dtb_module", "module_id = ?", array($sqlval['module_id']));
					if($cnt > 0) {
						// ���Ǥ˼�������Ƥ�����Ϲ������롣	
						$objQuery->update("dtb_module", $sqlval, "module_id = ?", array($sqlval['module_id']));
					} else {
						// �����쥳���ɤ��ɲ�
						$sqlval['create_date'] = "now()";
						$objQuery->insert("dtb_module", $sqlval);
					}
			} else {
				sfErrorHeader(">> �����������פ��ޤ��󡣡�".count($arrCSV));
			}
		}
		
		// ���פʥǡ�������
		if(count($arrInsID) > 0){
			$del_sql = "DELETE FROM dtb_module WHERE module_id NOT IN (?";
			
			for($i = 1; $i < count($arrInsID); $i++){
				$del_sql .= ",?";
			}
			$del_sql .= ")";
			
			$objQuery->query($del_sql, $arrInsID);
		}

		fclose($fp);
	}
}

// ���󥹥ȡ������
function lfInstallModule() {
	global $objPage;
	
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("module_id, extern_php, other_files, install_sql, latest_version", "dtb_module", "module_id = ?", array($_POST['module_id']));
	$flg_ok = true;	// ����������Ƚ��
	
	if(count($arrRet) > 0) {
		$arrFiles = array();
		if($arrRet[0]['other_files'] != "") {
			$arrFiles = split("\|", $arrRet[0]['other_files']);
		}
		$arrFiles[] = $arrRet[0]['extern_php'];
		foreach($arrFiles as $val) {
			// �����ե�����μ���
			$ret=lfCopyUpdateFile($val);
			if(!$ret) {
				$flg_ok = false;
			}
		}
	} else {
		sfErrorHeader(">> �оݤε�ǽ�ϡ����ۤ�λ���Ƥ���ޤ���");
		$flg_ok = false;
	}
	
	// ɬ�פ�SQLʸ�μ¹�
	if($arrRet[0]['install_sql'] != "") {
		// SQLʸ�¹ԡ��ѥ顼�᡼���ʤ������顼̵��
		$arrInstallSql = split(";",$arrRet[0]['install_sql']);
		foreach($arrInstallSql as $key => $val){
			if (trim($val) != ""){
				$ret = $objQuery->query(trim($val),"",true);
			}
		}
		if(DB::isError($ret)) {
			// ���顼ʸ���������
			ereg("\[(.*)\]", $ret->userinfo, $arrKey);
			$objPage->update_mess.=">> �ơ��֥빽�����ѹ��˼��Ԥ��ޤ�����<br>";
			$objPage->update_mess.= $arrKey[0] . "<br>";
			$flg_ok = false;
		} else {
			$objPage->update_mess.=">> �ơ��֥빽�����ѹ���Ԥ��ޤ�����<br>";
		}
	}
	
	if($flg_ok) {		
		$path = MODULE_PATH . $arrRet[0]['extern_php'];
		$sqlval['now_version'] = sfGetFileVersion($path);
		$sqlval['update_date'] = "now()";
		$objQuery->update("dtb_module", $sqlval, "module_id = ?", array($arrRet[0]['module_id']));
	}
}

// ���󥤥󥹥ȡ������
function lfUninstallModule() {
	global $objPage;
	
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("module_id, extern_php, other_files, install_sql, uninstall_sql, latest_version", "dtb_module", "module_id = ?", array($_POST['module_id']));
	$flg_ok = true;	// ����������Ƚ��
	
	if(count($arrRet) > 0) {
		
		// �⥸�塼��¦�˺���������������
		$req = new HTTP_Request(SITE_URL . "load_module.php");
		$req->addCookie("PHPSESSID", $_COOKIE["PHPSESSID"]);
		$req->setMethod(HTTP_REQUEST_METHOD_POST);
		$req->addPostData("module_id", $arrRet[0]['module_id']);
		$req->addPostData("mode", "module_del");
		$req->sendRequest();
		$req->clearPostData();

		$arrFiles = array();
		if($arrRet[0]['other_files'] != "") {
			$arrFiles = split("\|", $arrRet[0]['other_files']);
		}
		$arrFiles[] = $arrRet[0]['extern_php'];

		foreach($arrFiles as $val) {
			$path = MODULE_PATH . $val;
			// �ե������������
			if(file_exists($path) && unlink($path)) {
				$objPage->update_mess.= ">> " . $path . "���������<br>";
			} else {
				$objPage->update_mess.= ">> " . $path . "���������<br>";
			}
		}

		// ɬ�פ�SQLʸ�μ¹�
		if($arrRet[0]['uninstall_sql'] != "") {
			// SQLʸ�¹ԡ��ѥ顼�᡼���ʤ������顼̵��
			$ret = $objQuery->query($arrRet[0]['uninstall_sql'],"",true);
			if(DB::isError($ret)) {
				// ���顼ʸ���������
				ereg("\[(.*)\]", $ret->userinfo, $arrKey);
				$objPage->update_mess.=">> �ơ��֥빽�����ѹ��˼��Ԥ��ޤ�����<br>";
				$objPage->update_mess.= $arrKey[0] . "<br>";
				$flg_ok = false;
			} else {
				$objPage->update_mess.=">> �ơ��֥빽�����ѹ���Ԥ��ޤ�����<br>";
			}
		}
	} else {
		sfErrorHeader(">> �оݤε�ǽ�ϡ����ۤ�λ���Ƥ���ޤ���");
	}

	if($flg_ok) {
		// �С���������������롣
		$sqlval['now_version'] = "";
		$sqlval['update_date'] = "now()";
		$objQuery->update("dtb_module", $sqlval, "module_id = ?", array($arrRet[0]['module_id']));
	}
}

?>