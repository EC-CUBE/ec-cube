<?php
namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

class TaxRuleControllerTest extends WebTestCase
{

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = new Application(array(
            'env' => 'test',
        ));

        return $app;
    }

    public function test_routeing_AdminContentPage_index()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('admin_content_page'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminContentPage_edit()
    {
        $client = $this->createClient();
        $client->request('GET',
            $this->app['url_generator']
                ->generate('admin_content_page_edit',
                    array('page_id' => 1)));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminContentPage_editWithDevice()
    {
        $client = $this->createClient();
        $client->request('GET',
            $this->app['url_generator']
                ->generate('admin_content_page_edit_withDevice',
                    array('page_id' => 1, 'device_id' => 10)));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function test_routeing_AdminContentPage_delete()
    {
        $redirectUrl = $this->app['url_generator']->generate('admin_content_page');

        $client = $this->createClient();
        $client->request('GET',
            $this->app['url_generator']
                ->generate('admin_content_page_delete',
                    array('page_id' => 1)));

        $actual = $client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }

    public function test_routeing_AdminContentPage_deleteWithDevice()
    {
        $redirectUrl = $this->app['url_generator']->generate('admin_content_page');

        $client = $this->createClient();
        $client->request('GET',
            $this->app['url_generator']
                ->generate('admin_content_page_delete_withDevice',
                    array('page_id' => 1, 'device_id' => 10)));

        $actual = $client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }
}
