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

namespace Eccube\Tests\Form\Type\Install;

use Eccube\Form\Type\Install\Step4Type;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class Step4TypeTest extends AbstractTypeTestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * @var array デフォルト値を設定
     */
    protected $formData = [
        'database' => '',
        'database_host' => '',
        'database_port' => '',
        'database_name' => '',
        'database_user' => '',
        'database_password' => '',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->form = $this->formFactory
            ->createBuilder(Step4Type::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    // DB への接続チェックも行われてしまうので、テストが難しい
    public function testInvalidData()
    {
        // Request に依存しているため WebTest で代替する
        $this->markTestSkipped('Can not support of FormInterface::submit()');

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
        //var_dump($this->form->getErrorsAsString());die();
    }
}
