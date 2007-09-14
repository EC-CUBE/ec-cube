<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once(DATA_PATH. "module/Tar.php");

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
		
		$this->bkup_dir = USER_PATH . "bkup/";
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// ���å���󥯥饹
$objSess = new SC_Session();
// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// �Хå����åץơ��֥뤬�ʤ���к�������
lfCreateBkupTable();

switch($_POST['mode']) {
// �Хå����åפ��������
case 'bkup':
	// ����ʸ������Ѵ�
	$arrData = lfConvertParam($_POST);

	// ���顼�����å�
	$arrErr = lfCheckError($arrData);

	// ���顼���ʤ���ХХå����å׽�����Ԥ�	
	if (count($arrErr) <= 0) {
		// �Хå����åץե��������
		$arrErr = lfCreateBkupData($arrData['bkup_name']);
		
		// DB�˥ǡ�������
		if (count($arrErr) <= 0) {
			lfUpdBkupData($arrData);
		}else{
			$arrForm = $arrData;
		}
		
		$objPage->tpl_onload = "alert('�Хå����å״�λ���ޤ���');";
	}else{
		$arrForm = $arrData;
	}

	break;
	
// �ꥹ�ȥ�
case 'restore':
case 'restore_config':
	if ($_POST['mode'] == 'restore_config') {
		$objPage->mode = "restore_config";
	}

	lfRestore($_POST['list_name']);

	break;
	
// ���
case 'delete':
	$del_file = $objPage->bkup_dir.$_POST['list_name'] . ".tar.gz";
	// �ե�����κ��
	if(is_file($del_file)){
		$ret = unlink($del_file);
	}

	// DB������
	$delsql = "DELETE FROM dtb_bkup WHERE bkup_name = ?";
	$objQuery->query($delsql, array($_POST['list_name']));

	break;
	
// ���������
case 'download' :
	$filename = $_POST['list_name'] . ".tar.gz";
	$dl_file = $objPage->bkup_dir.$_POST['list_name'] . ".tar.gz";
	
	// ��������ɳ���
	Header("Content-disposition: attachment; filename=${filename}");
	Header("Content-type: application/octet-stream; name=${filename}");
	header("Content-Length: " .filesize($dl_file)); 
	readfile ($dl_file);
	exit();
	break;

default:
	break;
}

// �Хå����åץꥹ�Ȥ��������
$arrBkupList = lfGetBkupData("ORDER BY create_date DESC");
// �ƥ�ץ졼�ȥե�������Ϥ��ǡ����򥻥å�
$objPage->arrErr = $arrErr;
$objPage->arrForm = $arrForm;
$objPage->arrBkupList = $arrBkupList;

$objView->assignobj($objPage);		//�ѿ���ƥ�ץ졼�Ȥ˥������󤹤�
$objView->display(MAIN_FRAME);		//�ƥ�ץ졼�Ȥν���

//-------------------------------------------------------------------------------------------------------
/* ����ʸ������Ѵ� */
function lfConvertParam($array) {
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 *  a :  ���ѱѿ�����Ⱦ�ѱѿ������Ѵ�����
	 */
	$arrConvList['bkup_name'] = "a";
	$arrConvList['bkup_memo'] = "KVa";
	
	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}

