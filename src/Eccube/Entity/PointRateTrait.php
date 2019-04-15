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

use Doctrine\ORM\Mapping as ORM;

trait PointRateTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="point_rate", type="decimal", precision=10, scale=0, options={"unsigned":true}, nullable=true)
     */
    private $point_rate;

    /**
     * Set pointRate
     *
     * @param string $pointRate
     *
     * @return OrderItem
     */
    public function setPointRate($pointRate)
    {
        $this->point_rate = $pointRate;

        return $this;
    }

    /**
     * Get pointRate
     *
     * @return string
     */
    public function getPointRate()
    {
        return $this->point_rate;
    }
}
