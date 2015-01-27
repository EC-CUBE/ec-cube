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

namespace Eccube\Framework\View;

use Eccube\Application;
use Eccube\Framework\View;

class AdminView extends View
{
    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        parent::init();

        $this->_smarty->template_dir = realpath(TEMPLATE_ADMIN_REALDIR);
        $this->_smarty->compile_dir = realpath(COMPILE_ADMIN_REALDIR);
        $this->assign('TPL_URLPATH_PC', ROOT_URLPATH . USER_DIR . USER_PACKAGE_DIR . TEMPLATE_NAME . '/');
        $this->assign('TPL_URLPATH_DEFAULT', ROOT_URLPATH . USER_DIR . USER_PACKAGE_DIR . DEFAULT_TEMPLATE_NAME . '/');
        $this->assign('TPL_URLPATH', ROOT_URLPATH . USER_DIR . USER_PACKAGE_DIR . 'admin/');
    }
}
