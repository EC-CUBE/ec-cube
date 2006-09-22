<?php
$current_dir = realpath(dirname(__FILE__));
require_once($current_dir . "/../module/DB.php");

$objTar = "";

class SC_Tar{

	// コンストラクタ
	function SC_DbConn(){
		global $objTar;
	}
}

?>