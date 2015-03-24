<?php

namespace Eccube\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class PointTypeTest extends TypeTestCase
{

    /** @var \Eccube\Application */
    private $app;

    /** @var array デフォルト値（正常系）を設定 */
    private $formData = array(
        'point_rate' => 10,
        'welcome_point' => 10,
    );

    public function setUp()
    {
        parent::setUp();

        $this->app = new \Eccube\Application;
        $this->app['debug'] = true;
        $this->app['session.test'] = true;
        $this->app['exception_handler']->disable();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('point', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPointRate_NotBlank()
    {
        $this->formData['point_rate'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
    
    public function testInvalidPointRate_Min()
    {
        $this->formData['point_rate'] = -1;
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
    
    public function testInvalidPointRate_Max()
    {
        $this->formData['point_rate'] = 101;
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
    
    public function testInvalidWelcomePoint_NotBlank()
    {
        $this->formData['welcome_point'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
    
    public function testInvalidWelcomePoint_Min()
    {
        $this->formData['welcome_point'] = -1;
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
    
    public function testInvalidWelcomePoint_Max()
    {
        $this->formData['point_rate'] = 1000000000;
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
