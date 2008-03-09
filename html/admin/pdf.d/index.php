<?php
/*
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**
 * 帳票作成モジュール.
 */

// 各種モジュール呼び出し
require('japanese.php');
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		global $arrPref;
		// デフォルトの設定
		$this->pdf_download = 0;			// PDFのダウンロード形式（0:表示、1:ダウンロード）
		$this->tpl_title = "お買上げ明細書";		// タイトル
		$this->tpl_char = "EUC-JP, UTF-8";		// 文字コード	
		$this->tpl_pdf = "template_nouhin01.pdf";	// テンプレートファイル
		$this->tpl_dispmode = "real";			// 表示モード
		$this->arrPref = $arrPref;
		$this->width_cell = array(110.3,12,21.7,24.5);
		$label_cell[] = sjis_conv("商品名 / 商品コード / [ 規格 ]");
		$label_cell[] = sjis_conv("数量");
		$label_cell[] = sjis_conv("単価");
		$label_cell[] = sjis_conv("金額(税込)");
		$this->label_cell = $label_cell;
		$this->arrMessage = array(
			'このたびはお買上げいただきありがとうございます。',
			'下記の内容にて納品させていただきます。',
			'ご確認いただきますよう、お願いいたします。'
		);
	}
}



// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

$pdf  = new PDF_Japanese();
$conn = new SC_DbConn();
$objPage = new LC_Page();
$objInfo = new SC_SiteInfo();
$arrInfo = $objInfo->data;
// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();

// SJISフォント
$pdf->AddSJISFont();

//ページ総数取得
$pdf->AliasNbPages();

// マージン設定
$pdf->SetMargins(15, 20);

// PDFを読み込んでページ数を取得
$pageno = $pdf->setSourceFile($objPage->tpl_pdf);

// ページ番号よりIDを取得
$tplidx = $pdf->ImportPage(1);

// ページを追加（新規）
$pdf->AddPage();

//表示倍率(100%)
$pdf->SetDisplayMode($objPage->tpl_dispmode);

if(sfIsInt($_POST['order_id'])) {
	$objPage->disp_mode = true;
	$order_id = $_POST['order_id'];
}
$objPage->tpl_order_id = $order_id;


// タイトルが設定されていたら変更
if($_POST['chohyo_title']) {
	$objPage->tpl_title = $_POST['chohyo_title'];
}

// ダウンロード方式
if($_POST['download']) {
	$objPage->pdf_download = $_POST['download'];
}

// メッセージ
if($_POST['chohyo_msg1']) {
	$objPage->arrMessage[0] = $_POST['chohyo_msg1'];
}

if($_POST['chohyo_msg2']) {
	$objPage->arrMessage[1] = $_POST['chohyo_msg2'];
}

if($_POST['chohyo_msg3']) {
	$objPage->arrMessage[2] = $_POST['chohyo_msg3'];
}

// 備考
if ($_POST['chohyo_etc1']) {
  if($_POST['chohyo_etc1']) {
	$objPage->arrEtc[0] = $_POST['chohyo_etc1'];
  }

  if($_POST['chohyo_etc2']) {
	$objPage->arrEtc[1] = $_POST['chohyo_etc2'];
  }

  if($_POST['chohyo_etc3']) {
	$objPage->arrEtc[2] = $_POST['chohyo_etc3'];
  }
}


// テンプレート内容の位置、幅を調整 ※useTemplateに引数を与えなければ100%表示がデフォルト
$pdf->useTemplate($tplidx);

/**
 * PDF 書き込み開始
 *
 * PDF書込み ※$pdf->Text(x座標, y座標, テキスト);
 * フォントのセット $pdf->SetFont('SJIS', '', 8); ※SJIS(MSPGothic)でフォントサイズ8
 */

