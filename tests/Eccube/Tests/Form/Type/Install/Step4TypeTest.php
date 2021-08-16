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
        $this->markTestIncomplete('Can not support of FormInterface::submit()');

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
        //var_dump($this->form->getErrorsAsString());die();
    }
}
