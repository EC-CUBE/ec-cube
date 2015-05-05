<?php
namespace Eccube\Tests\Web\Order;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class EditControllerTest extends AbstractAdminWebTestCase
{
    public function test_routeing_AdminOrderEdit_index()
    {
        $this->client->request(
            'GET',
            $this->app['url_generator']->generate('admin_order_edit', array('orderId' => 0)));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
