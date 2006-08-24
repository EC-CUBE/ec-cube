<?php

require_once("../../require.php");
require_once("./index_csv.php");
//require_once("../../require2.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;
	var $arrProducts;
	var $arrPageMax;
	function LC_Page() {
//		$this->tpl_mainpage = 'products/index.tpl';
		$this->tpl_mainpage="products/test.tpl";

		$this->tpl_mainno = 'products';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_subno = 'index';
		$this->tpl_pager = ROOT_DIR . 'data/Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '商品マスタ';

	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

$arrProducts = Array
(
    '0' => Array
        (
            'product_id' => '18',
            'name' => 'test',
            'category_id' => '11',
            'main_list_image' => '08172054_44e458f942afc.gif',
            'status' => '1',
            'product_code' => 'cd 01',
            'price01' => '500',
            'price02' => '500',
            'stock' => '43',
            'stock_unlimited' => ""
        ),

    '1' => Array
        (
            'product_id' => '14',
            'name' => 'LPOエビス',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '14999',
            'stock_unlimited' => ""
        ),

    '2' => Array
        (
            'product_id' => '16',
            'name' => 'LPOエビス',
            'category_id' => '10',
            'main_list_image' => '08181941_44e59975c535d.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '14927',
            'stock_unlimited' => ""
        ),

    '3' => Array
        (
            'product_id' => '15',
            'name' => 'LPOエビス',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '14998',
            'stock_unlimited' => ""
        ),
    '4' => Array
        (
            'product_id' => '17',
            'name' => 'LPOエビス',
            'category_id' => '15',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '0',
            'stock_unlimited' => ""
        ),

    '5' => Array
        (
            'product_id' => '13',
            'name' => 'LPOエビス',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '15000',
            'stock_unlimited' => ""
        ),

    '6' => Array
        (
            'product_id' => '12',
            'name' => 'LPOエビス',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '15000',
            'stock_unlimited' => ""
        ),

    '7' => Array
        (
            'product_id' => '11',
            'name' => 'LPOエビス',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '15000',
            'stock_unlimited' => ""
        ),
    '8' => Array
        (
            'product_id' => '10',
            'name' => 'LPOエビス',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '15000',
            'stock_unlimited' => ""
        ),

    '9' => Array
        (
            'product_id' => '9',
            'name' => 'LPOエビス',
            'category_id' => '10',
            'main_list_image' => '08171740_44e42b7f67953.gif',
            'status' => '1',
            'product_code' => 'LPO',
            'price01' => '15000',
            'price02' => '15000',
            'stock' => '15000',
            'stock_unlimited' => ""
        )

);
$objPage->arrProducts = $arrProducts;

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

?>