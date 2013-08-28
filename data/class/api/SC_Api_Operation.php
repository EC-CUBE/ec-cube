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
 * APIの実行処理本体
 * 権限チェックと設定チェックを行い、APIオペレーション本体を呼び出す。
 * 結果データの生成
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */


class SC_Api_Operation {

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
     * @param string $member_id ログインID文字列
     * @param string $member_password ログインパスワード文字列
     * @return boolean ログイン情報が有効な場合 true
     */
    protected function checkMemberAccount($member_id, $member_password) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        //パスワード、saltの取得
        $cols = 'password, salt';
        $table = 'dtb_member';
        $where = 'login_id = ? AND del_flg <> 1 AND work = 1';
        $arrData = $objQuery->getRow($cols, $table, $where, array($member_id));
        if (SC_Utils_Ex::isBlank($arrData)) {
            return false;
        }
        // ユーザー入力パスワードの判定
        if (SC_Utils_Ex::sfIsMatchHashPassword($member_password, $arrData['password'], $arrData['salt'])) {
            return true;
        }
        return false;
    }

    /**
     * 会員ログインチェックを実行する.
     *
     * @param string $login_email ログインメールアドレス
     * @param string $password ログインパスワード
     * @return boolean ログインに成功した場合 true; 失敗した場合 false
     */
    protected function checkCustomerAccount($login_email, $login_password) {
        $objCustomer = new SC_Customer_Ex();
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
    protected function checkReferer() {
        $ret = false;
        if (!SC_Utils_Ex::isBlank($_SERVER['HTTP_REFERER'])) {
            $domain  = SC_Utils_Ex::sfIsHTTPS() ? HTTPS_URL : HTTP_URL;
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
    protected function checkApiSignature($operation_name, $arrParam, $arrApiConfig) {
        if (SC_Utils_Ex::isBlank($arrParam['Signature'])) {
            return false;
        }
        if (SC_Utils_Ex::isBlank($arrParam['Timestamp'])) {
            return false;
        }
/*
        $allow_account_id = SC_Api_Operation_Ex::getOperationSubConfig($operation_name, 'allow_account_id', $arrApiConfig);
        if (!SC_Utils_Ex::isBlank($allow_account_id) and) {
            $arrAllowAccountIds = explode('|', $allow_account_id);

        }
*/

        $access_key = $arrParam['AccessKeyId'];
        $secret_key = SC_Api_Operation_Ex::getApiSecretKey($access_key);
        if (SC_Utils_Ex::isBlank($secret_key)) {
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
                    $check_str .= '&' . SC_Utils_Ex::encodeRFC3986($key) . '=' . SC_Utils_Ex::encodeRFC3986($val);
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
    protected function checkIp($operation_name) {
        $ret = false;
        $allow_hosts = SC_Api_Utils_Ex::getOperationSubConfig($operation_name, 'allow_hosts');
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
     * @param string $access_key
     * @return string 秘密鍵文字列
     */
    protected function getApiSecretKey($access_key) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
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
    protected function checkOperationAuth($operation_name, &$arrParam, &$arrApiConfig) {
        if (SC_Utils_Ex::isBlank($operation_name)) {
            return false;
        }
        $arrAuthTypes = explode('|', $arrApiConfig['auth_types']);
        $result = false;
        foreach ($arrAuthTypes as $auth_type) {
            $ret = false;
            switch ($auth_type) {
                case self::API_AUTH_TYPE_REFERER:
                    $ret = SC_Api_Operation_Ex::checkReferer();
                    break;
                case self::API_AUTH_TYPE_SESSION_TOKEN:
                    $ret = SC_Helper_Session_Ex::isValidToken(false);
                    break;
                case self::API_AUTH_TYPE_API_SIGNATURE:
                    $ret = SC_Api_Operation_Ex::checkApiSignature($operation_name, $arrParam, $arrApiConfig);
                    break;
                case self::API_AUTH_TYPE_CUSTOMER:
                    $ret = SC_Api_Operation_Ex::checkCustomerAccount($arrParam['login_email'], $arrParam['login_password']);
                    break;
                case self::API_AUTH_TYPE_MEMBER:
                    $ret = SC_Api_Operation_Ex::checkMemberAccount($arrParam['member_id'], $arrParam['member_password']);
                    break;
                case self::API_AUTH_TYPE_CUSTOMER_LOGIN_SESSION:
                    $objCustomer = new SC_Customer_Ex();
                    $ret = $objCustomer->isLoginSuccess();
                    break;
                case self::API_AUTH_TYPE_MEMBER_LOGIN_SESSION:
                    $ret = SC_Utils_Ex::sfIsSuccess(new SC_Session_Ex(), false);
                    break;
                case self::API_AUTH_TYPE_IP:
                    $ret = SC_Api_Operation_Ex::checkIp($operation_name);
                    break;
                case self::API_AUTH_TYPE_HOST:
                    $ret = SC_Api_Operation_Ex::checkHost($operation_name);
                    break;
                case self::API_AUTH_TYPE_SSL:
                    $ret = SC_Utils_Ex::sfIsHTTPS();
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
     * @param object SC_FormParam
     * @return void
     */
    protected function setApiBaseParam(&$objFormParam) {
        $objFormParam->addParam(t('c_Operation_01'), 'Operation', STEXT_LEN, 'an', array('EXIST_CHECK', 'GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Service_01'), 'Service', STEXT_LEN, 'an', array('EXIST_CHECK', 'GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Style_01'), 'Style', STEXT_LEN, 'an', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Validate_01'), 'Validate', STEXT_LEN, 'an', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Version_01'), 'Version', STEXT_LEN, 'an', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * API実行
     *
     * @param array $arrPost リクエストパラメーター
     * @return array(string レスポンス名, array レスポンス配列)
     */
    public function doApiAction($arrPost) {
        // 実行時間計測用
        $start_time = microtime(true);

        $objFormParam = new SC_FormParam_Ex();
        SC_Api_Operation_Ex::setApiBaseParam($objFormParam);
        $objFormParam->setParam($arrPost);
        $objFormParam->convParam();

        $arrErr = SC_Api_Operation_Ex::checkParam($objFormParam);
        if (SC_Utils_Ex::isBlank($arrErr)) {
            $arrParam = $objFormParam->getHashArray();
            $operation_name = $arrParam['Operation'];
            $service_name = $arrParam['Service'];
            $style_name = $arrParam['Style'];
            $validate_flag = $arrParam['Validate'];
            $api_version = $arrParam['Version'];

            SC_Api_Utils_Ex::printApiLog('access', $start_time, $operation_name);
            // API設定のロード
            $arrApiConfig = SC_Api_Utils_Ex::getApiConfig($operation_name);

            if (SC_Api_Operation_Ex::checkOperationAuth($operation_name, $arrPost, $arrApiConfig)) {
                SC_Api_Utils_Ex::printApiLog('Authority PASS', $start_time, $operation_name);

                // オペレーション権限ＯＫ
                // API オブジェクトをロード
                $objApiOperation = SC_Api_Utils_Ex::loadApiOperation($operation_name, $arrParam);

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
                    $arrErr['ECCUBE.Operation.NoLoad'] = t('c_The operation could not be loaded._01');
                }
            } else {
                $arrErr['ECCUBE.Authority.NoAuthority'] = t('c_You do not have execution authority for the operation._01');
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
            SC_Api_Utils_Ex::printApiLog('Operation SUCCESS', $start_time, $response_outer);
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
            SC_Api_Utils_Ex::printApiLog('Operation FAILED', $start_time, $response_outer);
        }

        $arrResponse = array();
        $arrResponse['OperationRequest'] = SC_Api_Operation_Ex::getOperationRequestEcho($arrPost, $start_time);
        $arrResponse[$response_group_name] = array(); // Items
        $arrResponse[$response_group_name] = $arrResponseValidSection;
        if (is_array($arrResponseBody)) {
            $arrResponse[$response_group_name] = array_merge((array)$arrResponse[$response_group_name], (array)$arrResponseBody);
        }

        return array($response_outer, $arrResponse);
    }

    /**
     * APIのリクエストのエコー情報の作成
     *
     * @param array $arrParam リクエストパラメーター
     * @param float $start_time 実行時間計測用開始時間
     * @return array エコー情報配列 (XML用の _attributes 指定入り）
     */
    protected function getOperationRequestEcho($arrParam, $start_time) {
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

    // TODO: ここらへんは SC_Displayに持って行きたい所だが・・
    public function sendApiResponse($type, $response_outer_name, &$arrResponse) {
        switch ($type) {
            case 'xml':
                SC_Api_Utils_Ex::sendResponseXml($response_outer_name, $arrResponse);
                break;
            case 'php':
                SC_Api_Utils_Ex::sendResponsePhp($response_outer_name, $arrResponse);
                break;
            case 'json':
            default:
                SC_Api_Utils_Ex::sendResponseJson($response_outer_name, $arrResponse);
                break;
        }
    }

    /**
     * APIのリクエスト基本パラメーターのチェック
     *
     * @param object $objFormParam
     * @return array $arrErr
     */
    protected function checkParam($objFormParam)
    {
        $arrErr = $objFormParam->checkError();
        if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $objFormParam->getValue('Operation')) && !SC_Utils::isBlank($objFormParam->getValue('Operation'))) {
            $arrErr['ECCUBE.Operation.ParamError'] = 'Operationの値が不正です。';
        }
        if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $objFormParam->getValue('Service')) && !SC_Utils::isBlank($objFormParam->getValue('Service'))) {
            $arrErr['ECCUBE.Service.ParamError'] = 'Serviceの値が不正です。';
        }
        if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $objFormParam->getValue('Style')) && !SC_Utils::isBlank($objFormParam->getValue('Style'))) {
            $arrErr['ECCUBE.Style.ParamError'] = 'Styleの値が不正です。';
        }
        if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $objFormParam->getValue('Validate')) && !SC_Utils::isBlank($objFormParam->getValue('Validate'))) {
            $arrErr['ECCUBE.Validate.ParamError'] = 'Validateの値が不正です。';
        }
        if (!preg_match("/^[a-zA-Z0-9\-\_\.]+$/", $objFormParam->getValue('Version')) && !SC_Utils::isBlank($objFormParam->getValue('Version'))) {
            $arrErr['ECCUBE.Version.ParamError'] = 'Versionの値が不正です。';
        }
        return $arrErr;
    }
}
