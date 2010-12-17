<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
<!--{* ▼CONTENTS *}-->
<div id="home">

    <!--{* お知らせここから *}-->
    <div id="home-info">
        <!--{foreach item=info from=$arrInfo}-->
        <dl class="home-info-item">
            <dt class="date"><!--{$info.disp_date|sfDispDBDate:false|escape}--></dt>
            <dt class="title"><!--{$info.title}--></dt>
            <dd class="body"><!--{$info.body}--></dd>
        </dl>
        <!--{/foreach}-->
    </div>
    <!--{* お知らせここまで *}-->

    <!--{* メインエリア *}-->
    <div id="home-main">
    <form name="form1" method="post" action="#">

            <!--{* システム情報ここから *}-->
            <h2>システム情報</h2>
            <table summary="システム情報" class="shop-info">
                <tr>
                    <th>EC-CUBEバージョン</td>
                    <td><!--{$smarty.const.ECCUBE_VERSION}--></td>
                </tr>
                <tr>
                    <th>PHPバージョン</td>
                    <td><!--{$php_version}--></td>
                </tr>
                <tr>
                    <th>DBバージョン</td>
                    <td><!--{$db_version}--></td>
                </tr>
                <tr>
                    <th>詳細</td>
                    <td><a href="<!--{$smarty.const.URL_DIR}--><!--{$smarty.const.ADMIN_DIR}-->system/system.php">システム設定＞システム情報</a></td>
                </tr>
            </table>
            <!--{* システム情報ここまで *}-->
            
            <!--{* ショップの状況ここから *}-->
            <h2>ショップの状況</h2>
            <table summary="ショップの状況" class="shop-info">
                <tr>
                    <th>現在の会員数</td>
                    <td><!--{$customer_cnt|default:"0"|number_format}-->名</td>
                </tr>
                <tr>
                    <th>昨日の売上高</td>
                    <td><!--{$order_yesterday_amount|default:"0"|number_format}-->円</td>
                </tr>
                <tr>
                    <th>昨日の売上件数</td>
                    <td><!--{$order_yesterday_cnt|default:"0"|number_format}-->件</td>
                </tr>
                <tr>
                    <th><span>今月の売上高</span><span>(昨日まで) </span></td>
                    <td><!--{$order_month_amount|default:"0"|number_format}-->円</td>
                </tr>
                <tr>
                    <th><span>今月の売上件数 </span><span>(昨日まで) </span></td>
                    <td><!--{$order_month_cnt|default:"0"|number_format}-->件</td>
                </tr>
                <tr>
                    <th>昨日のレビュー書き込み数</td>
                    <td><!--{$review_yesterday_cnt|default:"0"}-->件</td>
                </tr>
                <tr>
                    <th>顧客の保持ポイント合計</td>
                    <td><!--{$customer_point|default:"0"}-->pt</td>
                </tr>
                <tr>
                    <th>レビュー書き込み非表示数</td>
                    <td><!--{$review_nondisp_cnt|default:"0"}-->件</td>
                </tr>
                <tr>
                    <th>品切れ商品</td>
                    <td>
                    <!--{section name=i loop=$arrSoldout}-->
                    <!--{$arrSoldout[i].product_id}-->:<!--{$arrSoldout[i].name|escape}--><br />
                    <!--{/section}-->
                    </td>
                </tr>
            </table>
            <!--{* ショップの状況ここまで *}-->

            <!--{* 新規受付一覧ここから *}-->
            <h2>新規受付一覧</h2>
            <table summary="新規受付一覧" id="home-order">
                <tr>
                    <th>受注日</th>
                    <th>顧客名</th>
                    <th>購入商品</th>
                    <th>支払方法</th>
                    <th>購入金額(円)</th>
                </tr>
                <!--{section name=i loop=$arrNewOrder}-->
                <tr>
                    <td><!--{$arrNewOrder[i].create_date}--></td>
                    <td><!--{$arrNewOrder[i].name01|escape}--> <!--{$arrNewOrder[i].name02|escape}--></td>
                    <td><!--{$arrNewOrder[i].product_name|escape}--></td>
                    <td><!--{$arrNewOrder[i].payment_method|escape}--></td>
                    <td class="right"><!--{$arrNewOrder[i].total|number_format}-->円</td>
                </tr>
                <!--{/section}-->
            </table>
            <!--{* 新規受付一覧ここまで *}-->

    </form>
    </div>
    <!--{* メインエリア *}-->

</div>
<!--{* ▲CONTENTS *}-->



































