// ���顼�����å�
function lfCheckError($array){
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("�Хå����å�̾", "bkup_name", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK","NO_SPTAB","ALNUM_CHECK"));
	$objErr->doFunc(array("�Хå����åץ��", "bkup_memo", MTEXT_LEN), array("MAX_LENGTH_CHECK"));
	
	// ��ʣ�����å�
	$ret = lfGetBkupData("WHERE bkup_name = ?", array($array['bkup_name']));
	if (count($ret) > 0) {
		$objErr->arrErr['bkup_name'] = "�Хå����å�̾����ʣ���Ƥ��ޤ�����̾�����Ϥ��Ƥ���������";
	}

	return $objErr->arrErr;
}

// �Хå����åץե��������
function lfCreateBkupData($bkup_name){
	global $objPage;
	$objQuery = new SC_Query();
	$csv_data = "";
	$csv_autoinc = "";
	$err = true;
	
	$bkup_dir = $objPage->bkup_dir;
	if (!is_dir(dirname($bkup_dir))) $err = mkdir(dirname($bkup_dir));		
	$bkup_dir = $bkup_dir . $bkup_name . "/";

	// ���ơ��֥����
	$arrTableList = lfGetTableList();
	
	// �ƥơ��֥������������
	foreach($arrTableList as $key => $val){
		
		if ($val != "dtb_bkup") {
			
			// ��ư���ַ��ι������������
			$csv_autoinc .= lfGetAutoIncrement($val);
			
			// ���ǡ��������
			if ($val == "dtb_pagelayout"){
				$arrData = $objQuery->getAll("SELECT * FROM $val ORDER BY page_id");
			}else{
				$arrData = $objQuery->getAll("SELECT * FROM $val");
			}
			
			// CSV�ǡ�������
			if (count($arrData) > 0) {
				
				// ������CSV������������
				$arrKyes = sfGetCommaList(array_keys($arrData[0]), false);
				
				// �ǡ�����CSV������������
				$data = "";
				foreach($arrData as $data_key => $data_val){
					//$val = str_replace("\"", "\\\"", $val);
					$data .= lfGetCSVList($arrData[$data_key]);

				}
				// CSV���ϥǡ�������
				$csv_data .= $val . "\n";
				$csv_data .= $arrKyes . "\n";
				$csv_data .= $data;
				$csv_data .= "\n";
			}	
			
			// �����ॢ���Ȥ��ɤ�
			sfFlush();
		}
	}

	$csv_file = $bkup_dir . "bkup_data.csv";
	$csv_autoinc_file = $bkup_dir . "autoinc_data.csv";
	mb_internal_encoding(CHAR_CODE);
	// CSV����
	// �ǥ��쥯�ȥ꤬¸�ߤ��Ƥ��ʤ���к�������		
	if (!is_dir(dirname($csv_file))) {
		$err = mkdir(dirname($csv_file));
	}
	if ($err) {
		// data��CSV����
		$fp = fopen($csv_file,"w");
		if($fp) {
			if($csv_data != ""){
				$err = fwrite($fp, $csv_data);
			}
			fclose($fp);
		}
		
		// ��ư���֤�CSV����
		$fp = fopen($csv_autoinc_file,"w");
		if($fp) {
			if($csv_autoinc != ""){
				$err = fwrite($fp, $csv_autoinc);
			}
			fclose($fp);
		}
	}

	// �Ƽ�ե����륳�ԡ�
	if ($err) {
		// ���ʲ����ե�����򥳥ԡ�
		// �ǥ��쥯�ȥ꤬¸�ߤ��Ƥ��ʤ���к�������		
		$image_dir = $bkup_dir . "save_image/";
		if (!is_dir(dirname($image_dir))) $err = mkdir(dirname($image_dir));		
		$copy_mess = "";
		$copy_mess = sfCopyDir("../../upload/save_image/",$image_dir, $copy_mess);
		
		// �ƥ�ץ졼�ȥե�����򥳥ԡ�
		// �ǥ��쥯�ȥ꤬¸�ߤ��Ƥ��ʤ���к�������		
		$templates_dir = $bkup_dir . "templates/";
		if (!is_dir(dirname($templates_dir))) $err = mkdir(dirname($templates_dir));		
		$copy_mess = "";
		$copy_mess = sfCopyDir("../../user_data/templates/",$templates_dir, $copy_mess);
		
		// ���󥯥롼�ɥե�����򥳥ԡ�
		// �ǥ��쥯�ȥ꤬¸�ߤ��Ƥ��ʤ���к�������		
		$inc_dir = $bkup_dir . "include/";
		if (!is_dir(dirname($inc_dir))) $err = mkdir(dirname($inc_dir));		
		$copy_mess = "";
		$copy_mess = sfCopyDir("../../user_data/include/",$inc_dir, $copy_mess);
	
		// CSS�ե�����򥳥ԡ�
		// �ǥ��쥯�ȥ꤬¸�ߤ��Ƥ��ʤ���к�������		
		$css_dir = $bkup_dir . "css/";
		if (!is_dir(dirname($css_dir))) $err = mkdir(dirname($css_dir));		
		$copy_mess = "";
		$copy_mess = sfCopyDir("../../user_data/css/",$css_dir, $copy_mess);

		//���̥ե饰TRUE��gzip���̤򤪤��ʤ�
		$tar = new Archive_Tar($objPage->bkup_dir . $bkup_name.".tar.gz", TRUE);

		//bkup�ե�����˰�ư����
		chdir($objPage->bkup_dir);

		//���̤򤪤��ʤ�
		$zip = $tar->create("./" . $bkup_name . "/");

		// �Хå����åץǡ����κ��
		if ($zip) sfDelFile($bkup_dir);
	}

	if (!$err) {
		$arrErr['bkup_name'] = "�Хå����åפ˼��Ԥ��ޤ�����";
		// �Хå����åץǡ����κ��
		sfDelFile($bkup_dir);
	}
	
	return $arrErr;
}

/* ��������Ǥ�CSV�ե����ޥåȤǽ��Ϥ��롣*/
function lfGetCSVList($array) {
	if (count($array) > 0) {
		foreach($array as $key => $val) {
			$val = mb_convert_encoding($val, CHAR_CODE, CHAR_CODE);
			$val = str_replace("\"", "\\\"", $val);
			$line .= "\"".$val."\",";
		}
		$line = ereg_replace(",$", "\n", $line);
	}else{
		return false;
	}
	return $line;
}

// ���ơ��֥�ꥹ�Ȥ��������
function lfGetTableList(){
	$objQuery = new SC_Query();
	
	if(DB_TYPE == "pgsql"){
		$sql = "SELECT tablename FROM pg_tables WHERE tableowner = ? ORDER BY tablename ; ";
		$arrRet = $objQuery->getAll($sql, array(DB_USER));
		$arrRet = sfSwapArray($arrRet);
		$arrRet = $arrRet['tablename'];
	}else if(DB_TYPE == "mysql"){
		$sql = "SHOW TABLES;";
		$arrRet = $objQuery->getAll($sql);
		$arrRet = sfSwapArray($arrRet);
		
		// ���������
		$arrKey = array_keys($arrRet);
		
		$arrRet = $arrRet[$arrKey[0]];
	}
	return $arrRet;
}

// ��ư���ַ���CSV���Ϸ������Ѵ�����
function lfGetAutoIncrement($table_name){
	$arrColList = lfGetColumnList($table_name);
	$ret = "";
	
	if(DB_TYPE == "pgsql"){
		$match = 'nextval(\'';
	}else if(DB_TYPE == "mysql"){
		$match = "auto_incr";
	}

	foreach($arrColList['col_def'] as $key => $val){
		
		if (substr($val,0,9) == $match) {
			$col = $arrColList['col_name'][$key];
			$autoVal = lfGetAutoIncrementVal($table_name, $col);
			$ret .= "$table_name,$col,$autoVal\n";
		}
	}
	
	return $ret;
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
		$arrColList = $objQuery->getAll($sql, array($table_name));
		$arrColList = sfSwapArray($arrColList);
		
		$arrRet['col_def'] = $arrColList['defval'];
		$arrRet['col_name'] = $arrColList['attname'];
	}else if(DB_TYPE == "mysql"){
		$sql = "SHOW COLUMNS FROM $table_name";
		$arrColList = $objQuery->getAll($sql);
		$arrColList = sfSwapArray($arrColList);
		
		$arrRet['col_def'] = $arrColList['Extra'];
		$arrRet['col_name'] = $arrColList['Field'];
	}
	return $arrRet;
}

