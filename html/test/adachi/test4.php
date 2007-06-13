<?php

require_once('DB.php');

$db_type     = 'mysql';
$db_user     = 'user_name';
$db_password = 'password';
$db_server   = '127.0.0.1';
$db_name     = 'db_name';

$dsn = "$db_type://$db_user:$db_password@$db_server/$db_name";

$db = DB::connect($dsn);

if (PEAR::isError($db)) {
    die($db->getMessage());
}

?>
