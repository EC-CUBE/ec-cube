<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
class LC_CatPage {
    function LC_CatPage() {
        /** ɬ���ѹ����� **/
        $this->tpl_mainpage = 'include/bloc/category.tpl';	// �ᥤ��
    }
}

$objSubPage = new LC_CatPage();
$objSubView = new SC_UserView(USER_PATH);

// ������Υ��ƥ���ID��Ƚ�ꤹ��
$category_id = sfGetCategoryId($_GET['product_id'], $_GET['category_id']);

// ������Υ��ƥ���ID
$objSubPage->tpl_category_id = $category_id;
$objSubPage = lfGetCatTree($category_id, true, $objSubPage);

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
// ���ƥ���ĥ꡼�μ���
function lfGetCatTree($parent_category_id, $count_check = false, $objSubPage) {
    $objQuery = new SC_Query();
    $col = "*";
    $from = "dtb_category left join dtb_category_total_count using (category_id)";
    // ��Ͽ���ʿ��Υ����å�
    if($count_check) {
        $where = "del_flg = 0 AND product_count > 0";
    } else {
        $where = "del_flg = 0";
    }
    $objQuery->setoption("ORDER BY rank DESC");
    $arrRet = $objQuery->select($col, $from, $where);

    $arrParentID = sfGetParents($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $parent_category_id);
    $arrBrothersID = sfGetBrothersArray($arrRet, 'parent_category_id', 'category_id', $arrParentID);
    $arrChildrenID = sfGetUnderChildrenArray($arrRet, 'parent_category_id', 'category_id', $parent_category_id);

    $objSubPage->root_parent_id = $arrParentID[0];

    $arrDispID = array_merge($arrBrothersID, $arrChildrenID);

    foreach($arrRet as $key => $array) {
        foreach($arrDispID as $val) {
            if($array['category_id'] == $val) {
                $arrRet[$key]['display'] = 1;
                break;
            }
        }
    }

    $objSubPage->arrTree = $arrRet;
    return $objSubPage;
}
?>