// ショップ情報
$pdf->SetFont('SJIS', 'B', 8);
$pdf->Text(125, 60, sjis_conv($arrInfo['shop_name']));					//ショップ名
$pdf->SetFont('SJIS', '', 8);
$pdf->Text(125, 63, sjis_conv($arrInfo['law_url']));					//URL
$pdf->Text(125, 68, sjis_conv($arrInfo['law_company']));				//会社名
$pdf->Text(125, 71, sjis_conv("〒 ".$arrInfo['zip01']." - ".$arrInfo['zip02']));	//郵便番号
$pdf->Text(125, 74, sjis_conv($objPage->arrPref[$arrInfo['pref']].$arrInfo['addr01']));	//都道府県+住所1
$pdf->Text(125, 77, sjis_conv($arrInfo['addr02']));					//住所2
$pdf->Text(125, 80, sjis_conv("TEL: ".$arrInfo['tel01']."-".$arrInfo['tel02']."-".$arrInfo['tel03']."　"."FAX: ".$arrInfo['fax01']."-".$arrInfo['fax02']."-".$arrInfo['fax03']));	//TEL・FAX
$pdf->Text(125, 83, sjis_conv("Email: ".$arrInfo['law_email']));			//Email


// メッセージ
$pdf->SetFont('SJIS', '', 8);
$pdf->Text(27, 70, sjis_conv($objPage->arrMessage[0]));  //メッセージ1
$pdf->Text(27, 74, sjis_conv($objPage->arrMessage[1]));  //メッセージ2
$pdf->Text(27, 78, sjis_conv($objPage->arrMessage[2]));  //メッセージ3
$pdf->Text(158, 288, sjis_conv("作成日: ".$_POST['year']."年".$_POST['month']."月".$_POST['day']."日"));  //作成日


// DBから受注情報を読み込む
lfGetOrderData($order_id);

// 購入者情報
$pdf->SetFont('SJIS', '', 10);
$pdf->Text(23, 43, sjis_conv("〒 ".$objPage->arrDisp['order_zip01']." - ".$objPage->arrDisp['order_zip02']));           //購入者郵便番号
$pdf->Text(27, 47, sjis_conv($objPage->arrPref[$objPage->arrDisp['order_pref']] . $objPage->arrDisp['order_addr01']));  //購入者都道府県+住所1
$pdf->Text(27, 51, sjis_conv($objPage->arrDisp['order_addr02']));  							//購入者住所2
$pdf->SetFont('SJIS', '', 11);
$pdf->Text(27, 59, sjis_conv($objPage->arrDisp['order_name01']."　".$objPage->arrDisp['order_name02']."　様"));		//購入者氏名

// お届け先情報
$pdf->SetFont('SJIS', '', 10);
$pdf->Text(22, 128, sjis_conv("〒 ".$objPage->arrDisp['deliv_zip01']." - ".$objPage->arrDisp['deliv_zip02']));		//お届け先郵便番号
$pdf->Text(26, 132, sjis_conv($objPage->arrPref[$objPage->arrDisp['deliv_pref']] . $objPage->arrDisp['deliv_addr01'])); //お届け先都道府県+住所1
$pdf->Text(26, 136, sjis_conv($objPage->arrDisp['deliv_addr02']));							//お届け先住所2
$pdf->Text(26, 140, sjis_conv($objPage->arrDisp['deliv_name01']."　".$objPage->arrDisp['deliv_name02']."　様"));	//お届け先氏名

$pdf->Text(144, 121, sjis_conv($objPage->arrDisp['create_date']));    //ご注文日
$pdf->Text(144, 131, sjis_conv($objPage->arrDisp['order_disp_id']));  //注文番号

$pdf->SetFont('SJIS', 'B', 15);
$pdf->Cell(0, 10, sjis_conv($objPage->tpl_title), 0, 2, 'C', 0, '');  //文書タイトル（納品書・請求書）
$pdf->Cell(0, 66, '', 0, 2, 'R', 0, '');
$pdf->Cell(5, 0, '', 0, 0, 'R', 0, '');
$pdf->Cell(67, 8, sjis_conv(number_format($objPage->arrDisp['payment_total'])." 円"), 0, 2, 'R', 0, '');
$pdf->Cell(0, 45, '', 0, 2, '', 0, '');

$pdf->SetFont('SJIS', '', 9);

//ロゴ画像
$pdf->Image('logo.png', 124, 46, 60);

$monetary_unit = sjis_conv("円");
$point_unit = sjis_conv("ﾎﾟｲﾝﾄ");

