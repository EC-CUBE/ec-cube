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

namespace Eccube\Tests\Form\Validator;

use Eccube\Form\Validator\Email;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailValidatorTest extends AbstractTypeTestCase
{
    /** @var ValidatorInterface */
    protected $validator;

    public function setUp()
    {
        parent::setUp();
        $this->validator = self::$container->get('validator');
    }

    /**
     * @dataProvider EmailProvider
     */
    public function testValidateEmailStrict($email, $rfc, $norfc)
    {
        $constraint = new Email(['strict' => true]);
        $validator = $this->validator;

        $errors = $validator->validate($email, $constraint);
        self::assertSame($rfc, count($errors) === 0);
    }

    /**
     * @dataProvider EmailProvider
     */
    public function testValidateEmailNoStrict($email, $rfc, $norfc)
    {
        $constraint = new Email(['strict' => false]);
        $validator = $this->validator;

        $errors = $validator->validate($email, $constraint);
        self::assertSame($norfc, count($errors) === 0);
    }

    /**
     * @return array[email, rfc result, no rfc result]
     */
    public function EmailProvider()
    {
        return [
            ['test@example.com', true, true],
            ['test.@example.com', false, true],
            ['tes..t@example.com', false, true],
            ['test@@example.com', false, false],
            ['test@test@example.com', false, false],
        ];
    }
}
