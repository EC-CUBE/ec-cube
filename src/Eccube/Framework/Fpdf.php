<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\CustomerHelper;
use Eccube\Framework\Helper\PurchaseHelper;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * PDF 納品書を出力する
 *
 * TODO ページクラスとすべき要素を多々含んでいるように感じる。
 */

define('PDF_TEMPLATE_REALDIR', TEMPLATE_ADMIN_REALDIR . 'pdf/');

class Fpdf extends Helper\FPDI
{
    public function __construct($download, $title, $tpl_pdf = 'nouhinsyo1.pdf')
    {
        $this->FPDF();
        // デフォルトの設定
        $this->tpl_pdf = PDF_TEMPLATE_REALDIR . $tpl_pdf;  // テンプレートファイル
        $this->pdf_download = $download;      // PDFのダウンロード形式（0:表示、1:ダウンロード）
        $this->tpl_title = $title;
        $this->tpl_dispmode = 'real';      // 表示モード
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->width_cell = array(110.3,12,21.7,24.5);

        $this->label_cell[] = '商品名 / 商品コード / [ 規格 ]';
        $this->label_cell[] = '数量';
        $this->label_cell[] = '単価';
        $this->label_cell[] = '金額(税込)';

        $this->arrMessage = array(
            'このたびはお買上げいただきありがとうございます。',
            '下記の内容にて納品させていただきます。',
            'ご確認くださいますよう、お願いいたします。'
        );

        // SJISフォント
        $this->AddSJISFont();
        $this->SetFont('SJIS');

        //ページ総数取得
        $this->AliasNbPages();

        // マージン設定
        $this->SetMargins(15, 20);

        // PDFを読み込んでページ数を取得
        $this->pageno = $this->setSourceFile($this->tpl_pdf);
    }

    public function setData($arrData)
    {
        $this->arrData = $arrData;

        // ページ番号よりIDを取得
        $tplidx = $this->ImportPage(1);

        // ページを追加（新規）
        $this->AddPage();

        //表示倍率(100%)
        $this->SetDisplayMode($this->tpl_dispmode);

        if (Utils::sfIsInt($arrData['order_id'])) {
            $this->disp_mode = true;
        }

        // テンプレート内容の位置、幅を調整 ※useTemplateに引数を与えなければ100%表示がデフォルト
        $this->useTemplate($tplidx);

        $this->setShopData();
        $this->setMessageData();
        $this->setOrderData();
        $this->setEtcData();
    }

    private function setShopData()
    {
        // ショップ情報

        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $arrInfo = $objDb->getBasisData();

        // ショップ名
        $this->lfText(125, 60, $arrInfo['shop_name'], 8, 'B');
        // URL
        $this->lfText(125, 63, $arrInfo['law_url'], 8);
        // 会社名
        $this->lfText(125, 68, $arrInfo['law_company'], 8);
        // 郵便番号
        $text = '〒 ' . $arrInfo['law_zip01'] . ' - ' . $arrInfo['law_zip02'];
        $this->lfText(125, 71, $text, 8);
        // 都道府県+所在地
        $text = $this->arrPref[$arrInfo['law_pref']] . $arrInfo['law_addr01'];
        $this->lfText(125, 74, $text, 8);
        $this->lfText(125, 77, $arrInfo['law_addr02'], 8);

        $text = 'TEL: '.$arrInfo['law_tel01'].'-'.$arrInfo['law_tel02'].'-'.$arrInfo['law_tel03'];
        //FAX番号が存在する場合、表示する
        if (strlen($arrInfo['law_fax01']) > 0) {
            $text .= '　FAX: '.$arrInfo['law_fax01'].'-'.$arrInfo['law_fax02'].'-'.$arrInfo['law_fax03'];
        }
        $this->lfText(125, 80, $text, 8);  //TEL・FAX

        if (strlen($arrInfo['law_email']) > 0) {
            $text = 'Email: '.$arrInfo['law_email'];
            $this->lfText(125, 83, $text, 8);      //Email
        }

        //ロゴ画像
        $logo_file = PDF_TEMPLATE_REALDIR . 'logo.png';
        $this->Image($logo_file, 124, 46, 40);
    }

