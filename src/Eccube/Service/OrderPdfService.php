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
use Eccube\Service\Pdf\PdfWriter;
use Eccube\Twig\Extension\EccubeExtension;
use Eccube\Twig\Extension\TaxExtension;

/**
 * Class OrderPdfService.
 * Do export pdf function.
 */
class OrderPdfService
{
    protected OrderPdfRepository $orderPdfRepository;

    // ====================================
    // 定数宣言
    // ====================================

    /** ダウンロードするPDFファイルのデフォルト名 */
    public const DEFAULT_PDF_FILE_NAME = 'nouhinsyo.pdf';

    /** FONT ゴシック */
    public const FONT_GOTHIC = 'kozgopromedium';
    /** FONT 明朝 */
    public const FONT_SJIS = 'kozminproregular';

    /** ロゴ画像の描画位置(x, y)と幅(mm). 高さは画像の縦横比で決まる */
    public const LOGO_X = 124.0;
    public const LOGO_Y = 46.0;
    public const LOGO_WIDTH = 40.0;

    /** 店舗情報欄の先頭行の y 座標(mm) */
    public const SHOP_INFO_FIRST_Y = 58;

    /** 店舗情報欄の行送り(mm). 4.3 までの行送りに合わせる */
    public const SHOP_INFO_LINE_HEIGHT = 3;

    /**
     * 総合計金額の描画基準 y 座標(mm).
     *
     * この位置から空セル(高さ7mm)を挟んで金額セルを描画するため, 金額の実描画は 102.5mm 以降になる.
     * 店舗情報欄はこの基準位置より上に収める（安全側のマージンを含む下限）.
     */
    public const PAYMENT_TOTAL_BASE_Y = 95.5;

    // ====================================
    // 変数宣言
    // ====================================
    public BaseInfo $baseInfoRepository;

    protected PdfWriter $pdfWriter;

    /** 購入詳細情報 ラベル配列
     * @var array<int, string>
     */
    protected array $labelCell = [];

    /** 購入詳細情報 幅サイズ配列
     * @var array<int, float|int>
     */
    protected array $widthCell = [];

    /** @var string|null 最後に処理した注文番号 */
    protected ?string $lastOrderId = null;

    // --------------------------------------
    // Font情報のバックアップデータ
    /** @var string フォント名 */
    protected string $bakFontFamily = '';
    /** @var string フォントスタイル */
    protected string $bakFontStyle = '';
    /** @var float フォントサイズ(pt) */
    protected float $bakFontSize = 0.0;
    // --------------------------------------
    // lfTextのoffset
    protected int $baseOffsetX = 0;
    protected int $baseOffsetY = -4;

    /** @var string|null ダウンロードファイル名 */
    protected ?string $downloadFileName = null;

    /** @var string 発行日 */
    protected string $issueDate = '';

    /**
     * OrderPdfService constructor.
     *
     * @throws \Exception
     */
    public function __construct(protected EccubeConfig $eccubeConfig, protected OrderRepository $orderRepository, protected ShippingRepository $shippingRepository, protected TaxRuleService $taxRuleService, BaseInfoRepository $baseInfoRepository, protected EccubeExtension $eccubeExtension, protected TaxExtension $taxExtension)
    {
        $this->baseInfoRepository = $baseInfoRepository->get();

        $this->pdfWriter = $this->createPdfWriter();

        // 購入詳細情報の設定を行う
        // 動的に入れ替えることはない
        $this->labelCell[] = '商品名 / 商品コード';
        $this->labelCell[] = '数量';
        $this->labelCell[] = '単価';
        $this->labelCell[] = '金額(税込)';
        $this->widthCell = [110.3, 12, 21.7, 24.5];

        // Fontの設定しておかないと文字化けを起こす
        $this->pdfWriter->setFont(self::FONT_SJIS, '', 12);

        // PDFの余白(上左右)を設定
        $this->pdfWriter->setMargins(15, 20);
    }

