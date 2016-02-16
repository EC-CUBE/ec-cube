<?php

namespace Eccube\Tests\Plugin\Web\Admin\Content;

use Eccube\Event\EccubeEvents;
use Eccube\Tests\Plugin\Web\Admin\AbstractAdminWebTestCase;

class LayoutControllerTest extends AbstractAdminWebTestCase
{

    public function testIndex()
    {
        $this->client->request('GET', $this->app->url('admin_content_layout'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $hookpoins = array(
            EccubeEvents::ADMIN_CONTENT_LAYOUT_INDEX_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function testIndexWithPost()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url(
                'admin_content_layout',
                array('id' => 1)
            ),
            array(
                'form' => array(
                    '_token' => 'dummy'
                ),
                'name_1' => 'カゴの中',
                'id_1' => 2,
                'target_id_1' => 2,
                'top_1' => 2,
                'name_2' => '商品検索',
                'id_2' => 3,
                'target_id_2' => 3,
                'top_2' => 3,
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->app->url('admin_content_layout_edit', array('id' => 1))
        ));

        $hookpoins = array(
            EccubeEvents::ADMIN_CONTENT_LAYOUT_INDEX_INITIALIZE,
            EccubeEvents::ADMIN_CONTENT_LAYOUT_INDEX_COMPLETE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function testIndexWithPostPreview()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url(
                'admin_content_layout_preview',
                array('id' => 1)
            ),
            array(
                'form' => array(
                    '_token' => 'dummy'
                ),
                'name_1' => 'カゴの中',
                'id_1' => 2,
                'target_id_1' => 2,
                'top_1' => 2,
                'name_2' => '商品検索',
                'id_2' => 3,
                'target_id_2' => 3,
                'top_2' => 3,
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->app->url('homepage').'?preview=1'
        ));

        $hookpoins = array(
            EccubeEvents::ADMIN_CONTENT_LAYOUT_INDEX_INITIALIZE,
            EccubeEvents::ADMIN_CONTENT_LAYOUT_INDEX_COMPLETE,
        );
        $this->verifyOutputString($hookpoins);
    }
}
