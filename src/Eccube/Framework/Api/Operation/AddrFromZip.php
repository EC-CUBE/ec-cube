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

namespace Eccube\Framework\Api\Operation;

use Eccube\Application;
use Eccube\Framework\Util\Utils;

/**
 * APIの基本クラス
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 */
class AddrFromZip extends Base
{
    protected $operation_name = 'AddrFromZip';
    protected $operation_description = '郵便番号から住所を検索します。';
    protected $default_auth_types = self::API_AUTH_TYPE_REFERER;
    protected $default_enable = '1';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

    public function doAction($arrParam)
    {
        $arrRequest = $this->doInitParam($arrParam);
        if (!$this->isParamError()) {
            $zipcode = $arrRequest['zip1'] . $arrRequest['zip2'];
            $arrAddrList = Utils::sfGetAddress($zipcode);
            if (!Utils::isBlank($arrAddrList)) {
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

    protected function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('郵便番号1', 'zip1', ZIP01_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('郵便番号2', 'zip2', ZIP02_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    public function getResponseGroupName()
    {
        return 'AddressResponse';
    }
}
