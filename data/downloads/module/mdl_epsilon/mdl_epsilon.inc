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
					$ret = mb_convert_encoding(urldecode($val), 'EUC-JP', 'SJIS');
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
function sfPostPaymentData($order_url, $arrData, $err_msg = "<br /><br /><br />この手続きは無効となりました。", $return_top = true, $payment_kbn = PAYMENT_CREDIT_ID){
	global $objSiteSess;
	global $uniqid;
	
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
		sfDispSiteError(FREE_ERROR_MSG, "", $return_top, "購入処理中に以下のエラーが発生しました。<br /><br /><br />・" . $err_detail . $err_msg);
	} else {
		// 正常な推移であることを記録しておく
		$objSiteSess->setRegistFlag();
		
		// 支払い区分により処理をわける
		if($payment_kbn == PAYMENT_CONVENIENCE_ID){
			
			$conveni_code = sfGetXMLValue($arrVal,'RESULT','CONVENI_CODE');	// コンビニコード
			$receipt_no   = sfGetXMLValue($arrVal,'RESULT','RECEIPT_NO');	// 払込票番号
			$payment_url = sfGetXMLValue($arrVal,'RESULT','HARAIKOMI_URL');	// 払込票URL(PC)
			$company_code = sfGetXMLValue($arrVal,'RESULT','KIGYOU_CODE');	// 企業コード
			$order_no = sfGetXMLValue($arrVal,'RESULT','ORDER_NUMBER');		// 受付番号
			$tel = $_POST["order_tel01"]."-".$_POST["order_tel02"]."-".$_POST["order_tel03"];	// 電話番号
			$payment_limit = sfGetXMLValue($arrVal,'RESULT','CONVENI_LIMIT');	// 支払期日
			
			//コンビニの種類
			switch($conveni_code) {
			//セブンイレブン
			case '11':
				$arrRet['cv_type'] = $conveni_code;									//コンビニの種類
				$arrRet['cv_payment_url'] = $payment_url;						//払込票URL(PC)
				$arrRet['cv_receipt_no'] = $receipt_no;		//払込票番号
				break;
			//ファミリーマート
			case '21':
				$arrRet['cv_type'] = $conveni_code;							//コンビニの種類
				$arrRet['cv_company_code'] = $company_code;					//企業コード
				$arrRet['cv_order_no'] = $receipt_no;							//受付番号
				break;
			//ローソン
			case '31':
				$arrRet['cv_type'] = $conveni_code;				//コンビニの種類
				$arrRet['cv_receipt_no'] = $receipt_no;			//払込票番号
				$arrRet['cv_tel'] = $tel;			//電話番号
				break;
			//セイコーマート
			case '32':
				$arrRet['cv_type'] = $conveni_code;									//コンビニの種類
				$arrRet['cv_receipt_no'] = $receipt_no;		//払込票番号
				$arrRet['cv_tel'] = $tel;			//電話番号
				break;
			//ミニストップ
			case '33':
				$arrRet['cv_type'] = $conveni_code;										//コンビニの種類
				$arrRet['cv_payment_url'] = $payment_url;		//払込票URL
				break;
			//デイリーヤマザキ
			case '34':
				$arrRet['cv_type'] = $conveni_code;										//コンビニの種類
				$arrRet['cv_payment_url'] = $payment_url;		//払込票URL
				break;
			}
			
			//支払期限
			$arrRet['cv_payment_limit'] = $payment_limit;
			//コンビニ決済情報を格納
			$sqlval['conveni_data'] = serialize($arrRet);			
			
			$url = URL_SHOP_COMPLETE;

			// 受注一時テーブルに更新
			sfRegistTempOrder($uniqid, $arrVal);
			
		}else{
			$url = sfGetXMLValue($arrVal,'RESULT','REDIRECT');
		}
		//header("Location: " . $url);
	}
}

?>