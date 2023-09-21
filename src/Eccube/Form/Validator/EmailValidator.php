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

namespace Eccube\Form\Validator;

use Eccube\Validator\EmailValidator\NoRFCEmailValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\EmailValidator as BaseEmailValidator;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * フォームで使用するEmailのバリデータ.
 *
 * eccube_rfc_email_checkがtrueの場合, Symfony\Component\Validator\Constraints\EmailValidatorを使用してチェックを行います.
 * falseの場合は, Eccube\Validator\EmailValidator\NoRFCEmailValidatorを使用してチェックを行います.
 * NoRFCEmailValidatorは, 日本のキャリアメールで使用されていた, ..や.@の形式を許容します.
 */
class EmailValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Email) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Email');
        }

        if ($constraint->mode === Email::VALIDATION_MODE_STRICT) {
            $baseEmailValidator = new BaseEmailValidator(Email::VALIDATION_MODE_STRICT);
            $baseEmailValidator->initialize($this->context);
            $baseEmailValidator->validate($value, $constraint);

            return;
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string) $value;

        $noRfcValidator = new NoRFCEmailValidator();
        if (!$noRfcValidator->isValid($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(Email::INVALID_FORMAT_ERROR)
                ->addViolation();
        }
    }
}
