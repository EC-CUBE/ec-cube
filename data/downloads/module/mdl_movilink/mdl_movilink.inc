<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @link		http://www.lockon.co.jp/
 *
 */
require_once(DATA_PATH . "include/csv_output.inc");
require_once(DATA_PATH . "module/Request.php");
require_once(DATA_PATH . "lib/slib.php");

/**** ▼定数宣言 *********************************************************************************************/

define("MDL_MOVILINK_ID", 12);
define("MODULE_NAME", "モビル連携モジュール");

/**** ▼定数宣言 *********************************************************************************************/
// セッション開始
sfDomainSessionStart();
if($_GET['aflkey'] != "") {
	$_SESSION['aflkey'] = $_GET['aflkey'];
}
sfPrintR($_SESSION);

/**************************************************************************************************************
 * 関数名	：sfSetModuleDB
 * 処理内容	：必要なデータを取得する。
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfSetModuleDB(){
    global $objQuery;
	$arrVal['status'] = $_POST['status'];
	$arrVal['site_id'] = $_POST['site_id'];
	$sqlval['sub_data'] = serialize($arrVal);	
    $objQuery->update("dtb_module", $sqlval, "module_id = ?", array(MDL_MOVILINK_ID));
}

/**************************************************************************************************************
 * 関数名	：sfGetModuleDB
 * 処理内容	：必要なデータを取得する。
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
// DBからデータを取得する
function sfGetModuleDB($module_id = MDL_MOVILINK_ID){
   	$objQuery = new SC_Query();
    $arrVal = array($module_id);
    $arrRet = array();
    $sql = "SELECT 
                sub_data
            FROM dtb_module WHERE module_id = ? " . $where;
    $arrRet = $objQuery->getall($sql, $arrVal);
    return unserialize($arrRet[0]['sub_data']);
}

/**************************************************************************************************************
 * 関数名	：sfIsMoviLink
 * 処理内容	：モビリンクが有効であるかどうか
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
// DBからデータを取得する
function sfIsMoviLink(){
	$arrRet = sfGetModuleDB();
	if($arrRet['status'] == '1') 
	{
		return true;	
	}
	return false;
}

/**************************************************************************************************************
 * 関数名	：sfMakeMoviLinkColumn
 * 処理内容	：必要なカラムがあれば生成する。
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfMakeMoviLinkColumn() {
	$objQuery = new SC_Query();
	$arrInsCol[] = array("name" => "movilink_net_percent", "type" => "numeric");
	$arrInsCol[] = array("name" => "movilink_net_fix", "type" => "numeric");
	$arrInsCol[] = array("name" => "movilink_draft_text1", "type" => "text");
	$arrInsCol[] = array("name" => "movilink_draft_text2", "type" => "text");
	$arrInsCol[] = array("name" => "movilink_code1", "type" => "text");
	$arrInsCol[] = array("name" => "movilink_kana", "type" => "text");
	$arrInsCol[] = array("name" => "movilink_price", "type" => "numeric");
	
	foreach($arrInsCol as $key => $val) {
		// 項目の存在チェック
		if(!sfColumnExists("dtb_products", $val["name"])){
			// モジュールIDを追加
			$objQuery->query("alter table dtb_products add " . $val["name"] . " " . $val["type"]);
		}
	}
}

/**************************************************************************************************************
 * 関数名	：sfSetMoviLinkCSV
 * 処理内容	：CSV出力用のレコードを設定する
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfSetMoviLinkCSV() {
	$objQuery = new SC_Query();
	$rank = 0;
	
	$objQuery->delete("dtb_csv", "csv_id = ?", array(CSV_ID_MOVI));
	
	// 更新データ
	$arrInsData[]= array("name"=>"広告主様商品コード",value=>"movilink_code1");
	$arrInsData[]= array("name"=>"商品名",value=>"name");
	$arrInsData[]= array("name"=>"商品名(フリガナ)",value=>"movilink_kana");
	$arrInsData[]= array("name"=>"商品報酬率",value=>"movilink_net_percent");
	$arrInsData[]=array("name"=>"商品報酬額",value=>"movilink_net_fix");
	$arrInsData[]=array("name"=>"所属カテゴリ",value=>"(SELECT category_name FROM dtb_category as cat WHERE dtb_products.category_id = cat.category_id) AS movilink_category");
	$arrInsData[]=array("name"=>"広告タグ原稿 - テキスト1",value=>"movilink_draft_text1");
	$arrInsData[]=array("name"=>"広告タグ原稿 - テキスト2",value=>"movilink_draft_text2");
	$arrInsData[]=array("name"=>"広告タグ原稿 - テキスト2",value=>"movilink_draft_text2");
	$arrInsData[]=array("name"=>"商品要約",value=>"main_comment");
	$arrInsData[]=array("name"=>"商品詳細",value=>"main_list_comment");
	$arrInsData[]=array("name"=>"商品価格",value=>"movilink_price");
	$arrInsData[]=array("name"=>"商品url",value=>"product_id as movilink_url");
	$arrInsData[]=array("name"=>"キャリア別商品url（docomo）",value=>"'' as movilink_url_j");
	$arrInsData[]=array("name"=>"キャリア別商品url（au）",value=>"'' as movilink_url_ez");
	$arrInsData[]=array("name"=>"キャリア別商品url（ソフトバンクモバイル）",value=>"'' as movilink_url_sb");
	$arrInsData[]=array("name"=>"商品画像url",value=>"main_list_image as movilink_image_url");
	$arrInsData[]=array("name"=>"商品画像url(モバイル)",value=>"main_list_image as movilink_image_url_mobile");
	$arrInsData[]=array("name"=>"商品画像サムネイルurl",value=>"main_list_image as movilink_image_thumbnail_url");
	$arrInsData[]=array("name"=>"商品コミッション率",value=>"'' as commission_percent");
	
	// 更新データ分ループ
	foreach($arrInsData as $key => $val){
		$sql = "INSERT INTO dtb_csv(csv_id,col,disp_name,rank,create_date,update_date) VALUES (?,?,?,?,now(),now())";
		$arrVal = array(CSV_ID_MOVI, $val["value"], $val["name"], ++$rank);
		$objQuery->query($sql,$arrVal);
	}
	/*
	SELECT
    (SELECT category_name FROM dtb_category as cat WHERE prdcls.category_id = cat.category_id) AS movilink_category
    ,*
FROM
    vw_products_allclass as prdcls
	*/
}


