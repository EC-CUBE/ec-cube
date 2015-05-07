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


namespace Eccube\Page\Preview;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Session;
use Eccube\Framework\Helper\PageLayoutHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\View\SiteView;

/**
 * プレビュー のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractPage
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
        parent::process();

        $objView = new SiteView();
        $objSess = new Session();

        Utils::sfIsSuccess($objSess);

        if (isset($_SESSION['preview']) && $_SESSION['preview'] === 'ON') {
            // プレビュー用のレイアウトデザインを取得
            /* @var $objLayout PageLayoutHelper */
            $objLayout = Application::alias('eccube.helper.page_layout');
            $objLayout->sfGetPageLayout($this, true);

            // 画面の表示
            $objView->assignobj($this);
            $objView->display(SITE_FRAME);

            return;
        }
        Utils::sfDispSiteError(PAGE_ERROR);
    }
}
