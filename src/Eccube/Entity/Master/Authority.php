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
use Eccube\Repository\Master\AuthorityRepository;

if (!class_exists(Authority::class, false)) {
    /**
     * Authority
     */
    #[ORM\Table(name: 'mtb_authority')]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: AuthorityRepository::class)]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    class Authority extends AbstractMasterEntity
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
