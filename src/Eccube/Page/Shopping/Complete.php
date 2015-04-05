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

namespace Eccube\Page\Shopping;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Helper\DbHelper;

/**
 * ご注文完了 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Complete extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_title = 'ご注文完了';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
        $this->action();
        $this->sendResponse();
        // プラグインなどで order_id を取得する場合があるため,  ここで unset する
        unset($_SESSION['order_id']);
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        $this->arrInfo = Application::alias('eccube.helper.db')->getBasisData();
    }

    /**
     * 決済モジュールから遷移する場合があるため, トークンチェックしない.
     */
    public function doValidToken()
    {
        // nothing.
    }
}
