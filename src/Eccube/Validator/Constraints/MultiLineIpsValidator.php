<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Validator\Constraints;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MultiLineIpsValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof MultiLineIps) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\MultiLineIps');
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string) $value;

        switch ($constraint->version) {
            case MultiLineIps::V4:
                $flag = FILTER_FLAG_IPV4;
                break;

            case MultiLineIps::V6:
                $flag = FILTER_FLAG_IPV6;
                break;

            case MultiLineIps::V4_NO_PRIV:
                $flag = FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE;
                break;

            case MultiLineIps::V6_NO_PRIV:
                $flag = FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE;
                break;

            case MultiLineIps::ALL_NO_PRIV:
                $flag = FILTER_FLAG_NO_PRIV_RANGE;
                break;

            case MultiLineIps::V4_NO_RES:
                $flag = FILTER_FLAG_IPV4 | FILTER_FLAG_NO_RES_RANGE;
                break;

            case MultiLineIps::V6_NO_RES:
                $flag = FILTER_FLAG_IPV6 | FILTER_FLAG_NO_RES_RANGE;
                break;

            case MultiLineIps::ALL_NO_RES:
                $flag = FILTER_FLAG_NO_RES_RANGE;
                break;

            case MultiLineIps::V4_ONLY_PUBLIC:
                $flag = FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
                break;

            case MultiLineIps::V6_ONLY_PUBLIC:
                $flag = FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
                break;

            case MultiLineIps::ALL_ONLY_PUBLIC:
                $flag = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
                break;

            default:
                $flag = null;
                break;
        }

        // 改行でパースして、すべての行が指定のIPアドレスであるかチェック
        $ips = preg_split("/\R/", $value, NULL, PREG_SPLIT_NO_EMPTY);
        $is_ips = true;
        foreach($ips as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP, $flag)) {
                $is_ips = false;
            }
        }

        if (!$is_ips) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->addViolation();
            } else {
                $this->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->addViolation();
            }
        }
    }
}