<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Interactions;

use Facebook\WebDriver\WebDriverAction;

class WaitAction implements WebDriverAction
{
    /**
     * @var int
     */
    private $timeout_in_second;

    /**
     * @param integer $timeout_in_second
     */
    public function __construct($timeout_in_second)
    {
        $this->timeout_in_second = $timeout_in_second;
    }

    public function perform()
    {
        sleep($this->timeout_in_second);
    }
}
