<?php

declare(strict_types=1);

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

namespace Eccube\Service\Mcp;

/**
 * search_* (一覧系) ツールが返すサマリ射影のフィールド定義。
 *
 * 各定義は対象 Entity の allow_list の部分集合となる「スカラ名」または 1 段のドット path
 * (例 `OrderStatus.name`)。 実際の公開可否は `EntityArraySerializer::toSummary` が allow_list で
 * 最終判定する (許可外は出力されない) ため、 ここは「一覧に出したい候補」の宣言に徹する。
 */
final class McpSummaryFields
{
    /**
     * @var list<string>
     */
    public const ORDER = ['id', 'order_no', 'order_date', 'payment_total', 'name01', 'name02', 'email', 'OrderStatus.name'];

    /**
     * @var list<string>
     */
    public const CUSTOMER = ['id', 'name01', 'name02', 'email', 'phone_number', 'buy_times', 'buy_total', 'last_buy_date'];

    /**
     * @var list<string> Product 基底のサマリ。 価格・在庫は `ProductPriceStockSummarizer` が別途付加する
     */
    public const PRODUCT = ['id', 'name', 'Status.name'];
}
