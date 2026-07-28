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

namespace Customize\Lib;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Entity を持つ第三者バンドル相当 (issue #6979 の「準備1」/ 実環境では league/oauth2-server-bundle).
 *
 * prefix が Customize\Lib\Entity になり Kernel::addEntityExtensionPass の明示登録対象外のため、
 * auto_mapping が生成する素の AttributeDriver がこの prefix で MappingDriverChain に残る.
 * 素のドライバは 1 インスタンスに集約されているため、その getAllClassNames() は
 * app/Customize/Entity を含む自身の全パスを走査してしまう (= redeclare の発火条件).
 */
class CustomizeLibBundle extends Bundle
{
}
