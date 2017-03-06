<?php

namespace Eccube\Tests\Plugin\Web;

use Eccube\Event\EccubeEvents;

/**
 * @group plugin
 */
class BlockControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        // FIXME サブリクエストの場合はイベントハンドラがコールされていない？
        $this->markTestIncomplete('Event Handler is not implemented.');
        $client = $this->createClient();
        $crawler = $client->request(
            'GET',
            $this->app->path('block_search_product')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());

        $hookpoins = array(
            EccubeEvents::FRONT_BLOCK_SEARCH_PRODUCT_INDEX_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }
}
