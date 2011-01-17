<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/*----------------------------------------------------------------------
 * [名称] SC_Fpdf
 * [概要] pdfファイルを表示する。
 *----------------------------------------------------------------------
 */

require(DATA_REALDIR . 'module/fpdf/fpdf.php');
require(DATA_REALDIR . 'module/fpdf/japanese.php');
define('PDF_TEMPLATE_REALDIR', TEMPLATE_ADMIN_REALDIR . 'pdf/');

class SC_Fpdf {
    function SC_Fpdf($download, $title, $tpl_pdf = 'nouhinsyo1.pdf') {
        // デフォルトの設定
        $this->tpl_pdf = PDF_TEMPLATE_REALDIR . $tpl_pdf;  // テンプレートファイル
        $this->pdf_download = $download;      // PDFのダウンロード形式（0:表示、1:ダウンロード）
        $this->tpl_title = $title;
        $this->tpl_dispmode = "real";      // 表示モード
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->width_cell = array(110.3,12,21.7,24.5);

        $this->label_cell[] = $this->sjis_conv("商品名 / 商品コード / [ 規格 ]");
        $this->label_cell[] = $this->sjis_conv("数量");
        $this->label_cell[] = $this->sjis_conv("単価");
        $this->label_cell[] = $this->sjis_conv("金額(税込)");

        $this->arrMessage = array(
            'このたびはお買上げいただきありがとうございます。',
            '下記の内容にて納品させていただきます。',
            'ご確認くださいますよう、お願いいたします。'
        );

        $this->pdf  = new PDF_Japanese();

        // SJISフォント
        $this->pdf->AddSJISFont();

        //ページ総数取得
        $this->pdf->AliasNbPages();

        // マージン設定
        $this->pdf->SetMargins(15, 20);

        // PDFを読み込んでページ数を取得
        $pageno = $this->pdf->setSourceFile($this->tpl_pdf);
    }

    function setData($arrData) {
        $this->arrData = $arrData;

        // ページ番号よりIDを取得
        $tplidx = $this->pdf->ImportPage(1);

        // ページを追加（新規）
        $this->pdf->AddPage();

        //表示倍率(100%)
        $this->pdf->SetDisplayMode($this->tpl_dispmode);

        if (SC_Utils_Ex::sfIsInt($arrData['order_id'])) {
            $this->disp_mode = true;
            $order_id = $arrData['order_id'];
        }

        // テンプレート内容の位置、幅を調整 ※useTemplateに引数を与えなければ100%表示がデフォルト
        $this->pdf->useTemplate($tplidx);

        $this->setShopData();
        $this->setMessageData();
        $this->setOrderData();
        $this->setEtcData();

    }

    function setShopData() {
        // ショップ情報

        $objInfo = new SC_SiteInfo();
        $arrInfo = $objInfo->data;

        $this->lfText(125, 60, $arrInfo['shop_name'], 8, 'B');          //ショップ名
        $this->lfText(125, 63, $arrInfo['law_url'], 8);          //URL
        $this->lfText(125, 68, $arrInfo['law_company'], 8);        //会社名
        $text = "〒 ".$arrInfo['law_zip01']." - ".$arrInfo['law_zip02'];
        $this->lfText(125, 71, $text, 8);  //郵便番号
        $text = $this->arrPref[$arrInfo['law_pref']].$arrInfo['law_addr01'];
        $this->lfText(125, 74, $text, 8);  //都道府県+住所1
        $this->lfText(125, 77, $arrInfo['law_addr02'], 8);          //住所2

        $text = "TEL: ".$arrInfo['law_tel01']."-".$arrInfo['law_tel02']."-".$arrInfo['law_tel03'];
        //FAX番号が存在する場合、表示する
        if (strlen($arrInfo['law_fax01']) > 0) {
            $text .= "　FAX: ".$arrInfo['law_fax01']."-".$arrInfo['law_fax02']."-".$arrInfo['law_fax03'];
        }
        $this->lfText(125, 80, $text, 8);  //TEL・FAX

        if ( strlen($arrInfo['law_email']) > 0 ) {
            $text = "Email: ".$arrInfo['law_email'];
            $this->lfText(125, 83, $text, 8);      //Email
        }

        //ロゴ画像
        $logo_file = PDF_TEMPLATE_REALDIR . 'logo.png';
        $this->pdf->Image($logo_file, 124, 46, 40);
    }

    function setMessageData() {
        // メッセージ
        $this->lfText(27, 70, $this->arrData['msg1'], 8);  //メッセージ1
        $this->lfText(27, 74, $this->arrData['msg2'], 8);  //メッセージ2
        $this->lfText(27, 78, $this->arrData['msg3'], 8);  //メッセージ3
        $text = "作成日: ".$this->arrData['year']."年".$this->arrData['month']."月".$this->arrData['day']."日";
        $this->lfText(158, 288, $text, 8);  //作成日
    }

