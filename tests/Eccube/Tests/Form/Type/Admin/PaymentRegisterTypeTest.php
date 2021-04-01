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

use Eccube\Form\Type\Admin\PaymentRegisterType;
use Symfony\Component\HttpFoundation\Request;

class PaymentRegisterTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'method' => '1',
        'charge' => '10000',
        'rule_min' => '100',
        'rule_max' => '10000',
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        // 会員管理会員登録・編集
        $this->form = $this->formFactory
            ->createBuilder(PaymentRegisterType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
        self::$container->get('request_stack')->push(new Request());
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidChargeBlank()
    {
        $this->formData['charge'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidChargeNotNumeric()
    {
        $this->formData['charge'] = 'abc';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidChargeHasMinus()
    {
        $this->formData['charge'] = '-123456';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidChargeTooLarge()
    {
        $this->formData['charge'] = $this->eccubeConfig['eccube_price_max'] + 1;

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidRuleMinNotNumeric()
    {
        $this->formData['rule_min'] = 'abc';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidRuleMinHasMinus()
    {
        $this->formData['rule_min'] = '-123456';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidRuleMinTooLarge()
    {
        $this->formData['rule_min'] = $this->eccubeConfig['eccube_price_max'] + 1;
        $this->formData['rule_max'] = '100';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidRuleMaxNotNumeric()
    {
        $this->formData['rule_max'] = 'abc';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidRuleMaxHasMinus()
    {
        $this->formData['rule_max'] = '-123456';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidRuleMaxTooLarge()
    {
        $this->formData['rule_max'] = $this->eccubeConfig['eccube_price_max'] + 1;

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
