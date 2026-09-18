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
 * 商品説明をプロトコル非依存に保持する DTO.
 *
 * ACP / UCP いずれの description も plain / html / markdown の最低 1 形式を持つ
 * 開放型オブジェクトであるため、本 DTO もそれに合わせて 3 形式を任意で保持する。
 * 描画側 (プラットフォーム) は html を信頼できない入力として必ずサニタイズする前提。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/common/types/description.json
 */
final readonly class AgentCatalogDescriptionDto
{
    public function __construct(
        public ?string $plain = null,
        public ?string $html = null,
        public ?string $markdown = null,
    ) {
    }

    /**
     * いずれの形式も持たない (全 null) かどうか.
     */
    public function isEmpty(): bool
    {
        return $this->plain === null && $this->html === null && $this->markdown === null;
    }
}
