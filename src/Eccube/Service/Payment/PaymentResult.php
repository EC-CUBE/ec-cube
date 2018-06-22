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

class PaymentResult
{
    /**
     * @var array
     */
    private $errors;

    /**
     * @var boolean
     */
    private $success;

    /**
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
     * @return boolean
     */
    public function isSuccess()
    {
        return true;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return [];
    }

    /**
     * @param array $errors
     *
     * @return PaymentResult
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }
}
