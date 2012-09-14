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
 * APIの抽象クラス
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */

abstract class SC_Api_Abstract {

    /** 認証タイプ */
    const API_AUTH_TYPE_REFERER = '1';          // リファラー
    const API_AUTH_TYPE_SESSION_TOKEN = '2';    // CSRF TOKEN
    const API_AUTH_TYPE_API_SIGNATURE = '3';    // API 署名認証 推奨
    const API_AUTH_TYPE_CUSTOMER = '4';         // 会員認証
    const API_AUTH_TYPE_MEMBER = '5';           // 管理者認証
    const API_AUTH_TYPE_CUSTOMER_LOGIN_SESSION = '6';   // 顧客ログインセッションが有効
    const API_AUTH_TYPE_MEMBER_LOGIN_SESSION = '7';     // 管理者ログインセッションが有効
    const API_AUTH_TYPE_IP = '8';               // IP認証
    const API_AUTH_TYPE_HOST = '9';             // ホスト認証
    const API_AUTH_TYPE_SSL = '10';             // SSL強制
    const API_AUTH_TYPE_OPEN = '99';            // 完全オープン

    /** API Operation default */
    protected $operation_name = 'operation_name';
    protected $operation_description = 'operation description';
    protected $default_auth_types = '0'; // |区切り
    protected $default_enable = '0';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

    protected $status = true;
    protected $arrErr = array();

    public function __construct() {}

    final public function __destruct() {}

    abstract public function doAction($arrParam);

    abstract public function getResponseGroupName();

    abstract protected function lfInitParam(&$objFormParam);

    public function getResponseArray() {
        return $this->arrResponse;
    }

    public function getErrorArray() {
        return $this->arrErr;
    }

    public function getDefaultConfig() {
        $arrApiConfig = array();
        $arrApiConfig['operation_name'] = $this->operation_name;
        $arrApiConfig['operation_description'] = $this->operation_description;
        $arrApiConfig['auth_types'] = $this->default_auth_types;
        $arrApiConfig['enable'] = $this->default_enable;
        $arrApiConfig['is_log'] = $this->default_is_log;
        $arrApiConfig['sub_data'] = $this->default_sub_data;
        return $arrApiConfig;
    }

    protected function setResponse($key, $data) {
        $this->arrResponse[$key] = $data;
    }

    protected function addError($arrErr) {
        $this->arrErr = array_merge((array)$this->arrErr, (array)$arrErr);
    }

    protected function isParamError() {
        return !SC_Utils_Ex::isBlank($this->arrErr);
    }

    protected function checkErrorExtended($arrParam) {
    }

    protected function doInitParam($arrParam = array()) {
        $this->objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($this->objFormParam);
        $this->objFormParam->setParam($arrParam);
        $this->objFormParam->convParam();
        $this->arrErr = $this->objFormParam->checkError(false);
        $this->arrErr = array_merge((array)$this->arrErr, (array)$this->checkErrorExtended($arrParam));
        return $this->objFormParam->getHashArray();
    }

    public function getRequestValidate() {
        $arrParam = $this->objFormParam->getHashArray();
        if (!SC_Utils_Ex::isBlank($arrParam)) {
            return $arrParam;
        }
        return;
    }

}
