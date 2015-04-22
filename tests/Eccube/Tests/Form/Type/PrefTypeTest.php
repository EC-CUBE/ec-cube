<?php

namespace Eccube\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class PrefTypeTest extends TypeTestCase
{

    /** @var \Eccube\Application */
    private $app;

    /** @var \Symfony\Component\Form\FormInterface */
    private $form;

    /** @var array デフォルト値（正常系）を設定 */
    private $formData = array(
        'pref' => '5',
    );

    public function setUp()
    {
        parent::setUp();

        $this->app = new \Eccube\Application(array(
            'env' => 'test',
        ));
        $this->app->boot();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('form', null, array(
                'csrf_protection' => false,
            ))
            ->add('pref', 'pref')
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

}
