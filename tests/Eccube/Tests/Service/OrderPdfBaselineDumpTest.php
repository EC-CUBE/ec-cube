<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Service;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Service\OrderPdfService;
use Eccube\Service\TaxRuleService;
use Eccube\Twig\Extension\EccubeExtension;
use Eccube\Twig\Extension\TaxExtension;

/**
 * 納品書 PDF の基準ファイルを出力する移行用ツール.
 *
 * PDF エンジン差し替えの前後で出力を機械比較するための基準を作る。
 * 通常の CI では意味を持たないため, 明示的に --filter で呼び出して使う。
 *
 * 使い方と比較手順は本クラスの各テストの docblock に記す（移行の経緯は #7083）。
 */
final class OrderPdfBaselineDumpTest extends AbstractServiceTestCase
{
    private const FIXED_DATE = '2026-01-01 00:00:00';

    /** 出力先. ORDER_PDF_DUMP_DIR で差し替えて移行前後を並べて比較する */
    private ?string $outDir = null;

    public function testDumpBaseline(): void
    {
        $outDir = getenv('ORDER_PDF_DUMP_DIR');
        if ($outDir === false || $outDir === '') {
            self::markTestSkipped('出力先が未指定. ORDER_PDF_DUMP_DIR を指定したときだけ実行する');
        }
        $this->outDir = $outDir;

        if (!is_dir($this->outDir) && !mkdir($this->outDir, 0777, true) && !is_dir($this->outDir)) {
            self::fail(sprintf('出力先を作成できない: %s (コンテナは www-data で動くため書き込み権限を確認する)', $this->outDir));
        }

        $written = [];
        foreach ($this->patterns() as $name => $spec) {
            $written[$name] = $this->dumpOne($name, $spec);
        }

        $this->assertCount(count($this->patterns()), $written, '全パターンが生成されていること');
        foreach ($written as $name => $size) {
            $this->assertGreaterThan(1000, $size, "$name のサイズが小さすぎる");
        }
    }

    /**
     * 出力パターン定義.
     *
     * 描画コードの分岐を一通り通すために選んである。網羅の基準は次の 4 つ:
     *
     * 1. 店舗情報の表示・非表示（`BaseInfo` のフラグと値の有無の組み合わせ）
     * 2. 明細の形（規格の段数・軽減税率・送料/手数料の明細・複数出荷・改ページ）
     * 3. 折り返しと字幅（長い商品名・欧文タイトル・Latin-1・備考の最大長）
     * 4. 外部ファイル（user_data のロゴを読むか, 管理画面既定へ落ちるか）
     *
     * @return array<string, array<string, mixed>>
     */
    private function patterns(): array
    {
        return [
            '01-all-visible' => ['baseInfo' => 'allVisible'],
            '02-minimal' => ['baseInfo' => 'allHidden', 'products' => 1, 'feeItems' => false, 'class' => 'none', 'productCode' => false],
            '03-visible-but-empty' => ['baseInfo' => 'visibleButEmpty'],
            '04-address-partial' => ['baseInfo' => 'addressPartial'],
            // 01 と同条件にならないよう, 規格 2 段 + 商品コード無しにする
            '05-class-two' => ['baseInfo' => 'allVisible', 'class' => 'two', 'productCode' => false],
            '06-class-one' => ['baseInfo' => 'allVisible', 'class' => 'one', 'productCode' => false],
            '07-reduced-tax' => ['baseInfo' => 'allVisible', 'reducedTax' => true],
            '08-fee-items' => ['baseInfo' => 'allVisible', 'feeItems' => true],
            '09-pagebreak' => ['baseInfo' => 'allVisible', 'products' => 40],
            '10-multiple-shipping' => ['baseInfo' => 'allVisible', 'products' => 2, 'multiple' => true],
            '11-long-product-name' => ['baseInfo' => 'allVisible', 'longName' => true],
            '12-user-logo' => ['baseInfo' => 'allVisible', 'userLogo' => false],
            // 13-15: エンジン移行で差が出やすい条件。タイトルは中央寄せなので字幅がそのまま位置に出る
            '13-title-en' => ['baseInfo' => 'allVisible', 'title' => 'Delivery Slip'],
            '14-title-ascii-short' => ['baseInfo' => 'allVisible', 'title' => 'INVOICE'],
            // 欧文字幅表(U+0020..U+007E)の外側。折り返しと整列に差が出るか
            '15-latin1' => ['baseInfo' => 'allVisible', 'latin1' => true],
            // 16-17: 備考が最大長 (eccube_stext_len 255 x 3 行) のとき紙面からあふれないか。
            // 表が下端まで伸びるほど備考の開始位置が下がるので, 明細数を変えて 2 通り見る
            '16-long-note' => ['baseInfo' => 'allVisible', 'longNote' => true],
            '17-long-note-tall-table' => ['baseInfo' => 'allVisible', 'products' => 12, 'longNote' => true],
        ];
    }