// 購入商品情報
for ($i = 0; $i < count($objPage->arrDisp['quantity']); $i++) {

	// 購入数量
	$data[0] = $objPage->arrDisp['quantity'][$i];

	// 税込金額（単価）
	$data[1] = sfPreTax($objPage->arrDisp['price'][$i], $arrInfo['tax'], $arrInfo['tax_rule']);

	// 小計（商品毎）
	$data[2] = $data[0] * $data[1];

	$arrOrder[$i][0]  = sjis_conv($objPage->arrDisp['product_name'][$i]." / ");
	$arrOrder[$i][0] .= sjis_conv($objPage->arrDisp['product_code'][$i]." / ");
	if($objPage->arrDisp['classcategory_name1'][$i]) {
		$arrOrder[$i][0] .= sjis_conv(" [ ".$objPage->arrDisp['classcategory_name1'][$i]);
		if($objPage->arrDisp['classcategory_name2'][$i] == "") {
			$arrOrder[$i][0] .= " ]";
		} else {
			$arrOrder[$i][0] .= sjis_conv(" * ".$objPage->arrDisp['classcategory_name2'][$i]." ]");
		}
	}
	$arrOrder[$i][1]  = number_format($data[0]);
	$arrOrder[$i][2]  = number_format($data[1]).$monetary_unit;
	$arrOrder[$i][3]  = number_format($data[2]).$monetary_unit;

}

$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = "";
$arrOrder[$i][3] = "";

$i++;
$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = sjis_conv("商品合計");
$arrOrder[$i][3] = number_format($objPage->arrDisp['subtotal']).$monetary_unit;

$i++;
$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = sjis_conv("送料");
$arrOrder[$i][3] = number_format($objPage->arrDisp['deliv_fee']).$monetary_unit;

$i++;
$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = sjis_conv("手数料");
$arrOrder[$i][3] = number_format($objPage->arrDisp['charge']).$monetary_unit;

$i++;
$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = sjis_conv("値引き");
$arrOrder[$i][3] = "- ".number_format($objPage->arrDisp['use_point'] + $objPage->arrDisp['discount']).$monetary_unit;

$i++;
$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = sjis_conv("請求金額");
$arrOrder[$i][3] = number_format($objPage->arrDisp['payment_total']).$monetary_unit;

$i++;
$arrOrder[$i][0] = "";
$arrOrder[$i][1] = "";
$arrOrder[$i][2] = "";
$arrOrder[$i][3] = "";

// ポイント表記
if ($_POST['disp_point'] && $objPage->arrDisp['customer_id']) {
  $i++;
  $arrOrder[$i][0] = "";
  $arrOrder[$i][1] = "";
  $arrOrder[$i][2] = sjis_conv("利用ﾎﾟｲﾝﾄ");
  $arrOrder[$i][3] = number_format($objPage->arrDisp['use_point']).$point_unit;

  $i++;
  $arrOrder[$i][0] = "";
  $arrOrder[$i][1] = "";
  $arrOrder[$i][2] = sjis_conv("加算ﾎﾟｲﾝﾄ");
  $arrOrder[$i][3] = number_format($objPage->arrDisp['add_point']).$point_unit;

  $i++;
  $arrOrder[$i][0] = "";
  $arrOrder[$i][1] = "";
  $arrOrder[$i][2] = sjis_conv("所有ﾎﾟｲﾝﾄ");
  $arrOrder[$i][3] = number_format($objPage->arrDisp['point']).$point_unit;
}

$pdf->FancyTable($objPage->label_cell, $arrOrder, $objPage->width_cell);

if ($objPage->arrEtc[0]) {
  $pdf->Cell(0, 10, '', 0, 1, 'C', 0, '');
  $pdf->SetFont('SJIS', '', 9);
  $pdf->MultiCell(0, 6, sjis_conv("＜ 備 考 ＞"), 'T', 2, 'L', 0, '');  //備考
  $pdf->Ln();
  $pdf->SetFont('SJIS', '', 8);
  $pdf->MultiCell(0, 4, sjis_conv($objPage->arrEtc[0]."\n".$objPage->arrEtc[1]."\n".$objPage->arrEtc[2]), '', 2, 'L', 0, '');  //備考
}

// PDFをブラウザに送信
if($objPage->pdf_download == 1) {
	$pdf->Output(sjis_conv("nouhinsyo-No".$objPage->tpl_order_id.".pdf"), D);
} else {
	$pdf->Output();
}

// 入力してPDFファイルを閉じる
$pdf->Close();


//-----------------------------------------------------------------------------------------------------------------------------------
// 文字コードSJIS変換 -> japanese.phpで使用出来る文字コードはSJISのみ
function sjis_conv($conv_str) {
	global $objPage;
	return (mb_convert_encoding($conv_str, "SJIS", $objPage->tpl_char));
}

