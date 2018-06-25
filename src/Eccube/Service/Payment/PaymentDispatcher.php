<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\Payment;

use Symfony\Component\HttpFoundation\Response;

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

    public function isForward()
    {
        return $this->forward;
    }

    public function setForward($forward)
    {
        $this->forward = $forward;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    public function setQueryParameters(array $queryParameters)
    {
        $this->queryParameters = $queryParameters;

        return $this;
    }

    public function getPathParameters()
    {
        return $this->pathParameters;
    }

    public function setPathParameters(array $pathParameters)
    {
        $this->pathParameters = $pathParameters;

        return $this;
    }

    /**
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
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
