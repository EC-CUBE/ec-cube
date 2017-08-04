<?php

namespace Eccube\Form\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Twig\Error\Error;
use Twig\Loader\ArrayLoader;
use Twig\Source;

class TwigLintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        // valueがnullの場合は "Template is not defined"のエラーが投げられるので, 空文字でチェックする.
        if (is_null($value)) {
            $value = '';
        }

        try {
            $loader = new ArrayLoader(['' => $value]);
            $twig = new \Twig_Environment($loader);
            $nodeTree = $twig->parse($twig->tokenize(new Source($value, '')));
            $twig->compile($nodeTree);

        } catch (Error $e) {

            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ error }}', $e->getMessage())
                ->addViolation();
        }
    }
}
