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
class Operation extends Base
{
    protected $operation_name = 'Default';
    protected $operation_description = 'Default Operation';
    protected $default_auth_types = '99';
    protected $default_enable = '1';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

    public function doAction($arrParam)
    {
        $this->arrResponse = array('DefaultEmpty' => array());

        return true;
    }

    public function getRequestValidate()
    {
        return array('DefaultResponse' => array());
    }

    public function getResponseGroupName()
    {
        return 'DefaultResponse';
    }

    protected function lfInitParam(&$objFormParam)
    {
    }
}
