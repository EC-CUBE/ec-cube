<?php

namespace Eccube\Tests\Web\Admin;

class PointController extends AbstractAdminWebTestCase
{
    public function testRoutingAdminBasisPoint()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/basis/point');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
