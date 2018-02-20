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

use Eccube\Form\Type\Admin\LogType;

class LogTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData;

    protected $fileName;

    protected $logTest;

    public function setUp()
    {
        parent::setUp();

        $this->fileName = '_test_site_'.date('YmdHis').'.log';
        $this->logTest = $this->container->getParameter('kernel.logs_dir').'/test/'.$this->fileName;

        // Check and create the file to test if it does not exist
        if (!file_exists($this->logTest)) {
            @mkdir(dirname($this->logTest));
            file_put_contents($this->logTest, 'Lorem Ipsum is simply dummy text ...');
        }

        $this->formData = array(
            'files' => $this->fileName,
            'line_max' => '50',
        );

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(LogType::class, null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function tearDown()
    {
        // Delete the previously created file
        @unlink($this->logTest);

        parent::tearDown();
    }

    public function testValidData()
    {
        if(!file_exists($this->logTest)){
            $this->markTestSkipped('テスト時には'.$this->fileName.'は存在しないのでテストできない');
        }
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalid_NonexistentFile()
    {
        $this->formData['files'] = "hogehogehogehoge";
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_NotNumber()
    {
        $this->formData['line_max'] = "abcdefg";
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
