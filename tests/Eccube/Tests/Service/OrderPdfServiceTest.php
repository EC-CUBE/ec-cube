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
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Eccube\Service\OrderPdfService;
use Eccube\Service\TaxRuleService;
use Eccube\Twig\Extension\EccubeExtension;
use Eccube\Twig\Extension\TaxExtension;

/**
 * 納品書PDFの店舗情報欄の描画座標を検証する (#6197).
 *
 * PDF のバイナリからは座標を検証できないため, TCPDF へ描画する直前の座標を
 * {@see OrderPdfLayoutProbe} で記録して検証する.
 */
final class OrderPdfServiceTest extends AbstractServiceTestCase
{
    /**
     * 出力項目トグルを全て ON にした状態（=行数が最大）でも, 店舗情報欄が
     * ロゴにも総合計金額欄にも重ならないこと.
     */
    public function testShopDataFitsBetweenLogoAndPaymentTotal(): void
    {
        $probe = $this->createProbe();
        $probe->baseInfoRepository = $this->createBaseInfoWithAllItemsVisible();

        $probe->renderShopDataForTest();

        // 店名 / 店名（英語表記）/ 〒 / 住所1 / 住所2 / 会社名 / 電話番号 / 営業時間 / Email / 登録番号
        $this->assertCount(10, $probe->lineYs, '全項目 ON では 10 行描画されるはず');

        $this->assertGreaterThanOrEqual(
            $this->getLogoBottomY(),
            min($probe->lineYs),
            '店舗情報欄の先頭行がロゴの下端より上にある（ロゴと重なる）'
        );

        $this->assertLessThan(
            OrderPdfService::PAYMENT_TOTAL_BASE_Y,
            max($probe->lineYs),
            '店舗情報欄の最終行が総合計金額の描画基準位置に達している（合計金額欄を侵食する）'
        );
    }

    /**
     * 行送りが {@see OrderPdfService::SHOP_INFO_LINE_HEIGHT} で均等であること.
     */
    public function testShopDataLineHeightIsEven(): void
    {
        $probe = $this->createProbe();
        $probe->baseInfoRepository = $this->createBaseInfoWithAllItemsVisible();

        $probe->renderShopDataForTest();

        $lineYs = $probe->lineYs;
        for ($i = 1, $count = count($lineYs); $i < $count; ++$i) {
            $this->assertEqualsWithDelta(
                OrderPdfService::SHOP_INFO_LINE_HEIGHT,
                $lineYs[$i] - $lineYs[$i - 1],
                0.001,
                sprintf('%d 行目の行送りが %smm ではない', $i + 1, OrderPdfService::SHOP_INFO_LINE_HEIGHT)
            );
        }
    }

    /**
     * トグルが OFF の項目・値が空の項目は座標を空けず, 後続の行が繰り上がること.
     */
    public function testShopDataSkipsInvisibleAndEmptyItems(): void
    {
        $probe = $this->createProbe();
        $BaseInfo = $this->createBaseInfoWithAllItemsVisible();
        // 店名（英語表記）はトグル OFF, 会社名は値が空
        $BaseInfo->setOrderPdfVisibleShopNameEng(false)->setCompanyName();
        $probe->baseInfoRepository = $BaseInfo;

        $probe->renderShopDataForTest();

        $this->assertCount(8, $probe->lineYs, 'OFF・空値の 2 行が詰められるはず');
        $this->assertEqualsWithDelta(
            OrderPdfService::SHOP_INFO_LINE_HEIGHT,
            $probe->lineYs[1] - $probe->lineYs[0],
            0.001,
            '詰めた分だけ後続行が繰り上がるはず'
        );
    }

    private function createProbe(): OrderPdfLayoutProbe
    {
        $container = static::getContainer();

        return new OrderPdfLayoutProbe(
            $container->get(EccubeConfig::class),
            $this->entityManager->getRepository(Order::class),
            $this->entityManager->getRepository(Shipping::class),
            $container->get(TaxRuleService::class),
            $this->entityManager->getRepository(BaseInfo::class),
            $container->get(EccubeExtension::class),
            $container->get(TaxExtension::class)
        );
    }

    /**
     * 全ての出力項目トグルを ON にし, 全項目へ値を入れた BaseInfo を組み立てる.
     *
     * dtb_base_info を書き換えると後続テストへ影響するため, 永続化しない実体を使う.
     */
    private function createBaseInfoWithAllItemsVisible(): BaseInfo
    {
        $BaseInfo = new BaseInfo();
        $BaseInfo->setShopName('テスト店舗')
            ->setShopNameEng('Test Shop')
            ->setPostalCode('5300001')
            ->setAddr01('大阪市北区梅田2-4-9')
            ->setAddr02('ブリーゼタワー13F')
            ->setCompanyName('テスト株式会社')
            ->setPhoneNumber('0000-0000-0000')
            ->setBusinessHour('10:00-19:00')
            ->setEmail01('test@example.com')
            ->setInvoiceRegistrationNumber('T1234567890123')
            ->setOrderPdfVisibleShopName(true)
            ->setOrderPdfVisibleShopNameEng(true)
            ->setOrderPdfVisibleAddress(true)
            ->setOrderPdfVisibleCompanyName(true)
            ->setOrderPdfVisiblePhoneNumber(true)
            ->setOrderPdfVisibleBusinessHour(true)
            ->setOrderPdfVisibleEmail(true)
            ->setOrderPdfVisibleInvoiceNumber(true);

        return $BaseInfo;
    }

    /**
     * ロゴ画像の下端 y 座標(mm)を実寸から求める.
     *
     * ロゴは幅のみ指定して描画するため, 高さは画像の縦横比で決まる.
     */
    private function getLogoBottomY(): float
    {
        $eccubeConfig = static::getContainer()->get(EccubeConfig::class);
        $logoFile = $eccubeConfig->get('eccube_html_dir').'/user_data/assets/pdf/logo.png';
        if (!file_exists($logoFile)) {
            $logoFile = $eccubeConfig->get('eccube_html_admin_dir').'/assets/pdf/logo.png';
        }

        $imageSize = getimagesize($logoFile);
        $this->assertNotFalse($imageSize, 'ロゴ画像を読み込めなかった: '.$logoFile);

        return OrderPdfService::LOGO_Y + OrderPdfService::LOGO_WIDTH * $imageSize[1] / $imageSize[0];
    }
}

/**
 * 店舗情報欄の描画座標を記録するテスト用サブクラス.
 *
 * TCPDF のページ生成を行わずに座標だけを取り出すため, 描画系のメソッドを差し替える.
 */
final class OrderPdfLayoutProbe extends OrderPdfService
{
    /**
     * lfText() が紙面へ描画する y 座標(mm)の記録.
     *
     * @var array<int, float>
     */
    public array $lineYs = [];

    public function renderShopDataForTest(): void
    {
        $this->lineYs = [];
        $this->renderShopData();
    }

    #[\Override]
    protected function lfText(int|float $x, int|float $y, ?string $text, int $size = 0, string $style = ''): void
    {
        // lfText() は baseOffsetY 分ずらして描画するため, 紙面上の位置へ換算して記録する
        $this->lineYs[] = (float) $y + $this->baseOffsetY;
    }

    #[\Override]
    protected function setBasePosition(int|float|null $x = null, int|float|null $y = null): void
    {
        // ページが無い状態ではカーソル移動は不要
    }

    #[\Override]
    public function Image($file, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border = 0, $fitbox = false, $hidden = false, $fitonpage = false, $alt = false, $altimgs = [])
    {
        // ページが無い状態では画像を描画できない
    }
}
