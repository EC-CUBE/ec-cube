<?php

require_once("../require.php");
require_once("../../../data/module/Tar.php");
//require_once("./Tar.php");

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
		$this->bkup_dir = ROOT_DIR . "html/test/" . "bkup/";
//		$this->bkup_dir = "../../test/bkup/";
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
	}else{
		$arrForm = $arrData;
	}

	break;
	
// �ꥹ�ȥ�
case 'restore':
	lfRestore($_POST['list_name']);

	break;
	
// ���
case 'del':
	// �ե�����κ��
	unlink($objPage->bkup_dir.$_POST['list_name'] . ".tar.gz");

	// DB������
	$delsql = "DELETE FROM dtb_bkup WHERE bkup_name = ?";
	$objQuery->query($delsql, array($_POST['list_name']));

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
	$err = true;
	
	$bkup_dir = $objPage->bkup_dir;
	$bkup_dir = $bkup_dir . $bkup_name . "/";

	// ���ơ��֥����
	$arrTableList = lfGetTableList();
	
	// �ƥơ��֥������������
	foreach($arrTableList as $key => $val){
		
		if ($val != "dtb_bkup") {
			
			// ��ư���ַ��ι������������
			$csv_autoinc = lfGetAutoIncrement($val);
			
			//sfprintr($csv_autoinc);
			
			// ���ǡ��������
			$arrData = $objQuery->getAll("SELECT * FROM $val");
	
			// CSV�ǡ�������
			if (count($arrData) > 0) {
				
				// ������CSV������������
				$arrKyes = sfGetCommaList(array_keys($arrData[0]), false);
				
				// �ǡ�����CSV������������
				$data = "";
				foreach($arrData as $data_key => $data_val){
					$data .= lfGetCSVList($arrData[$data_key]);
				}
				
				// CSV���ϥǡ�������
				$csv_data .= $val . "\n";
				$csv_data .= $arrKyes . "\n";
				$csv_data .= $data;
				$csv_data .= "\n";
			}	
		}
	}

	$csv_file = $bkup_dir . "bkup_data.csv";
	// CSV����
	// �ǥ��쥯�ȥ꤬¸�ߤ��Ƥ��ʤ���к�������		
	if (!is_dir(dirname($csv_file))) {
		$err = mkdir(dirname($csv_file));
	}
	if ($err) {
		$fp = fopen($csv_file,"w");
		if($fp) {
			$err = fwrite($fp, $csv_data);
			fclose($fp);
		}
	}

	// ���ʲ����ե�����򥳥ԡ�
	if ($err) {
		$copy_mess = "";
		$copy_mess = sfCopyDir("../../upload/save_image/", $bkup_dir, $copy_mess);

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
	}
	
	return $arrErr;
}

/* ��������Ǥ�CSV�ե����ޥåȤǽ��Ϥ��롣*/
function lfGetCSVList($array) {
	if (count($array) > 0) {
		foreach($array as $key => $val) {
			if ($val == "") {
				$line .= "NULL,";
			}else{
				$line .= "'".$val."',";
			}
		}
		$line = ereg_replace(",$", "\n", $line);
		return $line;
	}else{
		return false;
	}
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
		
	}
	
	return $arrRet;
}

// ��ư���ַ���CSV���Ϸ������Ѵ�����
function lfGetAutoIncrement($table_name){
	$arrColList = lfGetColumnList($table_name);
	
	foreach($arrColList['defval'] as $key => $val){
		if (substr($val,1,10) == 'nextval(\'') {
			sfprintr($val);
		}		
	}
	
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
	
	return sfSwapArray($arrRet);
}

// ��ư���ַ����ͤ��������
function lfGetAutoIncrementVal($table_name){
	$objQuery = new SC_Query();

	if(DB_TYPE == "pgsql"){
		$sql = "SELECT
				    ci.relname,
				    i.indkey
				FROM
				    pg_index i,
				    pg_class c,
				    pg_class ci
				WHERE
				    i.indrelid = c.oid AND
				    i.indexrelid = ci.oid AND
				    c.relname = ?
				";
		$arrRet = $objQuery->getAll($sql, array($table_name));
	}
	return $arrRet;
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
	$objQuery = new SC_Query();
	$csv_data = "";
	$err = true;
	
	$bkup_dir = $objPage->bkup_dir;
	
	//file�ե�����˰�ư����
	chdir($bkup_dir);
	
	//���̥ե饰TRUE��gzip����򤪤��ʤ�
	$tar = new Archive_Tar($bkup_name . ".tar.gz", TRUE);
	
	//���ꤵ�줿�ե������˲��ह��
	$err = $tar->extract("./");
	
	// ̵������Ǥ���С��ꥹ�ȥ���Ԥ�
	if ($err) {
		
		// INSERTʸ����
		lfCreateInsertSQL($bkup_dir . $bkup_name . "/bkup_data.csv");
		
	}
}

// CSV�ե����뤫�饤�󥵡���ʸ����
function lfCreateInsertSQL($csv){
	// csv�ե����뤫��ǡ����μ���
	$arrCsvData = file($csv);
	
	$sql = "";
	$base_sql = "";
	$tbl_flg = false;
	$col_flg = false;
	
	foreach($arrCsvData as $key => $val){
		$data = trim($val);
		//����ԤΤȤ��ϥơ��֥��ѹ�
		if ($data == "") {
			$base_sql = "";
			$tbl_flg = false;
			$col_flg = false;
			continue;
		}
		
		// �ơ��֥�ե饰�����äƤ��ʤ����ˤϥơ��֥�̾���å�
		if (!$tbl_flg) {
			$base_sql = "INSERT INTO $data ";
			$tbl_flg = true;
			continue;
		}
		
		// �����ե饰�����äƤ��ʤ����ˤϥ���ॻ�å�
		if (!$col_flg) {
			$base_sql .= " ($data) VALUES ";
			$col_flg = true;
			continue;
		}
		
		// ���󥵡��Ȥ����ͤ򥻥å�
		$sql .= $base_sql . " ($data);";
	}
}



?>
