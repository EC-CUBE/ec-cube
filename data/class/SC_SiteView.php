<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

class SC_SiteView extends SC_View_Ex
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

        SC_Helper_Plugin_Ex::hook("SC_SiteView_Construct_After", array($this));
    }

    public function setPrevURL()
    {
            $objCartSess = new SC_CartSession_Ex();
            $objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
    }
}
