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

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Block;
use Eccube\Entity\Calendar;
use Eccube\Entity\Category;
use Eccube\Entity\DeliveryFee;
use Eccube\Entity\DeliveryTime;
use Eccube\Entity\Layout;
use Eccube\Entity\Master\OrderItemType as OrderItemTypeMaster;
use Eccube\Entity\News;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\TaxRule;
use Eccube\Entity\Template;
use Eccube\Form\Type\Admin\BlockType;
use Eccube\Form\Type\Admin\CalendarType;
use Eccube\Form\Type\Admin\CategoryType;
use Eccube\Form\Type\Admin\DeliveryFeeType;
use Eccube\Form\Type\Admin\DeliveryTimeType;
use Eccube\Form\Type\Admin\LayoutType;
use Eccube\Form\Type\Admin\NewsType;
use Eccube\Form\Type\Admin\OrderItemType;
use Eccube\Form\Type\Admin\OrderType;
use Eccube\Form\Type\Admin\ProductType;
use Eccube\Form\Type\Admin\ShopMasterType;
use Eccube\Form\Type\Admin\TaxRuleType;
use Eccube\Form\Type\Admin\TemplateType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Form\FormInterface;

/**
 * 既存レコードのテキスト欄を空にして送信しても TypeError にならないことを検証する.
 *
 * ビュー変換器を持たないフィールドは空文字が null へ落ちるため, empty_data の指定が無いと
 * バリデーションより前のマッピング段階で非 nullable な setter へ null が渡り 500 になる.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/7149
 */
final class EmptyTextSubmitTest extends AbstractTypeTestCase
{
    /**
     * 必須項目(NotBlank)は空文字のまま保たれ, バリデーションエラーになる.
     *
     * @param class-string $formType
     * @param callable(self): object $factory 値が入った状態のエンティティを返す
     * @param callable(object): mixed $reader 対象フィールドの現在値を返す
     * @param array<string, mixed> $extraSubmit 対象フィールド以外に送信が必要な値
     */
    #[DataProvider(methodName: 'provideRequiredFields')]
    public function testSubmitEmptyKeepsEmptyStringAndFailsValidation(
        string $formType,
        string $field,
        callable $factory,
        callable $reader,
        array $extraSubmit = [],
    ): void {
        $data = $factory($this);

        $form = $this->formFactory->create($formType, $data, ['csrf_protection' => false]);
        $form->submit($extraSubmit + [$field => ''], false);

        $this->assertSame('', $reader($data), '空文字が null へ落ちて setter が TypeError になっていないこと');
        $this->assertGreaterThan(0, self::countErrorsOf($form, $field), 'NotBlank が発火してバリデーションエラーになること');
    }

    /**
     * 任意項目は空文字が null として保存される.
     *
     * @param class-string $formType
     * @param callable(self): object $factory
     * @param callable(object): mixed $reader
     */
    #[DataProvider(methodName: 'provideOptionalFields')]
    public function testSubmitEmptyStoresNull(
        string $formType,
        string $field,
        callable $factory,
        callable $reader,
    ): void {
        $data = $factory($this);

        $form = $this->formFactory->create($formType, $data, ['csrf_protection' => false]);
        $form->submit([$field => ''], false);

        $this->assertNull($reader($data));
        $this->assertSame(0, self::countErrorsOf($form, $field));
    }

    /**
     * 金額・日付項目(MoneyType / DateType 系)は常にビュー変換器を持ち, その reverseTransform が
     * 空文字を null へ変換する. empty_data では空文字を保てないため, setter を nullable に
     * したうえで NotBlank に委ねる.
     *
     * @param class-string $formType
     * @param callable(self): object $factory
     * @param callable(object): mixed $reader
     */
    #[DataProvider(methodName: 'provideRequiredTransformedFields')]
    public function testSubmitEmptyStoresNullAndFailsValidation(
        string $formType,
        string $field,
        callable $factory,
        callable $reader,
    ): void {
        $data = $factory($this);

        $form = $this->formFactory->create($formType, $data, ['csrf_protection' => false]);
        $form->submit([$field => ''], false);

        $this->assertNull($reader($data), '空文字が setter へ渡って TypeError になっていないこと');
        $this->assertGreaterThan(0, self::countErrorsOf($form, $field), 'NotBlank が発火してバリデーションエラーになること');
    }

