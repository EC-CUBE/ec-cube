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

use Eccube\Entity\Product;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Service\ProductStructuredDataService;
use Eccube\Twig\Extension\EccubeExtension;
use Symfony\Component\Asset\Packages;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class ProductStructuredDataServiceTest extends AbstractServiceTestCase
{
    private ?ProductStructuredDataService $service = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = static::getContainer()->get(ProductStructuredDataService::class);
    }

    /**
     * @return array<string, mixed>
     */
    private function build(Product $Product): array
    {
        return $this->service->createProductJsonLd(
            $Product,
            'https://example.com',
            'https://example.com/products/detail/'.$Product->getId(),
            'JPY'
        );
    }

    public function testBaseStructure(): void
    {
        $Product = $this->createProduct('構造化データ商品', 0);

        $data = $this->build($Product);

        $this->assertSame('https://schema.org/', $data['@context']);
        $this->assertSame('Product', $data['@type']);
        $this->assertSame('構造化データ商品', $data['name']);
        $this->assertNotEmpty($data['image']);
        $this->assertStringStartsWith('https://example.com', $data['image'][0]);
    }

    public function testSinglePriceOfferWithoutStrikethrough(): void
    {
        $Product = $this->createProduct('単一価格商品', 0);
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setPrice01(null);
        $ProductClass->setPrice02('1000')->setPrice02IncTax('1100');

        $data = $this->build($Product);

        $this->assertSame('Offer', $data['offers']['@type']);
        $this->assertSame('JPY', $data['offers']['priceCurrency']);
        $this->assertSame('https://example.com/products/detail/'.$Product->getId(), $data['offers']['url']);
        $this->assertSame('NewCondition', $data['offers']['itemCondition']);
        $this->assertArrayNotHasKey('priceSpecification', $data['offers']);
    }

    public function testStrikethroughPriceWhenListPriceIsHigher(): void
    {
        $Product = $this->createProduct('セール商品', 0);
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setPrice01('2000')->setPrice01IncTax('2200'); // 通常価格
        $ProductClass->setPrice02('1000')->setPrice02IncTax('1100'); // 販売価格

        $data = $this->build($Product);

        $this->assertSame('Offer', $data['offers']['@type']);
        $this->assertArrayHasKey('priceSpecification', $data['offers']);
        $this->assertSame('UnitPriceSpecification', $data['offers']['priceSpecification']['@type']);
        $this->assertSame('StrikethroughPrice', $data['offers']['priceSpecification']['priceType']);
        // 販売価格 < 通常価格（いずれも税込）
        $this->assertLessThan((float) $data['offers']['priceSpecification']['price'], (float) $data['offers']['price']);
    }

    public function testNoStrikethroughWhenListPriceIsNotHigher(): void
    {
        $Product = $this->createProduct('通常商品', 0);
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setPrice01('500')->setPrice01IncTax('550'); // 通常価格 <= 販売価格
        $ProductClass->setPrice02('1000')->setPrice02IncTax('1100');

        $data = $this->build($Product);

        $this->assertArrayNotHasKey('priceSpecification', $data['offers']);
    }

    public function testAggregateOfferForPriceRange(): void
    {
        $Product = $this->createProduct('価格帯商品', 2);
        $visibleClasses = [];
        foreach ($Product->getProductClasses() as $ProductClass) {
            if ($ProductClass->isVisible()) {
                $visibleClasses[] = $ProductClass;
            }
        }
        $this->assertCount(2, $visibleClasses);
        $visibleClasses[0]->setPrice02('1000')->setPrice02IncTax('1100');
        $visibleClasses[1]->setPrice02('2000')->setPrice02IncTax('2200');

        $data = $this->build($Product);

        $this->assertSame('AggregateOffer', $data['offers']['@type']);
        $this->assertSame(2, $data['offers']['offerCount']);
        $this->assertLessThan((float) $data['offers']['highPrice'], (float) $data['offers']['lowPrice']);
        $this->assertArrayNotHasKey('price', $data['offers']);
    }

    public function testNoOffersWhenPriceIsUnavailable(): void
    {
        $Product = $this->createProduct('価格なし商品', 0);
        // 有効な規格を無くすと価格が算出できずに offers を出さない
        foreach ($Product->getProductClasses() as $ProductClass) {
            $ProductClass->setVisible(false);
        }

        $data = $this->build($Product);

        $this->assertArrayNotHasKey('offers', $data);
    }

    public function testJsonLdEventCanInjectAggregateRating(): void
    {
        $Product = $this->createProduct('レビュー注入商品', 0);

        // プラグインが JSON-LD 配列へ review 等を注入する拡張点を検証する
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(EccubeEvents::FRONT_PRODUCT_DETAIL_JSON_LD, function (EventArgs $event): void {
            $data = $event->getArgument('json_ld');
            $data['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => '4.5',
                'reviewCount' => '10',
            ];
            $event->setArgument('json_ld', $data);
        });
        $service = new ProductStructuredDataService($dispatcher, static::getContainer()->get(Packages::class));

        $data = $service->createProductJsonLd($Product, 'https://example.com', 'https://example.com/products/detail/1', 'JPY');

        $this->assertArrayHasKey('aggregateRating', $data);
        $this->assertSame('4.5', $data['aggregateRating']['ratingValue']);
    }

    public function testEncodeJsonLdEscapesClosingScriptTag(): void
    {
        /** @var EccubeExtension $extension */
        $extension = static::getContainer()->get(EccubeExtension::class);

        $json = $extension->encodeJsonLd([
            '@type' => 'Product',
            'name' => 'evil</script><script>alert(1)</script>',
        ]);

        // </script> がそのまま出力されず、< > は < > にエスケープされる
        $this->assertStringNotContainsString('</script>', $json);
        $this->assertStringNotContainsString('<script>', $json);
        $this->assertStringContainsString('\u003C', $json);
        // JSON として妥当で、デコードすれば元の文字列に戻る
        $decoded = json_decode($json, true);
        $this->assertSame('evil</script><script>alert(1)</script>', $decoded['name']);
    }
}
