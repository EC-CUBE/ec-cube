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
use Eccube\Framework\Query;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;

/**
 * API関係処理のユーティリティ
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 */
define('API_UPLOAD_REALDIR', DATA_REALDIR . 'downloads/api/');
define('API_CLASS_EX_REALDIR', CLASS_EX_REALDIR . 'api_extends/operations/');
define('API_CLASS_REALDIR', CLASS_REALDIR . 'api/operations/');

class ApiUtils
{
    /** API XML Namspase Header */
    const API_XMLNS = 'http://www.ec-cube.net/ECCUBEApi/';

    /** API XML lang */
    const API_XML_LANG = 'ja';

    /** API LOGFILE_NAME */
    const API_LOGFILE = 'logs/api.log';

    /** API_DEBUG_MODE */
    const API_DEBUG_MODE = false;

    /**
     * オペレーション名に対応した追加の設定情報を取得する
     *
     * @return string 秘密鍵文字列
     */
    public function getOperationSubConfig($operation_name, $key_name = '', $arrApiConfig = '')
    {
        if (Utils::isBlank($arrApiConfig)) {
            $arrApiConfig = static::getAuthConfig($operation_name);
        }
        if (!Utils::isBlank($arrApiConfig['sub_data'])) {
            $arrData = @unserialize($arrApiConfig['sub_data']);
            if ($arrData === FALSE) {
                return $arrApiConfig['sub_data'];
            } else {
                if ($key_name != '') {
                    return $arrData['key_name'];
                } else {
                    return $arrData;
                }
            }
        }

        return false;
    }

    /**
     * オペレーション名に対応した認証の設定情報を取得する
     * Configが無い場合は、APIデフォルトを取得する
     *
     * @param  string $operation_name
     * @return array  設定配列
     */
    public function getApiConfig($operation_name)
    {
        // 設定優先度 DB > plugin default > base
        $objQuery = Application::alias('eccube.query');
        $where = 'operation_name Like ? AND del_flg = 0 AND enable = 1';
        $arrApiConfig = $objQuery->getRow('*', 'dtb_api_config', $where, array($operation_name));
        if (Utils::isBlank($arrApiConfig)) {
            $objApi = static::loadApiOperation($operation_name);
            if (is_object($objApi)) {
                $arrApiConfig = $objApi->getDefaultConfig();
            }
            if (!Utils::isBlank($arrApiConfig)) {
                // デフォルト設定がロード出来た場合は自動で設定に反映
                $arrData = $arrApiConfig;
                $arrData['update_date'] = 'CURRENT_TIMESTAMP';
                $arrData['api_config_id'] = $objQuery->nextVal('dtb_api_config_api_config_id');
                $objQuery->insert('dtb_api_config', $arrData);
            } else {
                // ロード出来ない場合はAPI_Defaultを適用
                $operation_name = 'Default';
                $objApi = static::loadApiOperation($operation_name);
                $arrApiConfig = $objApi->getDefaultConfig();
            }
        }

        return $arrApiConfig;
    }

    /**
     * APIログ
     *
     * @param string $msg            出力文字列
     * @param text $operation_name
     * @return void
     */
    public function printApiLog($msg, $start_time = '' , $operation_name = '')
    {
        if (!Utils::isBlank($operation_name)) {
            $msg = 'API_' . $operation_name . ':' . $msg;
        }
        if (!Utils::isBlank($start_time)) {
            $msg = '(RequestId:' . $start_time . ')' . $msg;
        }
        GcUtils::gfPrintLog($msg, DATA_REALDIR . self::API_LOGFILE, self::API_DEBUG_MODE);
    }

