<?php

namespace Eccube\Tests\EventListener;

use Eccube\Tests\Web\AbstractWebTestCase;

class ForwardOnlyListenerTest extends AbstractWebTestCase
{

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    public function testForwardOnly()
    {
        try {
            $this->client->request('GET', $this->app->url("shopping_check_to_cart"));
            self::fail();
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {
            self::assertEquals('Eccube\Controller\ShoppingController:checkToCart is Forward Only', $e->getMessage());
        }
    }
}
