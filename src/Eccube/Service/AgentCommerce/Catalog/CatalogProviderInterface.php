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

namespace Eccube\Service\AgentCommerce\Catalog;

use Eccube\Entity\Product;

/**
 * カタログ対象 (公開中) の Product を供給するインターフェイス.
 *
 * products.jsonl の全置換生成 (ACP push) や UCP Catalog のページング生成で利用する。
 * 大規模カタログを前提とし、全件をメモリに蓄積せず逐次供給できることを要件とする。
 */
interface CatalogProviderInterface
{
    /**
     * 公開中 (ProductStatus::DISPLAY_SHOW) の Product を逐次供給する.
     *
     * 実装はメモリ蓄積を避けるため Doctrine の toIterable() 等で 1 件ずつ yield する。
     *
     * @return iterable<Product>
     */
    public function provideDisplayProducts(): iterable;
}
