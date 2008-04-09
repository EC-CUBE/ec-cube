<?php
/**
 * 
 * @copyright	2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: mdl_epsilon.inc 7162 2006-11-18 09:53:33Z kakinaka $
 * @link		http://www.lockon.co.jp/
 *
 */

define("MDL_EPSILON_ID", 4);

define ("CHARGE_MAX", 500000);
define ("SEVEN_CHARGE_MAX", 300000);

//コンビニの種類
$arrConvenience = array(
	11 => 'セブンイレブン'
	,21 => 'ファミリーマート'
	,31 => 'ローソン'
	,32 => 'セイコーマート'
	,33 => 'ミニストップ'
	,34 => 'デイリーヤマザキ'
);

$arrConveni_message = array(
	11 => "上記のページをプリントアウトされるか払込票番号をメモして、
お支払い期限までに、最寄りのセブンイレブンにて代金をお支払いください。"
	,21 =>"ファミリーマート店頭にございます
Famiポート／ファミネットにて以下の「企業コード」と「注文番号」を入力し、
申込券を印字後、お支払い期限までに代金をお支払い下さい。"
	,31 =>"＜お支払い方法＞
1. ローソンの店内に設置してあるLoppiのトップ画面の中から、
  「インターネット受付」をお選びください。

2. 次画面のジャンルの中から「インターネット受付」をお選びください。

3. 画面に従って「お支払い受付番号」と、ご注文いただいた際の
  「電話番号」をご入力下さい。→Loppiより「申込券」が発券されます。 
    ※申込券の有効時間は30分間です。お早めにレジへお持ち下さい。

4. 申込券に現金またはクレジットカードを添えてレジにて代金を
   お支払い下さい。

5. 代金と引換に「領収書」をお渡しいたします。領収書は大切に保管
   してください。代金払込の証書となります。"
	,32 =>"＜お支払い方法＞
1.　セイコーマートの店内に設置してあるセイコーマートクラブステーション
   （情報端末）のトップ画面の中から、「インターネット受付」をお選び下さい。

2.  画面に従って「お支払い受付番号」と、お申し込み時の「電話番号」を
　　ご入力いただくとセイコーマートクラブステーションより「決済サービス
　　払込取扱票・払込票兼受領証・領収書（計3枚）」が発券されます。

3.  発券された「決済サービス払込取扱票・払込票兼受領証・領収書（計3枚）」
　　をお持ちの上、レジにて代金をお支払い下さい。 "
	,33 =>"お支払い期限までにミニストップにて代金をお支払い下さい。
お支払いの際には「払込取扱票」が必要となりますので、上記URLで表示
されるページを印刷してレジまでお持ち下さい。"
	,34 =>"お支払い期限までにデイリーヤマザキ／ヤマザキデイリーストア
にて代金をお支払い下さい。
お支払いの際には「払込取扱票」が必要となりますので、上記URLで表示
されるページを印刷してレジまでお持ち下さい。"
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

/**************************************************************************************************************
 * 関数名	：sfPostPaymentData
 * 処理内容	：イプシロンへデータを送信する。
 * 引数1	：$order_url	･･･ 送信先URL
 * 引数2	：$arrData		･･･ POSTデータ
 **************************************************************************************************************/
function sfPostPaymentData($order_url, $arrData, $err_page = true){
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

	// Shift-JISをUNICODEに変換する
	$response = str_replace("x-sjis-cp932", "UTF-8", $response);
	// XML内のエンコードもUNICODEに変換
	$response = mb_convert_encoding($response, "UTF-8", "UTF-8");

	// XMLパーサを生成する。
	$parser = xml_parser_create('utf-8');
	
	// 空白文字は読み飛ばしてXMLを読み取る
	xml_parser_set_option($parser,XML_OPTION_TARGET_ENCODING,"UTF-8");
	xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
	
	// 配列にXMLのデータを格納する
	$err = xml_parse_into_struct($parser,$response,$arrVal,$idx);

	// 開放する
	xml_parser_free($parser);
	
	return $arrVal;
}

?>