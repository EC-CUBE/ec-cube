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

namespace Page\Admin;

class TopPage
{
    public static $受付状況_新規受付 = '#order-status .card-body .d-block:nth-child(1) a';
    public static $受付状況_新規受付数 = '#order-status .card-body .d-block:nth-child(1) .row div:last-child span';
    public static $受付状況_入金済み = '#order-status .card-body .d-block:nth-child(2) a';
    public static $受付状況_対応中 = '#order-status .card-body .d-block:nth-child(3) a';

    public static $お知らせ = '#ec-cube-news .card-header .card-title'; //#cube_news
    public static $売上状況 = '#chart-statistics .card-header .card-title';
    public static $ショップ状況 = '#shop-statistical .card-header .card-title';
    public static $ショップ状況_在庫切れ商品 = '#shop-statistical > div.card-body.p-0 > div:nth-child(1) > a'; //'#shop_info .link_list .tableish a:nth-child(1)';
    public static $ショップ状況_取扱商品数 = '#shop-statistical > div.card-body.p-0 > div:nth-child(2) > a';
    public static $ショップ状況_会員数 = '#shop-statistical > div.card-body.p-0 > div:nth-child(3) > a'; //'#shop_info .link_list .tableish a:nth-child(2)';
    public static $おすすめのプラグイン = '#ec-cube-plugin .card-header .card-title';
}
