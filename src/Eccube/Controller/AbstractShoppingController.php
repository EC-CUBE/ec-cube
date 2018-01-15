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

namespace Eccube\Controller;


use Eccube\Annotation\Inject;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\PurchaseFlow\PurchaseFlowResult;

class AbstractShoppingController extends AbstractController
{
    /**
     * @Inject("eccube.purchase.flow.shopping")
     * @var PurchaseFlow
     * @required
     */
    protected $purchaseFlow;

    /**
     * @return PurchaseFlow
     */
    public function getPurchaseFlow(): PurchaseFlow
    {
        return $this->purchaseFlow;
    }

    /**
     * @param PurchaseFlow $purchaseFlow
     * @required
     */
    public function setPurchaseFlow(PurchaseFlow $purchaseFlow): void
    {
        $this->purchaseFlow = $purchaseFlow;
    }

    /**
     * @Inject("eccube.purchase.context")
     * @var PurchaseContext
     */
    protected $purchaseContext;


    /**
     * @var string 非会員用セッションキー
     */
    protected $sessionKey = 'eccube.front.shopping.nonmember';

    /**
     * @var string 非会員用セッションキー
     */
    protected $sessionCustomerAddressKey = 'eccube.front.shopping.nonmember.customeraddress';

    /**
     * @var string 複数配送警告メッセージ
     */
    protected $sessionMultipleKey = 'eccube.front.shopping.multiple';

    /**
     * @var string 受注IDキー
     */
    protected $sessionOrderKey = 'eccube.front.shopping.order.id';

    /**
     * @param ItemHolderInterface $itemHolder
     * @return PurchaseFlowResult
     */
    protected function executePurchaseFlow(ItemHolderInterface $itemHolder)
    {
        /** @var PurchaseFlowResult $flowResult */
        $flowResult = $this->purchaseFlow->calculate($itemHolder, new PurchaseContext($itemHolder, $itemHolder->getCustomer()));
        foreach ($flowResult->getWarning() as $warning) {
            $this->addRequestError($warning->getMessage());
        }
        foreach ($flowResult->getErrors() as $error) {
            $this->addRequestError($error->getMessage());
        }
        return $flowResult;
    }

}
