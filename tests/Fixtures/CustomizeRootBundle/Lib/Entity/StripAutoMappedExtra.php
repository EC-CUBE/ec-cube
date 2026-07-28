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

namespace Customize\Lib\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 第三者バンドルが持つ Entity. この prefix (Customize\Lib\Entity) は明示登録の対象外なので、
 * 素の AttributeDriver が MappingDriverChain に残り続ける.
 */
#[ORM\Entity]
#[ORM\Table(name: 'dtb_strip_auto_mapped_extra')]
class StripAutoMappedExtra
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
