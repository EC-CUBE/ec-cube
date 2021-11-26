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

namespace Eccube\Tests\Util;

use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\Master\PrefType;
use Eccube\Form\Type\Master\SexType;
use Eccube\Tests\EccubeTestCase;
use Eccube\Util\FormUtil;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;

class FormUtilTest extends EccubeTestCase
{
    protected $form;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    protected $formData = [
        'pref' => '28',
        'name' => 'パーコレータ',
        'date' => '2017-02-01',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->formFactory = self::$container->get('form.factory');
        $this->form = $this->formFactory
            ->createBuilder(
                FormType::class,
                null,
                [
                    'csrf_protection' => false,
                ]
            )
            ->add('pref', PrefType::class)
            ->add('name', TextType::class)
            ->add('date', DateType::class, [
                'label' => '受注日(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->getForm();
    }

    public function testGetViewData()
    {
        $this->form->submit($this->formData);

        $viewData = FormUtil::getViewData($this->form);

        // POSTしたデータと同じになるはず.
        $this->assertEquals($this->formData, $viewData);
    }

    public function testSubmitAndGetData()
    {
        $data = FormUtil::submitAndGetData($this->form, $this->formData);

        // formはsubmitされている.
        $this->assertTrue($this->form->isSubmitted());

        // prefはPrefエンティティに変換されている.
        $this->assertInstanceOf('\Eccube\Entity\Master\Pref', $data['pref']);
        $this->assertEquals(28, $data['pref']->getId());
        $this->assertEquals('兵庫県', $data['pref']->getName());

        // dateはDateTimeに変換されている.
        $this->assertInstanceOf('\DateTime', $data['date']);
    }

    /**
     * AddressTypeなど, 子要素をもつFormTypeのテスト.
     */
    public function testNestedFormType()
    {
        $formData = [
            'address' => [
                'pref' => '27',
                'addr01' => '北区',
                'addr02' => '梅田',
            ],
        ];

        $form = $this->formFactory
            ->createBuilder(
                FormType::class,
                null,
                [
                    'csrf_protection' => false,
                ]
            )
            ->add('address', AddressType::class)
            ->getForm();

        $form->submit($formData);
        $viewData = FormUtil::getViewData($form);
        $this->assertEquals($formData, $viewData);
    }

    /**
     * choice typeのテスト
     */
    public function testChoiceType()
    {
        $formData = [
            'sex' => '1',
        ];

        $form = $this->formFactory
            ->createBuilder(
                FormType::class,
                null,
                [
                    'csrf_protection' => false,
                ]
            )
            ->add('sex', SexType::class)
            ->getForm();

        $form->submit($formData);
        $viewData = FormUtil::getViewData($form);
        $this->assertEquals($formData, $viewData);
    }

    /**
     * choice type(multiple)のテスト
     */
    public function testChoiceTypeMultiple()
    {
        $formData = [
            'sex' => ['1', '2'],
        ];

        $form = $this->formFactory
            ->createBuilder(
                FormType::class,
                null,
                [
                    'csrf_protection' => false,
                ]
            )
            ->add('sex', SexType::class, [
                'multiple' => true,
            ])
            ->getForm();

        $form->submit($formData);
        $viewData = FormUtil::getViewData($form);
        $this->assertEquals($formData, $viewData);
    }
}
