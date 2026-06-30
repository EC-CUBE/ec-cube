<?php

declare(strict_types=1);

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

use Eccube\Rector\CodingStyle\AttributeArgumentsOrderRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([
        AttributeArgumentsOrderRector::class,
    ]);
