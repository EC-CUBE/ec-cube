<?php

namespace Eccube\Tests\Web\Admin;

class TaxRuleControllerTest extends AbstractAdminWebTestCase
{

    public function test_routeing_AdminBasisTax_index()
    {
        $this->logIn();

        $this->client->request('GET', $this->app['url_generator']->generate('admin_basis_tax_rule'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminBasisTax_edit()
    {
        $this->logIn();

        $this->client->request('GET', $this->app['url_generator']
            ->generate('admin_basis_tax_rule_edit', array('tax_rule_id' => 0))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminBasisTax_delete()
    {
        $this->logIn();

        $redirectUrl = $this->app['url_generator']->generate('admin_basis_tax_rule');

        $this->client->request('GET', $this->app['url_generator']
            ->generate('admin_basis_tax_rule_delete', array('tax_rule_id' => 0))
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }
}
