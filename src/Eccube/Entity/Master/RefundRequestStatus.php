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
use Eccube\Repository\Master\RefundRequestStatusRepository;

/**
 * RefundRequestStatus
 */
#[ORM\Table(name: 'mtb_refund_request_status')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: RefundRequestStatusRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class RefundRequestStatus extends AbstractMasterEntity
{
    /** 新規申請. */
    public const NEW = 1;
    /** 処理中. */
    public const PROCESSING = 2;
    /** 承認済. */
    public const ACCEPTED = 3;
    /** 却下. */
    public const DECLINED = 4;
    /** 追加情報依頼. */
    public const INFO_REQUESTED = 5;
}
