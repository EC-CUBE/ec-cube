<?php

namespace Eccube\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class CustomerTypeTest extends TypeTestCase
{

    // デフォルト値（正常系）を設定
    private $formData = array(
        'name01' => 'たかはし',
        'name02' => 'しんいち',
        'kana01' => 'タカハシ',
        'kana02' => 'シンイチ',
        'email' => 'default@example.com',
        'password' => array(
            'first' => 'password',
            'second' => 'password',
        )
    );

    public function setUp()
    {
        parent::setUp();

        $this->app = new \Eccube\Application;

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('customer', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidNam01_NotBlank()
    {
        $this->formData['name01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNam02_NotBlank()
    {
        $this->formData['name02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana01_NotBlank()
    {
        $this->formData['kana01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana02_NotBlank()
    {
        $this->formData['kana02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_NotBlank()
    {
        $this->formData['email'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_InvalidEmail()
    {
        $this->formData['email'] = 'sample.example.com';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_InvalidCallback()
    {
        $testVal = 'sample@example.com';
        $customer = $this->app['eccube.repository.customer']->newCustomer()
            ->setName01($this->formData['name01'])
            ->setName02($this->formData['name02'])
            ->setKana01($this->formData['kana01'])
            ->setKana02($this->formData['kana02'])
            ->setPassword($this->formData['password'])
            ->setEmail($testVal);

        $form = $this->app['form.factory']
            ->createBuilder('customer', $customer, array(
                'csrf_protection' => false,
            ))
            ->getForm();

        $this->app['orm.em']->persist($customer);
        $this->formData['email'] = $testVal;
        $form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPassword_Invalid()
    {
        $this->formData['password']['first'] = 'poss';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

}