    private function setMessageData()
    {
        // メッセージ
        $this->lfText(27, 70, $this->arrData['msg1'], 8);  //メッセージ1
        $this->lfText(27, 74, $this->arrData['msg2'], 8);  //メッセージ2
        $this->lfText(27, 78, $this->arrData['msg3'], 8);  //メッセージ3
        $text = '作成日: '.$this->arrData['year'].'年'.$this->arrData['month'].'月'.$this->arrData['day'].'日';
        $this->lfText(158, 288, $text, 8);  //作成日
    }

    private function setOrderData()
    {
        $arrOrder = array();
        // DBから受注情報を読み込む
        $this->lfGetOrderData($this->arrData['order_id']);

        // 購入者情報
        $text = '〒 '.$this->arrDisp['order_zip01'].' - '.$this->arrDisp['order_zip02'];
        $this->lfText(23, 43, $text, 10); //購入者郵便番号
        $text = $this->arrPref[$this->arrDisp['order_pref']] . $this->arrDisp['order_addr01'];
        $this->lfText(27, 47, $text, 10); //購入者都道府県+住所1
        $this->lfText(27, 51, $this->arrDisp['order_addr02'], 10); //購入者住所2
        $text = $this->arrDisp['order_name01'].'　'.$this->arrDisp['order_name02'].'　様';
        $this->lfText(27, 59, $text, 11); //購入者氏名

        // お届け先情報
        $this->SetFont('SJIS', '', 10);
        $this->lfText(25, 125, Utils::sfDispDBDate($this->arrDisp['create_date']), 10); //ご注文日
        $this->lfText(25, 135, $this->arrDisp['order_id'], 10); //注文番号

        $this->SetFont('Gothic', 'B', 15);
        $this->Cell(0, 10, $this->tpl_title, 0, 2, 'C', 0, '');  //文書タイトル（納品書・請求書）
        $this->Cell(0, 66, '', 0, 2, 'R', 0, '');
        $this->Cell(5, 0, '', 0, 0, 'R', 0, '');
        $this->SetFont('SJIS', 'B', 15);
        $this->Cell(67, 8, number_format($this->arrDisp['payment_total']).' 円', 0, 2, 'R', 0, '');
        $this->Cell(0, 45, '', 0, 2, '', 0, '');

        $this->SetFont('SJIS', '', 8);

        $monetary_unit = '円';
        $point_unit = 'Pt';

        // 購入商品情報
        for ($i = 0; $i < count($this->arrDisp['quantity']); $i++) {
            // 購入数量
            $data[0] = $this->arrDisp['quantity'][$i];

            // 税込金額（単価）
            $data[1] = Application::alias('eccube.helper.db')->calcIncTax($this->arrDisp['price'][$i], $this->arrDisp['tax_rate'][$i], $this->arrDisp['tax_rule'][$i]);

            // 小計（商品毎）
            $data[2] = $data[0] * $data[1];

            $arrOrder[$i][0]  = $this->arrDisp['product_name'][$i].' / ';
            $arrOrder[$i][0] .= $this->arrDisp['product_code'][$i].' / ';
            if ($this->arrDisp['classcategory_name1'][$i]) {
                $arrOrder[$i][0] .= ' [ '.$this->arrDisp['classcategory_name1'][$i];
                if ($this->arrDisp['classcategory_name2'][$i] == '') {
                    $arrOrder[$i][0] .= ' ]';
                } else {
                    $arrOrder[$i][0] .= ' * '.$this->arrDisp['classcategory_name2'][$i].' ]';
                }
            }
            $arrOrder[$i][1]  = number_format($data[0]);
            $arrOrder[$i][2]  = number_format($data[1]).$monetary_unit;
            $arrOrder[$i][3]  = number_format($data[2]).$monetary_unit;
        }

        $arrOrder[$i][0] = '';
        $arrOrder[$i][1] = '';
        $arrOrder[$i][2] = '';
        $arrOrder[$i][3] = '';

        $i++;
        $arrOrder[$i][0] = '';
        $arrOrder[$i][1] = '';
        $arrOrder[$i][2] = '商品合計';
        $arrOrder[$i][3] = number_format($this->arrDisp['subtotal']).$monetary_unit;

        $i++;
        $arrOrder[$i][0] = '';
        $arrOrder[$i][1] = '';
        $arrOrder[$i][2] = '送料';
        $arrOrder[$i][3] = number_format($this->arrDisp['deliv_fee']).$monetary_unit;

        $i++;
        $arrOrder[$i][0] = '';
        $arrOrder[$i][1] = '';
        $arrOrder[$i][2] = '手数料';
        $arrOrder[$i][3] = number_format($this->arrDisp['charge']).$monetary_unit;

        $i++;
        $arrOrder[$i][0] = '';
        $arrOrder[$i][1] = '';
        $arrOrder[$i][2] = '値引き';
        $arrOrder[$i][3] = '- '.number_format(($this->arrDisp['use_point'] * POINT_VALUE) + $this->arrDisp['discount']).$monetary_unit;

        $i++;
        $arrOrder[$i][0] = '';
        $arrOrder[$i][1] = '';
        $arrOrder[$i][2] = '請求金額';
        $arrOrder[$i][3] = number_format($this->arrDisp['payment_total']).$monetary_unit;

        // ポイント表記
        if ($this->arrData['disp_point'] && $this->arrDisp['customer_id']) {
            $i++;
            $arrOrder[$i][0] = '';
            $arrOrder[$i][1] = '';
            $arrOrder[$i][2] = '';
            $arrOrder[$i][3] = '';

            $i++;
            $arrOrder[$i][0] = '';
            $arrOrder[$i][1] = '';
            $arrOrder[$i][2] = '利用ポイント';
            $arrOrder[$i][3] = number_format($this->arrDisp['use_point']).$point_unit;

            $i++;
            $arrOrder[$i][0] = '';
            $arrOrder[$i][1] = '';
            $arrOrder[$i][2] = '加算ポイント';
            $arrOrder[$i][3] = number_format($this->arrDisp['add_point']).$point_unit;
        }

        $this->FancyTable($this->label_cell, $arrOrder, $this->width_cell);
    }

