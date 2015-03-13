<?php

namespace Eccube\Tests\Controller;

use Eccube\Form\Type\ContactType;
use Symfony\Component\Form\Test\TypeTestCase;

class ContactTypeTest extends TypeTestCase
{
	// デフォルト値（正常系）を設定
	private $formData = array(
            'name01' => 'たかはし',
            'name02' => 'しんいち',
            'kana01' => 'タカハシ',
            'kana02' => 'シンイチ',
            'zip01' => '530',
            'zip02' => '0001',
            // カスタムフィールドタイプ
            'pref' => array(
            	'pref' => 5
            ),
            'addr01' => '北区',
            'addr02' => '梅田',
            'tel01' => '012',
            'tel02' => '345',
            'tel03' => '6789',
            'email' => 'shinichi_takahashi@lockon.co.jp',
            'contents' => 'お問い合わせ内容テスト',
        );

    public function setUp() {
        parent::setUp();
        
        $this->app = new \Eccube\Application;

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
              ->createBuilder('contact', null, array(
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

    public function testInvalidZip01_NotBlank()
    {
        $this->formData['zip01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip01_LengthMin()
    {
        $this->formData['zip01'] = '1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip01_LengthMax()
    {
        $this->formData['zip01'] = '1234';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_NotBlank()
    {
        $this->formData['zip02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_LengthMin()
    {
        $this->formData['zip02'] = '1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_LengthMax()
    {
        $this->formData['zip02'] = '12345';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPref_NotBlank()
    {
        $this->formData['pref'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPref_Invalid()
    {
        $this->formData['pref']['pref'] = '100';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr01_NotBlank()
    {
        $this->formData['addr01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr02_NotBlank()
    {
        $this->formData['addr02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel01_NotBlank()
    {
        $this->formData['tel01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel01_LengthMin()
    {
        $this->formData['tel01'] = '1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel01_LengthMax()
    {
        $this->formData['tel01'] = '1234';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_NotBlank()
    {
        $this->formData['tel02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_LengthMin()
    {
        $this->formData['tel02'] = '1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_LengthMax()
    {
        $this->formData['tel02'] = '12345';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_NotBlank()
    {
        $this->formData['tel03'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_LengthMin()
    {
        $this->formData['tel03'] = '1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_LengthMax()
    {
        $this->formData['tel03'] = '12345';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_NotBlank()
    {
        $this->formData['email'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_Invalid()
    {
        $this->formData['email'] = 'sample.example.com';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidContents_NotBlank()
    {
        $this->formData['contents'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

}