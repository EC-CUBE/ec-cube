<?php

namespace Eccube\Tests\Web\Admin;

class BlockControllerTest extends AbstractAdminWebTestCase
{

    public function test_routeing_AdminContentBlock_index()
    {
        $this->logIn();

        $this->client->request('GET', $this->app['url_generator']->generate('admin_content_block'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminContentBlock_edit()
    {
        // TODO: テンプレートファイルの参照等がconstant.yml.distで定まらずCIで落ちるためスキップ
        self::markTestSkipped();

        $this->logIn();

        $this->client->request('GET',
            $this->app['url_generator']
                ->generate('admin_content_block_edit',
                    array('block_id' => 1)));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminContentBlock_editWithDevice()
    {
        // TODO: テンプレートファイルの参照等がconstant.yml.distで定まらずCIで落ちるためスキップ
        self::markTestSkipped();

        $this->logIn();

        $this->client->request('GET',
            $this->app['url_generator']
                ->generate('admin_content_block_edit_withDevice',
                    array('page_id' => 1, 'device_type_id' => 10)));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminContentBlock_delete()
    {
        $this->logIn();

        $redirectUrl = $this->app['url_generator']->generate('admin_content_block');

        $this->client->request('GET',
            $this->app['url_generator']
                ->generate('admin_content_block_delete',
                    array('block_id' => 1)));

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }

    public function test_routeing_AdminContentBlock_deleteWithDevice()
    {
        $this->logIn();

        $redirectUrl = $this->app['url_generator']->generate('admin_content_block');

        $this->client->request('GET',
            $this->app['url_generator']
                ->generate('admin_content_block_delete_withDevice',
                    array('block_id' => 1, 'device_type_id' => 10)));

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }
}
