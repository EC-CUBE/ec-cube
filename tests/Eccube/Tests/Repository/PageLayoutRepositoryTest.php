<?php
namespace Eccube\Tests\Repository;

use Eccube\Application;

class PageLayoutRepositoryTest extends \PHPUnit_Framework_TestCase
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

/* privateなMethodにしたのでテストは別途考える
    public function test_getNewPageId()
    {
        $app = $this->createApplication();

        $actual = $app['eccube.repository.page_layout']
            ->getNewPageId($app['config']['device_type_pc']);
        $expected = 29;
        $this->assertSame($actual, $expected);

    }
*/

    public function test_findOrCreate_pageIdNullisCreate()
    {
        $app = $this->createApplication();

        $expected = null;

        $PageLayout = $app['eccube.repository.page_layout']
            ->findOrCreate(null, $app['config']['device_type_pc']);
        $actual = $PageLayout->getUrl();

        $this->assertSame($actual, $expected);
    }

    public function test_findOrCreate_findTopPage()
    {
        $app = $this->createApplication();

        $expected = array(
            'url' => 'index.php',
            'device_type_id' => $app['config']['device_type_pc'],
        );

        $PageLayout = $app['eccube.repository.page_layout']
            ->findOrCreate(1, $app['config']['device_type_pc']);
        $actual = array(
            'url' => $PageLayout->getUrl(),
            'device_type_id' => $PageLayout->getDeviceTypeId(),
        );

        $this->assertSame($actual, $expected);
    }

    public function test_findOrCreate_findMobileMyPage()
    {
        $app = $this->createApplication();

        $expected = array(
            'url' => 'mypage/index.php',
            'device_type_id' => $app['config']['device_type_mobile'],
        );

        $PageLayout = $app['eccube.repository.page_layout']
            ->findOrCreate(6, $app['config']['device_type_mobile']);
        $actual = array(
            'url' => $PageLayout->getUrl(),
            'device_type_id' => $PageLayout->getDeviceTypeId(),
        );

        $this->assertSame($actual, $expected);
    }

    public function test_findOrCreate_findSmartphoneProduct()
    {
        $app = $this->createApplication();

        $expected = array(
            'url' => 'products/list.php',
            'device_type_id' => $app['config']['device_type_smartphone'],
        );

        $PageLayout = $app['eccube.repository.page_layout']
            ->findOrCreate(2,  $app['config']['device_type_smartphone']);
        $actual = array(
            'url' => $PageLayout->getUrl(),
            'device_type_id' => $PageLayout->getDeviceTypeId(),
        );

        $this->assertSame($actual, $expected);
    }

    /* FIXME: CI環境で定数が整っていないのでコケるひとまずコメントアウト
    public function test_getTemplateFile_DefaultTemplateFile_isValid()
    {
        $app = $this->createApplication();

        $actual = $app['eccube.repository.page_layout']
            ->getTemplateFile('mypage/change', 10);

        $expected = array(
            'file_name' =>'change.tpl',
            'tpl_data' => file_get_contents($app['config']['template_realdir'] . 'mypage/change.tpl')
        );

        $this->assertSame($actual, $expected);
    }
    */

    public function tearDown()
    {
        $app = $this->createApplication();
        $app['orm.em']->getConnection()->close();
        parent::tearDown();
    }
}
