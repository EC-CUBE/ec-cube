<?php
	require_once("../../require.php");
	$objMasterData = new SC_DB_MasterData_Ex();
	
	$objMasterData->insertMasterData("mtb_constants", "OS_TYPE", "\"WIN\"", "OS種別：WIN|LINUX");
	$objMasterData->insertMasterData("mtb_constants", "SMTP_HOST", "\"210.188.192.18\"", "SMTPサーバ");
	$objMasterData->insertMasterData("mtb_constants", "SMTP_PORT", "\"25\"", "SMTPポート");	
	$objMasterData->insertMasterData("mtb_constants", "MODULE2_DIR", "\"downloads/module2/\"", "EC-CUBE2対応モジュール");
	$objMasterData->insertMasterData("mtb_constants", "MODULE2_PATH", "DATA_PATH . MODULE2_DIR", "EC-CUBE2対応モジュール");
	$arrMasterData = $objMasterData->getMasterData("mtb_constants");
	$objMasterData->createCache("mtb_constants", $arrMasterData, true, array("id", "remarks", "rank"));
?>
