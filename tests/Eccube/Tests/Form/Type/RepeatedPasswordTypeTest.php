<?php

declare(strict_types=1);

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

use Eccube\Form\Type\RepeatedPasswordType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;

final class RepeatedPasswordTypeTest extends AbstractTypeTestCase
{
    protected ?FormInterface $form = null;

    /** @var array デフォルト値（正常系）を設定 */
    protected ?array $formData = [
        'password' => [
            'first' => 'eccube1@example.com',
            'second' => 'eccube1@example.com',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('password', RepeatedPasswordType::class, [])
            ->getForm();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->form = null;
    }

    /**
     * 同じパスワードを両欄に設定して送信するヘルパ.
     */
    private function submitPassword(string $password): void
    {
        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidNotSameValue()
    {
        $this->formData['password']['second'] = 'eccube3@example.com';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNotBlank()
    {
        $this->submitPassword('');

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLengthMin()
    {
        // NIST SP 800-63B-4 対応で min は 15. min-1 文字は不可.
        $password = str_repeat('a', $this->eccubeConfig['eccube_password_min_len'] - 1);
        $this->submitPassword($password);

        $this->assertFalse($this->form->isValid());
    }

    public function testValidLengthMin()
    {
        $password = str_repeat('a', $this->eccubeConfig['eccube_password_min_len']);
        $this->submitPassword($password);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidLengthMax()
    {
        $password = str_repeat('a', $this->eccubeConfig['eccube_password_max_len'] + 1);
        $this->submitPassword($password);

        $this->assertFalse($this->form->isValid());
    }

    /**
     * NIST SP 800-63B-4 では文字種の複雑さを求めないため, ひらがなのみでも有効.
     */
    public function testValidHiragana()
    {
        $password = str_repeat('あ', $this->eccubeConfig['eccube_password_min_len']);
        $this->submitPassword($password);

        $this->assertTrue($this->form->isValid());
    }

    /**
     * 英字のみ(数字・記号なし)でも有効.
     */
    public function testValidAlphabetOnly()
    {
        $password = str_repeat('a', $this->eccubeConfig['eccube_password_min_len']);
        $this->submitPassword($password);

        $this->assertTrue($this->form->isValid());
    }

    /**
     * 数字のみでも有効.
     */
    public function testValidNumericOnly()
    {
        $password = '987654321098765';
        $this->submitPassword($password);

        $this->assertTrue($this->form->isValid());
    }

    /**
     * パスワード中のスペースは許可される.
     */
    public function testValidContainsSpace()
    {
        $password = 'correct horse battery staple';
        $this->submitPassword($password);

        $this->assertTrue($this->form->isValid());
    }

    /**
     * 制御文字(改行・タブ)を含む場合は不可.
     */
    public function testInvalidControlCharacter()
    {
        $password = "abcdefghijklmno\nabc";
        $this->submitPassword($password);

        $this->assertFalse($this->form->isValid());
    }

    /**
     * ブロックリストに掲載されたパスワードは不可.
     */
    public function testInvalidBlocklisted()
    {
        $this->submitPassword('passwordpassword');

        $this->assertFalse($this->form->isValid());
    }
}
