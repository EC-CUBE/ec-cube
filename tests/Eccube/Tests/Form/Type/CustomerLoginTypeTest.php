<?php

namespace Eccube\Tests\Form\Type;

class CustomerLoginTypeTest extends AbstractTypeTestCase
{

    /** @var \Eccube\Application */
    protected $app;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'login_email' => 'default@example.com',
        'login_pass' => 'dummypass',
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('customer_login', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

}
