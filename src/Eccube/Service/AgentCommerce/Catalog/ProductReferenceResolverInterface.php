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

use Eccube\Entity\ProductClass;

/**
 * エージェント (ACP / UCP) から渡される商品参照子 (sku / product_class_id / barcode) を
 * EC-CUBE の ProductClass に解決するインターフェイス.
 *
 * Customize で独自の参照体系 (外部 SKU / GTIN マスタ等) を持つ場合は本インターフェイスを
 * 差し替える (services.yaml の alias を上書き) ことで解決ロジックを拡張できる。
 */
interface ProductReferenceResolverInterface
{
    /**
     * 店舗商品コード (ProductClass.code) から ProductClass を解決する.
     */
    public function resolveBySku(string $sku): ?ProductClass;

    /**
     * ProductClass.id から ProductClass を解決する.
     */
    public function resolveByProductClassId(int $productClassId): ?ProductClass;

    /**
     * バーコード (GTIN / JAN 等) から ProductClass を解決する.
     *
     * EC-CUBE 標準には GTIN フィールドが無いため、標準実装は常に null を返す。
     * Customize 拡張で GTIN マスタ等を持つ場合に本メソッドを実装して解決させる
     * (barcodes[] 出力と対をなす Customize seam)。
     */
    public function resolveByBarcode(string $barcode): ?ProductClass;
}
