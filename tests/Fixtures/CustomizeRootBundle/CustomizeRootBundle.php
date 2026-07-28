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

namespace Customize;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * app/Customize 直下に置かれた Bundle (issue #6979 の再現構成).
 *
 * doctrine-bundle は Bundle クラスファイルのあるディレクトリ + /Entity を auto_mapping 対象にするため、
 * この配置では app/Customize/Entity が prefix Customize\Entity で素の AttributeDriver にも登録される.
 */
class CustomizeRootBundle extends Bundle
{
}
