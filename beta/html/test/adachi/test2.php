<?php

require_once("../../require.php");
require_once("SC_FormParamsManager.php");

error_reporting(E_ALL);

// Smartyへのassign用連想配列
$arrAssignVars = array(
    'tpl_mainpage' => 'basis/kiyaku.tpl',
    'tpl_subnavi'  => 'basis/subnavi.tpl',
    'tpl_subno'    => 'kiyaku',
    'tpl_subtitle' => '会員規約登録',
    'tpl_mainno'   => 'basis'
);



$objView = new SC_View();
$objView->_smarty->template_dir = '/home/web/dev.ec-cube.net/html/test/adachi/templates/';
$objView->_smarty->compile_dir  = '/home/web/dev.ec-cube.net/html/test/adachi/templates/';
$objView->assignArray($arrAssignVars);
$objView->display('/home/web/dev.ec-cube.net/html/test/adachi/templates/test2.tpl');
?>