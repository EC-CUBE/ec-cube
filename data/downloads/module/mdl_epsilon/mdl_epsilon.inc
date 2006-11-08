<?php

//コンビニの種類
$arrConvenience = array(
	11 => 'セブンイレブン'
	,21 => 'ファミリーマート'
	,31 => 'ローソン'
	,32 => 'セイコーマート'
	,33 => 'ミニストップ'
	,34 => 'デイリーヤマザキ'
);


/**************************************************************************************************************
 * 関数名	：sfGetXMLValue
 * 処理内容	：XMLタグの内容を取得する
 * 引数1	：$arrVal	･･･ Valueデータ
 * 引数2	：$tag		･･･ Tagデータ
 * 引数3	：$att		･･･ 対象タグ名
 * 戻り値	：取得結果
 **************************************************************************************************************/
function sfGetXMLValue($arrVal, $tag, $att) {
	$ret = "";
	foreach($arrVal as $array) {
		if($tag == $array['tag']) {
			if(!is_array($array['attributes'])) {
				continue;
			}
			foreach($array['attributes'] as $key => $val) {
				if($key == $att) {
					$ret = mb_convert_encoding(urldecode($val), 'EUC-JP', 'auto');
					break;
				}
			}			
		}
	}
	
	return $ret;
}

/**************************************************************************************************************
 * 関数名	：sfPostPaymentData
 * 処理内容	：イプシロンへデータを送信する。
 * 引数1	：$order_url	･･･ 送信先URL
 * 引数2	：$arrData		･･･ POSTデータ
 **************************************************************************************************************/
function sfPostPaymentData($order_url, $arrData){
	global $objSiteSess;
	
	// POSTデータを送信し、応答情報を取得する
	$response = sfSendPostData($order_url, $arrData);
	
	// なにも返ってこなれば、エラー
	if ($response == "") {
		// エラー画面を表示する。
		$_SESSION['site']['now_page'] ="";
		sfDispSiteError(FREE_ERROR_MSG, "", true, "購入処理中にエラーが発生しました。<br>この手続きは無効となりました。");
	}
	
	// XMLパーサを生成する。
	$parser = xml_parser_create();
	
	// 空白文字は読み飛ばしてXMLを読み取る
	xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
	
	// 配列にXMLのデータを格納する
	xml_parse_into_struct($parser,$response,$arrVal,$idx);
	
	// 開放する
	xml_parser_free($parser);
	
	// エラーがあるかチェックする
	$err_code = sfGetXMLValue($arrVal,'RESULT','ERR_CODE');
	
	if($err_code != "") {
		$err_detail = sfGetXMLValue($arrVal,'RESULT','ERR_DETAIL');
		//$_SESSION['site']['now_page'] ="";
		sfprintr($_SESSION["site"]);
		sfDispSiteError(FREE_ERROR_MSG, "", true, "購入処理中に以下のエラーが発生しました。<br /><br /><br />・" . $err_detail . "<br /><br /><br />この手続きは無効となりました。");
	} else {
		// 正常な推移であることを記録しておく
		$objSiteSess->setRegistFlag();
		$url = sfGetXMLValue($arrVal,'RESULT','REDIRECT');
		
		sfprintr($arrVal);
		//header("Location: " . $url);	
	}
}

?>