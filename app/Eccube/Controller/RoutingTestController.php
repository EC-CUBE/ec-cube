<?php

namespace Eccube2\Controller;

use Eccube\Application;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RoutingTestController
{
    /**
     * // シングルコーテーションは読めないので注意.
     *
     * @Route("/{_admin}/test")
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAdmin(Application $app)
    {
        return "管理画面のルーティングは, /{_admin}/xxx を指定します.";
    }

    /**
     * @Route("/{_user_data}/test")
     *
     * @param Application $app
     * @return string
     */
    public function testUserData(Application $app)
    {
        return "UserDataのルーティングは, /{_user_data}/xxx を指定します";
    }
}
