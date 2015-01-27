<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Admin;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\Response;
use Eccube\Framework\Session;

/**
 * ログアウト のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Logout extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        $this->lfDoLogout();

        // ログイン画面に遷移
        Application::alias('eccube.response')->sendRedirectFromUrlPath(ADMIN_DIR . DIR_INDEX_PATH);
    }

    /**
     * ログアウト処理
     *
     * @return void
     */
    public function lfDoLogout()
    {
        $objSess = new Session();
        $objSess->logout();
    }
}
