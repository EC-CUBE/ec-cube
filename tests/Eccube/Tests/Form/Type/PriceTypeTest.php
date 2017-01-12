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

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Eccube\Form\Type\PriceType;

class PriceTypeTest extends AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    public $config = array('price_len' => 8);

    /**
     * getValidTestData
     *
     * 正常系のデータパターンを返す
     *
     * @access public
     * @return array
     */
    public function getValidTestData()
    {
        return array(
            array(
                'data' => 0,
            ),
            array(
                'data' => 1,
            ),
            array(
                'data' => '0',
            ),
            array(
                'data' => '1',
            ),
        );
    }

    public function setUp()
    {
        parent::setUp();
        $this->form = $this->app['form.factory']
            ->createBuilder(PriceType::class)
            ->getForm();
    }

    /**
     * @dataProvider getValidTestData
     */
    public function testValidData($data)
    {
        $this->form->submit($data);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidData_PriceLen()
    {
        $this->form->submit(str_repeat('1', $this->config['price_len']));
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidData_Blank()
    {
        $this->form->submit('');
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Minus()
    {
        $this->form->submit('-1');
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_PriceLen()
    {
        $this->form->submit(str_repeat('1', $this->config['price_len']+1));
        $this->assertFalse($this->form->isValid());
    }

    public function testNotRequiredOption()
    {
        $form = $this->app['form.factory']->createBuilder(FormType::class)
            ->add('price', PriceType::class, array(
                'required' => false,
            ))
            ->getForm();

        $form->submit(['price' => '']);
        $this->assertTrue($form->isValid(), (string) $form->getErrors(true, false));
    }
}
