<?php

namespace Eccube\Tests\Form\Type;

class NameTypeTest extends AbstractTypeTestCase
{

    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'name' => array(
            'name01' => 'たかはし',
            'name02' => 'しんいち',
        ),
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('form', null, array(
                'csrf_protection' => false,
            ))
            ->add('name', 'name')
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

}
