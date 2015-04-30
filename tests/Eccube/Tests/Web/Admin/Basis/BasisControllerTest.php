<?php

namespace Eccube\Tests\Web\Admin;

class BasisControllerTest extends AbstractAdminWebTestCase
{

    public function testRoutingAdminBasis()
    {
        $this->logIn();

        $this->client->request('GET', $this->app['url_generator']->generate('admin_basis'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
