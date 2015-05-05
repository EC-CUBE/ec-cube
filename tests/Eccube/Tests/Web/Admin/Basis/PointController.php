<?php

namespace Eccube\Tests\Web\Admin\Basis;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class PointController extends AbstractAdminWebTestCase
{
    public function testRoutingAdminBasisPoint()
    {
        $this->client->request('GET', '/admin/basis/point');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
