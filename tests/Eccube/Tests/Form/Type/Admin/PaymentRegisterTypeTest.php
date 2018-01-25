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


namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Form\Type\Admin\PaymentRegisterType;
use Symfony\Component\HttpFoundation\Request;

class PaymentRegisterTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'method' => '1',
        'charge'=> '10000',
        'rule_min' => '100',
        'rule_max' => '10000'
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        // 会員管理会員登録・編集
        $this->form = $this->formFactory
            ->createBuilder(PaymentRegisterType::class, null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
        $this->container->get('request_stack')->push(new Request());
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidCharge_Blank()
    {
        $this->formData['charge'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidCharge_NotNumeric()
    {
        $this->formData['charge'] = 'abc';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidCharge_HasMinus()
    {
        $this->formData['charge'] = '-123456';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidCharge_TooLarge()
    {
        $this->formData['charge'] = $this->eccubeConfig['price_max'] + 1;

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidRuleMin_NotNumeric()
    {
        $this->formData['rule_min'] = 'abc';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidRuleMin_HasMinus()
    {
        $this->formData['rule_min'] = '-123456';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidRuleMin_TooLarge()
    {
        $this->formData['rule_min'] = $this->eccubeConfig['price_max'] + 1;
        $this->formData['rule_max'] = '100';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidRuleMax_NotNumeric()
    {
        $this->formData['rule_max'] = 'abc';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidRuleMax_HasMinus()
    {
        $this->formData['rule_max'] = '-123456';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidRuleMax_TooLarge()
    {
        $this->formData['rule_max'] = $this->eccubeConfig['price_max'] + 1;

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
