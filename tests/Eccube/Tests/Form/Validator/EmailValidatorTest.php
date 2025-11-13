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
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailValidatorTest extends AbstractTypeTestCase
{
    protected ?ValidatorInterface $validator = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * @param mixed $email
     * @param mixed $rfc
     * @param mixed $norfc
     */
    #[DataProvider(methodName: 'EmailProvider')]
    public function testValidateEmailStrict($email, $rfc, $norfc)
    {
        $constraint = new Email(null, null, Email::VALIDATION_MODE_STRICT);
        $validator = $this->validator;

        $errors = $validator->validate($email, $constraint);
        $this->assertSame($rfc, count($errors) === 0);
    }

    /**
     * @param mixed $email
     * @param mixed $rfc
     * @param mixed $norfc
     */
    #[DataProvider(methodName: 'EmailProvider')]
    public function testValidateEmailNoStrict($email, $rfc, $norfc)
    {
        $constraint = new Email(null, null, Email::VALIDATION_MODE_HTML5);
        $validator = $this->validator;

        $errors = $validator->validate($email, $constraint);
        $this->assertSame($norfc, count($errors) === 0);
    }

    /**
     * @return array[email, rfc result, no rfc result]
     */
    public static function EmailProvider(): \Iterator
    {
        yield ['test@example.com', true, true];
        yield ['test.@example.com', false, true];
        yield ['tes..t@example.com', false, true];
        yield ['test@@example.com', false, false];
        yield ['test@test@example.com', false, false];
    }
}
