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

if (!class_exists(TaxDisplayType::class, false)) {
    /**
     * TaxDisplayType
     *
     * 税抜表示 / 税込表示
     *
     * @ORM\Table(name="mtb_tax_display_type")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\Master\TaxDisplayTypeRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class TaxDisplayType extends \Eccube\Entity\Master\AbstractMasterEntity
    {
        /**
         * 税抜.
         *
         * @var integer
         */
        const EXCLUDED = 1;

        /**
         * 税込.
         *
         * @var integer
         */
        const INCLUDED = 2;
    }
}
