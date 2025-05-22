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

if (!class_exists(Authority::class, false)) {
    /**
     * Authority
     *
     * @ORM\Table(name="mtb_authority")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\Master\AuthorityRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class Authority extends \Eccube\Entity\Master\AbstractMasterEntity
    {
        /**
         * システム管理者
         */
        public const ADMIN = 0;

        /**
         * 店舗オーナー
         */
        public const OWNER = 1;
    }
}
