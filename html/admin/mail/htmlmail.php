<?php

require_once("../require.php");

class LC_Page {
	
	var $arrForm;
	
	var $arrTempProduct;
	var $subProductNum;
	var $arrFileName;
	
	
	function LC_Page() {
		$this->tpl_mainpage = 'mail/htmlmail.tpl';
		$this->tpl_mainno = 'mail';
		$this->tpl_subnavi = 'mail/subnavi.tpl';
		$this->tpl_subno = "template";
	}
}


class LC_Products{
	
	var $conn;
	var $arrProduct;
	var $arrProductKey;
	
	function LC_Products ($conn=""){
		
		$DB_class_name = "SC_DbConn";
		if ( is_object($conn)){
			if ( is_a($conn, $DB_class_name)){
				// $connが$DB_class_nameのインスタンスである
				$this->conn = $conn;
			}
		} else {
			if (class_exists($DB_class_name)){
				//$DB_class_nameのインスタンスを作成する
				$this->conn = new SC_DbConn();			
			}
		}
	}
	
	function setProduct($keyname, $id) {
		
		if ( sfCheckNumLength($id) ){
			$result = $this->getProductData($id);
		}
		
		if ( $result && (in_array($keyname, $this->arrProductKey) ) ){
	
			$this->arrProduct["${keyname}"] = $result;
		}
	}	
	
	function getProductData($id){
		$conn = $this->conn;
		// 商品情報を取得する
		$sql = "SELECT * FROM dtb_products WHERE product_id = ?";
		$result = $conn->getAll($sql, array($id));
		if ( is_array($result) ){
			$return = $result[0];
		}
		return $return;	
	}

	function getProductImageData($id){
		$conn = $this->conn;
		// 商品画像情報を取得する
		$sql = "SELECT main_image FROM dtb_products WHERE product_id = ?";
		$result = $conn->getAll($sql, array($id));
		if ( is_array($result) ){
			$return = $result[0]["main_image"];
		}
		return $return;	
	}
	function setHiddenList($arrPOST) {
		foreach($this->arrProductKey as $val) {
			$key = "temp_" . $val;
			if($arrPOST[$key] != "") {
				$this->setProduct($val, $arrPOST[$key]);
			}
		}
	}
}

// 登録カラム
$arrRegist = array(
					  "subject", "charge_image", "mail_method", "header", "main_title", "main_comment", "main_product_id", "sub_title", "sub_comment"
					, "sub_product_id01", "sub_product_id02", "sub_product_id03", "sub_product_id04", "sub_product_id05", "sub_product_id06", "sub_product_id07"
					, "sub_product_id08", "sub_product_id09", "sub_product_id10", "sub_product_id11", "sub_product_id12"
					);
					
// 既存の登録済み商品から画像表示を必要とする項目リスト					
$arrFileList = array(
						"main_product_id", "sub_product_id01", "sub_product_id02", "sub_product_id03", "sub_product_id04", "sub_product_id05"
						, "sub_product_id06", "sub_product_id07", "sub_product_id08", "sub_product_id09", "sub_product_id10", "sub_product_id11", "sub_product_id12"
					);

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// 認証可否の判定
sfIsSuccess($objSess);


// 画像処理クラス設定
$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
$objUpFile->addFile("メール担当写真", 'charge_image', array('jpg'),IMAGE_SIZE, true, HTMLMAIL_IMAGE_WIDTH, HTMLMAIL_IMAGE_HEIGHT);

// POST値の引継ぎ&入力値の変換
$objPage->arrForm = lfConvData($_POST);

// Hiddenからのデータを引き継ぐ
$objUpFile->setHiddenFileList($_POST);

