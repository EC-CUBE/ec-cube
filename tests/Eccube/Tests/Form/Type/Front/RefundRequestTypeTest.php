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

namespace Eccube\Tests\Form\Type\Front;

use Eccube\Form\Type\Front\RefundRequestType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Symfony\Component\Form\FormInterface;

final class RefundRequestTypeTest extends AbstractTypeTestCase
{
    private ?FormInterface $form = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->formFactory
            ->createBuilder(RefundRequestType::class, null, [
                'csrf_protection' => false,
                'max_quantity' => 10,
            ])
            ->getForm();
    }

    public function testValidData(): void
    {
        $this->form->submit([
            'quantity' => '1',
            'reason' => 'テスト返品理由',
        ]);

        $this->assertTrue($this->form->isValid());
    }

    public function testValidMaxQuantity(): void
    {
        $this->form->submit([
            'quantity' => '10',
            'reason' => 'テスト返品理由',
        ]);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidQuantityZero(): void
    {
        $this->form->submit([
            'quantity' => '0',
            'reason' => 'テスト返品理由',
        ]);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidQuantityExceed(): void
    {
        $this->form->submit([
            'quantity' => '11',
            'reason' => 'テスト返品理由',
        ]);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidQuantityNegative(): void
    {
        $this->form->submit([
            'quantity' => '-1',
            'reason' => 'テスト返品理由',
        ]);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidReasonBlank(): void
    {
        $this->form->submit([
            'quantity' => '1',
            'reason' => '',
        ]);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidReasonTooLong(): void
    {
        $this->form->submit([
            'quantity' => '1',
            'reason' => str_repeat('あ', 4001),
        ]);

        $this->assertFalse($this->form->isValid());
    }

    public function testValidReasonMaxLength(): void
    {
        $this->form->submit([
            'quantity' => '1',
            'reason' => str_repeat('あ', 4000),
        ]);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidQuantityBlank(): void
    {
        $this->form->submit([
            'quantity' => '',
            'reason' => 'テスト返品理由',
        ]);

        $this->assertFalse($this->form->isValid());
    }
}
