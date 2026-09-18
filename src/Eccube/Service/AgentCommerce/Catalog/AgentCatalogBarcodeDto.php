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

/**
 * バーコード (GTIN / JAN 等) をプロトコル非依存に保持する DTO.
 *
 * EC-CUBE 標準には GTIN フィールドが無いため、標準のマッピングでは barcodes は
 * 常に空配列になる。Customize 拡張で ProductReferenceResolverInterface /
 * CatalogMapper を差し替えた場合にのみ値が入る想定。
 */
final readonly class AgentCatalogBarcodeDto
{
    public function __construct(
        public string $type,
        public string $value,
    ) {
    }
}
