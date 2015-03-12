<?php

namespace Eccube\Tests\Controller;

use Eccube\Form\Type\ContactType;
use Symfony\Component\Form\Test\TypeTestCase;

class ContractControllerTest extends TypeTestCase
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

	public function testSubmitValidData()
	{
		$app = new \Eccube\Application;

		// CSRF tokenを無効にしてFormを作成
        $form = $app['form.factory']
              ->createBuilder('contact', null, array(
              		'csrf_protection' => false,
              	))
              ->getForm();

        $form->submit($this->formData);

        // Submitが正常か
        $this->assertTrue($form->isSynchronized());
        // Validationにひっかかってないか
        $this->assertTrue($form->isValid());
	}

	public function testSubmitInvalidNam01()
	{
	}


}