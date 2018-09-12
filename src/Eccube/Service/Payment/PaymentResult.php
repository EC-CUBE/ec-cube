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

/**
 * 決済結果のクラス.
 */
class PaymentResult
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var boolean
     */
    private $success;

    /**
     * @var Response
     */
    private $response;

    /**
     * 決済が成功したかどうかを設定します.
     *
     * 決済が成功した場合は true, 失敗した場合は false を設定します.
     *
     * @param boolean $success
     *
     * @return PaymentResult
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * 決済が成功したかどうか.
     *
     * 決済が成功した場合 true
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * 決済が失敗した場合のエラーの配列を返します.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * 決済が失敗した場合のエラーの配列を設定します.
     *
     * @param array $errors
     *
     * @return PaymentResult
     */
    public function setErrors(array $errors)
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
