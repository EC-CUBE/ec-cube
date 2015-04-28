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

    public function test_getTemplateFile_DefaultTemplateFile_isValid(){
        $app = $this->createApplication();

        $actual = $app['eccube.repository.page_layout']
            ->getTemplateFile('mypage/change', 10);

        $expected = array(
            'file_name' =>'change.tpl',
            'tpl_data' => file_get_contents($app['config']['template_realdir'] . 'mypage/change.tpl')
        );

        $this->assertSame($actual, $expected);
    }

    public function tearDown()
    {
        $app = $this->createApplication();
        $app['orm.em']->getConnection()->close();
        parent::tearDown();
    }
}
