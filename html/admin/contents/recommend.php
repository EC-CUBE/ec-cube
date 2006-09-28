<?
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
 require_once("../require.php");

class LC_Page {
	
	function LC_Page() {
		$this->tpl_mainpage = 'contents/recomend.tpl';
		$this->tpl_mainno = 'contents';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = "recommend";
		$this->tpl_subtitle = 'オススメ管理';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

$arrRegistColumn = array(
 							 array(  "column" => "product_id", "convert" => "n" ),
							 array(  "column" => "category_id", "convert" => "n" ),
							 array(  "column" => "rank", "convert" => "n" ),
							 array(  "column" => "title", "convert" => "aKV" ),
							 array(  "column" => "comment", "convert" => "aKV" ),
						);

// 認証可否の判定
sfIsSuccess($objSess);

//最大登録数の表示
$objPage->tpl_disp_max = RECOMMEND_NUM;

// 登録時
if ( $_POST['mode'] == 'regist' ){
		
	// 入力文字の強制変換
	$objPage->arrForm = $_POST;	
	$objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);
	// エラーチェック
	$objPage->arrErr[$objPage->arrForm['rank']] = lfErrorCheck();
	if ( ! $objPage->arrErr[$objPage->arrForm['rank']]) {
		// 古いのを消す
		$sql = "DELETE FROM dtb_best_products WHERE category_id = ? AND rank = ?";
		$conn->query($sql, array($objPage->arrForm['category_id'] ,$objPage->arrForm['rank']));
	
		// ＤＢ登録
		$objPage->arrForm['creator_id'] = $_SESSION['member_id'];
		$objPage->arrForm['update_date'] = "NOW()";
		$objPage->arrForm['create_date'] = "NOW()";
		
		$objQuery = new SC_Query();
		$objQuery->insert("dtb_best_products", $objPage->arrForm );
//		$conn->autoExecute("dtb_best_products", $objPage->arrForm );
	}	

} elseif ( $_POST['mode'] == 'delete' ){
// 削除時

	$sql = "DELETE FROM dtb_best_products WHERE category_id = ? AND rank = ?";
	$conn->query($sql, array($_POST['category_id'] ,$_POST['rank']));
	
}

// カテゴリID取得 無いときはトップページ
if ( sfCheckNumLength($_POST['category_id']) ){
	$objPage->category_id = $_POST['category_id'];
} else {
	$objPage->category_id = 0;
}

// 既に登録されている内容を取得する
$sql = "SELECT B.name, B.main_list_image, A.* FROM dtb_best_products as A INNER JOIN dtb_products as B USING (product_id)
		 WHERE A.category_id = ? AND A.del_flg = 0 ORDER BY rank";
$arrItems = $conn->getAll($sql, array($objPage->category_id));
foreach( $arrItems as $data ){
	$objPage->arrItems[$data['rank']] = $data;
}

// 商品変更時は、選択された商品に一時的に置き換える
if ( $_POST['mode'] == 'set_item'){
	$sql = "SELECT product_id, name, main_list_image FROM dtb_products WHERE product_id = ? AND del_flg = 0";
	$result = $conn->getAll($sql, array($_POST['product_id']));
	if ( $result ){
		$data = $result[0];
		foreach( $data as $key=>$val){
			$objPage->arrItems[$_POST['rank']][$key] = $val;
		}
		$objPage->arrItems[$_POST['rank']]['rank'] = $_POST['rank'];
	}
	$objPage->checkRank = $_POST['rank'];
}

//各ページ共通
$objPage->cnt_question = 6;
$objPage->arrActive = $arrActive;
$objPage->arrQuestion = $arrQuestion;

// カテゴリ取得
$objPage->arrCatList = sfGetCategoryList("level = 1");

//----　ページ表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


//---------------------------------------------------------------------------------------------------------------------------------------------------------
//----　取得文字列の変換
function lfConvertParam($array, $arrRegistColumn) {

	// カラム名とコンバート情報
	foreach ($arrRegistColumn as $data) {
		$arrConvList[ $data["column"] ] = $data["convert"];
	}
	// 文字変換
	$new_array = array();
	foreach ($arrConvList as $key => $val) {
		$new_array[$key] = $array[$key];
		if( strlen($val) > 0) {
			$new_array[$key] = mb_convert_kana($new_array[$key] ,$val);
		}
	}
	return $new_array;
	
}

/* 入力エラーチェック */
function lfErrorCheck() {
	$objErr = new SC_CheckError();
	
	$objErr->doFunc(array("見出しコメント", "title", STEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("オススメコメント", "comment", LTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	
	return $objErr->arrErr;
}

?>