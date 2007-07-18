<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * モバイルサイト/ご利用規約
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** 必ず変更する **/
		$this->tpl_mainpage = 'guide/kiyaku.tpl';	// メインテンプレート
		$this->tpl_title = 'ご利用規約';
	}
}

$objPage = new LC_Page();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

// 利用規約を取得する。
lfGetKiyaku(intval(@$_GET['page']), $objPage);

$objView = new SC_MobileView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

/**
 * 利用規約を取得し、ページオブジェクトに格納する。
 *
 * @param integer $index 規約のインデックス
 * @param object &$objPage ページオブジェクト
 * @return void
 */
function lfGetKiyaku($index, &$objPage)
{
	$objQuery = new SC_Query();
	$objQuery->setorder('rank DESC');
	$arrRet = $objQuery->select('kiyaku_title, kiyaku_text', 'dtb_kiyaku', 'del_flg <> 1');

	$number = count($arrRet);
	if ($number > 0) {
		$last = $number - 1;
	} else {
		$last = 0;
	}

	if ($index < 0) {
		$index = 0;
	} elseif ($index > $last) {
		$index = $last;
	}

	$objPage->tpl_kiyaku_title = @$arrRet[$index]['kiyaku_title'];
	$objPage->tpl_kiyaku_text = @$arrRet[$index]['kiyaku_text'];
	$objPage->tpl_kiyaku_index = $index;
	$objPage->tpl_kiyaku_last_index = $last;
	$objPage->tpl_kiyaku_is_first = $index <= 0;
	$objPage->tpl_kiyaku_is_last = $index >= $last;
}
?>