// ��ư���ַ����ͤ��������
function lfGetAutoIncrementVal($table_name , $colname = ""){
	$objQuery = new SC_Query();
	$ret = "";

	if(DB_TYPE == "pgsql"){
		$ret = $objQuery->nextval($table_name, $colname) - 1;
	}else if(DB_TYPE == "mysql"){
		$sql = "SHOW TABLE STATUS LIKE ?";
		$arrData = $objQuery->getAll($sql, array($table_name));
		$ret = $arrData[0]['Auto_increment'];
	}
	return $ret;
}

// �Хå����åץơ��֥�˥ǡ����򹹿�����
function lfUpdBkupData($data){
	$objQuery = new SC_Query();
	
	$sql = "INSERT INTO dtb_bkup (bkup_name,bkup_memo,create_date) values (?,?,now())";
	$objQuery->query($sql, array($data['bkup_name'],$data['bkup_memo']));
}

// �Хå����åץơ��֥뤫��ǡ������������
function lfGetBkupData($where = "", $data = array()){
	$objQuery = new SC_Query();
	
	$sql = "SELECT bkup_name, bkup_memo, create_date FROM dtb_bkup ";
	if ($where != "")	$sql .= $where;
	
	$ret = $objQuery->getall($sql,$data);
	
	return $ret;
}

