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

/**
 * ProductStatus
 *
 * @ORM\Table(name="mtb_product_status")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\Master\ProductStatusRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class ProductStatus extends \Eccube\Entity\Master\AbstractMasterEntity
{
    /**
     * 公開
     *
     * @var integer
     */
    const DISPLAY_SHOW = 1;

    /**
     * 非公開
     *
     * @var integer
     */
    const DISPLAY_HIDE = 2;

    /**
     * 廃止
     *
     * @var integer
     */
    const DISPLAY_ABOLISHED = 3;
}
