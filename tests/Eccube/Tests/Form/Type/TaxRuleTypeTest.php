<?php

namespace Eccube\Tests\Form\Type;

use Acme\TestBundle\Form\Type\TestedType;
use Acme\TestBundle\Model\TestObject;
use Symfony\Component\Validator\Constraints\DateTime;

class TaxRuleTypeTest extends AbstractTypeTestCase
{

    /** @var \Eccube\Application */
    protected $app;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'tax_rate' => 8,
        'calc_rule' => 1,
        'apply_date' => '2014-04-01 00:00',
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('tax_rule', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function test_getName_validTaxRule()
    {
        $this->assertSame('tax_rule', $this->form->getName());
    }

}
