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

class API_AddrFromZip extends SC_Api_Abstract_Ex {

    protected $operation_name = 'AddrFromZip';
    protected $operation_description = '';
    protected $default_auth_types = self::API_AUTH_TYPE_REFERER;
    protected $default_enable = '1';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

    public function __construct() {
        parent::__construct();
        $this->operation_description = t('c_Search for an address from the postal code._01');
    }

    public function doAction($arrParam) {
        $arrRequest = $this->doInitParam($arrParam);
        if (!$this->isParamError()) {
            $zipcode = $arrRequest['zip1'] . $arrRequest['zip2'];
            $arrAddrList = SC_Utils_Ex::sfGetAddress($zipcode); 
            if (!SC_Utils_Ex::isBlank($arrAddrList)) {
                $this->setResponse('Address', array(
                            'State' => $arrAddrList[0]['state'],
                            'City' => $arrAddrList[0]['city'],
                            'Town' => $arrAddrList[0]['town'],
                        )
                    );
                return true;
            }
        }
        return false;
    }

    protected function lfInitParam(&$objFormParam) {
        $objFormParam->addParam(t('c_Postal code 1_01'), 'zip1', ZIP01_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Postal code 2_01'), 'zip2', ZIP02_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    public function getResponseGroupName() {
        return 'AddressResponse';
    }
}
