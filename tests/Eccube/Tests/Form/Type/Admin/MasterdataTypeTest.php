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

use Eccube\Form\Type\Admin\MasterdataType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class MasterdataTypeTest extends AbstractTypeTestCase
{
    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(MasterdataType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    /**
     * 本体のメタデータのみ取得できているかどうかのテスト.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1403
     */
    public function testEntityMetadata()
    {
        $view = $this->form['masterdata']->createView();
        $choices = $view->vars['choices'];

        $expect = 'Eccube-Entity';
        foreach ($choices as $choice) {
            $actual = $choice->value;
            $this->assertStringStartsWith($expect, $actual);
        }
    }
}
