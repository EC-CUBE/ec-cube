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

namespace Eccube\Form\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TwigLint extends Constraint
{
    public $message = 'Invalid twig format. {{ error }}';
}