    /**
     * APIオペレーションに対応したAPIクラスをインスタンス化
     *
     * @param  string $operation_name オペレーション名
     * @param  array  $arrParam       リクエストパラメーター
     * @return object APIオペレーションクラスオブジェクト
     */
    public function loadApiOperation($operation_name, $arrParam = array())
    {
        // API_UPLOADのほうが優先
        // API_UPLOAD > API_CLASS_EX > API_CLASS
        if (file_exists(API_UPLOAD_REALDIR . $operation_name . '.php')) {
            $api_operation_file =  API_UPLOAD_REALDIR . $operation_name . '.php';
            $api_class_name = 'API_' . $operation_name;
        } elseif (file_exists(API_CLASS_EX_REALDIR . $operation_name . '_Ex.php')) {
            $api_operation_file =  API_CLASS_EX_REALDIR . $operation_name . '_Ex.php';
            $api_class_name = 'API_' . $operation_name . '_Ex';
        } elseif (file_exists(API_CLASS_REALDIR . $operation_name . '.php')) {
            $api_operation_file =  API_CLASS_REALDIR . $operation_name . '.php';
            $api_class_name = 'API_' . $operation_name;
        } else {
            return false;
        }
        require_once $api_operation_file;
        $objApiOperation = new $api_class_name ($arrParam);

        return $objApiOperation;
    }

    /**
     * API Operationファイル一覧
     *
     * @return array $arrFiles
     */
    public function getApiDirFiles()
    {
        $arrFiles = array();
        // Core API ディレクトリ
        if (is_dir(API_CLASS_EX_REALDIR)) {
            if ($dh = opendir(API_CLASS_EX_REALDIR)) {
                while (($file_name = readdir($dh)) !== false) {
                    if ($file_name != '.' && $file_name != '..' && substr($file_name, -4) == '.php') {
                        $arrFiles[] = API_CLASS_EX_REALDIR . $file_name;
                    }
                }
                closedir($dh);
            }
        }

        // downaloads APIディレクトリ (for Plugin)
        if (is_dir(API_UPLOAD_REALDIR)) {
            if ($dh = opendir(API_UPLOAD_REALDIR)) {
                while (($file_name = readdir($dh)) !== false) {
                    if ($file_name != '.' && $file_name != '..' && substr($file_name, -4) == '.php') {
                        $arrFiles[] = API_UPLOAD_REALDIR . $file_name;
                    }
                }
                closedir($dh);
            }
        }

        return $arrFiles;
    }

    /**
     * @param string $response_outer_name
     */
    public function sendResponseJson($response_outer_name, &$arrResponse)
    {
        if (isset($arrResponse["callback"])) {
            $callback = $arrResponse["callback"];
            unset($arrResponse["callback"]);
            header('Content-Type: application/javascript; charset=UTF-8');
            $arrResponse['response_name'] = $response_outer_name;
            echo $callback . "(" . Utils::jsonEncode($arrResponse) . ")";
        } else {
            header('Content-Type: application/json; charset=UTF-8');
            $arrResponse['response_name'] = $response_outer_name;
            echo Utils::jsonEncode($arrResponse);
        }
    }

    /**
     * @param string $response_outer_name
     */
    public function sendResponsePhp($response_outer_name, &$arrResponse)
    {
        header('Content-Type: application/php; charset=UTF-8');
        $arrResponse['response_name'] = $response_outer_name;
        echo serialize($arrResponse);
    }

    /**
     * @param string $response_outer_name
     */
    public function sendResponseXml($response_outer_name, &$arrResponse)
    {
        $options = array(
            'mode' => 'simplexml',
            'indent' => "\t",
            'linebreak' => "\n",
            'typeHints' => false,
            'addDecl' => true,
            'encoding' => 'UTF-8',
            'rootName' => $response_outer_name,
            'rootAttributes' => array('xmlns' => self::API_XMLNS . ECCUBE_VERSION,
                                        'xml:lang' => self::API_XML_LANG),
            'defaultTagName' => 'Response',
            'attributesArray' => '_attributes'
        );

        $objSerializer = new \XML_Serializer($options);
        $ret = $objSerializer->serialize($arrResponse);
        $xml = $objSerializer->getSerializedData();
        header('Content-Type: text/xml; charset=UTF-8');
        echo $xml;
    }
}
