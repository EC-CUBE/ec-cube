<?php

namespace Eccube\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class ClacRuleTypeTest extends TypeTestCase
{

    /** @var \Eccube\Application */
    private $app;

    /** @var array デフォルト値（正常系）を設定 */
    private $formData = array();

    public function setUp()
    {
        parent::setUp();

        $this->app = new \Eccube\Application(array(
            'env' => 'test',
        ));
        $this->app->boot();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('calc_rule', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function test_getName_is_calc_rule()
    {
        $this->assertSame('calc_rule', $this->form->getName());
    }

}