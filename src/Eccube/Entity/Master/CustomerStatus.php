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

if (!class_exists(CustomerStatus::class, false)) {
    /**
     * CustomerStatus
     *
     * @ORM\Table(name="mtb_customer_status")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\Master\CustomerStatusRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class CustomerStatus extends \Eccube\Entity\Master\AbstractMasterEntity
    {
        /**
         * 仮会員.
         *
         * @deprecated
         */
        const NONACTIVE = 1;

        /**
         * 本会員.
         *
         * @deprecated
         */
        const ACTIVE = 2;

        /**
         * 仮会員.
         */
        const PROVISIONAL = 1;

        /**
         * 本会員
         */
        const REGULAR = 2;

        /**
         * 退会
         */
        const WITHDRAWING = 3;
    }
}
