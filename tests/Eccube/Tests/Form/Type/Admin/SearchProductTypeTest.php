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

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Form\Type\Admin\SearchProductType;
use Symfony\Component\Form\FormInterface;

class SearchProductTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(SearchProductType::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    /**
     * EC-CUBE 4.0.4 以前のバージョンで互換性を保つため yyyy-MM-dd のフォーマットもチェック
     *
     * @dataProvider dataFormDateProvider
     *
     * @param string $formName
     */
    public function testDateSearch(string $formName)
    {
        $formData = [
            $formName => '2020-07-09',
        ];

        $this->form->submit($formData);
        $this->assertTrue($this->form->isValid());
    }

    /**
     * Data provider date form test.
     *
     * @return array
     */
    public function dataFormDateProvider()
    {
        return [
            ['create_date_start'],
            ['update_date_start'],
            ['create_date_end'],
            ['update_date_end'],
        ];
    }

    /**
     * EC-CUBE 4.0.5 以降で yyyy-MM-dd HH:mm:ss のフォーマットでの検索機能を追加
     *
     * @dataProvider dataFormDateTimeProvider
     *
     * @param string $formName
     */
    public function testDateTimeSearch(string $formName)
    {
        $formData = [
            $formName => '2020-07-09 09:00:00',
        ];

        $this->form->submit($formData);
        $this->assertTrue($this->form->isValid());
    }

    /**
     * Data provider datetime form test.
     *
     * @return array
     */
    public function dataFormDateTimeProvider()
    {
        return [
            ['create_datetime_start'],
            ['update_datetime_start'],
            ['create_datetime_end'],
            ['update_datetime_end'],
        ];
    }
}