// �Хå����åץե������ꥹ�ȥ�����
function lfRestore($bkup_name){
	global $objPage;
	$objQuery = new SC_Query("", false);
	$csv_data = "";
	$err = true;
	
	$bkup_dir = $objPage->bkup_dir . $bkup_name . "/";
	
	//�Хå����åץե�����˰�ư����
	chdir($objPage->bkup_dir);
	
	//���̥ե饰TRUE��gzip����򤪤��ʤ�
	$tar = new Archive_Tar($bkup_name . ".tar.gz", TRUE);
	
	//���ꤵ�줿�ե������˲��ह��
	$err = $tar->extract("./");
	
	// ̵������Ǥ���С��ꥹ�ȥ���Ԥ�
	if ($err) {
		
		// �ȥ�󥶥�����󳫻�
		$objQuery->begin();
		
		// DB�򥯥ꥢ
		$err = lfDeleteAll($objQuery);
		
		// INSERT�¹�
		if ($err) $err = lfExeInsertSQL($objQuery, $bkup_dir . "bkup_data.csv");

		// ��ư���֤��ͤ򥻥å�
		if ($err) lfSetAutoInc($objQuery, $bkup_dir . "autoinc_data.csv");

		// �Ƽ�ե�����Υ��ԡ�
		if ($err) {
			// �����Υ��ԡ�
			$image_dir = $bkup_dir . "save_image/";
			$copy_mess = "";
			$copy_mess = sfCopyDir($image_dir, "../../upload/save_image/", $copy_mess, true);		
	
			// �ƥ�ץ졼�ȤΥ��ԡ�
			$tmp_dir = $bkup_dir . "templates/";
			$copy_mess = "";
			$copy_mess = sfCopyDir($tmp_dir, "../../user_data/templates/", $copy_mess, true);		
			
			// ���󥯥롼�ɥե�����Υ��ԡ�
			$inc_dir = $bkup_dir . "include/";
			$copy_mess = "";
			$copy_mess = sfCopyDir($inc_dir, "../../user_data/include/", $copy_mess, true);		
			
			// CSS�Υ��ԡ�
			$css_dir = $bkup_dir . "css/";
			$copy_mess = "";
			$copy_mess = sfCopyDir($css_dir, "../../user_data/css/", $copy_mess, true);		

			// �Хå����åץǡ����κ��
			sfDelFile($bkup_dir);
		}

		// �ꥹ�ȥ������ʤ饳�ߥåȼ��Ԥʤ����Хå�
		if ($err) {
			$objQuery->commit();
			$objPage->restore_msg = "�ꥹ�ȥ���λ���ޤ�����";
			$objPage->restore_err = true;
		}else{
			$objQuery->rollback();
			$objPage->restore_msg = "�ꥹ�ȥ��˼��Ԥ��ޤ�����";
			$objPage->restore_name = $bkup_name;
			$objPage->restore_err = false;
		}
	}
}

