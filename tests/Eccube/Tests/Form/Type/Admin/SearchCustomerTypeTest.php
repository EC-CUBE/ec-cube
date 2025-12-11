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

use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Form\FormInterface;

final class SearchCustomerTypeTest extends AbstractTypeTestCase
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
            ->createBuilder(SearchCustomerType::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    public function testPhoneNumberNotValidData()
    {
        $formData = [
            'phone_number' => str_repeat('A', 55),
        ];

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testBuyProductNameNotValiedData()
    {
        $formData = [
            'buy_product_name' => str_repeat('A', $this->eccubeConfig['eccube_stext_len'] + 1),
        ];

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
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
     * Data provider date form test.
     */
    public static function dataFormDateProvider(): \Iterator
    {
        yield ['create_date_start'];
        yield ['update_date_start'];
        yield ['last_buy_start'];
        yield ['create_date_end'];
        yield ['update_date_end'];
        yield ['last_buy_end'];
        yield ['birth_start'];
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
        yield ['create_datetime_start'];
        yield ['update_datetime_start'];
        yield ['last_buy_datetime_start'];
        yield ['create_datetime_end'];
        yield ['update_datetime_end'];
        yield ['last_buy_datetime_end'];
    }
}
