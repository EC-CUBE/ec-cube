<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\EventListener;

use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ForwardOnlyListenerTest extends AbstractWebTestCase
{
    public function testForwardOnly()
    {
        $this->markTestIncomplete('function shopping is not implement');
        try {
            $this->client->request('GET', $this->generateUrl('shopping_check_to_cart'));
            self::fail();
        } catch (AccessDeniedHttpException$e) {
            self::assertEquals('Eccube\Controller\ShoppingController:checkToCart is Forward Only', $e->getMessage());
        }
    }
}
