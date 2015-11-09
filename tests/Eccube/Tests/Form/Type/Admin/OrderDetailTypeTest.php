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

use Symfony\Component\HttpFoundation\Request;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;

class OrderDetailTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'price' => '10000',
        'quantity'=> '10000',
        'tax_rate' => '10.0',
        'Product' => null,
        'ProductClass' => null
    );

    public function setUp()
    {
        parent::setUp();


        // CSRF tokenを無効にしてFormを作成
        // 会員管理会員登録・編集
        $this->form = $this->app['form.factory']
            ->createBuilder('order_detail', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
        $this->app['request'] = new Request();
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());

        //var_dump($this->form->getErrorsAsString());die;
    }

    /*
    public function testInvalidTel_Blank()
    {
        $this->app['request'] = new Request();
        $this->formData['tel']['tel01'] = '';
        $this->formData['tel']['tel02'] = '';
        $this->formData['tel']['tel03'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
    */
}
