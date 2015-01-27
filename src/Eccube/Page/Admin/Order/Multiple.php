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

namespace Eccube\Page\Admin\Order;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;

/**
 * 複数配送設定 のページクラス.
 *
 * @package Page
 * @author Kentaro Ohkouchi
 */
class Multiple extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'order/multiple.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = '';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = '複数配送設定';
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
        $this->setTemplate($this->tpl_mainpage);
    }
}
