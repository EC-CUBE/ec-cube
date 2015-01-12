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

namespace Eccube\Page\Contact;

use Eccube\Application;
use Eccube\Page\AbstractPage;

/**
 * 問い合わせ(完了ページ) のページクラス.
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
        $this->tpl_title = 'お問い合わせ(完了ページ)';
        $this->tpl_mainno = 'contact';
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
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        // do nothing...

    }
}
