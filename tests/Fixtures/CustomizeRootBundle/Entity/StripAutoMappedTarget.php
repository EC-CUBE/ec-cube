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

namespace Customize\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * app/Customize/Entity に置かれる Entity (issue #6979 の再現構成).
 *
 * テスト実行時に同一 FQCN の Proxy が app/proxy/entity 配下に複製され、
 * Kernel::loadEntityProxies() が先にロードする.
 */
#[ORM\Entity]
#[ORM\Table(name: 'dtb_strip_auto_mapped_target')]
class StripAutoMappedTarget
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
