<?php
namespace Eccube\Tests\Repository\Master;

use Eccube\Application;

class PrefRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /*
     * たぶんMockを使うべき
     * http://symfony.com/doc/current/cookbook/testing/database.html
     */
    public function testfindMasterData()
    {
        $app = new Application();

        $app['debug'] = true;
        $app['session.test'] = true;
        $app['exception_handler']->disable();

        $pref = $app['orm.em']
            ->getRepository('Eccube\Entity\Master\Pref')
            ->findMasterData();

        $this->assertCount(48, $pref);
        $this->assertEquals('', $pref['']);
        $this->assertEquals('北海道', $pref['1']);
    }
}
