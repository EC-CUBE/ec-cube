<?php

require_once("../../require.php");

sfGetParentsArray("dtb_category","parent_category_id", "category_id", 230);
sfGetChildrenArray("dtb_category","parent_category_id", "category_id", 6);

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
		$arrParents = array_merge($arrParents, $ret);
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


?>