    /**
     * 注文情報からPDFファイルを作成する.
     *
     * @param array{
     *     ids: string,
     *     issue_date: \DateTime,
     *     title: string,
     *     message1: string,
     *     message2: string,
     *     message3: string,
     *     note1: string,
     *     note2: string,
     *     note3: string
     *  } $formData
     *  [KEY]
     *  ids:
     *  issue_date: 発行日
     *  title: タイトル
     *  message1: メッセージ1行目
     *  message2: メッセージ2行目
     *  message3: メッセージ3行目
     *  note1: 備考1行目
     *  note2: 備考2行目
     *  note3: 備考3行目
     */
    public function makePdf(array $formData): bool
    {
        // 発行日の設定
        $this->issueDate = '作成日: '.$formData['issue_date']->format('Y年m月d日');
        // ダウンロードファイル名の初期化
        $this->downloadFileName = null;

        // データが空であれば終了
        if (!$formData['ids']) {
            return false;
        }

        // フッタは全ページ共通で発行日を右寄せで出す
        $this->pdfWriter->setFooter($this->issueDate, [self::FONT_SJIS, '', 8]);

        // 出荷番号をStringからarrayに変換
        $ids = explode(',', (string) $formData['ids']);

        foreach ($ids as $id) {
            $this->lastOrderId = $id;

            // 出荷番号から出荷情報を取得する
            /** @var Shipping|null $Shipping */
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
            $this->pdfWriter->setTemplateFile($userPath);

            // PDFにページを追加する
            $this->addPdfPage();

            // タイトルを描画する
            $this->renderTitle($formData['title']);

            // 店舗情報を描画する
            $this->renderShopData();

            // 注文情報を描画する
            $this->renderOrderData($Shipping);

            // メッセージを描画する
            $messageData = [
                'message1' => $formData['message1'],
                'message2' => $formData['message2'],
                'message3' => $formData['message3'],
            ];
            $this->renderMessageData($messageData);

            // 出荷詳細情報を描画する
            $this->renderOrderDetailData($Shipping);

            // 備考を描画する
            $this->renderEtcData($formData);
        }

        return true;
    }

    /**
     * PDFファイルを出力する.
     */
    public function outputPdf(): string
    {
        return $this->pdfWriter->output();
    }

    /**
     * PDFファイル名を取得する
     * PDFが1枚の時は注文番号をファイル名につける.
     *
     * @return string ファイル名
     */
    public function getPdfFileName(): string
    {
        if (!is_null($this->downloadFileName)) {
            return $this->downloadFileName;
        }
        $this->downloadFileName = self::DEFAULT_PDF_FILE_NAME;
        if ($this->pdfWriter->getPageCount() == 1) {
            $this->downloadFileName = 'nouhinsyo-No'.$this->lastOrderId.'.pdf';
        }

        return $this->downloadFileName;
    }

    /**
     * 描画器を組み立てる.
     *
     * 差し替え点として protected にしてある（プラグインが独自の描画器を返せる）.
     */
    protected function createPdfWriter(): PdfWriter
    {
        return new PdfWriter();
    }

    /**
     * 作成するPDFのテンプレートファイルを指定する.
     */
    protected function addPdfPage(): void
    {
        $this->pdfWriter->addPage();
    }

