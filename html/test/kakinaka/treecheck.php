<?php

require_once("../../require.php");

$objView = new SC_UserView("./templates/");

sfprintr($_POST);

$objView->display("treecheck.tpl")

?>