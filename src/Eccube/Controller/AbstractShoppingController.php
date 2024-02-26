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

namespace Eccube\Controller;

use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\PurchaseFlow\PurchaseFlowResult;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AbstractShoppingController extends AbstractController
{
    /**
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

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
     * @param bool $returnResponse レスポンスを返すかどうか. falseの場合はPurchaseFlowResultを返す.
     *
     * @return PurchaseFlowResult|RedirectResponse|null
     */
    protected function executePurchaseFlow(ItemHolderInterface $itemHolder, $returnResponse = true)
    {
        /** @var PurchaseFlowResult $flowResult */
        $flowResult = $this->purchaseFlow->validate($itemHolder, new PurchaseContext(clone $itemHolder, $itemHolder->getCustomer()));
        foreach ($flowResult->getWarning() as $warning) {
            $this->addWarning($warning->getMessage());
        }
        foreach ($flowResult->getErrors() as $error) {
            $this->addError($error->getMessage());
        }

        if (!$returnResponse) {
            return $flowResult;
        }

        if ($flowResult->hasError()) {
            log_info('Errorが発生したため購入エラー画面へ遷移します.', [$flowResult->getErrors()]);

            return $this->redirectToRoute('shopping_error');
        }

        if ($flowResult->hasWarning()) {
            log_info('Warningが発生したため注文手続き画面へ遷移します.', [$flowResult->getWarning()]);

            return $this->redirectToRoute('shopping');
        }

        return null;
    }
}
