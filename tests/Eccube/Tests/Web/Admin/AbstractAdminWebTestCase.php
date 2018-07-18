<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web\Admin;

use Eccube\Tests\Web\AbstractWebTestCase;

abstract class AbstractAdminWebTestCase extends AbstractWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->logIn();
    }

    /**
     * @deprecated \Eccube\Tests\Web\AbstractWebTestCase::loginTo() を使用してください.
     */
    public function logIn($user = null)
    {
        if (!is_object($user)) {
            $user = $this->createMember();
        }

        $this->loginTo($user);

        return $user;
    }
}
