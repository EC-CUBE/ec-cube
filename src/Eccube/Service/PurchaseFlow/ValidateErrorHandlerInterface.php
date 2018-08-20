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

namespace Eccube\Service\PurchaseFlow;

/**
 * PurchaseFlow でのエラーハンドラ.
 *
 * このインターフェイスを実装することで、 InvalidItemException がスローされた場合の遷移先を指定できます。
 */
interface ValidateErrorHandlerInterface
{
    /**
     * @return string The name of the route
     */
    public function getRoute();

    /**
     * @return string[] An array of parameters
     */
    public function getRouteParameters();
}
