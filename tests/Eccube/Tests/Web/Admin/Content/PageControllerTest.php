<?php

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class PageControllerTest extends AbstractAdminWebTestCase
{

    public function test_routeing_AdminContentPage_index()
    {


        $this->client->request('GET', $this->app['url_generator']->generate('admin_content_page'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminContentPage_edit()
    {
        // TODO: テンプレートファイルの参照等がconstant.yml.distで定まらずCIで落ちるためスキップ
        self::markTestSkipped();



        $this->client->request('GET',
            $this->app['url_generator']
                ->generate('admin_content_page_edit',
                    array('page_id' => 1)));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminContentPage_editWithDevice()
    {
        // TODO: テンプレートファイルの参照等がconstant.yml.distで定まらずCIで落ちるためスキップ
        self::markTestSkipped();



        $this->client->request('GET',
            $this->app['url_generator']
                ->generate('admin_content_page_edit_withDevice',
                    array('page_id' => 1, 'device_id' => 10)));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminContentPage_delete()
    {


        $redirectUrl = $this->app['url_generator']->generate('admin_content_page');

        $this->client->request('GET',
            $this->app['url_generator']
                ->generate('admin_content_page_delete',
                    array('page_id' => 1)));

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }

    public function test_routeing_AdminContentPage_deleteWithDevice()
    {


        $redirectUrl = $this->app['url_generator']->generate('admin_content_page');

        $this->client->request('GET',
            $this->app['url_generator']
                ->generate('admin_content_page_delete_withDevice',
                    array('page_id' => 1, 'device_id' => 10)));

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }
}
