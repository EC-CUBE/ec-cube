<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\OrderPdfRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ShippingRepository;
use Eccube\Twig\Extension\EccubeExtension;
use Eccube\Twig\Extension\TaxExtension;
use setasign\Fpdi\TcpdfFpdi;

/**
 * Class OrderPdfService.
 * Do export pdf function.
 */
class OrderPdfService extends TcpdfFpdi
{
    /** @var OrderRepository */
    protected $orderRepository;

    /** @var ShippingRepository */
    protected $shippingRepository;

    /** @var OrderPdfRepository */
    protected $orderPdfRepository;

    /** @var TaxRuleService */
    protected $taxRuleService;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var EccubeExtension
     */
    protected $eccubeExtension;

    /**
     * @var TaxExtension
     */
    protected $taxExtension;

    // ====================================
    // 定数宣言
    // ====================================

    /** ダウンロードするPDFファイルのデフォルト名 */
    const DEFAULT_PDF_FILE_NAME = 'nouhinsyo.pdf';

    /** FONT ゴシック */
    const FONT_GOTHIC = 'kozgopromedium';
    /** FONT 明朝 */
    const FONT_SJIS = 'kozminproregular';

    // ====================================
    // 変数宣言
    // ====================================

    /** @var BaseInfo */
    public $baseInfoRepository;

    /** 購入詳細情報 ラベル配列
     * @var array
     */
    protected $labelCell = [];

    /*** 購入詳細情報 幅サイズ配列
     * @var array
     */
    protected $widthCell = [];

    /** 最後に処理した注文番号 @var string */
    protected $lastOrderId = null;

    // --------------------------------------
    // Font情報のバックアップデータ
    /** @var string フォント名 */
    protected $bakFontFamily;
    /** @var string フォントスタイル */
    protected $bakFontStyle;
    /** @var string フォントサイズ */
    protected $bakFontSize;
    // --------------------------------------

    // lfTextのoffset
    protected $baseOffsetX = 0;
    protected $baseOffsetY = -4;

    /** ダウンロードファイル名 @var string */
    protected $downloadFileName = null;

    /** 発行日 @var string */
    protected $issueDate = '';

    /**
     * OrderPdfService constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param OrderRepository $orderRepository
     * @param ShippingRepository $shippingRepository
     * @param TaxRuleService $taxRuleService
     * @param BaseInfoRepository $baseInfoRepository
     * @param EccubeExtension $eccubeExtension
     * @param TaxExtension $taxExtension
     *
     * @throws \Exception
     */
    public function __construct(EccubeConfig $eccubeConfig, OrderRepository $orderRepository, ShippingRepository $shippingRepository, TaxRuleService $taxRuleService, BaseInfoRepository $baseInfoRepository, EccubeExtension $eccubeExtension, TaxExtension $taxExtension)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->baseInfoRepository = $baseInfoRepository->get();
        $this->orderRepository = $orderRepository;
        $this->shippingRepository = $shippingRepository;
        $this->taxRuleService = $taxRuleService;
        $this->eccubeExtension = $eccubeExtension;
        $this->taxExtension = $taxExtension;

        parent::__construct();

        // 購入詳細情報の設定を行う
        // 動的に入れ替えることはない
        $this->labelCell[] = '商品名 / 商品コード';
        $this->labelCell[] = '数量';
        $this->labelCell[] = '単価';
        $this->labelCell[] = '金額(税込)';
        $this->widthCell = [110.3, 12, 21.7, 24.5];

        // Fontの設定しておかないと文字化けを起こす
        $this->SetFont(self::FONT_SJIS);

        // PDFの余白(上左右)を設定
        $this->SetMargins(15, 20);

        // ヘッダーの出力を無効化
        $this->setPrintHeader(false);

