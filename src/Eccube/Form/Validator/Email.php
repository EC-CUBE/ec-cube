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

/**
 * Annotation
 * Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * ANNOTATIONは存在しないため、TARGET_CLASSとIS_REPEATABLE で代用
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Email extends \Symfony\Component\Validator\Constraints\Email
{
}
