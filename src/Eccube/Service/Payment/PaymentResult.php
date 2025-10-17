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
 * 決済結果のクラス.
 */
class PaymentResult
{
    /**
     * @var array<int, string>
     */
    private $errors = [];

    /**
     * @var bool
     */
    private $success;

    /**
     * @var Response|null
     */
    private $response;

    /**
     * 決済が成功したかどうかを設定します.
     *
     * 決済が成功した場合は true, 失敗した場合は false を設定します.
     *
     * @param bool $success
     *
     * @return PaymentResult
     */
    public function setSuccess($success): PaymentResult
    {
        $this->success = $success;

        return $this;
    }

    /**
     * 決済が成功したかどうか.
     *
     * 決済が成功した場合 true
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * 決済が失敗した場合のエラーの配列を返します.
     *
     * @return array<int, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * 決済が失敗した場合のエラーの配列を設定します.
     *
     * @param array<int, string> $errors
     *
     * @return PaymentResult
     */
    public function setErrors(array $errors): PaymentResult
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Response を設定します.
     *
     * 3Dセキュアなど, 決済中に他のサイトへリダイレクトが必要な特殊な用途に使用します.
     *
     * @param Response $response
     *
     * @return PaymentResult
     */
    public function setResponse(Response $response): PaymentResult
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