switch ($_POST['mode']){
	
	//画像アップロード
	case 'upload_image':
	// 画像保存処理
	$objPage->arrErr[$_POST['image_key']] = $objUpFile->makeTempFile($_POST['image_key']);
	break;
	
	//確認
	case 'confirm':
	
	// エラーチェック
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	//ファイル存在チェック
	$objPage->arrErr = array_merge((array)$objPage->arrErr, (array)$objUpFile->checkEXISTS());
		
	//エラーなしの場合、確認ページへ
	 if (!$objPage->arrErr){
		// 	アップロードファイル情報配列を渡す。
		$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
		//削除要求のあった画像を表示しない
		for($i = 1; $i <= HTML_TEMPLATE_SUB_MAX; $i++) {
			if($_POST['delete_sub'.$i] == "1") {
				$arrSub['delete'][$i] = "on";
			}else{
				$arrSub['delete'][$i] = "";
			}
		}
		$objPage->arrSub = $arrSub;
		$objPage->tpl_mainpage = 'mail/htmlmail_confirm.tpl';
	 }
	break;
	
	// 確認ページからの戻り
	case 'return':
	break;
	
	//　テンプレート登録
	case 'complete':
	// 入力値の変換
	$objPage->arrForm = lfConvData($_POST);
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);	// 入力エラーチェック

	// アップロード画像をセーブディレクトリに移行
	$objUpFile->moveTempFile();

	// DB登録
	if (is_numeric($objPage->arrForm["template_id"])) {	//　編集時
		lfUpdateData($arrRegist);
	} else {
		ifRegistData($arrRegist);
	}
	$objPage->tpl_mainpage = 'mail/htmlmail_complete.tpl';
	break;
}

// 検索結果からの編集時
if ($_GET["mode"] == "edit" && is_numeric($_GET["template_id"])) {
	$objPage->edit_mode = "on";
	//テンプレート情報読み込み
	lfSetRegistData($_GET["template_id"]);
	// DBデータから画像ファイル名の読込
	$objUpFile->setDBFileList($objPage->arrForm);

}

if ($_GET['mode'] != 'edit'){
//登録情報の読み込み
$objPage->arrFileName = lfGetProducts();
}

// HIDDEN用に配列を渡す。
$objPage->arrHidden = array_merge((array)$objPage->arrHidden, (array)$objUpFile->getHiddenFileList());
// アップロードファイル情報配列を渡す。
$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-------------------------------------------------------------------------------------------------------------------------

/* 商品画像の読み込み */
function lfGetProducts() {
	global $objQuery;
	
	if ($_POST['main_product_id'] != ""){
	$MainFile = $objQuery->select("main_image, name, product_id", "dtb_products", "product_id=?", array($_POST['main_product_id']));
	$arrFileName[0] = $MainFile[0];
	}
	for($i = 1; $i <= HTML_TEMPLATE_SUB_MAX; $i++) {
		$sub_keyname = "sub_product_id" . sprintf("%02d", $i);
		if($_POST[$sub_keyname] != "") {
			$arrSubFile = $objQuery->select("main_image, name, product_id", "dtb_products", "product_id = ?", array($_POST[$sub_keyname]));
			$arrFileName[$i] = $arrSubFile[0];
		}
	}
	return $arrFileName;
}

/* 登録済みデータ読み込み */
function lfSetRegistData($template_id) {
	global $objQuery;
	global $objPage;
	$arrRet = $objQuery->select("*", "dtb_mailmaga_template", "template_id=?", array($template_id));
	$arrProductid = $arrRet[0];
	//画像以外の情報取得
	$objPage->arrForm = $arrRet[0];
		if ($arrProductid['main_product_id'] != ""){
			$MainFile = $objQuery->select("main_image, name, product_id", "dtb_products", "product_id=?", array($arrProductid['main_product_id']));
			$arrFileName[0] = $MainFile[0];
		}
	for ($i=1; $i<=HTML_TEMPLATE_SUB_MAX; $i++){
		if ($arrProductid['sub_product_id'.sprintf("%02d", $i)] != ""){
			$arrSubFile = $objQuery->select("main_image, name, product_id", "dtb_products", "product_id=?", array($arrProductid['sub_product_id'.sprintf("%02d", $i)]));
			$arrFileName[$i] = $arrSubFile[0];
		}
	}
	//画像の情報取得
	$objPage->arrFileName = $arrFileName;
	
	return $objPage;
}

