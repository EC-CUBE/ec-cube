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

use Eccube\Entity\Member;
use Eccube\Form\Type\Admin\MemberType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class MemberTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'name' => 'タカハシ',
        'department' => 'EC-CUBE事業部',
        'login_id' => 'takahashi',
        'plain_password' => [
            'first' => 'password',
            'second' => 'password',
        ],
        'Authority' => 1,
        'Work' => 1,
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(MemberType::class, new Member(), [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidNameNotBlank()
    {
        $this->formData['name'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNameMaxLengthInvalid()
    {
        $name = str_repeat('S', $this->eccubeConfig['eccube_stext_len']).'S';

        $this->formData['name'] = $name;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNameMaxLengthValid()
    {
        $name = str_repeat('S', $this->eccubeConfig['eccube_stext_len']);

        $this->formData['name'] = $name;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidDepartmentMaxLengthInvalid()
    {
        $department = str_repeat('S', $this->eccubeConfig['eccube_stext_len']).'S';

        $this->formData['department'] = $department;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDepartmentMaxLengthValid()
    {
        $department = str_repeat('S', $this->eccubeConfig['eccube_stext_len']);

        $this->formData['department'] = $department;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidLoginIdNotBlank()
    {
        $this->formData['login_id'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLoginIdAlnumCheck()
    {
        $this->formData['login_id'] = 'あいうえお';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPasswordNoBlank()
    {
        $this->formData['password']['first'] = '';
        $this->formData['password']['second'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPasswordInvalid()
    {
        $this->formData['password']['first'] = '12345';
        $this->formData['password']['second'] = '54321';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPasswordGraph()
    {
        $this->formData['password']['first'] = 'あいうえお';
        $this->formData['password']['second'] = 'あいうえお';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAuthorityNotBlank()
    {
        $this->formData['Authority'] = null;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAuthorityInvalid()
    {
        $Authority = $this->entityManager->getRepository('Eccube\Entity\Master\Authority')
            ->findOneBy([], ['id' => 'DESC']);
        $id = $Authority->getId() + 1;

        $this->formData['Authority'] = $id;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidWorkNotBlank()
    {
        $this->formData['Work'] = null;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidWorkInvalid()
    {
        $Work = $this->entityManager->getRepository('Eccube\Entity\Master\Work')
            ->findOneBy([], ['id' => 'DESC']);
        $id = $Work->getId() + 1;

        $this->formData['Work'] = $id;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testLoginIdNotChanged()
    {
        $Member = $this->createMember();
        $loginId = $Member->getLoginId();

        $this->form = $this->formFactory
            ->createBuilder(MemberType::class, $Member, [
                'csrf_protection' => false,
            ])
            ->getForm();

        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());

        // login_idが変更されないことを確認
        $this->assertSame($Member->getLoginId(), $loginId);
    }
}
