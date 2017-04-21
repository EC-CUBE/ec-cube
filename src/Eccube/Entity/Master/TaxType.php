<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
 * @link https://www.nta.go.jp/taxanswer/shohi/6209.htm
 */
class TaxType extends \Eccube\Entity\Master\AbstractMasterEntity
{
    /**
     * 課税.
     * @var integer
     */
    const TAXATION = 1;

    /**
     * 不課税.
     * @var integer
     */
    const NON_TAXABLE = 2;

    /**
     * 非課税.
     * @var integer
     */
    const TAX_EXEMPT = 2;
}
