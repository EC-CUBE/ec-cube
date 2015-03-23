<?php

namespace Eccube\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class CustomerLoginTypeTest extends TypeTestCase
{

    /** @var \Eccube\Application */
    private $app;

    /** @var array デフォルト値（正常系）を設定 */
    private $formData = array(
        'login_email' => 'default@example.com',
        'login_pass' => 'dummypass',
    );

    public function setUp()
    {
        parent::setUp();

        $this->app = new \Eccube\Application;

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
