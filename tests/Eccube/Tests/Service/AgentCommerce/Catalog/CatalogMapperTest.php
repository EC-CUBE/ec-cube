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

namespace Eccube\Tests\Service\AgentCommerce\Catalog;

use Eccube\Entity\ClassCategory;
use Eccube\Entity\ClassName;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogDescriptionDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogVariantDto;
use Eccube\Service\AgentCommerce\Catalog\AvailabilityStatus;
use Eccube\Service\AgentCommerce\Catalog\CatalogMapper;
use Eccube\Service\AgentCommerce\MinorUnitConverter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * Layer 1 (mapping) tests for CatalogMapper.
 *
 * Exercises Product / ProductClass -> AgentCatalogItemDto / AgentCatalogVariantDto:
 * availability resolution, hidden-product / hidden-variant exclusion, minor-unit
 * price conversion, variant option mapping and the default-empty barcodes seam.
 * DB-free: entities are built in memory and the UrlGenerator is mocked.
 */
final class CatalogMapperTest extends TestCase
{
    private CatalogMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturnCallback(
            static fn (string $name, array $params = [], int $type = UrlGeneratorInterface::ABSOLUTE_PATH): string => 'https://shop.example.com/products/detail/'.($params['id'] ?? '')
        );
        $context = new RequestContext('', 'GET', 'shop.example.com', 'https');
        $urlGenerator->method('getContext')->willReturn($context);

