<?php
/**
 * 
 * @copyright   2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version CVS: $Id: blayn.php 1.30 2006-06-04 06:38:01Z matsumura $
 * @link        http://www.lockon.co.jp/
 *
 */
 

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

echo "aaa";

$objView->assignobj($objPage);      //変数をテンプレートにアサインする
$objView->display(MAIN_FRAME);      //テンプレートの出力

// ------------------------------------------------------------------------------------------------------

function lfRegist() {
    
    global $objQuery;
    

}
?>
