<?php

// SC_SendMailの拡張
if(file_exists(MODULE2_PATH . "mdl_speedmail/SC_SendMail_Ex.php")) {
	require_once(MODULE2_PATH . "mdl_speedmail/SC_SendMail_Ex.php");
} else {
	require_once(CLASS_EX_PATH . "SC_SendMail_Ex.php");
}

?>