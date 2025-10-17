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
     * @var Response|null
     */
    private $response;

    /**
     * @var bool
     */
    private $forward;

    /**
     * @var string
     */
    private $route;

    /**
     * @var array<string, string>
     */
    private $pathParameters = [];

    /**
     * @var array<string, string>
     */
    private $queryParameters = [];

    /**
     * Forward を使用するかどうか.
     *
     * @return bool
     */
    public function isForward(): bool
    {
        return $this->forward;
    }

    /**
     * Forward を使用するかどうかを設定します.
     *
     * Forward を使用する場合は true, Redirect を使用する場合は false を設定します.
     *
     * @param bool $forward
     *
     * @return self
     */
    public function setForward($forward): PaymentDispatcher
    {
        $this->forward = $forward;

        return $this;
    }

    /**
     * 処理を移譲するルート名を返します.
     *
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * 処理を移譲するルート名を設定します.
     *
     * @param string $route
     *
     * @return self
     */
    public function setRoute($route): PaymentDispatcher
    {
        $this->route = $route;

        return $this;
    }

    /**
     * クエリパラメータの配列を返します.
     *
     * @return array<string, string>
     */
    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    /**
     * クエリパラメータの配列を設定します.
     *
     * @param array<string, string> $queryParameters
     *
     * @return self
     */
    public function setQueryParameters(array $queryParameters): PaymentDispatcher
    {
        $this->queryParameters = $queryParameters;

        return $this;
    }

    /**
     * パスパラメータの配列を返します.
     *
     * @return array<string, string>
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    /**
     * パスパラメータの配列を設定します.
     *
     * @param array<string, string> $pathParameters
     *
     * @return PaymentDispatcher
     */
    public function setPathParameters(array $pathParameters): PaymentDispatcher
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
     * @return self
     */
    public function setResponse(Response $response): PaymentDispatcher
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Response を返します.
     *
     * @return Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
