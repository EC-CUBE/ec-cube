<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\Payment;

use Symfony\Component\HttpFoundation\Response;

/**
 * 他のコントローラに処理を移譲するための情報を設定するクラス.
 */
class PaymentDispatcher
{
    /**
     * @var Response
     */
    private $response;

    /**
     * @var boolean
     */
    private $forward;

    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $pathParameters = [];

    /**
     * @var array
     */
    private $queryParameters = [];

    /**
     * Forward を使用するかどうか.
     *
     * @return boolean
     */
    public function isForward()
    {
        return $this->forward;
    }

    /**
     * Forward を使用するかどうかを設定します.
     *
     * Forward を使用する場合は true, Redirect を使用する場合は false を設定します.
     *
     * @param boolean $forward
     *
     * @return PaymentDispatcher
     */
    public function setForward($forward)
    {
        $this->forward = $forward;

        return $this;
    }

    /**
     * 処理を移譲するルート名を返します.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * 処理を移譲するルート名を設定します.
     *
     * @param string $route
     *
     * @return PaymentDispatcher
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * クエリパラメータの配列を返します.
     *
     * @return array
     */
    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    /**
     * クエリパラメータの配列を設定します.
     *
     * @param array
     *
     * @return PaymentDispatcher
     */
    public function setQueryParameters(array $queryParameters)
    {
        $this->queryParameters = $queryParameters;

        return $this;
    }

    /**
     * パスパラメータの配列を返します.
     *
     * @return array
     */
    public function getPathParameters()
    {
        return $this->pathParameters;
    }

    /**
     * パスパラメータの配列を設定します.
     *
     * @param array
     *
     * @return PaymentDispatcher
     */
    public function setPathParameters(array $pathParameters)
    {
        $this->pathParameters = $pathParameters;

        return $this;
    }

    /**
     * Response を設定します.
     *
     * 外部のサイトへリダイレクトする等, 特殊な用途に使用してください.
     *
     * @param Response $response
     *
     * @return PaymentResult
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Response を返します.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
