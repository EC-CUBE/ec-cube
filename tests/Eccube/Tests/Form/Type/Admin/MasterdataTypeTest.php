<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class MasterdataTypeTest extends AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array();

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('admin_system_masterdata', null, array(
                'csrf_protection' => false,
            ))
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
            $actual = $choice->data;
            $this->assertStringStartsWith($expect, $actual);
        }
    }
}
