<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("./require.php");

$objSess = new SC_Session();
$objSess->logout();

header("Location: " . URL_DIR . "admin/index.php");
?>