    /**
     * @param array<string, mixed> $spec
     *
     * @return int 書き出したバイト数
     */
    private function dumpOne(string $name, array $spec): int
    {
        $restore = ($spec['userLogo'] ?? true) ? null : $this->hideUserLogo();

        try {
            $Shipping = $this->buildShipping($name, $spec);
            $pdf = $this->makePdf(
                $Shipping,
                $this->baseInfo((string) ($spec['baseInfo'] ?? 'allVisible')),
                (string) ($spec['title'] ?? '納品書'),
                (bool) ($spec['longNote'] ?? false)
            );
        } finally {
            if ($restore !== null) {
                $restore();
            }
        }

        $this->assertStringStartsWith('%PDF', $pdf, "$name が PDF になっていない");

        $path = $this->outDir."/$name.pdf";
        $this->assertNotFalse(file_put_contents($path, $pdf), "書き込めない: $path");
        $this->assertFileExists($path);
        $this->assertSame(strlen($pdf), filesize($path));

        return strlen($pdf);
    }

    /**
     * user_data のロゴを一時的に退避し, 管理画面既定へフォールバックさせる.
     *
     * @return callable(): void 復元処理
     */
    /**
     * user_data のロゴを判別可能な画像へ差し替え, 後始末の関数を返す.
     *
     * 読み込み先は `eccube_html_dir` から組み立てられる固定パスで差し替えられないため,
     * 実ファイルを退避して上書きする。復元は finally で行うが, プロセスが強制終了すると
     * `.baseline-bak` が残るので, 次回の呼び出し時に戻してから始める。
     */
    private function hideUserLogo(): callable
    {
        $config = static::getContainer()->get(EccubeConfig::class);
        $logo = $config->get('eccube_html_dir').'/user_data/assets/pdf/logo.png';
        $backup = $logo.'.baseline-bak';

        // 前回が強制終了していると退避したまま残る。先に戻してから始める
        if (file_exists($backup)) {
            @unlink($logo);
            rename($backup, $logo);
        }

        if (!file_exists($logo)) {
            return static function (): void {};
        }
        rename($logo, $backup);

        // user_data と管理画面既定のロゴは既定で同一ファイルのため, 退避しただけでは
        // 出力が変わらずフォールバック経路を検証できない. 判別可能な別画像を置く.
        //
        // 寸法は実物と同じ 301x38 にすること. Image() は幅のみ指定で高さを縦横比から決めるため,
        // 比率が違うとロゴが縦に伸びて店舗情報の行（Y=54mm 始まり）に重なり,
        // 「どちらのファイルが読まれたか」ではなくレイアウト差を見てしまう.
        [$w, $h] = [301, 38];
        $distinct = imagecreatetruecolor($w, $h);
        imagefilledrectangle($distinct, 0, 0, $w - 1, $h - 1, imagecolorallocate($distinct, 0, 102, 204));
        imagestring($distinct, 5, 8, 12, 'USER LOGO', imagecolorallocate($distinct, 255, 255, 255));
        imagepng($distinct, $logo);
        imagedestroy($distinct);

        return static function () use ($logo, $backup): void {
            @unlink($logo);
            if (file_exists($backup)) {
                rename($backup, $logo);
            }
        };
    }

    /**
     * @param array<string, mixed> $spec
     */
    private function buildShipping(string $name, array $spec): Shipping
    {
        $productCount = (int) ($spec['products'] ?? 3);

        $Customer = $this->createCustomer(sprintf('baseline-%s@example.com', $name));
        $ProductClasses = [];
        while (count($ProductClasses) < $productCount) {
            $Product = $this->createProduct('サンプル商品', min(3, $productCount - count($ProductClasses)));
            foreach ($Product->getProductClasses() as $ProductClass) {
                $ProductClasses[] = $ProductClass;
            }
        }
        $ProductClasses = array_slice($ProductClasses, 0, $productCount);

        $Order = $this->createOrderWithProductClasses($Customer, $ProductClasses);
        $this->normalize($Order, $spec);

        if ($spec['multiple'] ?? false) {
            $this->splitIntoTwoShippings($Order);
        }

        $this->entityManager->flush();

        $Shipping = $Order->getShippings()->first();
        $this->assertInstanceOf(Shipping::class, $Shipping, "$name の Shipping が取得できない");

        return $Shipping;
    }

