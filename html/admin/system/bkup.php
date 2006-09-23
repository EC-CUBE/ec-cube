<?php

require_once("../require.php");

//�ڡ����������饹
class LC_Page {
	//���󥹥ȥ饯��
	function LC_Page() {
		//�ᥤ��ƥ�ץ졼�Ȥλ���
		$this->tpl_mainpage = 'system/bkup.tpl';
		$this->tpl_subnavi = 'system/subnavi.tpl';
		$this->tpl_mainno = 'system';		
		$this->tpl_subno = 'bkup';
		$this->tpl_subtitle = '�Хå����å״���';
		
//		$this->bkup_dir = ROOT_DIR . USER_DIR . "bkup/";
		$this->bkup_dir = ROOT_DIR . "html/test/bkup/";
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
// �Хå����åפ��������
case 'bkup':
	// ���顼�����å�
	$arrErr = lfCheckError($_POST);

	// ���顼���ʤ���ХХå����å׽�����Ԥ�	
	if (count($arrErr) <= 0) {
		// �Хå����åץե��������
		$arrErr = lfCreateBkupData();
		
		// DB�˥ǡ�������
		
	}
	

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

// �ƥ�ץ졼�ȥե�������Ϥ��ǡ����򥻥å�
$objPage->arrErr = $arrErr;

$objView->assignobj($objPage);		//�ѿ���ƥ�ץ졼�Ȥ˥������󤹤�
$objView->display(MAIN_FRAME);		//�ƥ�ץ졼�Ȥν���

//-------------------------------------------------------------------------------------------------------

// ���顼�����å�
function lfCheckError($array){
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("�Хå����å�̾", "bkup_name", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	
	return $objErr->arrErr;
}

// �Хå����åץե��������
function lfCreateBkupData(){
	global $objPage;
	$objQuery = new SC_Query();
	$csv_data = "";
	$bkup_dir = $objPage->bkup_dir;
	
	// ���ơ��֥����
	$arrTableList = lfGetTableList();
	
	// �ƥơ��֥������������
	foreach($arrTableList as $key => $val){
		
		// �ơ��֥빽�������
		$arrColumnList = lfGetColumnList($val);
		
		// �ơ��֥빽����CSV���ϥǡ�������
		
		
		// ���ǡ��������
		$arrData = $objQuery->getAll("SELECT * FROM $val");

		// CSV�ǡ�������
		if (count($arrData) > 0) {
			
			// ������CSV������������
			$arrKyes = sfGetCommaList(array_keys($arrData[0]), false);
			
			// �ǡ�����CSV������������
			$data = "";
			foreach($arrData as $data_key => $data_val){
				$data .= sfGetCSVList($arrData[$data_key]);
			}
			
			// CSV���ϥǡ�������
			$csv_data .= $val . "\n";
			$csv_data .= $arrKyes . "\n";
			$csv_data .= $data;
			$csv_data .= "\n";
		}	
	}
	$bkup_dir = $bkup_dir . "test" . ".csv";

	// CSV����
	// �ǥ��쥯�ȥ꤬¸�ߤ��Ƥ��ʤ���к�������		
	if (!is_dir(dirname($bkup_dir))) {
		$err = mkdir(dirname($bkup_dir));
	}

	if ($err) {
		$fp = fopen($bkup_dir . "test" . ".csv","w");
		if($fp) {
			$err = fwrite($fp, $csv_data);
			fclose($fp);
		}
	}

	if (!$err) {
		$arrErr['bkup_name'] = "�Хå����åפ˼��Ԥ��ޤ�����";
	}
	
	return $arrErr;
}

// ���ơ��֥�ꥹ�Ȥ��������
function lfGetTableList(){
	$objQuery = new SC_Query();
	
	if(DB_TYPE == "pgsql"){
		$sql = "SELECT tablename FROM pg_tables where tableowner = ? ORDER BY tablename ; ";
		$arrRet = $objQuery->getAll($sql, array(DB_USER));
		$arrRet = sfSwapArray($arrRet);
		$arrRet = $arrRet['tablename'];
	}else if(DB_TYPE == "mysql"){
		
	}
	
	return $arrRet;
}

// �ơ��֥빽�����������
function lfGetColumnList($table_name){
	$objQuery = new SC_Query();

	if(DB_TYPE == "pgsql"){
		$sql = "SELECT 
					a.attname, t.typname, a.attnotnull, d.adsrc as defval, a.atttypmod,	a.attnum as fldnum,	e.description 
				FROM 
					pg_class c,
					pg_type t,
					pg_attribute a left join pg_attrdef d on (a.attrelid=d.adrelid and a.attnum=d.adnum)
								   left join pg_description e on (a.attrelid=e.objoid and a.attnum=e.objsubid)
				WHERE (c.relname=?) AND (c.oid=a.attrelid) AND (a.atttypid=t.oid) AND a.attnum > 0
				ORDER BY fldnum";
		$arrRet = $objQuery->getAll($sql, array($table_name));
	}
	
	return sfswaparray($arrRet);

}














// �����ե�����μ���
function lfCopyUpdateFile($val) {
	global $objPage;
	
	$src_path = sfRmDupSlash(UPDATE_HTTP . $val . ".txt");
	$dst_path = sfRmDupSlash(ROOT_DIR . $val);
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
	$path = UPDATE_HTTP . "update.txt";
	$fp = @fopen($path, "rb");
	
	if(!$fp) {
		sfErrorHeader(">> " . $path . "�μ����˼��Ԥ��ޤ�����");
	} else {
		while (!feof($fp)) {
			$arrCSV = fgetcsv($fp, UPDATE_CSV_LINE_MAX);
			// ������������Ǥ��ä����Τ�
			if(count($arrCSV) == UPDATE_CSV_COL_MAX) {
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
				// ��¸�쥳���ɤΥ����å�
				$cnt = $objQuery->count("dtb_update", "module_id = ?", array($sqlval['module_id']));
				if($cnt > 0) {
					// ���Ǥ˼�������Ƥ�����Ϲ������롣	
					$objQuery->update("dtb_update", $sqlval, "module_id = ?", array($sqlval['module_id']));
				} else {
					// �����쥳���ɤ��ɲ�
					$sqlval['create_date'] = "now()";
					$objQuery->insert("dtb_update", $sqlval);
				}
			}
		}
		fclose($fp);
	}
}

// ���󥹥ȡ������
function lfInstallModule() {
	global $objPage;
	
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("module_id, extern_php, other_files, install_sql, latest_version", "dtb_update", "module_id = ?", array($_POST['module_id']));
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
		$sqlval['now_version'] = $arrRet[0]['latest_version'];
		$sqlval['update_date'] = "now()";
		$objQuery->update("dtb_update", $sqlval, "module_id = ?", array($arrRet[0]['module_id']));
	}
}

// ���󥤥󥹥ȡ������
function lfUninstallModule() {
	global $objPage;
	
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("module_id, extern_php, other_files, install_sql, uninstall_sql, latest_version", "dtb_update", "module_id = ?", array($_POST['module_id']));
	$flg_ok = true;	// ����������Ƚ��
	
	if(count($arrRet) > 0) {
		$arrFiles = array();
		if($arrRet[0]['other_files'] != "") {
			$arrFiles = split("\|", $arrRet[0]['other_files']);
		}
		$arrFiles[] = $arrRet[0]['extern_php'];
		foreach($arrFiles as $val) {
			$path = ROOT_DIR . $val;
			if(file_exists($path)) {
				// �ե������������
				if(unlink($path)) {
					$objPage->update_mess.= ">> " . $path . "���������<br>";
				} else {
					$objPage->update_mess.= ">> " . $path . "���������<br>";
				}
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
		$objQuery->update("dtb_update", $sqlval, "module_id = ?", array($arrRet[0]['module_id']));
	}
}


?>
