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

use Eccube\Form\Validator\TwigLint;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TwigLintValidatorTest extends AbstractTypeTestCase
{
    /** @var ValidatorInterface */
    protected $validator;

    public function setUp()
    {
        parent::setUp();
        $this->validator = self::$container->get('validator');
    }

    public function testValidTemplate()
    {
        $constraint = new TwigLint();
        $validator = $this->validator;

        $value = '';
        $errors = $validator->validate($value, $constraint);
        self::assertCount(0, $errors);

        $value = null;
        $errors = $validator->validate($value, $constraint);
        self::assertCount(0, $errors);

        $value = '<div class="btn-default"></div>';
        $errors = $validator->validate($value, $constraint);
        self::assertCount(0, $errors);

        $value = '{{ var }}';
        $errors = $validator->validate($value, $constraint);
        self::assertCount(0, $errors);

        $value = '{% for product in products %}{% endfor %}';
        $errors = $validator->validate($value, $constraint);
        self::assertCount(0, $errors);

        $value = '{{ url("homepage") }}';
        $errors = $validator->validate($value, $constraint);
        self::assertCount(0, $errors);
    }

    public function testInValidTemplate()
    {
        $constraint = new TwigLint();
        $validator = $this->validator;

        $value = '{{ var }';
        $errors = $validator->validate($value, $constraint);
        self::assertCount(1, $errors);
        $message = $errors[0]->getMessage();
        self::assertContains('Unexpected "}" at line 1.', $message);

        $value = '{% for product in products %}{% endfo %}';
        $errors = $validator->validate($value, $constraint);
        self::assertCount(1, $errors);
        $message = $errors[0]->getMessage();
        self::assertContains(
            'Unexpected "endfo" tag (expecting closing tag for the "for" tag defined near line 1) at line 1.',
            $message
        );
    }
}
