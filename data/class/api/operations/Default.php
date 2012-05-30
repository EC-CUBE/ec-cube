<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

/**
 * APIの基本クラス
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
require_once CLASS_EX_REALDIR . 'api_extends/SC_Api_Abstract_Ex.php';

class API_Default extends SC_Api_Abstract_Ex {

    protected $operation_name = 'Default';
    protected $operation_description = 'Default Operation';
    protected $default_auth_types = '99';
    protected $default_enable = '1';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

    public function doAction($arrParam) {
        $this->arrResponse = array('DefaultEmpty' => array());
        return true;
    }

    public function getRequestValidate() {
        return array('DefaultResponse' => array());
    }

    public function getResponseGroupName() {
        return 'DefaultResponse';
    }

    protected function lfInitParam(&$objFormParam) {
    }
}
