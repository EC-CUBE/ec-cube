<?php
/*
 * MyPage
 */

require_once("../require.php");

class LC_Page{
	function LC_Page() {
		$this->tpl_mainpage = 'mypage/history.tpl';
		$this->tpl_title = 'MYページ/購入履歴一覧';
		session_cache_limiter('private-no-expire');
	}
}

define ("HISTORY_NUM", 5);

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();
$pageNo = isset($_GET['pageno']) ? $_GET['pageno'] : 0;

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

// ログインチェック
if(!isset($_SESSION['customer'])) {
	sfDispSiteError(CUSTOMER_ERROR);
}

$col = "order_id, create_date, payment_id, payment_total";
$from = "dtb_order";
$where = "del_flg = 0 AND customer_id=?";
$arrval = array($objCustomer->getvalue('customer_id'));
$order = "order_id DESC";

$linemax = $objQuery->count($from, $where, $arrval);
$objPage->tpl_linemax = $linemax;

// 取得範囲の指定(開始行番号、行数のセット)
$objQuery->setlimitoffset(HISTORY_NUM, $pageNo);
// 表示順序
$objQuery->setorder($order);

//購入履歴の取得
$objPage->arrOrder = $objQuery->select($col, $from, $where, $arrval);

// next
if ($pageNo + HISTORY_NUM < $linemax) {
	$next = "<a href='history.php?pageno=" . ($pageNo + HISTORY_NUM) . "'>次へ→</a>";
} else {
	$next = "次へ→";
}

// previous
if ($pageNo - HISTORY_NUM > 0) {
	$previous = "<a href='history.php?pageno=" . ($pageNo - HISTORY_NUM) . "'>←前</a>";
} elseif ($pageNo == 0) {
	$previous = "←前";
} else {
	$previous = "<a href='history.php?pageno=0'>←前</a>";
}

$objPage->tpl_strnavi = $previous . " | " . $next;
$objView->assignobj($objPage);				//$objpage内の全てのテンプレート変数をsmartyに格納
$objView->display(SITE_FRAME);				//パスとテンプレート変数の呼び出し、実行
?>