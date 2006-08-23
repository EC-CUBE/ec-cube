<?php

require_once("./require.php");

$objSess = new SC_Session();
$objSess->logout();

header("Location: /admin/index.php");

?>