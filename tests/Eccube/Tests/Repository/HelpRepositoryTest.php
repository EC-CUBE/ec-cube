<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Entity\Help;

/**
 * HelpRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class HelpRepositoryTest extends EccubeTestCase
{

    public function testGet()
    {
        $Help = $this->app['eccube.repository.help']->get();

        $this->expected = 1;
        $this->actual = $Help->getId();
        $this->verify();
    }

    public function testGetWithId()
    {
        $Help = $this->app['eccube.repository.help']->get(1);

        $this->expected = 1;
        $this->actual = $Help->getId();
        $this->verify();

        // MySQL では成功するが, PostgreSQL ではエラーになってしまう
        // $Help = $this->app['eccube.repository.help']->get('a');
        // $this->assertNull($Help);

        $Help = $this->app['eccube.repository.help']->get(5);
        $this->assertNull($Help);
    }
}
