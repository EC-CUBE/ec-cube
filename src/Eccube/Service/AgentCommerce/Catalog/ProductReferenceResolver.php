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
use Eccube\Repository\ProductClassRepository;

/**
 * ProductReferenceResolverInterface の標準実装.
 *
 * sku は ProductClass.code、product_class_id は ProductClass.id で解決する。
 * barcode は EC-CUBE 標準に GTIN フィールドが無いため常に null を返す (Customize seam)。
 */
class ProductReferenceResolver implements ProductReferenceResolverInterface
{
    public function __construct(
        private readonly ProductClassRepository $productClassRepository,
    ) {
    }

    public function resolveBySku(string $sku): ?ProductClass
    {
        if ($sku === '') {
            return null;
        }

        return $this->productClassRepository->findOneBy(['code' => $sku]);
    }

    public function resolveByProductClassId(int $productClassId): ?ProductClass
    {
        return $this->productClassRepository->find($productClassId);
    }

    /**
     * 標準では barcode 解決手段を持たないため常に null。
     * Customize で GTIN マスタ等を導入した場合に override する。
     */
    public function resolveByBarcode(string $barcode): ?ProductClass
    {
        return null;
    }
}
