<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Form\Type;

use Eccube\Form\Type\Admin\TaxRuleType;
use Symfony\Component\Form\FormInterface;

class TaxRuleTypeTest extends AbstractTypeTestCase
{
    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'tax_rate' => 10,
        'calc_rule' => 1,
        'apply_date' => [
            'date'  => '2014-04-01',
            'time'  => '00:00',
        ],
    );

    /** @var  FormInterface */
    protected $form;

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(TaxRuleType::class, null, array(
                'csrf_protection' => false,
            ))
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

    public function testValidDeliveryTaxRate_HasMinus()
    {
        $this->formData['tax_rate'] = '10.0';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }
}
