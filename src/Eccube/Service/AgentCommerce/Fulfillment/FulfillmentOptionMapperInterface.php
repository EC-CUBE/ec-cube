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

namespace Eccube\Service\AgentCommerce\Fulfillment;

use Eccube\Entity\Master\Pref;
use Eccube\Entity\ProductClass;

/**
 * 注文明細と配送先から、エージェントへ提示する配送方法の選択肢を組み立てる.
 *
 * 標準実装を `app/Customize` で差し替え可能にするための seam。
 */
interface FulfillmentOptionMapperInterface
{
    /**
     * 配送先 `Pref` に対して利用可能な配送方法の選択肢を返す.
     *
     * @param iterable<ProductClass> $productClasses 対象明細の商品規格
     *
     * @return array<int, FulfillmentOption>
     */
    public function mapForDestination(iterable $productClasses, Pref $pref, string $currencyCode = 'JPY'): array;
}
