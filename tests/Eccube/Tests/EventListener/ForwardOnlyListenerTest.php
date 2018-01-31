<?php

namespace Eccube\Tests\EventListener;

use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ForwardOnlyListenerTest extends AbstractWebTestCase
{

    public function testForwardOnly()
    {
        $this->markTestIncomplete("function shopping is not implement");
        try {
            $this->client->request('GET', $this->generateUrl("shopping_check_to_cart"));
            self::fail();
        } catch (AccessDeniedHttpException$e) {
            self::assertEquals('Eccube\Controller\ShoppingController:checkToCart is Forward Only', $e->getMessage());
        }
    }
}