    /**
     * PDFに店舗情報を設定する
     * ショップ名、ロゴ画像以外はdtb_helpに登録されたデータを使用する.
     */
    protected function renderShopData(): void
    {
        // 基準座標を設定する
        $this->setBasePosition();

        // 店舗情報は基準の x=125 から、上から順に「表示トグルが ON かつ 値が非空」の行だけを詰めて描画する。
        // 非表示・空値の行は座標を空けず後続の行が繰り上がる（#6197）。
        // 表示/非表示は基本設定の order_pdf_visible_* トグルで制御する。
        // プロパティ名に反して $baseInfoRepository の実体は BaseInfo エンティティ（コンストラクタで get() 済み）
        $BaseInfo = $this->baseInfoRepository;
        $x = 125;
        $lineHeight = self::SHOP_INFO_LINE_HEIGHT;
        $y = self::SHOP_INFO_FIRST_Y;

        // 店名（太字）
        if ($BaseInfo->isOrderPdfVisibleShopName() && !empty($BaseInfo->getShopName())) {
            $this->lfText($x, $y, $BaseInfo->getShopName(), 8, 'B');
            $y += $lineHeight;
        }

        // 店名（英語表記）
        if ($BaseInfo->isOrderPdfVisibleShopNameEng() && !empty($BaseInfo->getShopNameEng())) {
            $this->lfText($x, $y, $BaseInfo->getShopNameEng(), 8);
            $y += $lineHeight;
        }

        // 郵便番号・住所（〒 / 都道府県+addr01 / addr02 の最大3行を1トグルで制御。各行は空ならスキップ）
        if ($BaseInfo->isOrderPdfVisibleAddress()) {
            $postalCode = $BaseInfo->getPostalCode();
            if (!empty($postalCode)) {
                // 郵便マーク(〒)分だけ左に寄せる
                $this->lfText($x - 4, $y, "\u{3012}".' '.mb_substr($postalCode, 0, 3).' - '.mb_substr($postalCode, 3, 4), 8);
                $y += $lineHeight;
            }
            $address1 = $BaseInfo->getPref().$BaseInfo->getAddr01();
            if (!empty($address1)) {
                $this->lfText($x, $y, $address1, 8);
                $y += $lineHeight;
            }
            if (!empty($BaseInfo->getAddr02())) {
                $this->lfText($x, $y, $BaseInfo->getAddr02(), 8);
                $y += $lineHeight;
            }
        }

        // 会社名
        if ($BaseInfo->isOrderPdfVisibleCompanyName() && !empty($BaseInfo->getCompanyName())) {
            $this->lfText($x, $y, $BaseInfo->getCompanyName(), 8);
            $y += $lineHeight;
        }

        // 電話番号
        if ($BaseInfo->isOrderPdfVisiblePhoneNumber() && !empty($BaseInfo->getPhoneNumber())) {
            $this->lfText($x, $y, 'TEL: '.$BaseInfo->getPhoneNumber(), 8);
            $y += $lineHeight;
        }

        // 店舗営業時間
        if ($BaseInfo->isOrderPdfVisibleBusinessHour() && !empty($BaseInfo->getBusinessHour())) {
            $this->lfText($x, $y, $BaseInfo->getBusinessHour(), 8);
            $y += $lineHeight;
        }

        // メールアドレス
        if ($BaseInfo->isOrderPdfVisibleEmail() && strlen((string) $BaseInfo->getEmail01()) > 0) {
            $this->lfText($x, $y, 'Email: '.$BaseInfo->getEmail01(), 8);
            $y += $lineHeight;
        }

        // インボイス登録番号
        if ($BaseInfo->isOrderPdfVisibleInvoiceNumber() && !empty($BaseInfo->getInvoiceRegistrationNumber())) {
            $this->lfText($x, $y, '登録番号: '.$BaseInfo->getInvoiceRegistrationNumber(), 8);
            $y += $lineHeight;
        }

        // ロゴは店舗情報の描画後に重ねる（PDFは後から描いた要素が上になるため、元の重なり順を維持する）
        $this->renderLogo();
    }

    /**
     * ロゴ画像を描画する.
     */
    protected function renderLogo(): void
    {
        // user_dataにlogo.pngが配置されている場合は優先的に読み込む
        $logoFile = $this->eccubeConfig->get('eccube_html_dir').'/user_data/assets/pdf/logo.png';

        if (!file_exists($logoFile)) {
            $logoFile = $this->eccubeConfig->get('eccube_html_admin_dir').'/assets/pdf/logo.png';
        }

        $this->pdfWriter->image($logoFile, self::LOGO_X, self::LOGO_Y, self::LOGO_WIDTH);
    }

    /**
     * メッセージを設定する.
     *
     * @param array<string, string> $formData
     */
    protected function renderMessageData(array $formData): void
    {
        $this->lfText(27, 70, $formData['message1'], 8); // メッセージ1
        $this->lfText(27, 74, $formData['message2'], 8); // メッセージ2
        $this->lfText(27, 78, $formData['message3'], 8); // メッセージ3
    }

    /**
     * PDFに備考を設定数.
     *
     * @param array<string, string|\DateTime> $formData
     */
    protected function renderEtcData(array $formData): void
    {
        // フォント情報のバックアップ
        $this->backupFont();

        $this->pdfWriter->cell(0, 10, '', 0, 1, 'C');

        // 行頭近くの場合、表示崩れがあるためもう一個字下げする
        if (270 <= $this->pdfWriter->getY()) {
            $this->pdfWriter->cell(0, 10, '', 0, 1, 'C');
        }
        $this->pdfWriter->setFont(self::FONT_GOTHIC, 'B', 9);
        $this->pdfWriter->multiCell(0, 6, '＜ 備考 ＞', 'T', 'L', false, 0);

        $this->pdfWriter->setFont(self::FONT_SJIS, '', 8);

        $this->pdfWriter->newLine();
        // rtrimを行う
        $text = preg_replace('/\s+$/us', '', $formData['note1']."\n".$formData['note2']."\n".$formData['note3']);
        $this->pdfWriter->multiCell(0, 4, (string) $text, '', 'L', false, 0);

        // フォント情報の復元
        $this->restoreFont();
    }

