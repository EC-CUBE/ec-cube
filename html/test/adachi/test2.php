<?php

require_once("../../require.php");
require_once("SC_FormParamsManager.php");

// Smartyへのassign用連想配列
$arrAssignVars = array(
    'tpl_mainpage' => 'basis/kiyaku.tpl',
    'tpl_subnavi'  => 'basis/subnavi.tpl',
    'tpl_subno'    => 'kiyaku',
    'tpl_subtitle' => '会員規約登録',
    'tpl_mainno'   => 'basis'
);



$objView = new SC_AdminView();
$objView->assignArray($arrAssignVars);
$objView->display(MAIN_FRAME);
?>