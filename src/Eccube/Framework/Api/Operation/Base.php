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
use Eccube\Framework\FormParam;
use Eccube\Framework\Util\Utils;

/**
 * APIの抽象クラス
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 */

abstract class Base
{
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
    protected $arrResponse = array();

    final public function __construct()
    {
    }

    final public function __destruct()
    {
    }

    abstract public function doAction($arrParam);

    abstract public function getResponseGroupName();

    /**
     * @param FormParam $objFormParam
     */
    abstract protected function lfInitParam(&$objFormParam);

    public function getResponseArray()
    {
        return $this->arrResponse;
    }

    public function getErrorArray()
    {
        return $this->arrErr;
    }

    public function getDefaultConfig()
    {
        $arrApiConfig = array();
        $arrApiConfig['operation_name'] = $this->operation_name;
        $arrApiConfig['operation_description'] = $this->operation_description;
        $arrApiConfig['auth_types'] = $this->default_auth_types;
        $arrApiConfig['enable'] = $this->default_enable;
        $arrApiConfig['is_log'] = $this->default_is_log;
        $arrApiConfig['sub_data'] = $this->default_sub_data;

        return $arrApiConfig;
    }

    /**
     * @param string $key
     */
    protected function setResponse($key, $data)
    {
        $this->arrResponse[$key] = $data;
    }

    protected function addError($arrErr)
    {
        $this->arrErr = array_merge((array) $this->arrErr, (array) $arrErr);
    }

    protected function isParamError()
    {
        return !Utils::isBlank($this->arrErr);
    }

    protected function checkErrorExtended($arrParam)
    {
    }

    protected function doInitParam($arrParam = array())
    {
        $this->objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParam($this->objFormParam);
        $this->objFormParam->setParam($arrParam);
        $this->objFormParam->convParam();
        $this->arrErr = $this->objFormParam->checkError(false);
        $this->arrErr = array_merge((array) $this->arrErr, (array) $this->checkErrorExtended($arrParam));

        return $this->objFormParam->getHashArray();
    }

    public function getRequestValidate()
    {
        $arrParam = $this->objFormParam->getHashArray();
        if (!Utils::isBlank($arrParam)) {
            return $arrParam;
        }

        return;
    }
}
