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

if (!class_exists(OrderStatus::class, false)) {
    /**
     * OrderStatus
     *
     * @ORM\Table(name="mtb_order_status")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\Master\OrderStatusRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class OrderStatus extends \Eccube\Entity\Master\AbstractMasterEntity
    {
        /** 新規受付. */
        public const NEW = 1;
        /** 注文取消し. */
        public const CANCEL = 3;
        /** 対応中. */
        public const IN_PROGRESS = 4;
        /** 発送済み. */
        public const DELIVERED = 5;
        /** 入金済み. */
        public const PAID = 6;
        /** 決済処理中. */
        public const PENDING = 7;
        /** 購入処理中. */
        public const PROCESSING = 8;
        /** 返品 */
        public const RETURNED = 9;

        /**
         * 受注一覧画面で, ステータスごとの受注件数を表示するかどうか
         *
         * @var bool
         *
         * @ORM\Column(name="display_order_count", type="boolean", options={"default":false})
         */
        private $display_order_count = false;

        /**
         * @return bool
         */
        public function isDisplayOrderCount()
        {
            return $this->display_order_count;
        }

        /**
         * @param bool $display_order_count
         */
        public function setDisplayOrderCount($display_order_count = false)
        {
            $this->display_order_count = $display_order_count;
        }
    }
}
