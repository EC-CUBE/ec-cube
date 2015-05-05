<?php

namespace Eccube\Tests\Form\Type;

class ClacRuleTypeTest extends AbstractTypeTestCase
{

    /** @var \Eccube\Application */
    protected $app;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array();

    public function setUp()
    {
        parent::setUp();

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
