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

namespace Eccube\Framework\Api;

use Eccube\Application;
use Eccube\Framework\Customer;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Session;
use Eccube\Framework\Api\ApiUtils;
use Eccube\Framework\Helper\SessionHelper;
use Eccube\Framework\Util\Utils;

/**
 * APIの実行処理本体
 * 権限チェックと設定チェックを行い、APIオペレーション本体を呼び出す。
 * 結果データの生成
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 */

class Operation
{
    /** API_DEBUG_MODE */
    const API_DEBUG_MODE = false;

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

    /**
     * 有効な管理者ID/PASSかどうかチェックする
     *
     * @param  string  $member_id       ログインID文字列
     * @param  string  $member_password ログインパスワード文字列
     * @return boolean ログイン情報が有効な場合 true
     */
    protected function checkMemberAccount($member_id, $member_password)
    {
        $objQuery = Application::alias('eccube.query');
        //パスワード、saltの取得
        $cols = 'password, salt';
        $table = 'dtb_member';
        $where = 'login_id = ? AND del_flg <> 1 AND work = 1';
        $arrData = $objQuery->getRow($cols, $table, $where, array($member_id));
        if (Utils::isBlank($arrData)) {
            return false;
        }
        // ユーザー入力パスワードの判定
        if (Utils::sfIsMatchHashPassword($member_password, $arrData['password'], $arrData['salt'])) {
            return true;
        }

        return false;
    }

