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
use Eccube\Framework\CartSession;
use Eccube\Framework\View;
use Eccube\Framework\Helper\PluginHelper;

class SiteView extends View
{
    public function __construct($setPrevURL = true, $device = DEVICE_TYPE_PC)
    {
        parent::__construct();

        switch ($device) {
            case DEVICE_TYPE_MOBILE:
                $this->_smarty->template_dir = realpath(MOBILE_TEMPLATE_REALDIR);
                $this->_smarty->compile_dir = realpath(MOBILE_COMPILE_REALDIR);
                $this->assignTemplatePath(DEVICE_TYPE_MOBILE);
                break;

            case DEVICE_TYPE_SMARTPHONE:
                $this->_smarty->template_dir = realpath(SMARTPHONE_TEMPLATE_REALDIR);
                $this->_smarty->compile_dir = realpath(SMARTPHONE_COMPILE_REALDIR);
                $this->assignTemplatePath(DEVICE_TYPE_SMARTPHONE);
                break;

            case DEVICE_TYPE_PC:
                $this->_smarty->template_dir = realpath(TEMPLATE_REALDIR);
                $this->_smarty->compile_dir = realpath(COMPILE_REALDIR);
                $this->assignTemplatePath(DEVICE_TYPE_PC);
                break;
        }

        if ($setPrevURL) {
            $this->setPrevURL();
        }

        PluginHelper::hook("SiteView.Construct.After", array($this));
    }

    public function setPrevURL()
    {
        /* @var $objCartSess CartSession */
        $objCartSess = Application::alias('eccube.cart_session');
        $objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
    }
}