    /**
     * 実行日・乱数に依存しない値へ固定し, spec に従って明細を整形する.
     *
     * フィクスチャは Faker で都道府県・価格などを毎回変えるため, PDF に描画される値を
     * すべて固定しないと基準として使えない（同一コードでも指紋が変わる）。
     *
     * @param array<string, mixed> $spec
     */
    private function normalize(Order $Order, array $spec): void
    {
        $Pref = $this->entityManager->getRepository(Pref::class)->find(27); // 大阪府
        $fixedDate = new \DateTime(self::FIXED_DATE);

        $Order->setOrderNo('BASELINE-0001')
            ->setName01('納品')->setName02('太郎')
            ->setKana01('ノウヒン')->setKana02('タロウ')
            ->setCompanyName('株式会社サンプル')
            ->setPostalCode('5300001')
            ->setPref($Pref)
            ->setAddr01('大阪市北区梅田2-4-9')
            ->setAddr02('ブリーゼタワー13F')
            ->setPhoneNumber('0612345678')
            ->setOrderDate($fixedDate)
            ->setCreateDate($fixedDate);

        foreach ($Order->getShippings() as $Shipping) {
            $Shipping->setName01('納品')->setName02('太郎')
                ->setKana01('ノウヒン')->setKana02('タロウ')
                ->setCompanyName('株式会社サンプル')
                ->setPostalCode('5300001')
                ->setPref($Pref)
                ->setAddr01('大阪市北区梅田2-4-9')
                ->setAddr02('ブリーゼタワー13F')
                ->setPhoneNumber('0612345678');
        }

        // 送料・手数料・値引きの明細を落とすか
        if (($spec['feeItems'] ?? false) === false) {
            foreach ($Order->getOrderItems() as $OrderItem) {
                if (!$OrderItem->isProduct()) {
                    $Order->removeOrderItem($OrderItem);
                    $this->entityManager->remove($OrderItem);
                }
            }
        }

        $class = (string) ($spec['class'] ?? 'two');
        $withCode = (bool) ($spec['productCode'] ?? true);
        $longName = (bool) ($spec['longName'] ?? false);
        $latin1 = (bool) ($spec['latin1'] ?? false);
        $reduced = (bool) ($spec['reducedTax'] ?? false);

        $i = 0;
        $subtotal = '0';
        foreach ($Order->getOrderItems() as $OrderItem) {
            ++$i;
            $price = 1000 * $i;
            $quantity = ($i % 3) + 1;

            $productName = match (true) {
                $longName => sprintf('非常に長い商品名のサンプル%02d 折り返し確認用のダミーテキストをここに詰めています', $i),
                // 欧文字幅表(U+0020..U+007E)の外側。TCPDF は cw を持つが本実装では DW=1000 になる
                $latin1 => sprintf('Café Crème Brûlée Größe ×%d ½ Ø £ ¥ § © ® ± µ ¿ Æ Þ ß æ ÷ ø ÿ', $i),
                default => sprintf('サンプル商品%02d', $i),
            };

            $OrderItem->setProductName($productName)
                ->setProductCode($withCode ? sprintf('CODE-%03d', $i) : null)
                ->setPrice((string) $price)
                ->setQuantity((string) $quantity)
                // getTotalPrice() は getPriceIncTax()×数量 で, 税表示区分が税込でなければ
                // price + tax になる. tax を固定しないとフィクスチャの乱数が残る.
                ->setTax('0')
                // 既定の TaxRule は 10%. 8% にすると軽減税率対象と判定される.
                ->setTaxRate($reduced && $OrderItem->isProduct() ? '8' : '10');

            $this->applyClassCategory($OrderItem, $class, $i);

            $subtotal = bcadd($subtotal, bcmul((string) $price, (string) $quantity, 2), 2);
        }

        // 複数配送では商品以外の明細（送料・手数料・値引き）も描画される。
        // これらは Order のコレクションから外しても Shipping 側には残るため,
        // 値を固定しないとフィクスチャの乱数が出力に出る。
        foreach ($Order->getShippings() as $Shipping) {
            foreach ($Shipping->getOrderItems() as $OrderItem) {
                if (!$OrderItem instanceof OrderItem || $OrderItem->isProduct()) {
                    continue;
                }
                $OrderItem->setPrice('0')->setQuantity('1')->setTax('0')->setTaxRate('10');
            }
        }

        $Order->setSubtotal($subtotal)
            ->setDiscount('0')
            ->setDeliveryFeeTotal('0')
            ->setCharge('0')
            ->setTax('0')
            ->setTotal($subtotal)
            ->setPaymentTotal($subtotal);
    }

