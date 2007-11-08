<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
$PAYGENT_BATCH_DIR = realpath(dirname( __FILE__));
require_once($PAYGENT_BATCH_DIR . "/mdl_paygent.inc");
require_once($PAYGENT_BATCH_DIR . "/../../../conf/conf.php" );
require_once($PAYGENT_BATCH_DIR . "/../../../class/SC_DbConn.php");
require_once($PAYGENT_BATCH_DIR . "/../../../class/SC_Query.php");
require_once($PAYGENT_BATCH_DIR . "/../../../lib/glib.php");
require_once($PAYGENT_BATCH_DIR . "/../../../lib/slib.php");
sfPaygentBatch();


print("OK\n");


?>