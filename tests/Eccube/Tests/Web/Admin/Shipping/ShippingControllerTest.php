<?php

namespace Eccube\Tests\Web\Admin\Shipping;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class ShippingControllerTest extends AbstractAdminWebTestCase
{
    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_shipping')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
