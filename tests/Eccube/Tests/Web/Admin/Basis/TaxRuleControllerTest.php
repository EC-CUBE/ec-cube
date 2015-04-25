<?php
namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

class TaxRuleControllerTest extends WebTestCase
{

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = new Application(array(
            'env' => 'test',
        ));

        return $app;
    }

    public function test_routeing_AdminBasisTax_index()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('admin_basis_tax_rule'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminBasisTax_edit()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']
            ->generate('admin_basis_tax_rule_edit', array('tax_rule_id' => 0))
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminBasisTax_delete()
    {
        $TaxRule = $this->app['eccube.repository.tax_rule']->newTaxRule();
        $TaxRule->setMemberId(1);
        $TaxRule->setTaxRate(10);
        $TaxRule->setApplyDate(new \DateTime());
        $this->app['orm.em']->persist($TaxRule);
        $this->app['orm.em']->flush();

        $redirectUrl = $this->app['url_generator']->generate('admin_basis_tax_rule');

        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']
            ->generate('admin_basis_tax_rule_delete', array('tax_rule_id' => $TaxRule->getId()))
        );

        $actual = $client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }
}
