<?php
/**
 * Created by IntelliJ IDEA.
 * User: kiyotaka_oku
 * Date: 2016/11/01
 * Time: 13:47
 */

namespace Page\Admin;


class TopPage
{
    public static $受付状況 = '#order_info';
    public static $受付状況_新規受付 = '#order-status > div.card-body.p-0 > div:nth-child(1) > a'; // #order_info .link_list .tableish a:nth-child(1)
    public static $受付状況_新規受付数 = '#order-status .card-body .d-block:nth-child(1) .row div:last-child span';
    public static $受付状況_入金待ち = '#order-status .card-body .d-block:nth-child(2) .p-0 a';
    public static $受付状況_入金済み = '#order_info .link_list .tableish a:nth-child(3)';
    public static $受付状況_取り寄せ中 = '#order_info .link_list .tableish a:nth-child(4)';

    public static $お知らせ = '#ec-cube-news .card-header .card-title'; //#cube_news
    public static $売上状況 = '#chart-statistics .card-header .card-title';
    public static $ショップ状況 = '#shop-statistical .card-header .card-title';
    public static $ショップ状況_在庫切れ商品 = '#shop-statistical > div.card-body.p-0 > div:nth-child(1) > a'; //'#shop_info .link_list .tableish a:nth-child(1)';
    public static $ショップ状況_会員数 = '#shop-statistical > div.card-body.p-0 > div:nth-child(3) > a'; //'#shop_info .link_list .tableish a:nth-child(2)';

}