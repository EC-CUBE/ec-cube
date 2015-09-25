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

class ProductTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'name' => 'テスト商品(なべ)',
        'description_detail' => 'テスト商品詳細説明_詳細画面用',
        'description_list' => 'テスト商品詳細説明_リスト画面用',
        'Category' => 1, // カテゴリ インテリア選択
        'search_word' => 'テスト, 商品',
        'free_area' => 'テスト フリーエリア',
        'Status' => 1, // 公開選択
        'class'=> array(
            'product_type' => 1,
            'price01' => '2000',
            'price02' => '1000',
            'stock_unlimited' => 1, // 無制限選択
            'code' => 'test_product01',
            'sale_limit' => '100',
            'delivery_date' => null, // 指定なし
            'delivery_fee' => '500',
        ),
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        // 商品登録・編集
        $this->form = $this->app['form.factory']
            ->createBuilder('admin_product', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPrice1_Minus()
    {
        $this->formData['class']['price01'] = '-2000';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice2_Minus()
    {
        $this->formData['class']['price02'] = '-1000';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
