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

use Eccube\Api\AbstractApi;
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
class Index extends AbstractApi
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
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
                $this->getCustomer();
                break;
            case 'POST':
                $this->PostCustomer();
                break;
            case 'PUT':
                $this->PutCustomer();
                break;
            case 'DELETE':
                $this->DeleteCustomer();
                break;
        }
    }

    private function getCustomer()
    {
        $this->responseBody = json_encode(CustomerHelper::sfGetCustomerData($_REQUEST['customer_id']));
        $this->responseStatusCode = 200;
    }

    private function PostCustomer()
    {
        if (isset($this->requestBody['customer_id'])) {
            $customer = CustomerHelper::sfEditCustomerData($this->requestBody, $this->requestBody['customer_id']);
            $this->responseStatusCode = 200;
        } else {
            $this->responseStatusCode = 400;
        }
    }

    private function PutCustomer()
    {
        if (isset($this->requestBody['customer_id'])) {
            $this->responseStatusCode = 400;
        } else {
            if ($this->checkError($this->requestBody)) {
                $this->requestBody['update_date'] = 'now()';
                $query = new Query();

                $query->begin();
                $this->requestBody['customer_id'] = $query->nextVal('dtb_customer_customer_id');
                $query->insert('dtb_customer', $this->requestBody);
                $query->commit();
                $this->responseStatusCode = 201;
            } else {
                $this->responseStatusCode = 400;
            }
        }
    }

    private function DeleteCustomer()
    {
        if (isset($_REQUEST['customer_id'])) {
            if (CustomerHelper::delete($_REQUEST['customer_id'])) {
                $this->responseStatusCode = 200;
            } else {
                $this->responseStatusCode = 404;
            }
        } else {
            $this->responseStatusCode = 400;
        }
    }

    private function checkError($request)
    {
        $notNullParam = array('name01', 'name02', 'email', 'secret_key');
        foreach ($notNullParam as $param) {
            if (!isset($request[$param])) {
                return false;
            }
        }
        return true;
    }
}