    /**
     * 会員ログインチェックを実行する.
     *
     * @param  string  $login_email ログインメールアドレス
     * @param  string  $login_password    ログインパスワード
     * @return boolean ログインに成功した場合 true; 失敗した場合 false
     */
    protected function checkCustomerAccount($login_email, $login_password)
    {
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        if ($objCustomer->getCustomerDataFromEmailPass($login_password, $login_email)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * リファラーチェックを実行する.
     *
     * @return boolean チェックに成功した場合 true; 失敗した場合 false
     */
    protected function checkReferer()
    {
        $ret = false;
        if (!Utils::isBlank($_SERVER['HTTP_REFERER'])) {
            $domain  = Utils::sfIsHTTPS() ? HTTPS_URL : HTTP_URL;
            $pattern = sprintf('|^%s.*|', $domain);
            $referer = $_SERVER['HTTP_REFERER'];
            if (preg_match($pattern, $referer)) {
               $ret = true;
            }
        }

        return $ret;
    }

    /**
     * HMAC-SHA 署名認証チェック
     * Refer: http://www.soumu.go.jp/main_sosiki/joho_tsusin/top/ninshou-law/law-index.html
     *
     * @param  string 実行処理名
     * @param array リクエストパラメータ
     * @return boolean 署名認証に成功した場合 true; 失敗した場合 false
     */
    protected function checkApiSignature($operation_name, $arrParam, $arrApiConfig)
    {
        if (Utils::isBlank($arrParam['Signature'])) {
            return false;
        }
        if (Utils::isBlank($arrParam['Timestamp'])) {
            return false;
        }
/*
        $allow_account_id = static::getOperationSubConfig($operation_name, 'allow_account_id', $arrApiConfig);
        if (!Utils::isBlank($allow_account_id) and) {
            $arrAllowAccountIds = explode('|', $allow_account_id);
        }
*/

        $access_key = $arrParam['AccessKeyId'];
        $secret_key = static::getApiSecretKey($access_key);
        if (Utils::isBlank($secret_key)) {
            return false;
        }

        // バイト順に並び替え
        ksort($arrParam);

        // 規定の文字列フォーマットを作成する
        // Refer: https://images-na.ssl-images-amazon.com/images/G/09/associates/paapi/dg/index.html?Query_QueryAuth.html
        $check_str = '';
        foreach ($arrParam as $key => $val) {
            switch ($key) {
                case 'Signature':
                    break;
                default:
                    $check_str .= '&' . Utils::encodeRFC3986($key) . '=' . Utils::encodeRFC3986($val);
                    break;
            }
        }
        $check_str = substr($check_str, 1);
        $check_str = strtoupper($_SERVER['REQUEST_METHOD']) . "\n"
                     . strtolower($_SERVER['SERVER_NAME']) . "\n"
                     . $_SERVER['PHP_SELF'] . "\n"
                     . $check_str;
        $signature = base64_encode(hash_hmac('sha256', $check_str, $secret_key, true));
        if ($signature === $arrParam['Signature']) {
            return true;
        }

        return false;
    }

    /**
     * IPチェックを実行する.
     *
     * @param  string 実行処理名
     * @return boolean チェックに成功した場合 true; 失敗した場合 false
     */
    protected function checkIp($operation_name)
    {
        $ret = false;
        $allow_hosts = ApiUtils::getOperationSubConfig($operation_name, 'allow_hosts');
        $arrAllowHost = explode("\n", $allow_hosts);
        if (is_array($arrAllowHost) && count($arrAllowHost) > 0) {
            if (array_search($_SERVER['REMOTE_ADDR'], $arrAllowHost) !== FALSE) {
                $ret = true;
            }
        }

        return $ret;
    }

    /**
     * ApiAccessKeyに対応した秘密鍵を取得する。
     *
     * @param  string $access_key
     * @return string 秘密鍵文字列
     */
    protected function getApiSecretKey($access_key)
    {
        $objQuery = Application::alias('eccube.query');
        $secret_key = $objQuery->get('api_secret_key', 'dtb_api_account', 'api_access_key = ? and enable = 1 and del_flg = 0', array($access_key));

        return $secret_key;
    }

    /**
     * オペレーションの実行権限をチェックする
     *
     * @param string オペレーション名
     * @param array リクエストパラメータ
     * @return boolean 権限がある場合 true; 無い場合 false
     */
    protected function checkOperationAuth($operation_name, &$arrParam, &$arrApiConfig)
    {
        if (Utils::isBlank($operation_name)) {
            return false;
        }
        $arrAuthTypes = explode('|', $arrApiConfig['auth_types']);
        $result = false;
        foreach ($arrAuthTypes as $auth_type) {
            $ret = false;
            switch ($auth_type) {
                case self::API_AUTH_TYPE_REFERER:
                    $ret = static::checkReferer();
                    break;
                case self::API_AUTH_TYPE_SESSION_TOKEN:
                    $ret = SessionHelper::isValidToken(false);
                    break;
                case self::API_AUTH_TYPE_API_SIGNATURE:
                    $ret = static::checkApiSignature($operation_name, $arrParam, $arrApiConfig);
                    break;
                case self::API_AUTH_TYPE_CUSTOMER:
                    $ret = static::checkCustomerAccount($arrParam['login_email'], $arrParam['login_password']);
                    break;
                case self::API_AUTH_TYPE_MEMBER:
                    $ret = static::checkMemberAccount($arrParam['member_id'], $arrParam['member_password']);
                    break;
                case self::API_AUTH_TYPE_CUSTOMER_LOGIN_SESSION:
                    /* @var $objCustomer Customer */
                    $objCustomer = Application::alias('eccube.customer');
                    $ret = $objCustomer->isLoginSuccess();
                    break;
                case self::API_AUTH_TYPE_MEMBER_LOGIN_SESSION:
                    $ret = Utils::sfIsSuccess(new Session(), false);
                    break;
                case self::API_AUTH_TYPE_IP:
                    $ret = static::checkIp($operation_name);
                    break;
                case self::API_AUTH_TYPE_HOST:
                    $ret = static::checkHost($operation_name);
                    break;
                case self::API_AUTH_TYPE_SSL:
                    $ret = Utils::sfIsHTTPS();
                    break;
                case self::API_AUTH_TYPE_OPEN:
                    $result = true;
                    break 2;    // foreachも抜ける
                default:
                    $ret = false;
                    break;
            }
            if ($ret === true) {
                $result = true;
            } else {
                $result = false;
                break;  // 1つでもfalseがあれば，その時点で終了
            }
        }

        return $result;
    }

    /**
     * APIのリクエスト基本パラメーターの設定
     *
     * @param object FormParam
     * @param FormParam $objFormParam
     * @return void
     */
    protected function setApiBaseParam(&$objFormParam)
    {
        $objFormParam->addParam('Operation', 'Operation', STEXT_LEN, 'an', array('EXIST_CHECK', 'GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('Service', 'Service', STEXT_LEN, 'an', array('EXIST_CHECK', 'GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('Style', 'Style', STEXT_LEN, 'an', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('Validate', 'Validate', STEXT_LEN, 'an', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('Version', 'Version', STEXT_LEN, 'an', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * API実行
     *
     * @param  array        $arrPost リクエストパラメーター
     * @return array(string レスポンス名, array レスポンス配列)
     */
    public function doApiAction($arrPost)
    {
        // 実行時間計測用
        $start_time = microtime(true);

        $objFormParam = Application::alias('eccube.form_param');
        static::setApiBaseParam($objFormParam);
        $objFormParam->setParam($arrPost);
        $objFormParam->convParam();

        $arrErr = static::checkParam($objFormParam); 
        
        // API機能が有効であるかをチェック.
        if (API_ENABLE_FLAG == false) {
            $arrErr['ECCUBE.Function.Disable'] = 'API機能が無効です。';
        }
        if (Utils::isBlank($arrErr)) {
            $arrParam = $objFormParam->getHashArray();
            $operation_name = $arrParam['Operation'];
            $service_name = $arrParam['Service'];
            $style_name = $arrParam['Style'];
            $validate_flag = $arrParam['Validate'];
            $api_version = $arrParam['Version'];

            ApiUtils::printApiLog('access', $start_time, $operation_name);
            // API設定のロード
            $arrApiConfig = ApiUtils::getApiConfig($operation_name);

            if (static::checkOperationAuth($operation_name, $arrPost, $arrApiConfig)) {
                ApiUtils::printApiLog('Authority PASS', $start_time, $operation_name);

                // オペレーション権限ＯＫ
                // API オブジェクトをロード
                $objApiOperation = ApiUtils::loadApiOperation($operation_name, $arrParam);

                if (is_object($objApiOperation) && method_exists($objApiOperation, 'doAction')) {
                    // API オペレーション実行
                    $operation_result = $objApiOperation->doAction($arrPost);

                    // オペレーション結果処理
                    if ($operation_result) {
                        $arrOperationRequestValid = $objApiOperation->getRequestValidate();
                        $arrResponseBody =  $objApiOperation->getResponseArray();
                        $response_group_name = $objApiOperation->getResponseGroupName();
                    } else {
                        $arrErr = $objApiOperation->getErrorArray();
                    }
                } else {
                    $arrErr['ECCUBE.Operation.NoLoad'] = 'オペレーションをロード出来ませんでした。';
                }
            } else {
                $arrErr['ECCUBE.Authority.NoAuthority'] = 'オペレーションの実行権限がありません。';
            }
        }

        if (count($arrErr) == 0) {
            // 実行成功
            $arrResponseValidSection = array('Request' => array(
                                                            'IsValid' => 'True',
                                                            $operation_name . 'Request' => $arrOperationRequestValid
                                                            )
                                            );
            $response_outer = $operation_name . 'Response';
            ApiUtils::printApiLog('Operation SUCCESS', $start_time, $response_outer);
        } else {
            // 実行失敗
            $arrResponseErrorSection = array();
            foreach ($arrErr as $error_code => $error_msg) {
                $arrResponseErrorSection[] = array('Code' => $error_code, 'Message' => $error_msg);
            }
            $arrResponseValidSection = array('Request' => array(
                                                            'IsValid' => 'False',
                                                            'Errors' => array('Error' => $arrResponseErrorSection)
                                                            )
                                            );
            if (is_object($objApiOperation)) {
                $response_outer = $operation_name . 'Response';
            } else {
                $response_outer = 'ECCUBEApiCommonResponse';
            }
            ApiUtils::printApiLog('Operation FAILED', $start_time, $response_outer);
        }

        $arrResponse = array();
        $arrResponse['OperationRequest'] = static::getOperationRequestEcho($arrPost, $start_time);
        $arrResponse[$response_group_name] = array(); // Items
        $arrResponse[$response_group_name] = $arrResponseValidSection;
        if (is_array($arrResponseBody)) {
            $arrResponse[$response_group_name] = array_merge((array) $arrResponse[$response_group_name], (array) $arrResponseBody);
        }

        return array($response_outer, $arrResponse);
    }

    /**
     * APIのリクエストのエコー情報の作成
     *
     * @param  array $arrParam   リクエストパラメーター
     * @param  float $start_time 実行時間計測用開始時間
     * @return array エコー情報配列 (XML用の _attributes 指定入り）
     */
    protected function getOperationRequestEcho($arrParam, $start_time)
    {
        $arrRet = array(
                'HTTPHeaders' => array('Header' => array('_attributes' => array('Name' => 'UserAgent',
                                                                                'Value' => htmlspecialchars($_SERVER['HTTP_USER_AGENT'])))),
                'RequestId' => $start_time,
                'Arguments' => array(),
                );
        foreach ($arrParam as $key => $val) {
            $arrRet['Arguments'][] = array('_attributes' => array('Name' => htmlentities($key, ENT_NOQUOTES, 'UTF-8'), 'Value' => htmlentities($val, ENT_NOQUOTES, 'UTF-8')));
        }
        $arrRet['RequestProcessingTime'] = microtime(true) - $start_time;

        return $arrRet;
    }

    // TODO: ここらへんは Displayに持って行きたい所だが・・

    /**
     * @param string $type
     * @param string $response_outer_name
     */
    public function sendApiResponse($type, $response_outer_name, &$arrResponse)
    {
        switch ($type) {
            case 'xml':
                ApiUtils::sendResponseXml($response_outer_name, $arrResponse);
                break;
            case 'php':
                ApiUtils::sendResponsePhp($response_outer_name, $arrResponse);
                break;
            case 'json':
            default:
                ApiUtils::sendResponseJson($response_outer_name, $arrResponse);
                break;
        }
    }
    
    /**
     * APIのリクエスト基本パラメーターのチェック
     *
     * @param FormParam $objFormParam
     * @return array $arrErr
     */
    protected function checkParam($objFormParam)
    {
        $arrErr = $objFormParam->checkError();
        if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $objFormParam->getValue('Operation')) && !Utils::isBlank($objFormParam->getValue('Operation'))) {
            $arrErr['ECCUBE.Operation.ParamError'] = 'Operationの値が不正です。';
        }
        if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $objFormParam->getValue('Service')) && !Utils::isBlank($objFormParam->getValue('Service'))) {
            $arrErr['ECCUBE.Service.ParamError'] = 'Serviceの値が不正です。';
        }
        if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $objFormParam->getValue('Style')) && !Utils::isBlank($objFormParam->getValue('Style'))) {
            $arrErr['ECCUBE.Style.ParamError'] = 'Styleの値が不正です。';
        }
        if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $objFormParam->getValue('Validate')) && !Utils::isBlank($objFormParam->getValue('Validate'))) {
            $arrErr['ECCUBE.Validate.ParamError'] = 'Validateの値が不正です。';
        }
        if (!preg_match("/^[a-zA-Z0-9\-\_\.]+$/", $objFormParam->getValue('Version')) && !Utils::isBlank($objFormParam->getValue('Version'))) {
            $arrErr['ECCUBE.Version.ParamError'] = 'Versionの値が不正です。';
        }
        return $arrErr;
    }
}
