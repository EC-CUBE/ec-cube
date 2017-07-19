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


use Eccube\Application;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\Processor\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlowResult;

class AbstractShoppingController extends AbstractController
{

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
     * @param Application $app
     * @param ItemHolderInterface $itemHolder
     * @return PurchaseFlowResult
     */
    protected function executePurchaseFlow(Application $app, ItemHolderInterface $itemHolder)
    {
        /** @var PurchaseFlowResult $flowResult */
        $flowResult = $app['eccube.purchase.flow.shopping']->calculate($itemHolder, PurchaseContext::create($app));
        foreach ($flowResult->getWarning() as $warning) {
            $app->addRequestError($warning->getMessage());
        }
        foreach ($flowResult->getErrors() as $error) {
            $app->addRequestError($error->getMessage());
        }
        return $flowResult;
    }

}