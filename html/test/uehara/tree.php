<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");


$dir = USER_PATH;

$objView = new SC_UserView("./templates/");
$objQuery = new SC_Query();

if (is_dir($dir)) { 
    if ($dh = opendir($dir)) { 
        while (($file = readdir($dh)) !== false) { 
            echo "filename: ". $file . " : filetype: " . filetype($dir . $file) . "<br>\n"; 
        } 
        closedir($dh); 
    } 
} 

//$objView->assignobj($objPage);
$objView->display("tree.tpl")

//-----------------------------------------------------------------------------------------------------------------------------------

?>