// 編集データ取得
function lfGetEditData($id, $arrIdData) {
	global $conn;

	// DB登録情報
	$sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ? AND del_flg = 0";
	$result = $conn->getAll($sql, array($id));

	//　画像ファイル名
	for ($i = 0; $i < count($arrIdData); $i ++) {
		$data = "";
		if (is_numeric($result[0][ $arrIdData[$i] ]) ) {
			$sql = "SELECT name,product_id,main_image FROM dtb_products WHERE product_id = ?";
			$data = $conn->getAll($sql, array($result[0][ $arrIdData[$i] ]));
		}
		$arrFileName[] = $data[0];
	}
 	
	return array($result[0], $arrFileName);
}

// 確認データ取得
function lfGetConfirmData($arrPOST, $arrIdData) {
	global $conn;
	//　画像ファイル名
	for ($i = 0; $i < count($arrIdData); $i ++) {
		$data = "";
		if (is_numeric($arrPOST[ $arrIdData[$i] ]) ) {
			$sql = "SELECT name,product_id,main_image FROM dtb_products WHERE product_id = ?";
			$data = $conn->getAll($sql, array($arrPOST[ $arrIdData[$i] ]));
		}
		$arrFileName[] = $data[0];
	}
 	return array($arrPOST, $arrFileName);
}

// データベース登録
function ifRegistData($arrRegist) {
	global $conn;
	global $objUpFile;

	foreach ($arrRegist as $data) {
		if (strlen($_POST[$data]) > 0) {
			$arrRegistValue[$data] = $_POST[$data];
		}
	}
	$arrRegistValue["creator_id"] = $_SESSION["member_id"];		// 登録者ID（管理画面）
	$uploadfile = $objUpFile->getDBFileList();
	//削除要求のあった商品を削除する
	for ($i = 1; $i <= HTML_TEMPLATE_SUB_MAX; $i++){
		if ($_POST['delete_sub'.$i] == '1'){
			$arrRegistValue['sub_product_id'.sprintf("%02d", $i)] = NULL;
		}
	}
	$arrRegistValue = array_merge($arrRegistValue, $uploadfile);
	$conn->autoExecute("dtb_mailmaga_template", $arrRegistValue);
}

// データ更新
function lfUpdateData($arrRegist) {
	global $conn;
	global $objUpFile;

	foreach ($arrRegist as $data) {
		if (strlen($_POST[$data]) > 0) {
			$arrRegistValue[$data] = $_POST[$data];
		}
	}
	$arrRegistValue["creator_id"] = $_SESSION["member_id"];	
	$arrRegistValue["update_date"] = "NOW()";
	$uploadfile = $objUpFile->getDBFileList();
	//削除要求のあった商品を削除する
	for ($i = 1; $i <= HTML_TEMPLATE_SUB_MAX; $i++){
		if ($_POST['delete_sub'.$i] == '1'){
			$arrRegistValue['sub_product_id'.sprintf("%02d", $i)] = NULL;
		}
	}
	$arrRegistValue = array_merge($arrRegistValue, $uploadfile);
	
	$conn->autoExecute("dtb_mailmaga_template", $arrRegistValue, "template_id = ". addslashes($_POST["template_id"]));
}

// 入力値変換
function lfConvData( $data ){
	
	 // 文字列の変換（mb_convert_kanaの変換オプション）							
	$arrFlag = array(
					  "header" => "aKV"
					 ,"subject" => "aKV"
					 ,"main_title" => "aKV"
 					 ,"main_comment" => "aKV"
 					 ,"main_product_id" => "aKV"
 					 ,"sub_title" => "aKV"
					 ,"sub_comment" => "aKV"
				);
		
	if ( is_array($data) ){
		foreach ($arrFlag as $key=>$line) {
			$data[$key] = mb_convert_kana($data[$key], $line);
		}
	}

	return $data;
}

// 入力エラーチェック
function lfErrorCheck($array) {
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("メール形式", "mail_method"), array("EXIST_CHECK", "ALNUM_CHECK"));
	$objErr->doFunc(array("Subject", "subject", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("ヘッダーテキスト", 'header', LTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK") );
	$objErr->doFunc(array("メイン商品タイトル", 'main_title', STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK") );
	$objErr->doFunc(array("メイン商品コメント", 'main_comment', LTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("メイン商品画像", "main_product_id"), array("EXIST_CHECK"));
	$objErr->doFunc(array("サブ商品群タイトル", "sub_title", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("サブ商品群コメント", "sub_comment", LTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	
	return $objErr->arrErr;
}

?>