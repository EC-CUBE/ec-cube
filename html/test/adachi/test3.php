<?php

require_once("../../require.php");

$objView = new SC_View();
$arrAssignVars = array(
    'test1' => '<script>alert("hello")</script>',
    'test2' => 'test2<br>',
    'arr'   => array('1','2','3')
);

$objView->_smarty->template_dir = '/home/web/dev.ec-cube.net/html/test/adachi/templates/';
$objView->_smarty->compile_dir  = '/home/web/dev.ec-cube.net/html/test/adachi/templates_c/';
$objView->assignArray($arrAssignVars);
$objView->display('/home/web/dev.ec-cube.net/html/test/adachi/templates/test3.tpl');