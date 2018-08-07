<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

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
    const ADMIN = 0;

    /**
     * 店舗オーナー
     */
    const OWNER = 1;
}
