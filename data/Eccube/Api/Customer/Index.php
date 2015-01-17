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
        $this->sendResponseJson($this->responseBody);
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
        $this->responseBody = "get customer";
    }
    private function PostCustomer() {
        $this->responseBody = "post customer";
    }
    private function PutCustomer() {
        $this->responseBody = "put customer";
    }
    private function DeleteCustomer() {
        $this->responseBody = "delete customer";
    }
}