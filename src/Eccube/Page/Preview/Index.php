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

namespace Eccube\Page\Preview;

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
            $objLayout = new PageLayoutHelper();
            $objLayout->sfGetPageLayout($this, true);

            // 画面の表示
            $objView->assignobj($this);
            $objView->display(SITE_FRAME);

            return;
        }
        Utils::sfDispSiteError(PAGE_ERROR);
    }
}
