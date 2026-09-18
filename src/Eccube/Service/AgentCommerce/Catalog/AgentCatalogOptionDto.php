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
 * バリエーション選択肢 (variant option) をプロトコル非依存に保持する DTO.
 *
 * EC-CUBE では規格分類 (ClassCategory) が name = 規格名 (ClassName.name) /
 * value = 規格分類名 (ClassCategory.name) に対応する。
 */
final readonly class AgentCatalogOptionDto
{
    public function __construct(
        public string $name,
        public string $value,
    ) {
    }
}
