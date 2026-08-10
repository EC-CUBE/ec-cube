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
 * 在庫状況ステータス. ACP / UCP で共通の既知値を string backed enum で表現する.
 *
 * ACP availability.status / UCP variant.availability.status のいずれの既知値とも整合する。
 *
 * 標準実装 (CatalogMapper) が出力するのは IN_STOCK / OUT_OF_STOCK の 2 値のみ:
 *   - stock_unlimited、または stock > 0          -> IN_STOCK
 *   - stock_unlimited でなく stock <= 0 / null   -> OUT_OF_STOCK
 *
 * LIMITED_STOCK / BACKORDER / PREORDER / DISCONTINUED は仕様準拠のため定義しているが
 * 標準では出力しない (Customize で CatalogMapper を decoration して使う拡張ポイント。
 * 例: 少量在庫 -> LIMITED_STOCK、ProductStatus::DISPLAY_ABOLISHED -> DISCONTINUED、
 * 入荷予定 -> BACKORDER / PREORDER)。EC-CUBE 状態との対応表は CatalogMapper の docblock を参照。
 *
 * @see CatalogMapper
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json
 */
enum AvailabilityStatus: string
{
    case IN_STOCK = 'in_stock';
    case LIMITED_STOCK = 'limited_stock';
    case BACKORDER = 'backorder';
    case PREORDER = 'preorder';
    case OUT_OF_STOCK = 'out_of_stock';
    case DISCONTINUED = 'discontinued';
}
