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

namespace Eccube\Doctrine\ORM\Mapping\Driver;

/**
 * Legacy AnnotationDriver compatibility shim.
 *
 * In ORM 3.x the original Doctrine AnnotationDriver has been removed.
 * This class now simply extends HybridMappingDriver so that any code
 * referencing AnnotationDriver still works.
 *
 * @deprecated Use HybridMappingDriver directly instead.
 */
class AnnotationDriver extends HybridMappingDriver
{
}
