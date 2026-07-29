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

use Eccube\Form\Type\Admin\SearchRefundRequestType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Symfony\Component\Form\FormInterface;

final class SearchRefundRequestTypeTest extends AbstractTypeTestCase
{
    private ?FormInterface $form = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->formFactory
            ->createBuilder(SearchRefundRequestType::class)
            ->getForm();
    }

    public function testValidEmptyData(): void
    {
        $this->form->submit([]);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidMulti(): void
    {
        $this->form->submit([
            'multi' => 'テスト',
        ]);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidStatus(): void
    {
        $this->form->submit([
            'status' => ['1', '2'],
        ]);
        $this->assertTrue($this->form->isValid());
    }

    public function testFormHasExpectedFields(): void
    {
        $this->assertTrue($this->form->has('multi'));
        $this->assertTrue($this->form->has('status'));
        $this->assertTrue($this->form->has('create_date_start'));
        $this->assertTrue($this->form->has('create_date_end'));
        $this->assertTrue($this->form->has('update_date_start'));
        $this->assertTrue($this->form->has('update_date_end'));
    }
}
