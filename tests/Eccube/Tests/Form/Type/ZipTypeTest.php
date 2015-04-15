<?php

namespace Eccube\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class ZipTypeTest extends TypeTestCase
{

    /** @var \Eccube\Application */
    private $app;

    /** @var \Symfony\Component\Form\FormInterface */
    private $form;

    /** @var array デフォルト値（正常系）を設定 */
    private $formData = array(
        'zip' => array(
            'zip01' => '530',
            'zip02' => '0001',
        ),
    );

    public function setUp()
    {
        parent::setUp();

        $this->app = new \Eccube\Application;

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('form', null, array(
                'csrf_protection' => false,
            ))
            ->add('zip', 'zip')
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidZip01_LengthMin()
    {
        $this->formData['zip']['zip01'] = '1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip01_LengthMax()
    {
        $this->formData['zip']['zip01'] = '1234';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_LengthMin()
    {
        $this->formData['zip']['zip02'] = '1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_LengthMax()
    {
        $this->formData['zip']['zip02'] = '12345';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

}
