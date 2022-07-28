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

if (!class_exists(OrderItemType::class, false)) {
    /**
     * OrderItemType
     *
     * 受注明細種別
     *
     * @ORM\Table(name="mtb_order_item_type")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\Master\OrderItemTypeRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class OrderItemType extends \Eccube\Entity\Master\AbstractMasterEntity
    {
        /**
         * 商品.
         *
         * @var integer
         */
        public const PRODUCT = 1;

        /**
         * 送料.
         *
         * @var integer
         */
        public const DELIVERY_FEE = 2;

        /**
         * 手数料.
         *
         * @var integer
         */
        public const CHARGE = 3;

        /**
         * 値引き.
         *
         * @var integer
         */
        public const DISCOUNT = 4;

        /**
         * 税.
         *
         * @var integer
         */
        public const TAX = 5;

        /**
         * ポイント.
         *
         * @var integer
         */
        public const POINT = 6;

        /**
         * 商品かどうか
         *
         * @return bool
         */
        public function isProduct()
        {
            if ($this->id == self::PRODUCT) {
                return true;
            }

            return false;
        }
    }
}
