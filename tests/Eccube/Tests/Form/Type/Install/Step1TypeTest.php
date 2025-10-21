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

namespace Eccube\Tests\Form\Type\Install;

use Eccube\Form\Type\Install\Step1Type;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Form\FormInterface;

class Step1TypeTest extends AbstractTypeTestCase
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * getValidTestData
     *
     * 正常系のデータパターンを返す
     *
     * @return array
     */
    public static function getValidTestData()
    {
        return [
            [
                'data' => [
                    'agree' => true,
                ],
            ],
            [
                'data' => [
                    'agree' => false,
                ],
            ],
            [
                'data' => [
                    'agree' => null,
                ],
            ],
            [
                'data' => [
                    'agree' => '',
                ],
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->markTestIncomplete(static::class.' は未実装です');
        parent::setUp();

        $this->form = $this->formFactory
            ->createBuilder(Step1Type::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    /**
     * @param mixed $data
     */
    #[DataProvider(methodName: 'getValidTestData')]
    public function testValidData($data)
    {
        $this->form->submit($data);
        $this->assertTrue($this->form->isValid());
    }
}
