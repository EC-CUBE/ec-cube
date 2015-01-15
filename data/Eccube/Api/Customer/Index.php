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

namespace Eccube\Api\Customer;

use Eccube\Page\AbstractPage;
use Eccube\Common\Date;
use Eccube\Common\FormParam;
use Eccube\Common\PageNavi;
use Eccube\Common\Display;
use Eccube\Common\Query;
use Eccube\Common\Response;
use Eccube\Common\DB\MasterData;
use Eccube\Common\Helper\CustomerHelper;
use Eccube\Common\Helper\PaymentHelper;
use Eccube\Common\Util\Utils;

/**
 * 会員情報修正 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractPage
{
    var $data;
    var $requestBody;
    var $requestMethod;
    var $responseStatusCode;
    var $responseBody;


    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->requestMethod = $this->getRequestMethod();
        $this->requestBody = $this->getRequestBody();

        $this->responseStatusCode = 201;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponseJson();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        switch($this->requestMethod) {
            case 'GET':
                break;
            case 'POST':
                break;
            case 'PUT':
                break;
            case 'DELETE':
                break;
        }

        // $this->lfRegistData($objFormParam);
    }

    public function sendResponseJson () {
        $objDisplay = new Display();
        $objDisplay->prepare($this);
        $objDisplay->addHeader('HTTP/1.1', $this->responseStatusCode);
        $objDisplay->addHeader('Content-type', "application/json; charset=utf-8");
        $objDisplay->addHeader('Cache-Control', '');
        $objDisplay->addHeader('Pragma', '');

        $objDisplay->response->body = json_encode($this->responseData);
        $objDisplay->response->write();
        Response::actionExit();
    }

    /**
     * 登録処理
     *
     * @param  array $objFormParam フォームパラメータークラス
     * @return integer エラー配列
     */
    public function lfRegistData(&$objFormParam)
    {
        // 登録用データ取得
        $arrData = $objFormParam->getDbArray();
        // 足りないものを作る
        if (!Utils::isBlank($objFormParam->getValue('year'))) {
            $arrData['birth'] = $objFormParam->getValue('year') . '/'
                            . $objFormParam->getValue('month') . '/'
                            . $objFormParam->getValue('day')
                            . ' 00:00:00';
        }

        if (!is_numeric($arrData['customer_id'])) {
            $arrData['secret_key'] = Utils::sfGetUniqRandomId('r');
        } else {
            $arrOldCustomerData = CustomerHelper::sfGetCustomerData($arrData['customer_id']);
            if ($arrOldCustomerData['status'] != $arrData['status']) {
                $arrData['secret_key'] = Utils::sfGetUniqRandomId('r');
            }
        }

        return CustomerHelper::sfEditCustomerData($arrData, $arrData['customer_id']);
    }

    /**
     * メソッド取得
     * @return string 
     */
    protected function getRequestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    protected function getRequestBody() {
        $f = fopen('php://input', 'r');
        $content = stream_get_contents($f);
        fclose($f);
        return $content;
    }
}