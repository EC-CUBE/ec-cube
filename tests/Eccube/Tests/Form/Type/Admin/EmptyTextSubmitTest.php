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

namespace Eccube\Tests\Form\Type;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Block;
use Eccube\Entity\Calendar;
use Eccube\Entity\Category;
use Eccube\Entity\DeliveryTime;
use Eccube\Entity\Layout;
use Eccube\Entity\Master\OrderItemType as OrderItemTypeMaster;
use Eccube\Entity\News;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Template;
use Eccube\Form\Type\Admin\BlockType;
use Eccube\Form\Type\Admin\CalendarType;
use Eccube\Form\Type\Admin\CategoryType;
use Eccube\Form\Type\Admin\DeliveryTimeType;
use Eccube\Form\Type\Admin\LayoutType;
use Eccube\Form\Type\Admin\NewsType;
use Eccube\Form\Type\Admin\OrderItemType;
use Eccube\Form\Type\Admin\ShopMasterType;
use Eccube\Form\Type\Admin\TemplateType;
use PHPUnit\Framework\Attributes\DataProvider;

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
        $this->assertGreaterThan(0, $form->get($field)->getErrors()->count(), 'NotBlank が発火してバリデーションエラーになること');
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
        $this->assertCount(0, $form->get($field)->getErrors());
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
        yield 'CalendarType::title' => [
            CalendarType::class,
            'title',
            static fn (): Calendar => (new Calendar())->setTitle('祝日'),
            static fn (Calendar $Calendar): ?string => $Calendar->getTitle(),
        ];
    }
}
