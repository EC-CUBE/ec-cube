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

namespace Eccube\Framework\View;

use Eccube\Application;
use Eccube\Framework\View\SiteView;

class SmartphoneView extends SiteView
{
    public function __construct($setPrevURL = true)
    {
        parent::__construct($setPrevURL);
    }

    public function init()
    {
        parent::init();

        $this->_smarty->template_dir = realpath(SMARTPHONE_TEMPLATE_REALDIR);
        $this->_smarty->compile_dir = realpath(SMARTPHONE_COMPILE_REALDIR);
        $this->assignTemplatePath(DEVICE_TYPE_SMARTPHONE);
    }
}
