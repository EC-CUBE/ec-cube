<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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


namespace Eccube\Page\Upgrade\Helper;

use Eccube\Application;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;

/**
 * Enter description here...
 *
 */
class JsonHelper extends Services_JSON
{
    /** */
    public $arrData = array(
        'status'  => null,
        'errcode' => null,
        'msg'     => null,
        'data'    => array()
    );

    /**
     * Enter description here...
     *
     * @return Services_JSON
     */
    public function __construct()
    {
        parent::Services_JSON();
    }

    /**
     * Enter description here...
     *
     */
    public function isError()
    {
        return $this->isSuccess() ? false : true;
    }

    public function isSuccess()
    {
        if ($this->arrData['status'] === OSTORE_STATUS_SUCCESS) {
            return true;
        }

        return false;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $errCode
     */
    public function setError($errCode)
    {
        $masterData = Application::alias('eccube.db.master_data');
        $arrOStoreErrMsg = $masterData->getMasterData('mtb_ownersstore_err');

        $this->arrData['status']  = OSTORE_STATUS_ERROR;
        $this->arrData['errcode'] = $errCode;
        $this->arrData['msg']  = isset($arrOStoreErrMsg[$errCode])
            ? $arrOStoreErrMsg[$errCode]
            : $arrOStoreErrMsg[OSTORE_E_UNKNOWN];
    }

    /**
     * Enter description here...
     *
     * @param mixed $data
     */
    public function setSuccess($data = array(), $msg = '')
    {
        $this->arrData['status'] = OSTORE_STATUS_SUCCESS;
        $this->arrData['data']   = $data;
        $this->arrData['msg']    = $msg;
    }

    /**
     * Enter description here...
     *
     */
    public function display()
    {
        header('Content-Type: text/javascript; charset=UTF-8');
        echo $this->encode($this->arrData);
    }

    /**
     * JSONデータをデコードする.
     *
     * php5.2.0からpreg_match関数に渡せるデータ長に制限がある(?)ため,
     * Services_JSONが正常に動作しなくなる.
     * そのため5.2.0以上の場合は組み込み関数のjson_decode()を使用する.
     *
     * @param  string   $str
     * @return StdClass
     * @see Utils::jsonDecode
     */
    public function decode($str)
    {
        return Utils::jsonDecode($str);
    }
}
