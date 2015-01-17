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

namespace Eccube\Api;

use Eccube\Page\AbstractPage;
use Eccube\Common\Display;
use Eccube\Common\Query;
use Eccube\Common\Response;

abstract class AbstractApi
{
    var $requestMethod;
    var $requestBody;
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
        $this->responseStatusCode = 200;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
    }

    public function sendResponseJson () {
        $objDisplay = new Display();
        $objDisplay->addHeader('HTTP/1.1', $this->responseStatusCode);
        $objDisplay->addHeader('Content-type', "application/json; charset=utf-8");
        $objDisplay->addHeader('Cache-Control', '');
        $objDisplay->addHeader('Pragma', '');

        $objDisplay->response->body = json_encode($this->responseBody);
        $objDisplay->response->write();
        Response::actionExit();
    }

    /**
     * メソッド取得
     * @return string 
     */
    protected function getRequestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    /**
     * リクエストボディ取得
     * @return string 
     */
    protected function getRequestBody() {
        $f = fopen('php://input', 'r');
        $content = stream_get_contents($f);
        fclose($f);
        // 連想配列でreturn
        return json_decode($content, true);
    }

}