    /**
     * 指定したフィールドを起点とするエラーの件数.
     *
     * HiddenType は error_bubbling が既定で true のため, エラーは子ではなく親に積まれる.
     * 子の getErrors() だけを見ると取りこぼすので, 起点で数える.
     */
    private static function countErrorsOf(FormInterface $form, string $field): int
    {
        $count = 0;
        foreach ($form->getErrors(true) as $error) {
            if ($error->getOrigin()?->getName() === $field) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * @return \Iterator<string, array{0: class-string, 1: string, 2: callable(EmptyTextSubmitTest): object, 3: callable(object): mixed, 4?: array<string, mixed>}>
     */
    public static function provideRequiredFields(): \Iterator
    {
        yield 'BlockType::name' => [
            BlockType::class,
            'name',
            static fn (): Block => (new Block())->setName('ブロック名')->setFileName('block_file'),
            static fn (Block $Block): string => $Block->getName(),
        ];
        yield 'BlockType::file_name' => [
            BlockType::class,
            'file_name',
            static fn (): Block => (new Block())->setName('ブロック名')->setFileName('block_file'),
            static fn (Block $Block): string => $Block->getFileName(),
        ];
        yield 'CategoryType::name' => [
            CategoryType::class,
            'name',
            static fn (): Category => (new Category())->setName('カテゴリ名'),
            static fn (Category $Category): string => $Category->getName(),
        ];
        yield 'DeliveryTimeType::delivery_time' => [
            DeliveryTimeType::class,
            'delivery_time',
            static fn (): DeliveryTime => (new DeliveryTime())->setDeliveryTime('午前中'),
            static fn (DeliveryTime $DeliveryTime): string => $DeliveryTime->getDeliveryTime(),
        ];
        yield 'LayoutType::name' => [
            LayoutType::class,
            'name',
            static fn (): Layout => (new Layout())->setName('レイアウト名'),
            static fn (Layout $Layout): string => $Layout->getName(),
        ];
        yield 'NewsType::title' => [
            NewsType::class,
            'title',
            static fn (): News => (new News())->setTitle('新着情報'),
            static fn (News $News): string => $News->getTitle(),
        ];
        yield 'CalendarType::title' => [
            CalendarType::class,
            'title',
            static fn (): Calendar => (new Calendar())->setTitle('祝日'),
            static fn (Calendar $Calendar): ?string => $Calendar->getTitle(),
        ];
        yield 'OrderItemType::product_name' => [
            OrderItemType::class,
            'product_name',
            // POST_SUBMIT リスナーが明細種別を参照するため, 商品を必要としない送料明細で組み立てる
            static fn (self $testCase): OrderItem => (new OrderItem())
                ->setProductName('送料')
                ->setOrderItemType(
                    $testCase->entityManager
                        ->find(OrderItemTypeMaster::class, OrderItemTypeMaster::DELIVERY_FEE)
                ),
            static fn (OrderItem $OrderItem): string => $OrderItem->getProductName(),
            // PRE_SUBMIT リスナーが明細種別を参照するため, 部分送信でも渡す
            ['order_item_type' => (string) OrderItemTypeMaster::DELIVERY_FEE],
        ];
        yield 'ProductType::name' => [
            ProductType::class,
            'name',
            static fn (): Product => (new Product())->setName('商品名'),
            static fn (Product $Product): string => $Product->getName(),
            // POST_SUBMIT リスナーが画像欄のパスを走査するため, 部分送信でも空配列を渡す
            ['add_images' => [], 'delete_images' => []],
        ];
        yield 'TemplateType::code' => [
            TemplateType::class,
            'code',
            static fn (): Template => (new Template())->setCode('code')->setName('テンプレート'),
            static fn (Template $Template): string => $Template->getCode(),
        ];
        yield 'TemplateType::name' => [
            TemplateType::class,
            'name',
            static fn (): Template => (new Template())->setCode('code')->setName('テンプレート'),
            static fn (Template $Template): string => $Template->getName(),
        ];
    }

    /**
     * @return \Iterator<string, array{class-string, string, callable(EmptyTextSubmitTest): object, callable(object): mixed}>
     */
    public static function provideOptionalFields(): \Iterator
    {
        yield 'ShopMasterType::invoice_registration_number' => [
            ShopMasterType::class,
            'invoice_registration_number',
            static fn (): BaseInfo => (new BaseInfo())->setInvoiceRegistrationNumber('T1234567890123'),
            static fn (BaseInfo $BaseInfo): ?string => $BaseInfo->getInvoiceRegistrationNumber(),
        ];
    }

    /**
     * POST_SUBMIT で値を補完する項目は, 空送信でもエラーにならず補完後の値になる.
     */
    public function testSubmitEmptyIsCompensatedByPostSubmit(): void
    {
        $DeliveryTime = (new DeliveryTime())->setDeliveryTime('午前中')->setSortNo(1)->setVisible(true);

        $form = $this->formFactory->create(DeliveryTimeType::class, $DeliveryTime, ['csrf_protection' => false]);
        $form->submit(['visible' => ''], false);

        $this->assertTrue($DeliveryTime->isVisible(), 'POST_SUBMIT の補完が効くこと');
        $this->assertSame(0, self::countErrorsOf($form, 'visible'));
    }

    /**
     * テンプレートにウィジェットを描画しないマップ済みフィールドは, 送信のたびに欠落する.
     *
     * empty_data が無いと null が入り, 非 nullable な getter を持つ項目は
     * バリデーション失敗時の再描画で TypeError になる.
     */
    public function testUnrenderedMappedTotalsKeepZero(): void
    {
        $Order = $this->createOrder($this->createCustomer());

        $form = $this->formFactory->create(OrderType::class, $Order, ['csrf_protection' => false]);
        // 画面と同じく discount / delivery_fee_total / charge を送らない
        $form->submit(['message' => ''], true);

        $this->assertSame('0', $Order->getDeliveryFeeTotal());
        $this->assertSame('0', $Order->getDiscount());
        $this->assertSame('0', $Order->getCharge());

        $this->entityManager->clear();
    }

    /**
     * 識別子は制約を持たないため, 空送信でも TypeError にならず null になる.
     */
    public function testSubmitEmptyIdentifierStoresNull(): void
    {
        $Block = (new Block())->setId(5)->setName('ブロック名')->setFileName('block_file');

        $form = $this->formFactory->create(BlockType::class, $Block, ['csrf_protection' => false]);
        $form->submit(['id' => ''], false);

        $this->assertNull($Block->getId());
    }

    /**
     * ビュー変換器を常に持つ型は empty_data では空文字を保てないため, null になったうえで NotBlank になる.
     *
     * @return \Iterator<string, array{class-string, string, callable(EmptyTextSubmitTest): object, callable(object): mixed}>
     */
    public static function provideRequiredTransformedFields(): \Iterator
    {
        yield 'DeliveryFeeType::fee' => [
            DeliveryFeeType::class,
            'fee',
            static fn (): DeliveryFee => (new DeliveryFee())->setFee('500'),
            static fn (DeliveryFee $DeliveryFee): ?string => $DeliveryFee->getFee(),
        ];
        yield 'CalendarType::holiday' => [
            CalendarType::class,
            'holiday',
            static fn (): Calendar => (new Calendar())->setTitle('祝日')->setHoliday(new \DateTime('2031-03-18')),
            static fn (Calendar $Calendar): ?\DateTime => $Calendar->getHoliday(),
        ];
        yield 'DeliveryTimeType::sort_no' => [
            DeliveryTimeType::class,
            'sort_no',
            static fn (): DeliveryTime => (new DeliveryTime())->setDeliveryTime('午前中')->setSortNo(1),
            static fn (DeliveryTime $DeliveryTime): ?int => $DeliveryTime->getSortNo(),
        ];
        yield 'TaxRuleType::apply_date' => [
            TaxRuleType::class,
            'apply_date',
            static fn (): TaxRule => (new TaxRule())->setApplyDate(new \DateTime('2031-01-01')),
            static fn (TaxRule $TaxRule): ?\DateTime => $TaxRule->getApplyDate(),
        ];
    }
}
