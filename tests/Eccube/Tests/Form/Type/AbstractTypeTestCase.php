<?php

namespace Eccube\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

abstract class AbstractTypeTestCase extends TypeTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->app = new \Eccube\Application(array(
            'env' => 'test',
        ));
        $this->app->boot();
    }

    protected function tearDown()
    {
        parent::tearDown();

        // 初期化
        $this->app = null;
        $this->form = null;
        $this->formData = null;
    }
}
