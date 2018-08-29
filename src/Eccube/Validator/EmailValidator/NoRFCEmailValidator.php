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

namespace Eccube\Validator\EmailValidator;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\EmailValidation;

class NoRFCEmailValidator extends EmailValidator
{
    /**
     * @param $email
     * @param EmailValidation $emailValidation
     *
     * @return bool
     */
    public function isValid($email, EmailValidation $emailValidation)
    {

        if (substr_count($email, '@') < 1) {
            return false;
        }

        if (!preg_match('/^.+\@\S+\.\S+$/', $email)) {
            return false;
        }

        return true;
    }
}
