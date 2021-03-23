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

if (!class_exists(LoginHistoryStatus::class, false)) {
    /**
     * LoginHistoryStatus
     *
     * @ORM\Table(name="mtb_login_history_status")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\Master\LoginHistoryStatusRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class LoginHistoryStatus extends \Eccube\Entity\Master\AbstractMasterEntity
    {
        /**
         * 失敗
         */
        const FAILURE = 0;

        /**
         * 成功
         */
        const SUCCESS = 1;
    }
}
