<?php

namespace Eccube\Tests\Util;

use Eccube\Tests\EccubeTestCase;
use Eccube\Util\FormUtil;

class FormUtilTest extends EccubeTestCase
{
    protected $form;

    protected $formData = array(
        'pref' => '28',
        'name' => 'パーコレータ',
        'date' => '2017-02-01'
    );

    public function setUp()
    {
        parent::setUp();

        $this->form = $this->app['form.factory']
            ->createBuilder(
                'form',
                null,
                array(
                    'csrf_protection' => false,
                )
            )
            ->add('pref', 'pref')
            ->add('name', 'text')
            ->add('date', 'date', array(
                'label' => '受注日(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
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
        $formData = array(
            'address' => array(
                'pref' => '27',
                'addr01' => '北区',
                'addr02' => '梅田'
            )
        );

        $form = $this->app['form.factory']
            ->createBuilder(
                'form',
                null,
                array(
                    'csrf_protection' => false,
                )
            )
            ->add('address', 'address')
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
        $formData = array(
            'sex' => '1',
        );

        $form = $this->app['form.factory']
            ->createBuilder(
                'form',
                null,
                array(
                    'csrf_protection' => false,
                )
            )
            ->add('sex', 'sex')
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
        $formData = array(
            'sex' => array('1', '2')
        );

        $form = $this->app['form.factory']
            ->createBuilder(
                'form',
                null,
                array(
                    'csrf_protection' => false,
                )
            )
            ->add('sex', 'sex', array(
                'multiple' => true,
            ))
            ->getForm();

        $form->submit($formData);
        $viewData = FormUtil::getViewData($form);
        $this->assertEquals($formData, $viewData);
    }

}