        // フッターの出力を無効化
        $this->setPrintFooter(true);
        $this->setFooterMargin();
        $this->setFooterFont([self::FONT_SJIS, '', 8]);
    }

    /**
     * 注文情報からPDFファイルを作成する.
     *
     * @param array $formData
     *                        [KEY]
     *                        ids: 注文番号
     *                        issue_date: 発行日
     *                        title: タイトル
     *                        message1: メッセージ1行目
     *                        message2: メッセージ2行目
     *                        message3: メッセージ3行目
     *                        note1: 備考1行目
     *                        note2: 備考2行目
     *                        note3: 備考3行目
     *
     * @return bool
     */
    public function makePdf(array $formData)
    {
        // 発行日の設定
        $this->issueDate = '作成日: '.$formData['issue_date']->format('Y年m月d日');
        // ダウンロードファイル名の初期化
        $this->downloadFileName = null;

        // データが空であれば終了
        if (!$formData['ids']) {
            return false;
        }

        // 出荷番号をStringからarrayに変換
        $ids = explode(',', $formData['ids']);

        // 空文字列の場合のデフォルトメッセージを設定する
        $this->setDefaultData($formData);

        foreach ($ids as $id) {
            $this->lastOrderId = $id;

            // 出荷番号から出荷情報を取得する
            /** @var Shipping $Shipping */
            $Shipping = $this->shippingRepository->find($id);
            if (!$Shipping) {
                // 出荷情報の取得ができなかった場合
                continue;
            }

            // テンプレートファイルを読み込む
            $Order = $Shipping->getOrder();
            if ($Order->isMultiple()) {
                // 複数配送の時は読み込むテンプレートファイルを変更する
                $userPath = $this->eccubeConfig->get('eccube_html_admin_dir').'/assets/pdf/nouhinsyo_multiple.pdf';
            } else {
                $userPath = $this->eccubeConfig->get('eccube_html_admin_dir').'/assets/pdf/nouhinsyo.pdf';
            }
            $this->setSourceFile($userPath);

            // PDFにページを追加する
            $this->addPdfPage();

            // タイトルを描画する
            $this->renderTitle($formData['title']);

            // 店舗情報を描画する
            $this->renderShopData();

            // 注文情報を描画する
            $this->renderOrderData($Shipping);

            // メッセージを描画する
            $this->renderMessageData($formData);

            // 出荷詳細情報を描画する
            $this->renderOrderDetailData($Shipping);

            // 備考を描画する
            $this->renderEtcData($formData);
        }

        return true;
    }

    /**
     * PDFファイルを出力する.
     *
     * @return string|mixed
     */
    public function outputPdf()
    {
        return $this->Output($this->getPdfFileName(), 'S');
    }

    /**
     * PDFファイル名を取得する
     * PDFが1枚の時は注文番号をファイル名につける.
     *
     * @return string ファイル名
     */
    public function getPdfFileName()
    {
        if (!is_null($this->downloadFileName)) {
            return $this->downloadFileName;
        }
        $this->downloadFileName = self::DEFAULT_PDF_FILE_NAME;
        if ($this->PageNo() == 1) {
            $this->downloadFileName = 'nouhinsyo-No'.$this->lastOrderId.'.pdf';
        }

        return $this->downloadFileName;
    }

    /**
     * フッターに発行日を出力する.
     */
    public function Footer()
    {
        $this->Cell(0, 0, $this->issueDate, 0, 0, 'R');
    }

    /**
     * 作成するPDFのテンプレートファイルを指定する.
     */
    protected function addPdfPage()
    {
        // ページを追加
        $this->AddPage();

        // テンプレートに使うテンプレートファイルのページ番号を取得
        $tplIdx = $this->importPage(1);

        // テンプレートに使うテンプレートファイルのページ番号を指定
        $this->useTemplate($tplIdx, null, null, null, null, true);
        $this->setPageMark();
    }

    /**
     * PDFに店舗情報を設定する
     * ショップ名、ロゴ画像以外はdtb_helpに登録されたデータを使用する.
     */
    protected function renderShopData()
    {
        // 基準座標を設定する
        $this->setBasePosition();

        // ショップ名
        $this->lfText(125, 60, $this->baseInfoRepository->getShopName(), 8, 'B');

        // 都道府県+所在地
        $text = $this->baseInfoRepository->getPref().$this->baseInfoRepository->getAddr01();
        $this->lfText(125, 65, $text, 8);
        $this->lfText(125, 69, $this->baseInfoRepository->getAddr02(), 8);

        // 電話番号
        $text = 'TEL: '.$this->baseInfoRepository->getPhoneNumber();
        $this->lfText(125, 72, $text, 8); //TEL・FAX

        // メールアドレス
        if (strlen($this->baseInfoRepository->getEmail01()) > 0) {
            $text = 'Email: '.$this->baseInfoRepository->getEmail01();
            $this->lfText(125, 75, $text, 8); // Email
        }

        // user_dataにlogo.pngが配置されている場合は優先的に読み込む
        $logoFile = $this->eccubeConfig->get('eccube_html_dir').'/user_data/assets/pdf/logo.png';

        if (!file_exists($logoFile)) {
            $logoFile = $this->eccubeConfig->get('eccube_html_admin_dir').'/assets/pdf/logo.png';
        }

        $this->Image($logoFile, 124, 46, 40);
    }

    /**
     * メッセージを設定する.
     *
     * @param array $formData
     */
    protected function renderMessageData(array $formData)
    {
        $this->lfText(27, 70, $formData['message1'], 8); //メッセージ1
        $this->lfText(27, 74, $formData['message2'], 8); //メッセージ2
        $this->lfText(27, 78, $formData['message3'], 8); //メッセージ3
    }

    /**
     * PDFに備考を設定数.
     *
     * @param array $formData
     */
    protected function renderEtcData(array $formData)
    {
        // フォント情報のバックアップ
        $this->backupFont();

        $this->Cell(0, 10, '', 0, 1, 'C', 0, '');

        $this->SetFont(self::FONT_GOTHIC, 'B', 9);
        $this->MultiCell(0, 6, '＜ 備考 ＞', 'T', 2, 'L', 0, '');

        $this->SetFont(self::FONT_SJIS, '', 8);

        $this->Ln();
        // rtrimを行う
        $text = preg_replace('/\s+$/us', '', $formData['note1']."\n".$formData['note2']."\n".$formData['note3']);
        $this->MultiCell(0, 4, $text, '', 2, 'L', 0, '');

        // フォント情報の復元
        $this->restoreFont();
    }

    /**
     * タイトルをPDFに描画する.
     *
     * @param string $title
     */
    protected function renderTitle($title)
    {
        // 基準座標を設定する
        $this->setBasePosition();

        // フォント情報のバックアップ
        $this->backupFont();

        //文書タイトル（納品書・請求書）
        $this->SetFont(self::FONT_GOTHIC, '', 15);
        $this->Cell(0, 10, $title, 0, 2, 'C', 0, '');
        $this->Cell(0, 66, '', 0, 2, 'R', 0, '');
        $this->Cell(5, 0, '', 0, 0, 'R', 0, '');

        // フォント情報の復元
        $this->restoreFont();
    }

    /**
     * 購入者情報を設定する.
     *
     * @param Shipping $Shipping
     */
    protected function renderOrderData(Shipping $Shipping)
    {
        // 基準座標を設定する
        $this->setBasePosition();

        // フォント情報のバックアップ
        $this->backupFont();

        // =========================================
        // 購入者情報部
        // =========================================

        $Order = $Shipping->getOrder();

        // 購入者都道府県+住所1
        // $text = $Order->getPref().$Order->getAddr01();
        $text = $Shipping->getPref().$Shipping->getAddr01();
        $this->lfText(27, 47, $text, 10);
        $this->lfText(27, 51, $Shipping->getAddr02(), 10); //購入者住所2

        // 購入者氏名
        if (null !== $Shipping->getCompanyName()) {
            // 会社名
            $text = $Shipping->getCompanyName();
            $this->lfText(27, 57, $text, 11);
            // 氏名
            $text = $Shipping->getName01().'　'.$Shipping->getName02().'　様';
            $this->lfText(27, 63, $text, 11);
        } else {
            $text = $Shipping->getName01().'　'.$Shipping->getName02().'　様';
            $this->lfText(27, 59, $text, 11);
        }

        // =========================================
        // お買い上げ明細部
        // =========================================
        $this->SetFont(self::FONT_SJIS, '', 10);

        //ご注文日
        $orderDate = $Order->getCreateDate()->format('Y/m/d H:i');
        if ($Order->getOrderDate()) {
            $orderDate = $Order->getOrderDate()->format('Y/m/d H:i');
        }

        $this->lfText(25, 125, $orderDate, 10);
        //注文番号
        $this->lfText(25, 135, $Order->getOrderNo(), 10);

        // 総合計金額
        if (!$Order->isMultiple()) {
            $this->SetFont(self::FONT_SJIS, 'B', 15);
            $paymentTotalText = $this->eccubeExtension->getPriceFilter($Order->getPaymentTotal());

            $this->setBasePosition(120, 95.5);
            $this->Cell(5, 7, '', 0, 0, '', 0, '');
            $this->Cell(67, 8, $paymentTotalText, 0, 2, 'R', 0, '');
            $this->Cell(0, 45, '', 0, 2, '', 0, '');
        }

        // フォント情報の復元
        $this->restoreFont();
    }

    /**
     * 購入商品詳細情報を設定する.
     *
     * @param Shipping $Shipping
     */
    protected function renderOrderDetailData(Shipping $Shipping)
    {
        $arrOrder = [];
        // テーブルの微調整を行うための購入商品詳細情報をarrayに変換する

        // =========================================
        // 受注詳細情報
        // =========================================
        $i = 0;
        $isShowReducedTaxMess = false;
        $Order = $Shipping->getOrder();
        /* @var OrderItem $OrderItem */
        foreach ($Shipping->getOrderItems() as $OrderItem) {
            if (!$Order->isMultiple() && !$OrderItem->isProduct()) {
                continue;
            }
            // class categoryの生成
            $classCategory = '';
            /** @var OrderItem $OrderItem */
            if ($OrderItem->getClassCategoryName1()) {
                $classCategory .= ' [ '.$OrderItem->getClassCategoryName1();
                if ($OrderItem->getClassCategoryName2() == '') {
                    $classCategory .= ' ]';
                } else {
                    $classCategory .= ' * '.$OrderItem->getClassCategoryName2().' ]';
                }
            }

            // product
            $productName = $OrderItem->getProductName();
            if (null !== $OrderItem->getProductCode()) {
                $productName .= ' / '.$OrderItem->getProductCode();
            }
            if ($classCategory) {
                $productName .= ' / '.$classCategory;
            }
            if ($this->taxExtension->isReducedTaxRate($OrderItem)) {
                $productName .= ' ※';
                $isShowReducedTaxMess = true;
            }
            $arrOrder[$i][0] = $productName;
            // 購入数量
            $arrOrder[$i][1] = number_format($OrderItem->getQuantity());
            // 税込金額（単価）
            $arrOrder[$i][2] = $this->eccubeExtension->getPriceFilter($OrderItem->getPrice());
            // 小計（商品毎）
            $arrOrder[$i][3] = $this->eccubeExtension->getPriceFilter($OrderItem->getTotalPrice());

            ++$i;
        }

        if (!$Order->isMultiple()) {
            // =========================================
            // 小計
            // =========================================
            $arrOrder[$i][0] = '';
            $arrOrder[$i][1] = '';
            $arrOrder[$i][2] = '';
            $arrOrder[$i][3] = '';

            ++$i;
            $arrOrder[$i][0] = '';
            $arrOrder[$i][1] = '';
            $arrOrder[$i][2] = '商品合計';
            $arrOrder[$i][3] = $this->eccubeExtension->getPriceFilter($Order->getSubtotal());

            ++$i;
            $arrOrder[$i][0] = '';
            $arrOrder[$i][1] = '';
            $arrOrder[$i][2] = '送料';
            $arrOrder[$i][3] = $this->eccubeExtension->getPriceFilter($Order->getDeliveryFeeTotal());

            ++$i;
            $arrOrder[$i][0] = '';
            $arrOrder[$i][1] = '';
            $arrOrder[$i][2] = '手数料';
            $arrOrder[$i][3] = $this->eccubeExtension->getPriceFilter($Order->getCharge());

            ++$i;
            $arrOrder[$i][0] = '';
            $arrOrder[$i][1] = '';
            $arrOrder[$i][2] = '値引き';
            $arrOrder[$i][3] = $this->eccubeExtension->getPriceFilter($Order->getTaxableDiscount());

            ++$i;
            $arrOrder[$i][0] = '';
            $arrOrder[$i][1] = '';
            $arrOrder[$i][2] = '';
            $arrOrder[$i][3] = '';

            ++$i;
            $arrOrder[$i][0] = '';
            $arrOrder[$i][1] = '';
            $arrOrder[$i][2] = '合計';
            $arrOrder[$i][3] = $this->eccubeExtension->getPriceFilter($Order->getTaxableTotal());

            foreach ($Order->getTaxableTotalByTaxRate() as $rate => $total) {
                ++$i;
                $arrOrder[$i][0] = '';
                $arrOrder[$i][1] = '';
                $arrOrder[$i][2] = '('.$rate.'%対象)';
                $arrOrder[$i][3] = $this->eccubeExtension->getPriceFilter($total);
            }

            ++$i;
            $arrOrder[$i][0] = '';
            $arrOrder[$i][1] = '';
            $arrOrder[$i][2] = '';
            $arrOrder[$i][3] = '';

            foreach ($Order->getTaxFreeDiscountItems() as $Item) {
                ++$i;
                $arrOrder[$i][0] = '';
                $arrOrder[$i][1] = '';
                $arrOrder[$i][2] = $Item->getProductName();
                $arrOrder[$i][3] = $this->eccubeExtension->getPriceFilter($Item->getTotalPrice());
            }

            ++$i;
            $arrOrder[$i][0] = '';
            $arrOrder[$i][1] = '';
            $arrOrder[$i][2] = '請求金額';
            $arrOrder[$i][3] = $this->eccubeExtension->getPriceFilter($Order->getPaymentTotal());

            if ($isShowReducedTaxMess) {
                ++$i;
                $arrOrder[$i][0] = '※は軽減税率対象商品です。';
                $arrOrder[$i][1] = '';
                $arrOrder[$i][2] = '';
                $arrOrder[$i][3] = '';
            }
        }

        // PDFに設定する
        $this->setFancyTable($this->labelCell, $arrOrder, $this->widthCell);
    }

    /**
     * PDFへのテキスト書き込み
     *
     * @param int    $x     X座標
     * @param int    $y     Y座標
     * @param string $text  テキスト
     * @param int    $size  フォントサイズ
     * @param string $style フォントスタイル
     */
    protected function lfText($x, $y, $text, $size = 0, $style = '')
    {
        // 退避
        $bakFontStyle = $this->FontStyle;
        $bakFontSize = $this->FontSizePt;

        $this->SetFont('', $style, $size);
        $this->Text($x + $this->baseOffsetX, $y + $this->baseOffsetY, $text);

        // 復元
        $this->SetFont('', $bakFontStyle, $bakFontSize);
    }

    /**
     * Colored table.
     *
     * TODO: 後の列の高さが大きい場合、表示が乱れる。
     *
     * @param array $header 出力するラベル名一覧
     * @param array $data   出力するデータ
     * @param array $w      出力するセル幅一覧
     */
    protected function setFancyTable($header, $data, $w)
    {
        // フォント情報のバックアップ
        $this->backupFont();

        // 開始座標の設定
        $this->setBasePosition(0, 149);

        // Colors, line width and bold font
        $this->SetFillColor(216, 216, 216);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont(self::FONT_SJIS, 'B', 8);
        $this->SetFont('', 'B');

        // Header
        $this->Cell(5, 7, '', 0, 0, '', 0, '');
        $count = count($header);
        for ($i = 0; $i < $count; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(235, 235, 235);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = 0;
        $h = 4;
        foreach ($data as $row) {
            // 行のの処理
            $i = 0;
            $h = 4;
            $this->Cell(5, $h, '', 0, 0, '', 0, '');

            // Cellの高さを保持
            $cellHeight = 0;
            foreach ($row as $col) {
                // 列の処理
                // TODO: 汎用的ではない処理。この指定は呼び出し元で行うようにしたい。
                // テキストの整列を指定する
                $align = ($i == 0) ? 'L' : 'R';

                // セル高さが最大値を保持する
                if ($h >= $cellHeight) {
                    $cellHeight = $h;
                }

                // 最終列の場合は次の行へ移動
                // (0: 右へ移動(既定)/1: 次の行へ移動/2: 下へ移動)
                $ln = ($i == (count($row) - 1)) ? 1 : 0;

                $this->MultiCell(
                    $w[$i], // セル幅
                    $cellHeight, // セルの最小の高さ
                    $col, // 文字列
                    1, // 境界線の描画方法を指定
                    $align, // テキストの整列
                    $fill, // 背景の塗つぶし指定
                    $ln                 // 出力後のカーソルの移動方法
                );
                $h = $this->getLastH();

                ++$i;
            }
            $fill = !$fill;
        }
        $this->Cell(5, $h, '', 0, 0, '', 0, '');
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->SetFillColor(255);

        // フォント情報の復元
        $this->restoreFont();
    }

    /**
     * 基準座標を設定する.
     *
     * @param int $x
     * @param int $y
     */
    protected function setBasePosition($x = null, $y = null)
    {
        // 現在のマージンを取得する
        $result = $this->getMargins();

        // 基準座標を指定する
        $actualX = is_null($x) ? $result['left'] : $x;
        $this->SetX($actualX);
        $actualY = is_null($y) ? $result['top'] : $y;
        $this->SetY($actualY);
    }

    /**
     * データが設定されていない場合にデフォルト値を設定する.
     *
     * @param array $formData
     */
    protected function setDefaultData(array &$formData)
    {
        $defaultList = [
            'title' => trans('admin.order.delivery_note_title__default'),
            'message1' => trans('admin.order.delivery_note_message__default1'),
            'message2' => trans('admin.order.delivery_note_message__default2'),
            'message3' => trans('admin.order.delivery_note_message__default3'),
        ];

        foreach ($defaultList as $key => $value) {
            if (is_null($formData[$key])) {
                $formData[$key] = $value;
            }
        }
    }

    /**
     * Font情報のバックアップ.
     */
    protected function backupFont()
    {
        // フォント情報のバックアップ
        $this->bakFontFamily = $this->FontFamily;
        $this->bakFontStyle = $this->FontStyle;
        $this->bakFontSize = $this->FontSizePt;
    }

    /**
     * Font情報の復元.
     */
    protected function restoreFont()
    {
        $this->SetFont($this->bakFontFamily, $this->bakFontStyle, $this->bakFontSize);
    }
}
