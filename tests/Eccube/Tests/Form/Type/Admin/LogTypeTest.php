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
        $this->logTest = self::$container->getParameter('kernel.logs_dir').'/test/'.$this->fileName;

        // Check and create the file to test if it does not exist
        if (!file_exists($this->logTest)) {
            @mkdir(dirname($this->logTest));
            file_put_contents($this->logTest, 'Lorem Ipsum is simply dummy text ...');
        }

        $this->formData = [
            'files' => $this->fileName,
            'line_max' => '50',
        ];

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(LogType::class, null, [
                'csrf_protection' => false,
            ])
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
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidNonexistentFile()
    {
        $this->formData['files'] = 'hogehogehogehoge';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNotNumber()
    {
        $this->formData['line_max'] = 'abcdefg';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
