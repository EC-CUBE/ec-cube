<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'products/category.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 'category';
		$this->tpl_onload = " fnSetFocus('category_name'); ";
		$this->tpl_subtitle = '���ƥ��꡼��Ͽ';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);

// �̾���Ͽƥ��ƥ����0�����ꤹ�롣
$objPage->arrForm['parent_category_id'] = $_POST['parent_category_id'];

switch($_POST['mode']) {
case 'edit':
	$objFormParam->convParam();
	$arrRet =  $objFormParam->getHashArray();
	$objPage->arrErr = lfCheckError($arrRet);
	
	if(count($objPage->arrErr) == 0) {
		if($_POST['category_id'] == "") {
			$objQuery = new SC_Query();
			$count = $objQuery->count("dtb_category");
			if($count < CATEGORY_MAX) {			
				lfInsertCat($_POST['parent_category_id']);
			} else {
				print("���ƥ������Ͽ�������Ķ���ޤ�����");
			}
		} else {
			lfUpdateCat($_POST['category_id']);
		}
	} else {
		$objPage->arrForm = array_merge($objPage->arrForm, $objFormParam->getHashArray());
		$objPage->arrForm['category_id'] = $_POST['category_id'];
	}
	break;
case 'pre_edit':
	// �Խ����ܤΥ��ƥ���̾��DB���������롣
	$oquery = new SC_Query();
	$where = "category_id = ?";
	$cat_name = $oquery->get("dtb_category", "category_name", $where, array($_POST['category_id']));
	// ���Ϲ��ܤ˥��ƥ���̾�����Ϥ��롣
	$objPage->arrForm['category_name'] = $cat_name;
	// POST�ǡ���������Ѥ�
	$objPage->arrForm['category_id'] = $_POST['category_id'];
	break;
case 'delete':
	$objQuery = new SC_Query();
	// �ҥ��ƥ���Υ����å�
	$where = "parent_category_id = ? AND del_flg = 0";
	$count = $objQuery->count("dtb_category", $where, array($_POST['category_id']));
	if($count != 0) {
		$objPage->arrErr['category_name'] = "�� �ҥ��ƥ��꤬¸�ߤ��뤿�����Ǥ��ޤ���<br>";
	}
	// ��Ͽ���ʤΥ����å�
	$where = "category_id = ? AND del_flg = 0";
	$count = $objQuery->count("dtb_products", $where, array($_POST['category_id']));
	if($count != 0) {
		$objPage->arrErr['category_name'] = "�� ���ƥ�����˾��ʤ�¸�ߤ��뤿�����Ǥ��ޤ���<br>";
	}	
	
	if(!isset($objPage->arrErr['category_name'])) {
		// ����դ��쥳���ɤκ��(��������٤��θ���ƥ쥳���ɤ��Ⱥ�����롣)
		sfDeleteRankRecord("dtb_category", "category_id", $_POST['category_id'], "", true);
	}
	break;
case 'up':
	$objQuery = new SC_Query();
	$objQuery->begin();
	$up_id = lfGetUpRankID($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
	if($up_id != "") {
		// ��Υ��롼�פ�rank���鸺�������
		$my_count = lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
		// ��ʬ�Υ��롼�פ�rank�˲û������
		$up_count = lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $up_id);
		if($my_count > 0 && $up_count > 0) {
			// ��ʬ�Υ��롼�פ˲û�
			lfUpRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id'], $up_count);
			// ��Υ��롼�פ��鸺��
			lfDownRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $up_id, $my_count);
		}
	}
	$objQuery->commit();
	break;
case 'down':
	$objQuery = new SC_Query();
	$objQuery->begin();
	$down_id = lfGetDownRankID($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
	if($down_id != "") {
		// ���Υ��롼�פ�rank�˲û������
		$my_count = lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
		// ��ʬ�Υ��롼�פ�rank���鸺�������
		$down_count = lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $down_id);
		if($my_count > 0 && $down_count > 0) {
			// ��ʬ�Υ��롼�פ��鸺��
			lfUpRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $down_id, $my_count);
			// ���Υ��롼�פ˲û�
			lfDownRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id'], $down_count);
		}
	}
	$objQuery->commit();
	break;
case 'tree':
	break;
default:
	$objPage->arrForm['parent_category_id'] = 0;
	break;
}

$objPage->arrList = lfGetCat($objPage->arrForm['parent_category_id']);
$objPage->arrTree = sfGetCatTree($objPage->arrForm['parent_category_id']);

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------

// ���ƥ���ο����ɲ�
function lfInsertCat($parent_category_id) {
	global $objFormParam;
	
	$objQuery = new SC_Query();
	$objQuery->begin();	// �ȥ�󥶥������γ���
	
	
	if($parent_category_id == 0) {
		// ROOT���ؤǺ���Υ�󥯤�������롣		
		$where = "parent_category_id = ?";
		$rank = $objQuery->max("dtb_category", "rank", $where, array($parent_category_id)) + 1;
	} else {
		// �ƤΥ�󥯤�ʬ�Υ�󥯤Ȥ��롣
		$where = "category_id = ?";
		$rank = $objQuery->get("dtb_category", "rank", $where, array($parent_category_id));
		// �ɲå쥳���ɤΥ�󥯰ʾ�Υ쥳���ɤ��Ĥ����롣
		$sqlup = "UPDATE dtb_category SET rank = (rank + 1) WHERE rank >= ?";
		$objQuery->exec($sqlup, array($rank));
	}
	
	$where = "category_id = ?";
	// ��ʬ�Υ�٥���������(�ƤΥ�٥� + 1)	
	$level = $objQuery->get("dtb_category", "level", $where, array($parent_category_id)) + 1;
	
	// ���ϥǡ������Ϥ���
	$sqlval = $objFormParam->getHashArray();
	$sqlval['create_date'] = "Now()";
	$sqlval['update_date'] = "Now()";
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['parent_category_id'] = $parent_category_id;
	$sqlval['rank'] = $rank;
	$sqlval['level'] = $level;
	
	// INSERT�μ¹�
	$objQuery->insert("dtb_category", $sqlval);
	
	$objQuery->commit();	// �ȥ�󥶥������ν�λ
}

