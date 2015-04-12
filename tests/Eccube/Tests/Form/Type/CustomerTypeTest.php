<?php

namespace Eccube\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class CustomerTypeTest extends TypeTestCase
{

    /** @var \Eccube\Application */
    private $app;

    /** @var \Symfony\Component\Form\FormInterface */
    private $form;

    /** @var array デフォルト値（正常系）を設定 */
    private $formData = array(
        'name' => array(
            'name01' => 'たかはし',
            'name02' => 'しんいち',
        ),
        'kana'=> array(
            'kana01' => 'タカハシ',
            'kana02' => 'シンイチ',
        ),
        'company_name' => 'ロックオン',
        'zip' => array(
            'zip01' => '530',
            'zip02' => '0001',
        ),
        'address' => array(
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
        ),
        'tel' => array(
            'tel01' => '012',
            'tel02' => '345',
            'tel03' => '6789',
        ),
        'fax' => array(
            'fax01' => '112',
            'fax02' => '345',
            'fax03' => '6789',
        ),
        'email' => 'default@example.com',
        'sex' => 1,
        'job' => 1,
        'birth' => '1983-02-14',
        'password' => array(
            'first' => 'password',
            'second' => 'password',
        ),
        'reminder' => 1,
        'reminder_answer' => 'なし',
        'mailmaga_flg' => 1,
    );

    public function setUp()
    {
        parent::setUp();

        $this->app = new \Eccube\Application(array(
            'env' => 'test',
        ));

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
