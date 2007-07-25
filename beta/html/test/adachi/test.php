<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// Smartyへのassign用連想配列
$arrAssignVars = array(
    'tpl_mainpage' => 'basis/kiyaku.tpl',
    'tpl_subnavi'  => 'basis/subnavi.tpl',
    'tpl_subno'    => 'kiyaku',
    'tpl_subtitle' => '会員規約登録',
    'tpl_mainno'   => 'basis'
);

/** MODEの判定 **/
switch(SC_Form::getMode()) {
/** DB登録処理 **/
case 'register':
    $objForm = lfInitRegisterMode();
    $objForm->convert();
    
    if ($objForm->validate()->is_ok() === true) {
        $arrAddInsertData = array(
            'create_date' => 'NOW()',
            'update_date' => 'NOW()',
            'del_flg' => '0'
        );
        $objForm->insert('dtb_baseinfo', $arrAddInsertData);
    } else {
        $arrAssignVars['arrErr']  = $objForm->getEM();
        $arrAssignVars['arrForm'] = $objForm->getParams(); 
    }
    
    break:
/** 削除 **/
case 'delete':
    
    $objForm = lfInitDeleteMode();
    
    if ($objForm->validate()->is_ok === true) {
        sfDeleteRankRecord('dtb_kiyaku', 'kiyaku_id', $objForm->getParams(), '', true);
        sfReload();
    } else {
        sfDispError('PAGE_ERROR');
    }
    
    break;
/** 編集前処理 **/
case 'pre_edit':
    $objForm = lfInitPreEditMode();
    
    if ($objForm->validate()->is_ok === true) {
        // 編集項目をDBより取得する。
        $where = "kiyaku_id = ?";
        $objQuery = new SC_Query();
        $arrKiyakuInfo = $objQuery->select("kiyaku_text, kiyaku_title", "dtb_kiyaku", $where, array($_POST['kiyaku_id']));
        
        // 入力項目にカテゴリ名を入力する。
        $arrAssignVars['kiyaku_title'] = $arrKiyakuInfo[0]['kiyaku_title'];
        $arrAssignVars['kiyaku?text']  = $arrKiyakuInfo[0]['kiyaku_text'];
        $arrAssignVars['tpl_kiyaku_id'] = $objForm->getParams();
    } else {
        sfDispPage();
    }
    
    break;
case 'down':
	sfRankDown("dtb_kiyaku", "kiyaku_id", $_POST['kiyaku_id']);
	// 再表示
	sfReload();
	break;
case 'up':
	sfRankUp("dtb_kiyaku", "kiyaku_id", $_POST['kiyaku_id']);
	// 再表示
	sfReload();
	break;
default:
	break;
}

// 規格の読込
$where = "del_flg <> 1";
$objQuery->setOrder("rank DESC");
$arrAssignVars['arrKiyaku'] = $objQuery->select("kiyaku_title, kiyaku_text, kiyaku_id", "dtb_kiyaku", $where);

$objView = new SC_AdminView();
$objView->assignArray($arrAssignVars);
$objView->display(MAIN_FRAME);

function lfInitEditMode(){
    $arrParamsInfo = array(
        'kiyaku_text' => array(
            'dispName'     => '本文',
            'convertType'  => 'KVa',
            'validateType' => array(
                '' => true,
                'min'     => 1,
                'max'     => 5,
                'type'    => 'alnum',
                'htmlTag' => array(),
                
            )
        )
    );
    
    $objForm = new SC_Form($_POST, $arrParamsInfo);
    return $objForm;
}