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
    /** @var \AcceptanceTester */
    protected $tester;

    /**
     * AbstractAdminPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        $this->tester = $I;
    }

    /**
     * ページに移動。
     *
     * @param $url string URL
     *
     * @return $this
     */
    protected function goPage($url, $pageTitle = '')
    {
        $this->tester->amOnPage('/'.$url);

        return $this;
    }
}
