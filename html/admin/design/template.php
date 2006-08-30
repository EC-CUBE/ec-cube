<?php

require_once("../../require.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;
	var $arrSubnavi = array(
		1 => 'top',
		2 => 'product',
		3 => 'detail',
		4 => 'mypage',
	);

	function LC_Page() {
		$this->tpl_mainpage = 'design/template.tpl';
		$this->tpl_subnavi = 'design/subnavi.tpl';
		$this->tpl_subno = 'template';
		$this->tpl_subno_template = $this->arrSubnavi[1];
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = 'テンプレート選択';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// GETの値を受け取る
$get_tpl_subno_template = $_GET['tpl_subno_template'];

// GETで値が送られている場合にはその値を元に画面表示を切り替える
if ($get_tpl_subno_template != ""){
	// 送られてきた値が配列に登録されていなければTOPを表示
	if (in_array($get_tpl_subno_template,$objPage->arrSubnavi)){
		$tpl_subno_template = $get_tpl_subno_template;
	}else{
		$tpl_subno_template = $objPage->arrSubnavi[1];
	}
} else {
	// GETで値がなければPOSTの値を使用する
	if ($_POST['tpl_subno_template'] != ""){
		$tpl_subno_template = $_POST['tpl_subno_template'];
	}else{
		$tpl_subno_template = $objPage->arrSubnavi[1];
	}
}
$objPage->tpl_subno_template = $tpl_subno_template;

// 登録を押されたばあにはDBへデータを更新に行く
if ($_POST['mode'] == "confirm"){
	// DBへデータ更新
	lfUpdData();
	
	// テンプレートの上書き
	lfChangeTemplate();
	
	sfprintr($_POST);
}

// POST値の引き継ぎ
$objPage->arrForm = $_POST;

// 画像取得
$tpl_arrTemplate = array();
$objPage->arrTemplate = lfgetTemplate();

// デフォルトチェック取得
$objPage->MainImage = $objPage->arrTemplate['check'];
$objPage->arrTemplate['check'] = array($objPage->arrTemplate['check']=>"check");

sfprintr($objPage->arrTemplate);

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

/**************************************************************************************************************
 * 関数名	：lfgetTemplate
 * 処理内容	：画面に表示する画像を取得する
 * 引数		：なし
 * 戻り値	：画面に表示する画像(配列)
 **************************************************************************************************************/
function lfgetTemplate(){
	global $objPage;
	$filepath = "/test/kakinaka/";
	
	$arrTemplateImage = array();	// 画面表示画像格納用
	$Image = "";					// イメージの配列要素名格納用
	$disp = "";
	$arrDefcheck = array();			// radioボタンのデフォルトチェック格納用
	
	// DBから現在選択されているデータ取得
	$arrDefcheck = lfgetTemplaeBaseData();
	
	// テンプレートデータを取得する
	$objQuery = new SC_Query();
	$sql = "SELECT template_code,template_name,file_path FROM dtb_template ORDER BY create_date DESC";
	$arrTemplate = $objQuery->getall($sql);
	
	switch($objPage->tpl_subno_template) {
		// TOP
		case $objPage->arrSubnavi[1]:
			$Image = "TopImage.jpg";			// イメージの配列要素名格納用
			$disp = $objPage->arrSubnavi[1];
			break;
			
		// 商品一覧
		case $objPage->arrSubnavi[2]:
			$Image = "ProdImage.jpg";			// イメージの配列要素名格納用
			$disp = $objPage->arrSubnavi[2];
			break;
			
		// 商品詳細
		case $objPage->arrSubnavi[3]:
			$Image = "DetailImage.jpg";			// イメージの配列要素名格納用
			$disp = $objPage->arrSubnavi[3];
			break;
			
		// MYページ
		case $objPage->arrSubnavi[4]:
			$Image = "MypageImage.jpg";			//イメージの配列要素名格納用
			$disp = $objPage->arrSubnavi[4];
			break;
	}

	// 画像表示配列作成
	foreach($arrTemplate as $key => $val){
		$arrTemplateImage['image'][$val['template_code']] = $filepath . $val['template_code'] . "/" . $Image;
		$arrTemplateImage['code'][$key] = $val['template_code'];
	}
	
	
	// 初期チェック
	if (isset($arrDefcheck[$disp])){
		$arrTemplateImage['check'] = $arrDefcheck[$disp];
	}else{
		$arrTemplateImage['check'] = 1;
	}
	
	return $arrTemplateImage;
}

/**************************************************************************************************************
 * 関数名	：lfgetTemplaeBaseData
 * 処理内容	：DBに保存されているテンプレートデータを取得する
 * 引数		：なし
 * 戻り値	：DBに保存されているテンプレートデータ(配列)
 **************************************************************************************************************/
function lfgetTemplaeBaseData(){
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$sql = "";						// データ取得SQL生成用
	$arrRet = array();				// データ取得用
	
	$sql = "SELECT top_tpl AS top, product_tpl AS product, detail_tpl AS detail, mypage_tpl AS mypage FROM dtb_baseinfo";
	$arrRet = $objDBConn->getAll($sql);
	
	return $arrRet[0];
}

/**************************************************************************************************************
 * 関数名	：lfUpdData
 * 処理内容	：DBにデータを保存する
 * 引数		：なし
 * 戻り値	：成功 TRUE、エラー FALSE
 **************************************************************************************************************/
function lfUpdData(){
	global $objPage;
	$objDBConn = new SC_DbConn;		// DB操作オブジェクト
	$sql = "";						// データ取得SQL生成用
	$arrRet = array();				// データ取得用(更新判定)

	// データ取得	
	$sql = "SELECT top_tpl AS top, product_tpl AS product, detail_tpl AS detail, mypage_tpl AS mypage FROM dtb_baseinfo";
	$arrRet = $objDBConn->getAll($sql);

	$chk_tpl = $_POST['check_template'];
	// データが取得できなければINSERT、できればUPDATE
	if (isset($arrRet[0])){
		// UPDATE
		$arrVal = $arrRet[0];
		
		// TOPを変更した場合には全画面変更
		if ($objPage->tpl_subno_template == $objPage->arrSubnavi[1]){
			$arrVal = array($chk_tpl,$chk_tpl,$chk_tpl,$chk_tpl);
		}else{
			$arrVal[$objPage->tpl_subno_template] = $chk_tpl;
		}
		$sql= "update dtb_baseinfo set top_tpl = ?, product_tpl = ?, detail_tpl = ?, mypage_tpl = ?, update_date = now()";
	}else{
		// INSERT
		$arrVal = array(null,null,null,null);
		
		// TOPを変更した場合には全画面変更
		if ($objPage->tpl_subno_template == $objPage->arrSubnavi[1]){
			$arrVal = array($chk_tpl,$chk_tpl,$chk_tpl,$chk_tpl);
		}else{
			$arrVal[$chk_tpl-1] =$chk_tpl;
		}
		$sql= "insert into dtb_baseinfo (top_tpl,product_tpl,detail_tpl,mypage_tpl, update_date) values (?,?,?,?,now());";
	}

	// SQL実行	
	$arrRet = $objDBConn->query($sql,$arrVal);
	
	return $arrRet;
}

/**************************************************************************************************************
 * 関数名	：lfChangeTemplate
 * 処理内容	：テンプレートファイルを上書きする
 * 引数		：なし
 * 戻り値	：成功 TRUE、エラー FALSE
 **************************************************************************************************************/
function lfChangeTemplate(){
	global $objPage;
	$tpl_path = "test/kakinaka/";
	
	$tpl_name = "";
	$tpl_element = "";
	
	$chk_tpl = $_POST['check_template'];
	
	// テンプレートデータを取得する
	$objQuery = new SC_Query();
	$sql = "SELECT template_code,template_name,file_path FROM dtb_template WHERE template_code = ?";
	$arrTemplate = $objQuery->getall($sql, array($chk_tpl));	
	
	switch($objPage->tpl_subno_template) {
		// TOP
		case $objPage->arrSubnavi[1]:
			$tpl_element = "TopTemplate";			// イメージの配列要素名格納用
			$tpl_name = "top.tpl";
			break;
			
		// 商品一覧
		case $objPage->arrSubnavi[2]:
			$tpl_element = "ProdTemplate";			// イメージの配列要素名格納用
			$tpl_name = "product.tpl";
			break;
			
		// 商品詳細
		case $objPage->arrSubnavi[3]:
			$tpl_element = "DetailTemplate";			// イメージの配列要素名格納用
			$tpl_name = "detail.tpl";
			break;
			
		// MYページ
		case $objPage->arrSubnavi[4]:
			$tpl_element = "MypageTemplate";			//イメージの配列要素名格納用
			$tpl_name = "mypage.tpl";
			break;
			
		default:
			break;
	}
	
	sfprintr($arrTemplate);
	// TOPを変更した場合には全画面変更
	if ($objPage->tpl_subno_template == $objPage->arrSubnavi[1]){
		// テンプレートファイルをコピー
		copy(ROOT_DIR . $tpl_path . $arrTemplate[0]['template_code'] . "/top.tpl", ROOT_DIR . $tpl_path . "top.tpl");
//		copy($arrTemplate[$chk_tpl]["ProdTemplate"], ROOT_DIR . $tpl_path . "product.tpl");
//		copy($arrTemplate[$chk_tpl]["DetailTemplate"], ROOT_DIR . $tpl_path . "detail.tpl");
//		copy($arrTemplate[$chk_tpl]["MypageTemplate"], ROOT_DIR . $tpl_path . "mypage.tpl");
	}else{
		// テンプレートファイルをコピー
		copy($arrTemplate[$chk_tpl][$tpl_element], ROOT_DIR . INCLUDE_DIR . $tpl_name);
	}
}
