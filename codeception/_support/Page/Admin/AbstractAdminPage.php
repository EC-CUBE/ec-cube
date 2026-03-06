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
        $adminUrl = '/'.$config['eccube_admin_route'].$url;

        if ($pageTitle) {
            // XXX amOnPage() をコール直後に selector を参照すると、遷移しない場合があるためリトライする
            $attempts = 0;
            $maxAttempts = 10;
            while ($attempts < $maxAttempts) {
                $this->tester->amOnPage($adminUrl);
                $this->tester->wait(1); // XXX 画面遷移直後は selector の参照に失敗するため wait を入れる
                $title = $this->tester->grabTextFrom('.c-pageTitle');

                if ($title != $pageTitle) {
                    $attempts++;
                    $this->tester->expect('遷移に失敗したためリトライします('.$attempts.'/'.$maxAttempts.')');
                    $this->tester->wait(1);
                } else {
                    return $this->atPage($pageTitle);
                }
            }
        } else {
            $this->tester->wait(5);
            $this->tester->waitForJS("return location.pathname + location.search == '{$adminUrl}'");
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
        $this->tester->waitForText($pageTitle, 10, '.c-container .c-pageTitle__titles');

        return $this;
    }
}
