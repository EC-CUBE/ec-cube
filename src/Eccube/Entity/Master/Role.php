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

if (!class_exists(Role::class, false)) {
    /**
     * Role
     *
     * @ORM\Table(name="mtb_role")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\Master\RoleRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class Role extends \Eccube\Entity\Master\AbstractMasterEntity
    {        
        /**
         * 表示のみ
         */
        const READ_ONLY = 1;

        /**
         * 非表示
         */
        const HIDE = 2;
    }
}