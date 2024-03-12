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

namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(RoundingType::class, false)) {
    /**
     * RoundingType
     *
     * @ORM\Table(name="mtb_rounding_type")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\Master\RoundingTypeRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class RoundingType extends \Eccube\Entity\Master\AbstractMasterEntity
    {
        /**
         * 四捨五入.
         *
         * @var integer
         */
        public const ROUND = 1;
        /**
         * 切り捨て.
         *
         * @var integer
         */
        public const FLOOR = 2;
        /**
         * 切り上げ.
         *
         * @var integer
         */
        public const CEIL = 3;
    }
}
