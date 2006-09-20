<?php

require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $body;
	var $list_data;

	function LC_Page() {
		$this->tpl_mainpage = 'mail/preview.tpl';
	}
}

//---- ページ初期設定
$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objDate = new SC_Date();

// 認証可否の判定
sfIsSuccess($objSess);


if ( $_POST['body'] ){
	$objPage->body = $_POST['body'];

// HTMLメールテンプレートのプレビュー
} elseif ($_REQUEST["method"] == "template" && sfCheckNumLength($_REQUEST['id'])) {

	$sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ?";
	$result = $conn->getAll($sql, array($_REQUEST["id"]));
	$objPage->list_data = $result[0];
	
	//メール担当写真の表示
	$objUpFile = new SC_UploadFile(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
	$objUpFile->addFile("メール担当写真", 'charge_image', array('jpg'), IMAGE_SIZE, true, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
	$objUpFile->setDBFileList($objPage->list_data);
	// Form用配列を渡す。
	$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
	
	// メイン商品の情報取得
	$sql = "SELECT name, main_image, point_rate, deliv_fee, price01_min, price01_max, price02_min, price02_max FROM vw_products_allclass AS allcls WHERE product_id = ?";
	$main = $conn->getAll($sql, array($objPage->list_data["main_product_id"]));
	$objPage->list_data["main"] = $main[0];

	// サブ商品の情報取得
	$sql = "SELECT product_id, name, main_list_image, price01_min, price01_max, price02_min, price02_max FROM vw_products_allclass WHERE product_id = ?";
	$k = 0;
	$l = 0;
	for ($i = 1; $i <= 12; $i ++) {
		if ($l == 4) {
			$l = 0;
			$k ++;
		}
		$result = "";
		$j = sprintf("%02d", $i);
		if ($i > 0 && $i < 5 ) $k = 0;
		if ($i > 4 && $i < 9 ) $k = 1;
		if ($i > 8 && $i < 13 ) $k = 2;	
		
		if (is_numeric($objPage->list_data["sub_product_id" .$j])) {
			$result = $conn->getAll($sql, array($objPage->list_data["sub_product_id" .$j]));
			$objPage->list_data["sub"][$k][$l] = $result[0];
			$objPage->list_data["sub"][$k]["data_exists"] = "OK";	//当該段にデータが１つ以上存在するフラグ
		}
		$l ++;
	}
	$objPage->tpl_mainpage = 'mail/html_template.tpl';

} elseif ( sfCheckNumLength($_GET['send_id']) || sfCheckNumLength($_GET['id'])){
	if (is_numeric($_GET["send_id"])) {
		$id = $_GET["send_id"];
		$sql = "SELECT body, mail_method FROM dtb_send_history WHERE send_id = ?";
	} else {
		$sql = "SELECT body, mail_method FROM dtb_mailmaga_template WHERE template_id = ?";
		$id = $_GET['id'];
	}
	$result = $conn->getAll($sql, array($id));
	
	
	if ( $result ){
		if ( $result[0]["mail_method"] == 2 ){
			// テキスト形式の時はタグ文字をエスケープ
			$objPage->escape_flag = 1;
		}
		$objPage->body = $result[0]["body"];
	}

}
	
$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);

?>