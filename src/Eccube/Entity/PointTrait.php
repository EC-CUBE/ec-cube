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

namespace Eccube\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait PointTrait
{
    #[ORM\Column(name: 'add_point', type: Types::DECIMAL, precision: 12, scale: 0, options: ['unsigned' => true, 'default' => 0])]
    private ?string $add_point = '0';

    #[ORM\Column(name: 'use_point', type: Types::DECIMAL, precision: 12, scale: 0, options: ['unsigned' => true, 'default' => 0])]
    private ?string $use_point = '0';

    /**
     * Set addPoint
     */
    public function setAddPoint(string $addPoint): static
    {
        $this->add_point = $addPoint;

        return $this;
    }

    /**
     * Get addPoint
     */
    public function getAddPoint(): string
    {
        return $this->add_point;
    }

    /**
     * Set usePoint
     */
    public function setUsePoint(?string $usePoint): static
    {
        $this->use_point = $usePoint;

        return $this;
    }

    /**
     * Get usePoint
     */
    public function getUsePoint(): ?string
    {
        return $this->use_point;
    }
}
