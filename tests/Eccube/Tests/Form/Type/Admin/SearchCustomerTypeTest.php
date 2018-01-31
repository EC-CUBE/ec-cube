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
            'tel' => str_repeat('A' , 55)
        ];

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testBuyProductName_NotValiedData()
    {
        $formData = [
            'buy_product_name' => str_repeat('A' , $this->eccubeConfig['stext_len'] + 1)
        ];

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testBuyProductCode_NotValiedData()
    {
        $formData = array(
            'buy_product_code' => str_repeat('A' , $this->eccubeConfig['stext_len'] + 1)
        );

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }
}
