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

namespace Plugin\MasterEntityExtension\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Attribute\EntityExtension;
use Eccube\Entity\Master\DeviceType;

/**
 * マスタデータのEntity(Entity/Master配下)をtraitで拡張するサンプル.
 * Issue #5400 / #6273 の回帰テスト用.
 */
#[EntityExtension(DeviceType::class)]
trait DeviceTypeTrait
{
    #[ORM\Column(name: 'notes', type: Types::STRING, nullable: true)]
    public ?string $notes = null;
}
