<?php
$current_dir = realpath(dirname(__FILE__));
require_once($current_dir . "/../module/Tar.php");

$objTar = "";

class SC_Tar{

	// ���󥹥ȥ饯��
	function SC_DbConn(){
		global $objTar;
	}
}

?>