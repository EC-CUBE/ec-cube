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

namespace Page;

abstract class AbstractPage
{
    /**
     * AbstractAdminPage constructor.
     */
    public function __construct(protected \AcceptanceTester $tester)
    {
    }

    /**
     * ページに移動。
     *
     * @param $url string URL
     *
     * @return $this
     */
    protected function goPage($url, mixed $pageTitle = '')
    {
        $this->tester->amOnPage($url);
        $this->tester->waitForJS("return location.pathname + location.search == '{$url}'", 30);

        return $this;
    }
}
