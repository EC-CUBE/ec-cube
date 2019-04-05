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

namespace Eccube\Tests\Web\Block;

use Eccube\Tests\Web\AbstractWebTestCase;

class SearchProductControllerTest extends AbstractWebTestCase
{
    public function testRoutingCart()
    {
        $this->client->request('GET', '/block/search_product');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
