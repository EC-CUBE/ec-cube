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
 * TaxType
 *
 * @ORM\Table(name="mtb_tax_type")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\Master\TaxTypeRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 *
 * @see https://www.nta.go.jp/taxanswer/shohi/6209.htm
 */
class TaxType extends \Eccube\Entity\Master\AbstractMasterEntity
{
    /**
     * 課税.
     *
     * @var integer
     */
    const TAXATION = 1;

    /**
     * 不課税.
     *
     * @var integer
     */
    const NON_TAXABLE = 2;

    /**
     * 非課税.
     *
     * @var integer
     */
    const TAX_EXEMPT = 3;
}
