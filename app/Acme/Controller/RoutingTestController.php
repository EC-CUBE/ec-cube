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

namespace Acme\Controller;

use Eccube\Application;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class RoutingTestController
{
    /**
     * // シングルコーテーションは読めないので注意.
     *
     * @Route("/{_admin}/test", name="admin_test")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Application $app
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAdmin(Application $app)
    {
        return '管理画面のルーティングは, /{_admin}/xxx を指定します.';
    }

    /**
     * @Route("/{_user_data}/test")
     *
     * @param Application $app
     *
     * @return string
     */
    public function testUserData(Application $app)
    {
        return 'UserDataのルーティングは, /{_user_data}/xxx を指定します';
    }
}
