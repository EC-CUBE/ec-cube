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

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Form\Type\Admin\SearchCustomerType;
use Symfony\Component\Form\FormInterface;

class SearchCustomerTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(SearchCustomerType::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    public function testTel_NotValidData()
    {
        $formData = [
            'tel' => str_repeat('A', 55),
        ];

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testBuyProductName_NotValiedData()
    {
        $formData = [
            'buy_product_name' => str_repeat('A', $this->eccubeConfig['eccube_stext_len'] + 1),
        ];

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testBuyProductCode_NotValiedData()
    {
        $formData = [
            'buy_product_code' => str_repeat('A', $this->eccubeConfig['eccube_stext_len'] + 1),
        ];

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }
}
