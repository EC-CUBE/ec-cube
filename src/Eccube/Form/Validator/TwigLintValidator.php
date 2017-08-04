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
