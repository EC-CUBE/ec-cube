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

namespace Eccube\Tests\Form\Type;

use Eccube\Form\Type\Admin\CalendarType;
use Symfony\Component\Form\FormInterface;

class CalendarTypeTest extends AbstractTypeTestCase
{
    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'title' => 'タイトル',
        'holiday' => [
            'year' => '2021',
            'month' => '03',
            'day' => '18',
        ],
    ];

    /** @var FormInterface */
    protected $form;

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(CalendarType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function testValidGetName()
    {
        $this->assertSame('calendar', $this->form->getName());
    }

    public function testValidFormData()
    {
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidTitle_Blank()
    {
        $this->formData['title'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidHoliday_Blank()
    {
        $this->formData['holiday'] = [];

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