/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;

	// 配送先情報
	$objFormParam->addParam("お名前1", "deliv_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お名前2", "deliv_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ1", "deliv_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ2", "deliv_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("郵便番号1", "deliv_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("郵便番号2", "deliv_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("都道府県", "deliv_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("住所1", "deliv_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("住所2", "deliv_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("電話番号1", "deliv_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号2", "deliv_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号3", "deliv_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));

	// 受注商品情報
	$objFormParam->addParam("値引き", "discount", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("送料", "deliv_fee", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("手数料", "charge", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("利用ポイント", "use_point", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("お支払い方法", "payment_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("配送時間ID", "deliv_time_id", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("対応状況", "status", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("配達日", "deliv_date", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お支払方法名称", "payment_method");
	$objFormParam->addParam("配送時間", "deliv_time");
	
	// 受注詳細情報
	$objFormParam->addParam("単価", "price", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("個数", "quantity", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("商品ID", "product_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("ポイント付与率", "point_rate");
	$objFormParam->addParam("商品コード", "product_code");
	$objFormParam->addParam("商品名", "product_name");
	$objFormParam->addParam("規格1", "classcategory_id1");
	$objFormParam->addParam("規格2", "classcategory_id2");
	$objFormParam->addParam("規格名1", "classcategory_name1");
	$objFormParam->addParam("規格名2", "classcategory_name2");
	$objFormParam->addParam("メモ", "note", MTEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));

	// DB読込用
	$objFormParam->addParam("小計", "subtotal");
	$objFormParam->addParam("合計", "total");
	$objFormParam->addParam("支払い合計", "payment_total");
	$objFormParam->addParam("加算ポイント", "add_point");
	$objFormParam->addParam("お誕生日ポイント", "birth_point");
	$objFormParam->addParam("消費税合計", "tax");
	$objFormParam->addParam("最終保持ポイント", "total_point");
	$objFormParam->addParam("顧客ID", "customer_id");
	$objFormParam->addParam("現在のポイント", "point");
}

// 受注データの取得
function lfGetOrderData($order_id) {
	global $objFormParam;
	global $objPage;
	if(sfIsInt($order_id)) {

		// DBから受注情報を読み込む
		$objQuery = new SC_Query();
		$where = "order_id = ?";
		$arrRet = $objQuery->select("*", "dtb_order", $where, array($order_id));
		$objFormParam->setParam($arrRet[0]);
		list($point, $total_point) = sfGetCustomerPoint($order_id, $arrRet[0]['use_point'], $arrRet[0]['add_point']);
		$objFormParam->setValue('total_point', $total_point);
		$objFormParam->setValue('point', $point);
		$arrRet[0]['total_point'] = $total_point;
		$arrRet[0]['point'] = $point;
		$objPage->arrDisp = $arrRet[0];

		// 受注詳細データの取得
		$arrRet = lfGetOrderDetail($order_id);
		$arrRet = sfSwapArray($arrRet);
		$objPage->arrDisp = array_merge($objPage->arrDisp, $arrRet);
		$objFormParam->setParam($arrRet);

		// その他支払い情報を表示
		if($objPage->arrDisp["memo02"] != "") $objPage->arrDisp["payment_info"] = unserialize($objPage->arrDisp["memo02"]);
		if($objPage->arrDisp["memo01"] == PAYMENT_CREDIT_ID){
			$objPage->arrDisp["payment_type"] = "クレジット決済";
		}elseif($objPage->arrDisp["memo01"] == PAYMENT_CONVENIENCE_ID){
			$objPage->arrDisp["payment_type"] = "コンビニ決済";
		}else{
			$objPage->arrDisp["payment_type"] = "お支払い";
		}
	}
}

// 受注詳細データの取得
function lfGetOrderDetail($order_id) {
	$objQuery = new SC_Query();
	$col = "product_id, classcategory_id1, classcategory_id2, product_code, product_name, classcategory_name1, classcategory_name2, price, quantity, point_rate";
	$where = "order_id = ?";
	$objQuery->setorder("classcategory_id1, classcategory_id2");
	$arrRet = $objQuery->select($col, "dtb_order_detail", $where, array($order_id));
	return $arrRet;
}
?>

