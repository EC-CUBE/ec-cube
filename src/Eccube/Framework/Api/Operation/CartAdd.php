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

/**
 * APIの基本クラス
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 */
class CartAdd extends Base
{
    protected $operation_name = 'CartAdd';
    protected $operation_description = 'カートへの商品追加';
    protected $default_auth_types = self::API_AUTH_TYPE_SESSION_TOKEN;
    protected $default_enable = '0';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

    public function doAction($arrParam)
    {
        $this->arrResponse = array(
            'Version' => ECCUBE_VERSION);

        return true;
    }

    public function getRequestValidate()
    {
        return;
    }

    protected function lfInitParam(&$objFormParam)
    {
    }

    public function getResponseGroupName()
    {
        return 'VersionResponse';
    }
}
