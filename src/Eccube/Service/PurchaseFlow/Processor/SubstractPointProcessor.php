<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Service\PurchaseFlow\Processor;

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Service\PurchaseFlow\ItemHolderProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 利用ポイントに応じてポイントを減算する.
 */
class SubstractPointProcessor implements ItemHolderProcessor
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * SubstractPointProcessor constructor.
     *
     * @param $app
     */
    public function __construct(BaseInfo $BaseInfo)
    {
        $this->BaseInfo = $BaseInfo;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext     $context
     *
     * @return ProcessResult
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        /** @var Order $Order */
        $Order = $itemHolder;
        if ($Order->getUsePoint() > 0) {
            $Order->setAddPoint($this->substract($Order->getAddPoint(), $Order->getUsePoint(), $this->BaseInfo->getBasicPointRate()));
        }
        return ProcessResult::success();
    }

    /**
     * Substract point.
     *
     * @param integer $totalPoint 合計ポイント
     * @param integer $usePoint 利用ポイント
     * @param integer $pointRate ポイント付与率(%)
     * @return integer Point after substraction
     */
    protected function substract($totalPoint, $usePoint, $pointRate)
    {
        $add_point = $totalPoint - intval($usePoint * ($pointRate / 100));
        return $add_point < 0 ? 0 : $add_point;
    }
}
