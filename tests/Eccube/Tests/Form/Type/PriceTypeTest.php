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

namespace Eccube\Tests\Form\Type;

use Eccube\Form\Type\PriceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class PriceTypeTest extends AbstractTypeTestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * getValidTestData
     *
     * 正常系のデータパターンを返す
     *
     * @return array
     */
    public function getValidTestData()
    {
        return [
            ['data' => 0],
            ['data' => 1],
            ['data' => '0'],
            ['data' => '1'],
        ];
    }

    public function setUp()
    {
        parent::setUp();
        $this->form = $this->formFactory
            ->createBuilder(PriceType::class, null, ['csrf_protection' => false])
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

    public function testValidDataPriceLen()
    {
        $this->form->submit(str_repeat('1', $this->eccubeConfig['eccube_price_len']));
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidDataBlank()
    {
        $this->form->submit('');
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDataMinus()
    {
        $this->form->submit('-1');
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDataPriceLen()
    {
        $this->form->submit($this->eccubeConfig['eccube_price_max'] + 1);
        $this->assertFalse($this->form->isValid());
    }

    public function testNotRequiredOption()
    {
        $form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('price', PriceType::class, [
                'required' => false,
            ])
            ->getForm();

        $form->submit(['price' => '']);
        $this->assertTrue($form->isValid(), (string) $form->getErrors(true, false));
    }
}
