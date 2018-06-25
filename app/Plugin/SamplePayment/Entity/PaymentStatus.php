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

namespace Plugin\SamplePayment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\AbstractMasterEntity;

/**
 * PaymentStatus
 *
 * TODO プラグインのテーブルで必要なアノテーションを精査
 *
 * @ORM\Table(name="plg_sample_payment_payment_status")
 * @ORM\Entity(repositoryClass="Plugin\SamplePayment\Repository\PaymentStatusRepository")
 */
class PaymentStatus extends AbstractMasterEntity
{
    // TODO 定数名は要変更

    /**
     * 未決済
     */
    const OUTSTANDING = 1;
    /**
     * 有効性チェック済
     */
    const ENABLED = 2;
    /**
     * 仮売上
     */
    const PROVISIONAL_SALES = 3;
    /**
     * 実売上
     */
    const ACTUAL_SALES = 4;
    /**
     * キャンセル
     */
    const CANCEL = 5;
}
