<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: mdl_epsilon.inc 7162 2006-11-18 09:53:33Z kakinaka $
 * @link		http://www.lockon.co.jp/
 *
 */

/**** ▼定数宣言 *********************************************************************************************/

define("MDL_ZERO_ID", 8);
define("ZERO_PC", 1);        // dtb_payment の区分用
define("ZERO_MOBILE", 2);    // dtb_payment の区分用
define("ZERO_CREDIT_ID", 1);      // dtb_payment の区分用


// 送信パラメータ
define ("SEND_PARAM_CUSTOM", "yes");
define ("SEND_PARAM_ACT", "imode");
define ("SEND_PARAM_SEND", "jpall");
define ("SEND_PARAM_PC_URL", "https://credit.zeroweb.ne.jp/cgi-bin/order.cgi?orders");
define ("SEND_PARAM_MOBILE_URL", "https://credit.zeroweb.ne.jp/cgi-bin/order.cgi");
define ("SEND_PARAM_DELIMITER", "_");                           // sendidの分割文字

// 文字数制限
define ("CLIENTIP_LEN", 5);
define ("SEND_LEN", 20);

/**** ▼変数宣言 *********************************************************************************************/


/**** ▼関数 *********************************************************************************************/

/**
 * XMLタグの内容を取得する
 * @param array $arrVal Valueデータ
 * @param string $tag Tagデータ
 * @param string $att 対象タグ名
 * @return string
 */
function sfGetXMLValue($arrVal, $tag, $att) {
	$ret = "";
	foreach((array)$arrVal as $array) {
		if($tag == $array['tag']) {
			if(!is_array($array['attributes'])) {
				continue;
			}
			foreach($array['attributes'] as $key => $val) {
				if($key == $att) {
					$ret = mb_convert_encoding(urldecode($val), 'EUC-JP', 'SJIS');
					break;
				}
			}			
		}
	}
	
	return $ret;
}

/**
 * ゼロへデータを送信する。
 * @param string $order_url	 送信先URL
 * @param array $arrData POSTデータ
 * @param boolean $err_page エラーページ表示有無
 * @param boolean $is_xml XMLで応答情報が帰ってくる場合
 * @return array 応答情報
 */
function sfPostPaymentData($order_url, $arrData, $err_page = true, $is_xml = false){
	$arrVal = array();
	$response = "";

	// POSTデータを送信し、応答情報を取得する
	$response = sfSendPostData($order_url, $arrData, array(200));
    
	// なにも返ってこなれば、エラー
	if ($response == "") {
		if ($err_page) {
			// エラー画面を表示する。
			$_SESSION['site']['now_page'] ="";
			sfDispSiteError(FREE_ERROR_MSG, "", true, "購入処理中にエラーが発生しました。<br>この手続きは無効となりました。");
		}else{
			return "" ;
		}
	}

	// 応答のエンコードもUNICODEに変換
	$response = mb_convert_encoding($response, CHAR_CODE, auto);

    // 応答がXMLの場合
    if($is_xml){
		
		// XMLパーサを生成する。
		$parser = xml_parser_create('utf-8');
		
		// 空白文字は読み飛ばしてXMLを読み取る
		xml_parser_set_option($parser,XML_OPTION_TARGET_ENCODING,"UTF-8");
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
		
		// 配列にXMLのデータを格納する
		$err = xml_parse_into_struct($parser,$response,$arrVal,$idx);
		
		// 開放する
		xml_parser_free($parser);
    }else{
        $arrVal = $response;
    }

	return $arrVal;
}

?>