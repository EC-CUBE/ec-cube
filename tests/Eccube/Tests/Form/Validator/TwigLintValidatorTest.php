<?php

namespace Eccube\Tests\Form\Validator;

use Eccube\Application;
use Eccube\Form\Validator\TwigLint;
use Eccube\ServiceProvider\TwigLintServiceProvider;
use PHPUnit\Framework\TestCase;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;

class TwigLintValidatorTest extends TestCase
{
    /** @var Application */
    protected $app;

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        $app = new \Silex\Application();
        $app->register(new TwigServiceProvider());
        $app->register(new ValidatorServiceProvider());
        $app->register(new TwigLintServiceProvider());

        $this->app = $app;
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

        $value = '{{ url("homepage") }}';
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
