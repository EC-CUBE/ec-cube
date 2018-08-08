<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Form\Type;

use Eccube\Form\Type\Admin\TaxRuleType;
use Symfony\Component\Form\FormInterface;

class TaxRuleTypeTest extends AbstractTypeTestCase
{
    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'tax_rate' => 10,
        'calc_rule' => 1,
        'apply_date' => [
            'date' => '2014-04-01',
            'time' => '00:00',
        ],
    ];

    /** @var FormInterface */
    protected $form;

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(TaxRuleType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function test_getName_validTaxRule()
    {
        $this->assertSame('tax_rule', $this->form->getName());
    }

    public function testInValidDeliveryTaxRate_Blank()
    {
        $this->formData['tax_rate'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryTaxRate_OverMaxRength()
    {
        $this->formData['tax_rate'] = str_repeat('2', 101);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryTaxRate_NotNumeric()
    {
        $this->formData['tax_rate'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryTaxRate_HasMinus()
    {
        $this->formData['tax_rate'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
