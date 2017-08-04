<?php

namespace Eccube\Tests\Form\Validator;

use Eccube\Application;
use Eccube\Form\Validator\TwigLint;
use Eccube\Form\Validator\TwigLintValidator;
use Eccube\Tests\EccubeTestCase;

class TwigLintValidatorTest extends EccubeTestCase
{
    /** @var Application */
    protected $app;

    public function testNewInstance()
    {
        $constraint = new TwigLint();
        self::assertInstanceOf(TwigLint::class, $constraint);

        $validator = new TwigLintValidator();
        self::assertInstanceOf(TwigLintValidator::class, $validator);
    }

    public function testValidTemplate()
    {
        $constraint = new TwigLint();
        $validator = $this->app['validator'];

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
    }

    public function testInValidTemplate()
    {
        $constraint = new TwigLint();
        $validator = $this->app['validator'];

        $value = '{{ var }';
        $errors = $validator->validate($value, $constraint);
        self::assertCount(1, $errors);
        $message = $errors[0]->getMessage();
        self::assertSame('Twigのフォーマットが正しくありません。Unexpected "}" at line 1.', $message);

        $value = '{% for product in products %}{% endfo %}';
        $errors = $validator->validate($value, $constraint);
        self::assertCount(1, $errors);
        $message = $errors[0]->getMessage();
        self::assertSame('Twigのフォーマットが正しくありません。Unexpected "endfo" tag (expecting closing tag for the "for" tag defined near line 1) at line 1.', $message);
    }
}