/**************************************************************************************************************
 * 関数名	：sfGetMovilinkCSVList
 * 処理内容	：配列の要素をCSVフォーマットで出力する
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfGetMovilinkCSVList($array) {
    if (count($array) > 0) {
        foreach($array as $key => $val) {
        	switch($val) {
        	case	'広告タグ原稿 - テキスト2':
        		break;
        	default:
        		$val = mb_convert_encoding($val, CHAR_CODE, CHAR_CODE);
            	$line .= "\"".$val."\",";
        		break;
        	}
        }
        $line = ereg_replace(",$", "\n", $line);
    }else{
        return false;
    }
    return $line;
}

/**************************************************************************************************************
 * 関数名	：sfGetMovilinkCSV
 * 処理内容	： CSV出力データを作成する。(MOVILINK)
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfGetMovilinkCSV($where, $option, $arrval, $arrOutputCols) {
	global $arrPRODUCTS_CVSCOL;

	$from = "dtb_products";
	$cols = sfGetCommaList($arrOutputCols);
	
	$objQuery = new SC_Query();
	$objQuery->setoption($option);
	
	$list_data = $objQuery->select($cols, $from, $where, $arrval);
	$max = count($list_data);
	
	for($i = 0; $i < $max; $i++) {
		// 各項目をCSV出力用に変換する。
		$data .= sfMakeMovilinkCSV($list_data[$i]);	
	}
	return $data;
}

/**************************************************************************************************************
 * 関数名	：sfMakeMovilinkCSV
 * 処理内容	： 各項目をCSV出力用に変換する。(MOVILINK)
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfMakeMovilinkCSV($list) {
	global $arrDISP;
	$line = "";
	if(is_array($list)) {
		foreach($list as $key => $val) {
			// モビル用商品コードが入力されていない場合は出力しない。
			if($list['movilink_code1'] != "") {
				$tmp = "";
				$blank_flag = false;
				switch($key) {
				// 広告タグ原稿
				case 'movilink_draft_text1':
					$tmp = $list['movilink_draft_text1'] . "\n" . $list['movilink_draft_text2'];
					break;
				case 'movilink_draft_text2':
					$blank_flag = true;				
					break;
				// 商品画像url
				case 'movilink_image_url':
				case 'movilink_image_url_mobile':
				case 'movilink_image_thumbnail_url':
					$tmp = SITE_URL . "upload/save_image/" . $val;
					break;
				// 商品url
				case 'movilink_url':
					$tmp = MOBILE_SITE_URL . "products/detail.php?product_id=" . $val;
					break;
				default:
					$tmp = $val;
					break;
				}
				if(!$blank_flag) {
					$tmp = str_replace("\"", "\\\"", $tmp);
					$line .= "\"".$tmp."\",";
				}
			}
		}
		// 文末の","を変換
		$line = ereg_replace(",$", "\n", $line);
	}
	return $line;
}

/**************************************************************************************************************
 * 関数名	：sfConnectMovilinkURL($order_id)
 * 処理内容	：モビルURLに接続する
 * 引数1	：
 * 引数2	：
 * 引数3	：
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfConnectMovilinkURL($order_id) {
	/*
	 * URLフォーマット
	 * 
	 * http://c.moaf.jp/eres/?sid=【ECサイトID】&aflkey=【AFLキー】&uid1=【注文ID1】&uid2=【注文ID2】&items=【個別商品コード】
	 * 
	 */
	
	$objQuery = new SC_Query();
	$col = "(SELECT movilink_code1 FROM dtb_products WHERE dtb_products.product_id = dtb_order_detail.product_id) AS code,
			product_id, 
			quantity, 
			price
	";
	$arrRet = $objQuery->select($col, "dtb_order_detail", "order_id = ?", array($order_id));
	
	$items = "";
	// 個別商品コードの作成
	foreach($arrRet as $key => $val) {
		if($items != "") {
			$items.= ",";
		}
		$items.= $val['code'].";".$val['price']."x".$val['quantity'];
	}
	
	$arrRet = sfGetModuleDB();
	$site_id = $arrRet['site_id'];
	$aflkey = $_SESSION['movilink_aflkey'];
	
	$url = "http://c.moaf.jp/eres/?sid=" . $site_id . "&aflkey=" . $aflkey . "&uid1=" . $order_id . "&items=" . $items;
	
	sfPrintR($url);	
}
 

?>