    function setOrderData() {
        // DBから受注情報を読み込む
        $this->lfGetOrderData($this->arrData['order_id']);

        // 購入者情報
        $text = "〒 ".$this->arrDisp['order_zip01']." - ".$this->arrDisp['order_zip02'];
        $this->lfText(23, 43, $text, 10); //購入者郵便番号
        $text = $this->arrPref[$this->arrDisp['order_pref']] . $this->arrDisp['order_addr01'];
        $this->lfText(27, 47, $text, 10); //購入者都道府県+住所1
        $this->lfText(27, 51, $this->arrDisp['order_addr02'], 10); //購入者住所2
        $text = $this->arrDisp['order_name01']."　".$this->arrDisp['order_name02']."　様";
        $this->lfText(27, 59, $text, 11); //購入者氏名

        // お届け先情報
        $this->pdf->SetFont('SJIS', '', 10);
        $text = "〒 ".$this->arrDisp['deliv_zip01']." - ".$this->arrDisp['deliv_zip02'];
        $this->lfText(22, 128, $text, 10); //お届け先郵便番号
        $text = $this->arrPref[$this->arrDisp['deliv_pref']] . $this->arrDisp['deliv_addr01'];
        $this->lfText(26, 132, $text, 10); //お届け先都道府県+住所1
        $this->lfText(26, 136, $this->arrDisp['deliv_addr02'], 10); //お届け先住所2
        $text = $this->arrDisp['deliv_name01']."　".$this->arrDisp['deliv_name02']."　様";
        $this->lfText(26, 140, $text, 10); //お届け先氏名

        $this->lfText(144, 121, SC_Utils_Ex::sfDispDBDate($this->arrDisp['create_date']), 10); //ご注文日
        $this->lfText(144, 131, $this->arrDisp['order_id'], 10); //注文番号

        $this->pdf->SetFont('SJIS', 'B', 15);
        $this->pdf->Cell(0, 10, $this->sjis_conv($this->tpl_title), 0, 2, 'C', 0, '');  //文書タイトル（納品書・請求書）
        $this->pdf->Cell(0, 66, '', 0, 2, 'R', 0, '');
        $this->pdf->Cell(5, 0, '', 0, 0, 'R', 0, '');
        $this->pdf->Cell(67, 8, $this->sjis_conv(number_format($this->arrDisp['payment_total'])." 円"), 0, 2, 'R', 0, '');
        $this->pdf->Cell(0, 45, '', 0, 2, '', 0, '');

        $this->pdf->SetFont('SJIS', '', 8);

        $monetary_unit = $this->sjis_conv("円");
        $point_unit = $this->sjis_conv("Pt");

        // 購入商品情報
        for ($i = 0; $i < count($this->arrDisp['quantity']); $i++) {

            // 購入数量
            $data[0] = $this->arrDisp['quantity'][$i];

            // 税込金額（単価）
            $data[1] = SC_Helper_DB_Ex::sfCalcIncTax($this->arrDisp['price'][$i]);

            // 小計（商品毎）
            $data[2] = $data[0] * $data[1];

            $arrOrder[$i][0]  = $this->sjis_conv($this->arrDisp['product_name'][$i]." / ");
            $arrOrder[$i][0] .= $this->sjis_conv($this->arrDisp['product_code'][$i]." / ");
            if ($this->arrDisp['classcategory_name1'][$i]) {
                $arrOrder[$i][0] .= $this->sjis_conv(" [ ".$this->arrDisp['classcategory_name1'][$i]);
                if ($this->arrDisp['classcategory_name2'][$i] == "") {
                    $arrOrder[$i][0] .= " ]";
                } else {
                    $arrOrder[$i][0] .= $this->sjis_conv(" * ".$this->arrDisp['classcategory_name2'][$i]." ]");
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
        $arrOrder[$i][2] = $this->sjis_conv("商品合計");
        $arrOrder[$i][3] = number_format($this->arrDisp['subtotal']).$monetary_unit;

        $i++;
        $arrOrder[$i][0] = "";
        $arrOrder[$i][1] = "";
        $arrOrder[$i][2] = $this->sjis_conv("送料");
        $arrOrder[$i][3] = number_format($this->arrDisp['deliv_fee']).$monetary_unit;

        $i++;
        $arrOrder[$i][0] = "";
        $arrOrder[$i][1] = "";
        $arrOrder[$i][2] = $this->sjis_conv("手数料");
        $arrOrder[$i][3] = number_format($this->arrDisp['charge']).$monetary_unit;

        $i++;
        $arrOrder[$i][0] = "";
        $arrOrder[$i][1] = "";
        $arrOrder[$i][2] = $this->sjis_conv("値引き");
        $arrOrder[$i][3] = "- ".number_format(($this->arrDisp['use_point'] * POINT_VALUE) + $this->arrDisp['discount']).$monetary_unit;

        $i++;
        $arrOrder[$i][0] = "";
        $arrOrder[$i][1] = "";
        $arrOrder[$i][2] = $this->sjis_conv("請求金額");
        $arrOrder[$i][3] = number_format($this->arrDisp['payment_total']).$monetary_unit;

        // ポイント表記
        if ($this->arrData['disp_point'] && $this->arrDisp['customer_id']) {
            $i++;
            $arrOrder[$i][0] = "";
            $arrOrder[$i][1] = "";
            $arrOrder[$i][2] = "";
            $arrOrder[$i][3] = "";

            $i++;
            $arrOrder[$i][0] = "";
            $arrOrder[$i][1] = "";
            $arrOrder[$i][2] = $this->sjis_conv("利用ポイント");
            $arrOrder[$i][3] = number_format($this->arrDisp['use_point']).$point_unit;

            $i++;
            $arrOrder[$i][0] = "";
            $arrOrder[$i][1] = "";
            $arrOrder[$i][2] = $this->sjis_conv("加算ポイント");
            $arrOrder[$i][3] = number_format($this->arrDisp['add_point']).$point_unit;

            $i++;
            $arrOrder[$i][0] = "";
            $arrOrder[$i][1] = "";
            $arrOrder[$i][2] = $this->sjis_conv("所有ポイント");
            $arrOrder[$i][3] = number_format($this->arrDisp['point']).$point_unit;
        }

        $this->pdf->FancyTable($this->label_cell, $arrOrder, $this->width_cell);
    }

    function setEtcData() {
        $this->pdf->Cell(0, 10, '', 0, 1, 'C', 0, '');
        $this->pdf->SetFont('SJIS', '', 9);
        $this->pdf->MultiCell(0, 6, $this->sjis_conv("＜ 備 考 ＞"), 'T', 2, 'L', 0, '');  //備考
        $this->pdf->Ln();
        $this->pdf->SetFont('SJIS', '', 8);
        $this->pdf->MultiCell(0, 4, $this->sjis_conv($this->arrData['etc1']."\n".$this->arrData['etc2']."\n".$this->arrData['etc3']), '', 2, 'L', 0, '');  //備考
    }

    function createPdf() {
        // PDFをブラウザに送信
        ob_clean();
        if ($this->pdf_download == 1) {
            if ($this->pdf->PageNo() == 1) {
                $filename = "nouhinsyo-No".$this->arrData['order_id'].".pdf";
            } else {
                $filename = "nouhinsyo.pdf";
            }
            $this->pdf->Output($this->sjis_conv($filename), D);
        } else {
            $this->pdf->Output();
        }

        // 入力してPDFファイルを閉じる
        $this->pdf->Close();
    }

    // PDF_Japanese::Text へのパーサー
    function lfText($x, $y, $text, $size, $style = '') {
        $text = mb_convert_encoding($text, "SJIS-win", CHAR_CODE);

        $this->pdf->SetFont('SJIS', $style, $size);
        $this->pdf->Text($x, $y, $text);
    }

    // 受注データの取得
    function lfGetOrderData($order_id) {
        if(SC_Utils_Ex::sfIsInt($order_id)) {
            // DBから受注情報を読み込む
            $objQuery = new SC_Query();
            $where = "order_id = ?";
            $arrRet = $objQuery->select("*", "dtb_order", $where, array($order_id));
            $this->arrDisp = $arrRet[0];
            list($point) = SC_Helper_Customer_Ex::sfGetCustomerPoint($order_id, $arrRet[0]['use_point'], $arrRet[0]['add_point']);
            $this->arrDisp['point'] = $point;

            // 受注詳細データの取得
            $arrRet = $this->lfGetOrderDetail($order_id);
            $arrRet = SC_Utils_Ex::sfSwapArray($arrRet);
            $this->arrDisp = array_merge($this->arrDisp, $arrRet);

            // その他支払い情報を表示
            if($this->arrDisp["memo02"] != "") $this->arrDisp["payment_info"] = unserialize($this->arrDisp["memo02"]);
            if($this->arrDisp["memo01"] == PAYMENT_CREDIT_ID){
                  $this->arrDisp["payment_type"] = "クレジット決済";
            } elseif ($this->arrDisp["memo01"] == PAYMENT_CONVENIENCE_ID) {
                  $this->arrDisp["payment_type"] = "コンビニ決済";
            } else {
                  $this->arrDisp["payment_type"] = "お支払い";
            }
        }
    }

    // 受注詳細データの取得
    function lfGetOrderDetail($order_id) {
        $objQuery = new SC_Query();
        $col = "product_id, product_class_id, product_code, product_name, classcategory_name1, classcategory_name2, price, quantity, point_rate";
        $where = "order_id = ?";
        $objQuery->setOrder("product_class_id");
        $arrRet = $objQuery->select($col, "dtb_order_detail", $where, array($order_id));
        return $arrRet;
    }

    // 文字コードSJIS変換 -> japanese.phpで使用出来る文字コードはSJIS-winのみ
    function sjis_conv($conv_str) {
        return (mb_convert_encoding($conv_str, "SJIS-win", CHAR_CODE));
    }

}
?>