// CSV�ե����뤫�饤�󥵡��ȼ¹�
function lfExeInsertSQL($objQuery, $csv){
	global $objPage;

	$sql = "";
	$base_sql = "";
	$tbl_flg = false;
	$col_flg = false;
	$ret = true;
	$pagelayout_flg = false;
	$mode = $objPage->mode;
	
	// csv�ե����뤫��ǡ����μ���
	$fp = fopen($csv, "r");
	while (!feof($fp)) {
		$data = fgetcsv($fp, 1000000);
				
		//����ԤΤȤ��ϥơ��֥��ѹ�
		if (count($data) <= 1 and $data[0] == "") {
			$base_sql = "";
			$tbl_flg = false;
			$col_flg = false;
			continue;
		}
		
		// �ơ��֥�ե饰�����äƤ��ʤ����ˤϥơ��֥�̾���å�
		if (!$tbl_flg) {
			$base_sql = "INSERT INTO $data[0] ";
			$tbl_flg = true;
			
			if($data[0] == "dtb_pagelayout"){
				$pagelayout_flg = true;
			}
			
			continue;
		}
		
		// �����ե饰�����äƤ��ʤ����ˤϥ���ॻ�å�
		if (!$col_flg) {
			if ($mode != "restore_config"){
				$base_sql .= " ( $data[0] ";
				for($i = 1; $i < count($data); $i++){
					$base_sql .= "," . $data[$i];
				}
				$base_sql .= " ) ";
			}
			$col_flg = true;
			continue;
		}

		// ���󥵡��Ȥ����ͤ򥻥å�
		$sql = $base_sql . "VALUES ( ? ";
		for($i = 1; $i < count($data); $i++){
			$sql .= ", ?";
		}
		$sql .= " );";
		$data = str_replace("\\\"", "\"", $data);
		$err = $objQuery->query($sql, $data);

		// ���顼������н�λ
		if ($err->message != ""){
			sfErrorHeader(">> " . $objQuery->getlastquery(false));
			return false;
		}
		
		if ($pagelayout_flg) {
			// dtb_pagelayout�ξ��ˤϺǽ�Υǡ�����page_id = 0�ˤ���
			$sql = "UPDATE dtb_pagelayout SET page_id = '0'";
			$objQuery->query($sql);
			$pagelayout_flg = false;
		}

		// �����ॢ���Ȥ��ɤ�
		sfFlush();
	}
	fclose($fp);
	
	return $ret;
}

// ��ư���֤򥻥å�
function lfSetAutoInc($objQuery, $csv){
	// csv�ե����뤫��ǡ����μ���
	$arrCsvData = file($csv);

	foreach($arrCsvData as $key => $val){
		$arrData = split(",", trim($val));
		
		if ($arrData[2] == 0)	$arrData[2] = 1;
		$objQuery->setval($arrData[0], $arrData[1], $arrData[2]);
	}
}

// DB�����ƥ��ꥢ����
function lfDeleteAll($objQuery){
	$ret = true;

	$arrTableList = lfGetTableList();
	
	foreach($arrTableList as $key => $val){
		// �Хå����åץơ��֥�Ϻ�����ʤ�
		if ($val != "dtb_bkup") {
			$trun_sql = "DELETE FROM $val;";
			$ret = $objQuery->query($trun_sql);
			
			if (!$ret) return $ret;
		}
	}
	
	return $ret;
}

// �Хå����åץơ��֥���������
function lfCreateBkupTable(){
	$objQuery = new SC_Query();
	
	// �ơ��֥��¸�ߥ����å�
	$arrTableList = lfGetTableList();

	if(!in_array("dtb_bkup", $arrTableList)){
		// ¸�ߤ��Ƥ��ʤ���к���
		$cre_sql = "
			create table dtb_bkup
			(
				bkup_name	text,
				bkup_memo	text,
				create_date	timestamp
			);
		";
		
		$objQuery->query($cre_sql);
	}
}

?>