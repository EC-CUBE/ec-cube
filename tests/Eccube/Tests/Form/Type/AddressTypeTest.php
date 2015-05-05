<?php

namespace Eccube\Tests\Form\Type;

class AddressTypeTest extends AbstractTypeTestCase
{

    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'address' => array(
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
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
            ->add('address', 'address')
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

}
