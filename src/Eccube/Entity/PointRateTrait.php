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

trait PointRateTrait
{
    #[ORM\Column(name: 'point_rate', type: Types::DECIMAL, precision: 10, scale: 0, options: ['unsigned' => true], nullable: true)]
    private ?string $point_rate = null;

    /**
     * Set pointRate
     *
     * @return $this
     */
    public function setPointRate(?string $pointRate): static
    {
        $this->point_rate = $pointRate;

        return $this;
    }

    /**
     * Get pointRate
     */
    public function getPointRate(): ?string
    {
        return $this->point_rate;
    }
}