        $this->mapper = new CatalogMapper(new MinorUnitConverter(), $urlGenerator);
    }

    public function testMapProductReturnsNullForHiddenProduct(): void
    {
        $product = $this->buildProduct(1, ProductStatus::DISPLAY_HIDE);
        $this->addVisibleVariant($product, 11, '1000', 'JPY');

        $this->assertNotInstanceOf(AgentCatalogItemDto::class, $this->mapper->mapProduct($product), 'Non-displayed products must be excluded from the catalog');
    }

    public function testMapProductReturnsNullWhenNoVisibleVariant(): void
    {
        $product = $this->buildProduct(2, ProductStatus::DISPLAY_SHOW);
        $variant = $this->buildVariant(21, '1000', 'JPY', stock: '5', unlimited: false, visible: false);
        $variant->setProduct($product);
        $product->addProductClass($variant);

        $this->assertNotInstanceOf(AgentCatalogItemDto::class, $this->mapper->mapProduct($product), 'A displayed product with no visible variant must not be mapped (variants[] is required and non-empty)');
    }

    public function testMapProductMapsCoreFields(): void
    {
        $product = $this->buildProduct(3, ProductStatus::DISPLAY_SHOW, name: 'Test Coffee', descriptionDetail: '<p>Rich blend</p>');
        $this->addVisibleVariant($product, 31, '1200', 'JPY');

        $dto = $this->mapper->mapProduct($product);

        $this->assertInstanceOf(AgentCatalogItemDto::class, $dto);
        $this->assertSame('3', $dto->id, 'Product id must be mapped as a string');
        $this->assertSame('Test Coffee', $dto->title, 'Product name maps to title');
        $this->assertSame('https://shop.example.com/products/detail/3', $dto->url, 'Product url must be the absolute product_detail URL');
        $this->assertInstanceOf(AgentCatalogDescriptionDto::class, $dto->description, 'A non-empty descriptionDetail must produce a description DTO');
        $this->assertSame('<p>Rich blend</p>', $dto->description->html, 'descriptionDetail maps to the html field of the description');
        $this->assertCount(1, $dto->variants, 'Each visible ProductClass becomes one variant');
    }

    public function testMapProductExcludesHiddenVariantsButKeepsVisibleOnes(): void
    {
        $product = $this->buildProduct(4, ProductStatus::DISPLAY_SHOW);
        $visible = $this->buildVariant(41, '1000', 'JPY', stock: '5', unlimited: false, visible: true);
        $visible->setProduct($product);
        $product->addProductClass($visible);
        $hidden = $this->buildVariant(42, '2000', 'JPY', stock: '5', unlimited: false, visible: false);
        $hidden->setProduct($product);
        $product->addProductClass($hidden);

        $dto = $this->mapper->mapProduct($product);

        $this->assertInstanceOf(AgentCatalogItemDto::class, $dto);
        $this->assertCount(1, $dto->variants, 'Only visible ProductClass entries are mapped to variants');
        $this->assertSame('41', $dto->variants[0]->id, 'The visible variant must be the one retained');
    }

    public function testMapProductHasEmptyBarcodesAndCategoriesByDefault(): void
    {
        $product = $this->buildProduct(5, ProductStatus::DISPLAY_SHOW);
        $this->addVisibleVariant($product, 51, '1000', 'JPY');

        $dto = $this->mapper->mapProduct($product);

        $this->assertInstanceOf(AgentCatalogItemDto::class, $dto);
        $this->assertSame([], $dto->categories, 'Categories are a Customize seam and empty by default');
        $this->assertSame([], $dto->variants[0]->barcodes, 'Barcodes are a Customize seam and empty by default (no standard GTIN field)');
    }

    public function testMapProductMediaIsOrderedBySortNo(): void
    {
        $product = $this->buildProduct(6, ProductStatus::DISPLAY_SHOW);
        $this->addVisibleVariant($product, 61, '1000', 'JPY');
        $product->addProductImage($this->buildImage('second.jpg', 2));
        $product->addProductImage($this->buildImage('first.jpg', 1));

        $dto = $this->mapper->mapProduct($product);

        $this->assertInstanceOf(AgentCatalogItemDto::class, $dto);
        $this->assertCount(2, $dto->media, 'All product images map to media');
        $this->assertStringEndsWith('/first.jpg', $dto->media[0]->url, 'Media must be sorted ascending by sort_no');
        $this->assertStringEndsWith('/second.jpg', $dto->media[1]->url, 'Higher sort_no image comes second');
        $this->assertStringStartsWith('https://shop.example.com/', $dto->media[0]->url, 'Image URLs must be absolute (scheme + host from RequestContext)');
    }

    public function testMapVariantConvertsPriceToMinorUnitsForZeroDecimalCurrency(): void
    {
        $variant = $this->buildVariant(71, '1500', 'JPY', stock: '5', unlimited: false, visible: true);

        $dto = $this->mapper->mapVariant($variant);

        $this->assertSame(1500, $dto->priceMinorUnits, 'JPY price02 converts to identical minor units (0 fraction digits)');
        $this->assertSame('JPY', $dto->currency, 'Currency comes from the ProductClass currency code');
    }

    public function testMapVariantConvertsPriceToMinorUnitsForTwoDecimalCurrency(): void
    {
        $variant = $this->buildVariant(72, '10.99', 'USD', stock: '5', unlimited: false, visible: true);

        $dto = $this->mapper->mapVariant($variant);

        $this->assertSame(1099, $dto->priceMinorUnits, 'USD 10.99 converts to 1099 cents');
    }

    public function testMapVariantMapsListPriceWhenPriceSet(): void
    {
        $variant = $this->buildVariant(73, '900', 'JPY', stock: '5', unlimited: false, visible: true);
        $variant->setPrice01('1200');
        $variant->setPrice01IncTax('1200');

        $dto = $this->mapper->mapVariant($variant);

        $this->assertSame(1200, $dto->listPriceMinorUnits, 'price01 (通常価格) maps to list_price (reference / strikethrough price)');
        $this->assertSame(900, $dto->priceMinorUnits, 'price02 (販売価格) remains the active selling price');
    }

    public function testMapVariantUsesTaxIncludedPrice(): void
    {
        // 店頭表示と同様に税込価格を出力する: price02=1000 / price02_inc_tax=1100 のとき 1100 を採用する。
        $variant = $this->buildVariant(76, '1000', 'JPY', stock: '5', unlimited: false, visible: true);
        $variant->setPrice02IncTax('1100');
        $variant->setPrice01('2000');
        $variant->setPrice01IncTax('2200');

        $dto = $this->mapper->mapVariant($variant);

        $this->assertSame(1100, $dto->priceMinorUnits, '[実装方針] price は税込 price02_inc_tax を採用する (店頭整合・日本の総額表示。ACP/UCP spec MUST ではない)');
        $this->assertSame(2200, $dto->listPriceMinorUnits, '[実装方針] list_price も税込 price01_inc_tax を採用する');
    }

    public function testMapVariantListPriceIsNullWhenPrice01Absent(): void
    {
        $variant = $this->buildVariant(74, '900', 'JPY', stock: '5', unlimited: false, visible: true);
        $variant->setPrice01();

        $dto = $this->mapper->mapVariant($variant);

        $this->assertNull($dto->listPriceMinorUnits, 'A missing price01 must yield a null list price');
    }

    public function testMapVariantListPriceOmittedWhenNotDiscounted(): void
    {
        // price01 (通常価格) <= price02 (販売価格) のときは割引が無いため list_price を出さない。
        $variant = $this->buildVariant(77, '1000', 'JPY', stock: '5', unlimited: false, visible: true);
        $variant->setPrice01('1000');
        $variant->setPrice01IncTax('1000');

        $dto = $this->mapper->mapVariant($variant);

        $this->assertNull($dto->listPriceMinorUnits, '[実装方針・spec MUST ではない] 割引が無い (price01 <= price02) ときは list_price を省く (誤った割引表示の回避)');
    }

    public function testMapVariantUsesCodeAsSku(): void
    {
        $variant = $this->buildVariant(75, '900', 'JPY', stock: '5', unlimited: false, visible: true);
        $variant->setCode('SKU-XYZ');

        $dto = $this->mapper->mapVariant($variant);

        $this->assertSame('SKU-XYZ', $dto->sku, 'ProductClass code maps to the variant sku');
    }

    public function testMapVariantAvailabilityInStockWhenStockPositive(): void
    {
        $variant = $this->buildVariant(76, '900', 'JPY', stock: '3', unlimited: false, visible: true);

        $dto = $this->mapper->mapVariant($variant);

        $this->assertTrue($dto->available, 'Positive stock means available');
        $this->assertSame(AvailabilityStatus::IN_STOCK, $dto->availabilityStatus, 'Positive stock maps to in_stock');
    }

    public function testMapVariantAvailabilityInStockWhenUnlimited(): void
    {
        $variant = $this->buildVariant(77, '900', 'JPY', stock: '0', unlimited: true, visible: true);

        $dto = $this->mapper->mapVariant($variant);

        $this->assertTrue($dto->available, 'Unlimited stock is always available regardless of stock count');
        $this->assertSame(AvailabilityStatus::IN_STOCK, $dto->availabilityStatus, 'Unlimited stock maps to in_stock');
    }

    public function testMapVariantAvailabilityOutOfStockWhenZeroAndNotUnlimited(): void
    {
        $variant = $this->buildVariant(78, '900', 'JPY', stock: '0', unlimited: false, visible: true);

        $dto = $this->mapper->mapVariant($variant);

        $this->assertFalse($dto->available, 'Zero stock without unlimited flag means unavailable');
        $this->assertSame(AvailabilityStatus::OUT_OF_STOCK, $dto->availabilityStatus, 'Zero stock maps to out_of_stock (no limited_stock threshold by default)');
    }

    public function testMapVariantAvailabilityOutOfStockWhenStockNull(): void
    {
        $variant = $this->buildVariant(79, '900', 'JPY', stock: null, unlimited: false, visible: true);

        $dto = $this->mapper->mapVariant($variant);

        $this->assertFalse($dto->available, 'Null stock is treated as zero');
        $this->assertSame(AvailabilityStatus::OUT_OF_STOCK, $dto->availabilityStatus, 'Null stock maps to out_of_stock');
    }

    public function testMapVariantMapsClassCategoriesToOptions(): void
    {
        $variant = $this->buildVariant(80, '900', 'JPY', stock: '5', unlimited: false, visible: true);
        $variant->setClassCategory1($this->buildClassCategory('Color', 'Red'));
        $variant->setClassCategory2($this->buildClassCategory('Size', 'L'));

        $dto = $this->mapper->mapVariant($variant);

        $this->assertCount(2, $dto->options, 'Both class categories map to options');
        $this->assertSame('Color', $dto->options[0]->name, 'Option name comes from ClassName.name');
        $this->assertSame('Red', $dto->options[0]->value, 'Option value comes from ClassCategory.name');
        $this->assertSame('Size', $dto->options[1]->name, 'Second class category maps to the second option');
        $this->assertSame('L', $dto->options[1]->value, 'Second option value');
    }

    public function testMapVariantHasNoOptionsWhenNoClassCategory(): void
    {
        $variant = $this->buildVariant(81, '900', 'JPY', stock: '5', unlimited: false, visible: true);

        $dto = $this->mapper->mapVariant($variant);

        $this->assertSame([], $dto->options, 'A variant with no class categories has no options');
    }

    public function testMapVariantBarcodesEmptyByDefault(): void
    {
        $variant = $this->buildVariant(82, '900', 'JPY', stock: '5', unlimited: false, visible: true);

        $dto = $this->mapper->mapVariant($variant);

        $this->assertSame([], $dto->barcodes, 'Standard mapping never emits barcodes (Customize seam)');
    }

    private function buildProduct(int $id, int $statusId, string $name = 'Product', ?string $descriptionDetail = null): Product
    {
        $product = new Product();
        $this->setId($product, $id);
        $product->setName($name);
        $product->setDescriptionDetail($descriptionDetail);

        $status = new ProductStatus();
        $status->setId($statusId);
        $product->setStatus($status);

        return $product;
    }

    private function addVisibleVariant(Product $product, int $id, string $price02, string $currency): AgentCatalogVariantDto
    {
        $variant = $this->buildVariant($id, $price02, $currency, stock: '10', unlimited: false, visible: true);
        $variant->setProduct($product);
        $product->addProductClass($variant);

        // Return value unused by callers but keeps a single construction path.
        return $this->mapper->mapVariant($variant);
    }

    private function buildVariant(int $id, string $price02, string $currency, ?string $stock, bool $unlimited, bool $visible): ProductClass
    {
        $variant = new ProductClass();
        $this->setId($variant, $id);
        $variant->setPrice02($price02);
        // 税抜・税込が同額のケース (税率 0 相当)。税込が使われることの検証は別テストで price02 != inc_tax を用いる。
        $variant->setPrice02IncTax($price02);
        $variant->setCurrencyCode($currency);
        $variant->setStock($stock);
        $variant->setStockUnlimited($unlimited);
        $variant->setVisible($visible);

        return $variant;
    }

    private function buildImage(string $fileName, int $sortNo): ProductImage
    {
        $image = new ProductImage();
        $image->setFileName($fileName);
        $image->setSortNo($sortNo);

        return $image;
    }

    private function buildClassCategory(string $className, string $categoryName): ClassCategory
    {
        $name = new ClassName();
        $name->setName($className);

        $category = new ClassCategory();
        $category->setName($categoryName);
        $category->setClassName($name);

        return $category;
    }

    private function setId(object $entity, int $id): void
    {
        $ref = new \ReflectionProperty($entity, 'id');
        $ref->setValue($entity, $id);
    }
}
