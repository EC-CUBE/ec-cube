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

namespace Eccube\Page\Mypage;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Display;
use Eccube\Framework\Helper\PageLayoutHelper;

/**
 * 退会手続 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class RefusalComplete extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_title    = 'MYページ';

        if (Application::alias('eccube.display')->detectDevice() === DEVICE_TYPE_MOBILE) {
            $this->tpl_title .= '/退会手続き(完了ページ)';
        } else {
            $this->tpl_subtitle = '退会手続き(完了ページ)';
        }
        $this->tpl_navi     = Application::alias('eccube.helper.page_layout')->getTemplatePath(Application::alias('eccube.display')->detectDevice()) . 'mypage/navi.tpl';
        $this->tpl_mypageno = 'refusal';
        $this->point_disp   = false;
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
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
    }
}
