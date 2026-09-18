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
 * メディア (画像等) をプロトコル非依存に保持する DTO.
 *
 * ACP Variant.media[] / UCP product.media[] のいずれにも写し込めるよう
 * type / url を中心とした最小集合を持つ。url は絶対 URL を保持する。
 */
final readonly class AgentCatalogMediaDto
{
    public function __construct(
        public string $url,
        public string $type = 'image',
        public ?string $altText = null,
        public ?int $width = null,
        public ?int $height = null,
    ) {
    }
}
