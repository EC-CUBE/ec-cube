<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/../data/conf/conf.php");	

print("ok0");

require_once($include_dir . "/../data/lib/glib.php");
require_once($include_dir . "/../data/lib/slib.php");
require_once($include_dir . "/../data/class/SC_View.php");
print("ok1");

require_once($include_dir . "/../data/class/SC_DbConn.php");

print("ok2");

require_once($include_dir . "/../data/class/SC_Session.php");
require_once($include_dir . "/../data/class/SC_Query.php");
require_once($include_dir . "/../data/class/SC_SelectSql.php");
require_once($include_dir . "/../data/class/SC_CheckError.php");
require_once($include_dir . "/../data/class/SC_PageNavi.php");
require_once($include_dir . "/../data/class/SC_Date.php");
require_once($include_dir . "/../data/class/SC_Image.php");
require_once($include_dir . "/../data/class/SC_UploadFile.php");
require_once($include_dir . "/../data/class/SC_SiteInfo.php");
require_once($include_dir . "/../data/class/GC_SendMail.php");
require_once($include_dir . "/../data/class/SC_FormParam.php");
require_once($include_dir . "/../data/class/SC_CartSession.php");
require_once($include_dir . "/../data/class/SC_SiteSession.php");
require_once($include_dir . "/../data/class/SC_Customer.php");
require_once($include_dir . "/../data/class/SC_Cookie.php");
require_once($include_dir . "/../data/class/SC_Page.php");
require_once($include_dir . "/../data/class/SC_Pdf.php");
require_once($include_dir . "/../data/include/page_layout.inc");

?>