    /**
     * タイトルをPDFに描画する.
     */
    protected function renderTitle(string $title): void
    {
        // 基準座標を設定する
        $this->setBasePosition();

        // フォント情報のバックアップ
        $this->backupFont();

        // 文書タイトル（納品書・請求書）
        $this->pdfWriter->setFont(self::FONT_GOTHIC, '', 15);
        $this->pdfWriter->cell(0, 10, $title, 0, 2, 'C');
        $this->pdfWriter->cell(0, 66, '', 0, 2, 'R');
        $this->pdfWriter->cell(5, 0, '', 0, 0, 'R');

        // フォント情報の復元
        $this->restoreFont();
    }

    /**
     * 購入者情報を設定する.
     */
    protected function renderOrderData(Shipping $Shipping): void
    {
        // 基準座標を設定する
        $this->setBasePosition();

        // フォント情報のバックアップ
        $this->backupFont();

        // =========================================
        // 購入者情報部
        // =========================================

        $Order = $Shipping->getOrder();

        // 購入者郵便番号(3012は郵便マークのUTFコード)
        $postalCode = $Shipping->getPostalCode();
        if (!empty($postalCode)) {
            $text = "\u{3012}".' '.mb_substr($postalCode, 0, 3).' - '.mb_substr($postalCode, 3, 4);
            $this->lfText(22, 43, $text, 10);
        }

        // 購入者都道府県+住所1
        $text = $Shipping->getPref().$Shipping->getAddr01();
        $this->lfText(27, 47, $text, 10);
        $this->lfText(27, 51, $Shipping->getAddr02(), 10); // 購入者住所2

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
        $this->pdfWriter->setFont(self::FONT_SJIS, '', 10);

        // ご注文日
        $orderDate = $Order->getCreateDate()->format('Y/m/d H:i');
        if ($Order->getOrderDate()) {
            $orderDate = $Order->getOrderDate()->format('Y/m/d H:i');
        }

        $this->lfText(25, 125, $orderDate, 10);
        // 注文番号
        $this->lfText(25, 135, $Order->getOrderNo(), 10);

        // 総合計金額
        if (!$Order->isMultiple()) {
            $this->pdfWriter->setFont(self::FONT_SJIS, 'B', 15);
            $paymentTotalText = $this->eccubeExtension->getPriceFilter($Order->getPaymentTotal());

            $this->setBasePosition(120, self::PAYMENT_TOTAL_BASE_Y);
            $this->pdfWriter->cell(5, 7, '', 0, 0, '');
            $this->pdfWriter->cell(67, 8, $paymentTotalText, 0, 2, 'R');
            $this->pdfWriter->cell(0, 45, '', 0, 2, '');
        }

        // フォント情報の復元
        $this->restoreFont();
    }

    /**
     * 購入商品詳細情報を設定する.
     */
    protected function renderOrderDetailData(Shipping $Shipping): void
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
            $arrOrder[$i][1] = number_format((float) $OrderItem->getQuantity());
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

        // インボイス対応
        $this->backupFont();
        $this->pdfWriter->setLineWidth(.3);
        $this->pdfWriter->setFont(self::FONT_SJIS, '', 6);

        $this->pdfWriter->cell(0, 0, '', 0, 1, 'C');
        // 行頭近くの場合、表示崩れがあるためもう一個字下げする
        if (270 <= $this->pdfWriter->getY()) {
            $this->pdfWriter->cell(0, 0, '', 0, 1, 'C');
        }
        $width = array_reduce($this->widthCell, fn (float $n, float $w) => $n + $w, 0.0);
        $this->pdfWriter->setX(20);
        $message = '';
        foreach ($Order->getTotalByTaxRate() as $rate => $total) {
            $message .= '('.$rate.'%対象: ';
            $message .= $this->eccubeExtension->getPriceFilter($total);
            $message .= ' 内消費税: '.$this->eccubeExtension->getPriceFilter($Order->getTaxByTaxRate()[$rate]).')'.PHP_EOL;
        }
        $this->pdfWriter->multiCell($width, 4, $message, 0, 'R', false, 1);