// ���ƥ�����Խ�
function lfUpdateCat($category_id) {
	global $objFormParam;
	$objQuery = new SC_Query();
	// ���ϥǡ������Ϥ���
	$sqlval = $objFormParam->getHashArray();
	$sqlval['update_date'] = "Now()";
	$where = "category_id = ?";
	$objQuery->update("dtb_category", $sqlval, $where, array($category_id));
}

// ���ƥ���μ���
function lfGetCat($parent_category_id) {
	$objQuery = new SC_Query();
	
	if($parent_category_id == "") {
		$parent_category_id = '0';
	}
	
	$col = "category_id, category_name, level, rank";
	$where = "del_flg = 0 AND parent_category_id = ?";
	$objQuery->setoption("ORDER BY rank DESC");
	$arrRet = $objQuery->select($col, "dtb_category", $where, array($parent_category_id));
	return $arrRet;
}

/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("���ƥ���̾", "category_name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
}

/* �������ƤΥ����å� */
function lfCheckError($array) {
	global $objFormParam;
	$objErr = new SC_CheckError($array);
	$objErr->arrErr = $objFormParam->checkError();
	
	// ���إ����å�
	if(!isset($objErr->arrErr['category_name'])) {
		$objQuery = new SC_Query();
		$level = $objQuery->get("dtb_category", "level", "category_id = ?", array($_POST['parent_category_id']));
		
		if($level >= LEVEL_MAX) {
			$objErr->arrErr['category_name'] = "�� ".LEVEL_MAX."���ذʾ����Ͽ�ϤǤ��ޤ���<br>";
		}
	}
		
	// ��ʣ�����å�
	if(!isset($objErr->arrErr['category_name'])) {
		$objQuery = new SC_Query();
		$where = "parent_category_id = ? AND category_name = ?";
		$arrRet = $objQuery->select("category_id, category_name", "dtb_category", $where, array($_POST['parent_category_id'], $array['category_name']));
		// �Խ���Υ쥳���ɰʳ���Ʊ��̾�Τ�¸�ߤ�����
		if ($arrRet[0]['category_id'] != $_POST['category_id'] && $arrRet[0]['category_name'] == $_POST['category_name']) {
			$objErr->arrErr['category_name'] = "�� ����Ʊ�����Ƥ���Ͽ��¸�ߤ��ޤ���<br>";
		}
	}

	return $objErr->arrErr;
}


// �¤Ӥ�1�Ĳ���ID��������롣
function lfGetDownRankID($objQuery, $table, $pid_name, $id_name, $id) {
	// ��ID��������롣
	$col = "$pid_name";
	$where = "$id_name = ?";
	$pid = $objQuery->get($table, $col, $where, $id);
	// ���٤ƤλҤ�������롣
	$col = "$id_name";
	$where = "del_flg = 0 AND $pid_name = ? ORDER BY rank DESC";
	$arrRet = $objQuery->select($col, $table, $where, array($pid));
	$max = count($arrRet);
	$down_id = "";
	for($cnt = 0; $cnt < $max; $cnt++) {
		if($arrRet[$cnt][$id_name] == $id) {
			$down_id = $arrRet[($cnt + 1)][$id_name];
			break;
		}
	}
	return $down_id;
}

// �¤Ӥ�1�ľ��ID��������롣
function lfGetUpRankID($objQuery, $table, $pid_name, $id_name, $id) {
	// ��ID��������롣
	$col = "$pid_name";
	$where = "$id_name = ?";
	$pid = $objQuery->get($table, $col, $where, $id);
	// ���٤ƤλҤ�������롣
	$col = "$id_name";
	$where = "del_flg = 0 AND $pid_name = ? ORDER BY rank DESC";
	$arrRet = $objQuery->select($col, $table, $where, array($pid));
	$max = count($arrRet);
	$up_id = "";
	for($cnt = 0; $cnt < $max; $cnt++) {
		if($arrRet[$cnt][$id_name] == $id) {
			$up_id = $arrRet[($cnt - 1)][$id_name];
			break;
		}
	}
	return $up_id;
}

function lfCountChilds($objQuery, $table, $pid_name, $id_name, $id) {
	// ��ID���������
	$arrRet = sfGetChildrenArray($table, $pid_name, $id_name, $id);	
	return count($arrRet);
}

function lfUpRankChilds($objQuery, $table, $pid_name, $id_name, $id, $count) {
	// ��ID���������
	$arrRet = sfGetChildrenArray($table, $pid_name, $id_name, $id);	
	$line = sfGetCommaList($arrRet);
	$sql = "UPDATE $table SET rank = (rank + $count) WHERE $id_name IN ($line) ";
	$sql.= "AND del_flg = 0";
	$ret = $objQuery->exec($sql);
	return $ret;
}

function lfDownRankChilds($objQuery, $table, $pid_name, $id_name, $id, $count) {
	// ��ID���������
	$arrRet = sfGetChildrenArray($table, $pid_name, $id_name, $id);	
	$line = sfGetCommaList($arrRet);
	$sql = "UPDATE $table SET rank = (rank - $count) WHERE $id_name IN ($line) ";
	$sql.= "AND del_flg = 0";
	$ret = $objQuery->exec($sql);
	return $ret;
}
?>