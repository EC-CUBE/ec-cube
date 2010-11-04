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
<!--▼CONTENTS-->
<div id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|escape}--></h2>
    <!--{include file=$tpl_navi}-->
    <div id="mycontentsarea">
        <h3><!--{$tpl_subtitle|escape}--></h3>
        <p class="myconditionarea">
        <strong>購入日時：&nbsp;</strong><!--{$arrDisp.create_date|sfDispDBDate}--><br />
        <strong>注文番号：&nbsp;</strong><!--{$arrDisp.order_id}--><br />
        <strong>お支払い方法：&nbsp;</strong><!--{$arrPayment[$arrDisp.payment_id]|escape}-->
        <!--{if $arrDisp.deliv_time_id != ""}--><br />
        <strong>お届け時間：&nbsp;</strong><!--{$arrDelivTime[$arrDisp.deliv_time_id]|escape}-->
        <!--{/if}-->
        <!--{if $arrDisp.deliv_date != ""}--><br />
        <strong>お届け日：&nbsp;</strong><!--{$arrDisp.deliv_date|escape}-->
        <!--{/if}-->
        </p>

        <!--{* (開発者向けレビュー)
        <form action="order.php" method="post">
            <input type="hidden" name="order_id" value="<!--{$arrDisp.order_id}-->">
            <input type="submit" name="submit" value="再注文">
        </form>
        *}-->

        <table summary="購入商品詳細">
            <tr>
                <th>商品コード</th>
                <th>商品名</th>
                <th>配送商品/ダウンロード</th>
                <th>単価</th>
                <th>数量</th>
                <th>小計</th>
            </tr>
            <!--{foreach from=$tpl_arrOrderDetail item=orderDetail}-->
                <tr>
                    <td><!--{$orderDetail.product_code|escape}--></td>
                    <td><a<!--{if $orderDetail.enable}--> href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$orderDetail.product_id|escape:url}-->"<!--{/if}-->><!--{$orderDetail.product_name|escape}--></a></td>
                    <td>
                    <!--{ if $orderDetail.down == "2"}-->
                        <!--{ if $orderDetail.price == "0" || ( $orderDetail.status >= "4" && $orderDetail.effective == "1" )}-->
                            <a target="_self" href="<!--{$smarty.const.URL_DIR}-->mypage/download.php?order_id=<!--{$arrDisp.order_id}-->&product_id=<!--{$orderDetail.product_id}-->&product_class_id=<!--{$orderDetail.product_class_id}-->">ダウンロード</a>
                        <!--{ elseif $orderDetail.payment_date == "" || $orderDetail.status < "4"}-->
                            ダウンロード商品<BR />（入金確認中）
                        <!--{ elseif $orderDetail.effective != "1"}-->
                            ダウンロード商品<BR />（期限切れ）
                        <!--{ /if }-->
                    <!--{ else }-->
                            配送商品
                    <!--{ /if }-->
                    </td>
                    <!--{assign var=price value=`$orderDetail.price`}-->
                    <!--{assign var=quantity value=`$orderDetail.quantity`}-->
                    <td class="pricetd"><!--{$price|escape|number_format}-->円</td>
                    <td><!--{$quantity|escape}--></td>
                    <td class="pricetd"><!--{$price|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|sfMultiply:$quantity|number_format}-->円</td>
                </tr>
            <!--{/foreach}-->
            <tr>
                <th colspan="5" class="resulttd">小計</th>
                <td class="pricetd"><!--{$arrDisp.subtotal|number_format}-->円</td>
            </tr>
            <!--{assign var=point_discount value="`$arrDisp.use_point*$smarty.const.POINT_VALUE`"}-->
            <!--{if $point_discount > 0}-->
            <tr>
                <th colspan="5" class="resulttd">ポイント値引き</th>
                <td class="pricetd"><!--{$point_discount|number_format}-->円</td>
            </tr>
            <!--{/if}-->
            <!--{assign var=key value="discount"}-->
            <!--{if $arrDisp[$key] != "" && $arrDisp[$key] > 0}-->
            <tr>
                <th colspan="5" class="resulttd">値引き</th>
                <td class="pricetd"><!--{$arrDisp[$key]|number_format}-->円</td>
            </tr>
            <!--{/if}-->
            <tr>
                <th colspan="5" class="resulttd">送料</th>
                <td class="pricetd"><!--{assign var=key value="deliv_fee"}--><!--{$arrDisp[$key]|escape|number_format}-->円</td>
            </tr>
            <tr>
                <th colspan="5" class="resulttd">手数料</th>
                <!--{assign var=key value="charge"}-->
                <td class="pricetd"><!--{$arrDisp[$key]|escape|number_format}-->円</td>
            </tr>
            <tr>
                <th colspan="5" class="resulttd">合計</th>
                <td class="pricetd"><em><!--{$arrDisp.payment_total|number_format}-->円</em></td>
            </tr>
        </table>

        <!-- 使用ポイントここから -->
        <!--{if $smarty.const.USE_POINT !== false}-->
            <table summary="使用ポイント">
                <tr>
                    <th>ご使用ポイント</th>
                    <td class="pricetd"><!--{assign var=key value="use_point"}--><!--{$arrDisp[$key]|number_format|default:0}--> pt</td>
                </tr>
                <tr>
                    <th>今回加算されるポイント</th>
                    <td class="pricetd"><!--{$arrDisp.add_point|number_format|default:0}--> pt</td>
                </tr>
            </table>
        <!--{/if}-->
        <!-- 使用ポイントここまで -->

        <table summary="お届け先" class="delivname">
            <thead>
                <tr>
                    <th colspan="5">▼お届け先</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>お名前</th>
                    <!--{assign var=key1 value="deliv_name01"}-->
                    <!--{assign var=key2 value="deliv_name02"}-->
                    <td><!--{$arrDisp[$key1]|escape}-->&nbsp;<!--{$arrDisp[$key2]|escape}--></td>
                </tr>
                <tr>
                    <th>お名前(フリガナ)</th>
                    <!--{assign var=key1 value="deliv_kana01"}-->
                    <!--{assign var=key2 value="deliv_kana02"}-->
                    <td><!--{$arrDisp[$key1]|escape}-->&nbsp;<!--{$arrDisp[$key2]|escape}--></td>
                </tr>
                <tr>
                    <th>郵便番号</th>
                    <!--{assign var=key1 value="deliv_zip01"}-->
                    <!--{assign var=key2 value="deliv_zip02"}-->
                    <td>〒<!--{$arrDisp[$key1]}-->-<!--{$arrDisp[$key2]}--></td>
                </tr>
                <tr>
                    <th>住所</th>
                    <!--{assign var=pref value=`$arrDisp.deliv_pref`}-->
                    <!--{assign var=key value="deliv_addr01"}-->
                    <td><!--{$arrPref[$pref]}--><!--{$arrDisp[$key]|escape}--><!--{assign var=key value="deliv_addr02"}--><!--{$arrDisp[$key]|escape}--></td>
                </tr>
                <tr>
                    <th>電話番号</th>
                    <!--{assign var=key1 value="deliv_tel01"}-->
                    <!--{assign var=key2 value="deliv_tel02"}-->
                    <!--{assign var=key3 value="deliv_tel03"}-->
                    <td><!--{$arrDisp[$key1]}-->-<!--{$arrDisp[$key2]}-->-<!--{$arrDisp[$key3]}--></td>
                </tr>
            </tbody>
        </table>

        <br />

        <h3>メール配信履歴一覧</h3>
        <table>
            <tr>
                <th>処理日</th>
                <th>通知メール</th>
                <th>件名</th>
            </tr>
            <!--{section name=cnt loop=$arrMailHistory}-->
            <tr class="center">
                <td><!--{$arrMailHistory[cnt].send_date|sfDispDBDate|escape}--></td>
                <!--{assign var=key value="`$arrMailHistory[cnt].template_id`"}-->
                <td><!--{$arrMAILTEMPLATE[$key]|escape}--></td>
                <td><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="win02('./mail_view.php?send_id=<!--{$arrMailHistory[cnt].send_id}-->','mail_view','650','800'); return false;"><!--{$arrMailHistory[cnt].subject|escape}--></a></td>
            </tr>
            <!--{/section}-->
        </table>

        <div class="tblareabtn">
            <a href="./<!--{$smarty.const.DIR_INDEX_URL}-->" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_back_on.gif','change');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_back.gif','change');"><img src="<!--{$TPL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" name="change" id="change" /></a>
        </div>

    </div>
</div>
<!--▲CONTENTS-->
