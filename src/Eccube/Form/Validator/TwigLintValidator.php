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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Twig\Error\Error;
use Twig\Loader\ArrayLoader;
use Twig\Source;

class TwigLintValidator extends ConstraintValidator
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * TwigLintValidator constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        // valueがnullの場合は "Template is not defined"のエラーが投げられるので, 空文字でチェックする.
        if (is_null($value)) {
            $value = '';
        }

        $realLoader = $this->twig->getLoader();
        try {
            $temporaryLoader = new ArrayLoader(['' => $value]);
            $this->twig->setLoader($temporaryLoader);
            $nodeTree = $this->twig->parse($this->twig->tokenize(new Source($value, '')));
            $this->twig->compile($nodeTree);
        } catch (Error $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ error }}', $e->getMessage())
                ->addViolation();
        }
        $this->twig->setLoader($realLoader);
    }
}
