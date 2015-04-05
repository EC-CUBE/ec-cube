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

namespace Eccube\Page\Admin\OwnersStore;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;

/**
 * オーナーズストア：モジュール管理のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Module extends AbstractAdminPage
{
    public $tpl_subno = 'index';

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->tpl_mainpage = 'ownersstore/module.tpl';
        $this->tpl_mainno   = 'ownersstore';
        $this->tpl_subno    = 'module';
        $this->tpl_maintitle = 'オーナーズストア';
        $this->tpl_subtitle = 'モジュール管理';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        // nothing.
    }
}
