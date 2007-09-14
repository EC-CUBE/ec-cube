<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
    var $arrSession;
    var $list_data;
    var $default_template;
    var $default_template_mobile;
    var $arrMagazineType;
    
    function LC_Page() {
        $this->tpl_mainpage = 'basis/template.tpl';
        $this->tpl_mainno = 'basis';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_subno = 'mail';
        $this->tpl_subtitle = '�ƥ�ץ졼������';
    }
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

if ( $_GET['mode'] == "delete" && sfCheckNumLength($_GET['id'])===true ){
    
    // ��Ͽ���
    $sql = "UPDATE dtb_mailtemplate SET del_flg = 1 WHERE template_id = ?";
    $conn->query($sql, array($_GET['id']));
    sfReload();
}

$sql = "SELECT * FROM dtb_mailtemplate WHERE del_flg = 0 ORDER BY template_id ASC";
$list_data = $conn->getAll($sql);
$linemax = count($list_data);

for($i = 0;$i < count($list_data);$i++){
   $split_data = explode(".",$list_data[$i]["create_date"]);
   $list_data[$i]["create_date"] = $split_data[0];    
}

//print_r($list_data);
$objPage->list_data = $list_data;
$objPage->arrMagazineType = $arrMagazineTypeAll;

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
?>