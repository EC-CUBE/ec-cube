<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

$objQuery = new SC_Query();

$objQuery->begin();
//$objQuery->query("START TRANSACTION");

$objQuery->insert("test",array("test"=>"test"));
//$objQuery->query("insert into test values('test')");

//$objQuery->rollback();
$objQuery->commit();

//-------------------------------------------------------------------------------------------------------

?>