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

namespace Eccube\Tests\Repository;

use Eccube\Application;

/**
 * @deprecated Eccube\Tests\EccubeTestCase を使用してください
 */
class AbstractRepositoryTestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp() : void
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = new Application();
        $app->initialize();
        $app['session.test'] = true;
        $app['exception_handler']->disable();

        $app->boot();

        return $app;
    }
}
