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

namespace Eccube\Controller;

use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\PurchaseFlow\PurchaseFlowResult;

class AbstractShoppingController extends AbstractController
{
    /**
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    /**
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
     * @var string 受注IDキー
     */
    protected $sessionOrderKey = 'eccube.front.shopping.order.id';

    /**
     * @param PurchaseFlow $shoppingPurchaseFlow
     * @required
     */
    public function setPurchaseFlow(PurchaseFlow $shoppingPurchaseFlow)
    {
        $this->purchaseFlow = $shoppingPurchaseFlow;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     *
     * @return PurchaseFlowResult
     */
    protected function executePurchaseFlow(ItemHolderInterface $itemHolder)
    {
        /** @var PurchaseFlowResult $flowResult */
        $flowResult = $this->purchaseFlow->calculate($itemHolder, new PurchaseContext($itemHolder, $itemHolder->getCustomer()));
        foreach ($flowResult->getWarning() as $warning) {
            $this->addRequestError($warning);
        }
        foreach ($flowResult->getErrors() as $error) {
            $this->addRequestError($error);
        }

        return $flowResult;
    }
}
