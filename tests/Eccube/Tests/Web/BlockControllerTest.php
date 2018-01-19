<?php

namespace Eccube\Tests\Web;

class BlockControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('block_search_product')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
