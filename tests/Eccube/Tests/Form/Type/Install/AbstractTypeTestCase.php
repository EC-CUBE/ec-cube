<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Form\Type\Install;

use Eccube\Tests\Mock\CsrfTokenManagerMock;
use PHPUnit\Framework\TestCase;

abstract class AbstractTypeTestCase extends TestCase
{
    public function setUp()
    {
        $this->markTestIncomplete('Eccube\Application に依存しないようにする');
        parent::setUp();

        $this->app = new \Eccube\InstallApplication();
        $this->app['session.test'] = true;
        unset($this->app['exception_handler']);
        $this->app['csrf.token_manager'] = function () {
            return new CsrfTokenManagerMock();
        };
        $this->app->boot();
    }

    protected function tearDown()
    {
        parent::tearDown();

        // 初期化
        $this->app = null;
        $this->form = null;
        $this->formData = null;
    }
}