    private function applyClassCategory(OrderItem $OrderItem, string $class, int $i): void
    {
        match ($class) {
            'two' => $OrderItem->setClassCategoryName1(sprintf('サイズ%d', $i))
                ->setClassCategoryName2(sprintf('カラー%d', $i)),
            'one' => $OrderItem->setClassCategoryName1(sprintf('サイズ%d', $i))
                ->setClassCategoryName2(''),
            default => $OrderItem->setClassCategoryName1()->setClassCategoryName2(),
        };
    }

    /**
     * 明細を 2 つの Shipping へ分け, $Order->isMultiple() を真にする.
     */
    private function splitIntoTwoShippings(Order $Order): void
    {
        $First = $Order->getShippings()->first();
        $this->assertInstanceOf(Shipping::class, $First);

        $Second = clone $First;
        $Second->setName01('納品')->setName02('次郎');
        $this->entityManager->persist($Second);
        $Order->addShipping($Second);

        $items = array_values($Order->getOrderItems()->toArray());
        // 末尾の明細だけ 2 つ目の Shipping へ移す
        $Last = end($items);
        if ($Last instanceof OrderItem) {
            $Last->setShipping($Second);
        }
    }

    private function makePdf(Shipping $Shipping, BaseInfo $BaseInfo, string $title = '納品書', bool $longNote = false): string
    {
        $container = static::getContainer();

        $service = new OrderPdfService(
            $container->get(EccubeConfig::class),
            $this->entityManager->getRepository(Order::class),
            $this->entityManager->getRepository(Shipping::class),
            $container->get(TaxRuleService::class),
            $this->entityManager->getRepository(BaseInfo::class),
            $container->get(EccubeExtension::class),
            $container->get(TaxExtension::class)
        );
        // プロパティ名に反して実体は BaseInfo エンティティ. dtb_base_info を書き換えず差し替える.
        $service->baseInfoRepository = $BaseInfo;

        $service->makePdf([
            'ids' => (string) $Shipping->getId(),
            'issue_date' => new \DateTime(self::FIXED_DATE),
            'title' => $title,
            'message1' => 'この度はお買い上げいただきありがとうございます。',
            'message2' => '内容にご不明な点がございましたらご連絡ください。',
            'message3' => '今後ともよろしくお願いいたします。',
            'note1' => $longNote ? '1'.str_repeat('あ', 253).'X' : '備考1',
            'note2' => $longNote ? '2'.str_repeat('い', 253).'Y' : '備考2',
            'note3' => $longNote ? '3'.str_repeat('う', 253).'Z' : '備考3',
            'default' => false,
        ]);

        return $service->outputPdf();
    }

    private function baseInfo(string $kind): BaseInfo
    {
        $values = static fn (BaseInfo $b): BaseInfo => $b->setShopName('テスト店舗')
            ->setShopNameEng('Test Shop')
            ->setPostalCode('5300001')
            ->setAddr01('大阪市北区梅田2-4-9')
            ->setAddr02('ブリーゼタワー13F')
            ->setCompanyName('テスト株式会社')
            // PhoneNumberType は Assert\Type('digit') で数字のみ. ハイフン入りは UI から入力できない.
            ->setPhoneNumber('0612345678')
            ->setBusinessHour('10:00-19:00')
            ->setEmail01('test@example.com')
            ->setInvoiceRegistrationNumber('T1234567890123');

        $allFlags = static fn (BaseInfo $b, bool $on): BaseInfo => $b->setOrderPdfVisibleShopName($on)
            ->setOrderPdfVisibleShopNameEng($on)
            ->setOrderPdfVisibleAddress($on)
            ->setOrderPdfVisibleCompanyName($on)
            ->setOrderPdfVisiblePhoneNumber($on)
            ->setOrderPdfVisibleBusinessHour($on)
            ->setOrderPdfVisibleEmail($on)
            ->setOrderPdfVisibleInvoiceNumber($on);

        return match ($kind) {
            'allHidden' => $allFlags($values(new BaseInfo()), false),
            // フラグは ON だが値が空 = 「フラグ AND 値」の判定を検証する
            'visibleButEmpty' => $allFlags(new BaseInfo(), true),
            // 住所だけ部分欠け（郵便番号なし・addr02 なし）
            'addressPartial' => $allFlags($values(new BaseInfo())->setPostalCode()->setAddr02(), true),
            default => $allFlags($values(new BaseInfo()), true),
        };
    }
}