    /**
     * 備考の出力を行う
     *
     * @return string 変更後の文字列
     */
    private function setEtcData()
    {
        $this->Cell(0, 10, '', 0, 1, 'C', 0, '');
        $this->SetFont('Gothic', 'B', 9);
        $this->MultiCell(0, 6, '＜ 備考 ＞', 'T', 2, 'L', 0, '');
        $this->SetFont('SJIS', '', 8);
        $text = Utils::rtrim($this->arrData['etc1'] . "\n" . $this->arrData['etc2'] . "\n" . $this->arrData['etc3']);
        $this->MultiCell(0, 4, $text, '', 2, 'L', 0, '');
    }

    public function createPdf()
    {
        // PDFをブラウザに送信
        ob_clean();
        if ($this->pdf_download == 1) {
            if ($this->PageNo() == 1) {
                $filename = 'nouhinsyo-No'.$this->arrData['order_id'].'.pdf';
            } else {
                $filename = 'nouhinsyo.pdf';
            }
            $this->Output($this->lfConvSjis($filename), 'D');
        } else {
            $this->Output();
        }

        // 入力してPDFファイルを閉じる
        $this->Close();
    }

    // PDF_Japanese::Text へのパーサー

    /**
     * @param integer $x
     * @param integer $y
     */
    private function lfText($x, $y, $text, $size = 0, $style = '')
    {
        // 退避
        $bak_font_style = $this->FontStyle;
        $bak_font_size = $this->FontSizePt;

        $this->SetFont('', $style, $size);
        $this->Text($x, $y, $text);

        // 復元
        $this->SetFont('', $bak_font_style, $bak_font_size);
    }

    // 受注データの取得
    private function lfGetOrderData($order_id)
    {
        if (Utils::sfIsInt($order_id)) {
            // DBから受注情報を読み込む
            /* @var $objPurchase PurchaseHelper */
            $objPurchase = Application::alias('eccube.helper.purchase');
            $this->arrDisp = $objPurchase->getOrder($order_id);
            list($point) = Application::alias('eccube.helper.customer')->sfGetCustomerPoint($order_id, $this->arrDisp['use_point'], $this->arrDisp['add_point']);
            $this->arrDisp['point'] = $point;

            // 受注詳細データの取得
            $arrRet = $objPurchase->getOrderDetail($order_id);
            $arrRet = Utils::sfSwapArray($arrRet);
            $this->arrDisp = array_merge($this->arrDisp, $arrRet);

            // その他支払い情報を表示
            if ($this->arrDisp['memo02'] != '') {
                $this->arrDisp['payment_info'] = unserialize($this->arrDisp['memo02']);
            }
            $this->arrDisp['payment_type'] = 'お支払い';
        }
    }
}