        $this->restoreFont();
    }

    /**
     * PDFへのテキスト書き込み
     *
     * @param int $x X座標
     * @param int $y Y座標
     * @param string|null $text テキスト
     * @param int $size フォントサイズ
     * @param string $style フォントスタイル
     */
    protected function lfText(int $x, int $y, ?string $text, int $size = 0, string $style = ''): void
    {
        // 退避
        $bakFontStyle = $this->pdfWriter->getFontStyle();
        $bakFontSize = $this->pdfWriter->getFontSizePt();

        $this->pdfWriter->setFont(null, $style, $size);
        $this->pdfWriter->text($x + $this->baseOffsetX, $y + $this->baseOffsetY, $text ?? '');

        // 復元
        $this->pdfWriter->setFont(null, $bakFontStyle, $bakFontSize);
    }

    /**
     * Colored table.
     *
     * @param array<int, string> $header 出力するラベル名一覧
     * @param array<int, array<int, string>> $data 出力するデータ
     * @param array<int, float|int> $w 出力するセル幅一覧
     */
    protected function setFancyTable(array $header, array $data, array $w): void
    {
        // フォント情報のバックアップ
        $this->backupFont();

        // 開始座標の設定
        $this->setBasePosition(0, 149);

        // Colors, line width and bold font
        $this->pdfWriter->setFillColor(216, 216, 216);
        $this->pdfWriter->setTextColor(0, 0, 0);
        $this->pdfWriter->setDrawColor(0, 0, 0);
        $this->pdfWriter->setLineWidth(.3);
        $this->pdfWriter->setFont(self::FONT_SJIS, 'B', 8);

        // Header
        $this->pdfWriter->cell(5, 7, '', 0, 0, '');
        $count = count($header);
        for ($i = 0; $i < $count; ++$i) {
            $this->pdfWriter->cell((float) $w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->pdfWriter->newLine();

        // Color and font restoration
        $this->pdfWriter->setFillColor(235, 235, 235);
        $this->pdfWriter->setTextColor(0, 0, 0);
        $this->pdfWriter->setFont(null, '');
        // Data
        $fill = false;
        $writeRow = function (array $row, float $cellHeight, bool $fill, bool $isBorder) use ($w): float {
            $i = 0;
            $h = 0.0;
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

                $this->pdfWriter->multiCell(
                    (float) $w[$i], // セル幅
                    $cellHeight, // セルの最小の高さ
                    !$isBorder ? $col : '', // 文字列
                    $isBorder ? 1 : 0, // 境界線の描画方法を指定
                    $align, // テキストの整列
                    $fill, // 背景の塗つぶし指定
                    $ln // 出力後のカーソルの移動方法
                );
                $h = $this->pdfWriter->getLastCellHeight();
                $i++;
            }

            return $cellHeight;
        };

        foreach ($data as $row) {
            // 行の処理
            $h = 4.0;
            $this->pdfWriter->cell(5, $h, '', 0, 0, '');
            if ((277 - $this->pdfWriter->getY()) < ($h * 4)) {
                $this->pdfWriter->checkPageBreak($this->pdfWriter->getPageBreakTrigger() + 1);
            }

            $x = $this->pdfWriter->getX();
            $y = $this->pdfWriter->getY();
            // 1度目は文字だけ出力し、行の高さ最大を取得
            $h = $writeRow($row, $h, $fill, false);
            $this->pdfWriter->setXY($x, $y);
            // 2度目に最大の高さに合わせて、境界線を描画
            $writeRow($row, $h, $fill, true);

            $fill = !$fill;
        }
        $h = 4.0;
        $this->pdfWriter->cell(5, $h, '', 0, 0, '');
        $this->pdfWriter->cell((float) array_sum($w), 0, '', 'T');
        $this->pdfWriter->setFillColor(255, 255, 255);

        // フォント情報の復元
        $this->restoreFont();
    }

    /**
     * 基準座標を設定する.
     *
     * 注意: y の設定は x を左余白へ戻す。よって引数の $x は結果に残らない
     * （4.3 までの TCPDF でも同じ挙動で、この順序に依存した座標で組まれている）。
     */
    protected function setBasePosition(int|float|null $x = null, int|float|null $y = null): void
    {
        // 現在のマージンを取得する
        $result = $this->pdfWriter->getMargins();

        // 基準座標を指定する
        $this->pdfWriter->setX((float) ($x ?? $result['left']));
        $this->pdfWriter->setY((float) ($y ?? $result['top']));
    }

    /**
     * Font情報のバックアップ.
     */
    protected function backupFont(): void
    {
        // フォント情報のバックアップ
        $this->bakFontFamily = $this->pdfWriter->getFontFamily();
        $this->bakFontStyle = $this->pdfWriter->getFontStyle();
        $this->bakFontSize = $this->pdfWriter->getFontSizePt();
    }

    /**
     * Font情報の復元.
     */
    protected function restoreFont(): void
    {
        $this->pdfWriter->setFont($this->bakFontFamily, $this->bakFontStyle, $this->bakFontSize);
    }
}
