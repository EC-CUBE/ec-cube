<?php
require_once("../../require.php");

$test= "eee<{assign_product_id}>aaaa";
$test = ereg_replace("<{assign_product_id}>", "test", $test);
echo $test;
?>