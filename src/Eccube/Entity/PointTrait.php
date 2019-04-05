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

trait PointTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="add_point", type="decimal", precision=12, scale=0, options={"unsigned":true,"default":0})
     */
    private $add_point = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="use_point", type="decimal", precision=12, scale=0, options={"unsigned":true,"default":0})
     */
    private $use_point = '0';

    /**
     * Set addPoint
     *
     * @param string $addPoint
     *
     * @return Order
     */
    public function setAddPoint($addPoint)
    {
        $this->add_point = $addPoint;

        return $this;
    }

    /**
     * Get addPoint
     *
     * @return string
     */
    public function getAddPoint()
    {
        return $this->add_point;
    }

    /**
     * Set usePoint
     *
     * @param string $usePoint
     *
     * @return Order
     */
    public function setUsePoint($usePoint)
    {
        $this->use_point = $usePoint;

        return $this;
    }

    /**
     * Get usePoint
     *
     * @return string
     */
    public function getUsePoint()
    {
        return $this->use_point;
    }
}
