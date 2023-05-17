<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Page\Admin;

use Codeception\Util\Fixtures;
use Page\AbstractPage;

abstract class AbstractAdminPage extends AbstractPage
{
    /**
     * ページに移動。
     *
     * @param $url string URL
     * @param $pageTitle string ページタイトル
     *
     * @return $this
     */
    protected function goPage($url, $pageTitle = '')
    {
        $config = Fixtures::get('config');
        $this->tester->amOnPage('/'.$config['eccube_admin_route'].$url);

        if ($pageTitle) {
            return $this->atPage($pageTitle);
        }

        return $this;
    }

    /**
     * ページに移動しているかどうか確認。
     *
     * @param $pageTitle string ページタイトル
     *
     * @return $this
     */
    protected function atPage($pageTitle)
    {
        $this->tester->see($pageTitle, '.c-container .c-pageTitle__titles');

        return $this;
    }
}
