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

abstract class AbstractAdminPageStyleGuide extends AbstractAdminPage
{
    /**
     * ページに移動しているかどうか確認。
     *
     * @param $pageTitle string ページタイトル
     *
     * @return $this
     */
    protected function atPage($pageTitle)
    {
        if ($pageTitle === '権限管理システム設定' || $pageTitle === '支払方法一覧店舗設定') {
            $this->tester->wait(3);
        }
        $this->tester->see($pageTitle, '.c-pageTitle');

        return $this;
    }
}
