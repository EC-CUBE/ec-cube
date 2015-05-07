<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
