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

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Form\Type\Admin\SearchOrderType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Form\FormInterface;

class SearchOrderTypeTest extends AbstractTypeTestCase
{
    protected ?FormInterface $form = null;

    /**
     * {@inheritdoc}
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(SearchOrderType::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    /**
     * EC-CUBE 4.0.4 以前のバージョンで互換性を保つため yyyy-MM-dd のフォーマットもチェック
     */
    #[DataProvider(methodName: 'dataFormDateProvider')]
    public function testDateSearch(string $formName)
    {
        $formData = [
            $formName => '2020-07-09',
        ];

        $this->form->submit($formData);
        $this->assertTrue($this->form->isValid());
    }

    /**
     * EC-CUBE 4.0.4 以前のバージョンで互換性を保つため yyyy-MM-dd のフォーマットもチェック
     */
    #[DataProvider(methodName: 'dataFormDateProvider')]
    public function testOutOfRangeSearch(string $formName)
    {
        $formData = [
            $formName => '0001-01-01',
        ];

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * Data provider date form test.
     */
    public static function dataFormDateProvider(): \Iterator
    {
        yield ['order_date_start'];
        yield ['payment_date_start'];
        yield ['update_date_start'];
        yield ['shipping_delivery_date_start'];
        yield ['order_date_end'];
        yield ['payment_date_end'];
        yield ['update_date_end'];
        yield ['shipping_delivery_date_end'];
    }

    /**
     * EC-CUBE 4.0.5 以降で yyyy-MM-dd HH:mm:ss のフォーマットでの検索機能を追加
     */
    #[DataProvider(methodName: 'dataFormDateTimeProvider')]
    public function testDateTimeSearch(string $formName)
    {
        $formData = [
            $formName => '2020-07-09 09:00:00',
        ];

        $this->form->submit($formData);
        $this->assertTrue($this->form->isValid());
    }

    /**
     * Data provider datetime form test.
     */
    public static function dataFormDateTimeProvider(): \Iterator
    {
        yield ['order_datetime_start'];
        yield ['payment_datetime_start'];
        yield ['update_datetime_start'];
        yield ['shipping_delivery_datetime_start'];
        yield ['order_datetime_end'];
        yield ['payment_datetime_end'];
        yield ['update_datetime_end'];
        yield ['shipping_delivery_datetime_end'];
    }
}
