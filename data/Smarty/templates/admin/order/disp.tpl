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
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<h2>受注詳細</h2>

<table class="form">
    <tr>
        <th>対応状況</th>
        <td>
            <!--{if $arrForm.delete.value == 1}-->削除済み
            <!--{else}-->
            <!--{assign var=status value=`$arrForm.status.value`}-->
            <!--{$arrORDERSTATUS[$status]}-->
            <!--{/if}-->
        </td>
    </tr>
    <tr>
        <th>入金日</th>
        <td><!--{$arrForm.payment_date.value|sfDispDBDate|default:"未入金"}--></td>
    </tr>
    <tr>
        <th>発送日</th>
        <td><!--{$arrForm.commit_date.value|sfDispDBDate|default:"未発送"}--></td>
    </tr>
</table>

<h3>お客様情報</h3>
<table class="form">
    <tr>
        <th>注文番号</th>
        <td><!--{$arrForm.order_id.value}--></td>
    </tr>
    <tr>
        <th>受注日</th>
        <td><!--{$arrForm.create_date.value|sfDispDBDate}--></td>
    </tr>
    <tr>
        <th>顧客ID</th>
        <td>
        <!--{if $arrForm.customer_id.value > 0}-->
            <!--{$arrForm.customer_id.value}-->
        <!--{else}-->
            (非会員)
        <!--{/if}-->
        </td>
    </tr>
    <tr>
        <th>顧客名</th>
        <td><!--{$arrForm.order_name01.value|h}--> <!--{$arrForm.order_name02.value|h}--></td>
    </tr>
    <tr>
        <th>顧客名(カナ)</th>
        <td><!--{$arrForm.order_kana01.value|h}--> <!--{$arrForm.order_kana02.value|h}--></td>
    </tr>
    <tr>
        <th>メールアドレス</th>
        <td><a href="mailto:<!--{$arrForm.order_email.value|h}-->"><!--{$arrForm.order_email.value|h}--></a></td>
    </tr>
    <tr>
        <th>TEL</th>
        <td><!--{$arrForm.order_tel01.value}-->-<!--{$arrForm.order_tel02.value}-->-<!--{$arrForm.order_tel03.value}--></td>
    </tr>
    <tr>
        <th>住所</th>
        <td>
            〒<!--{$arrForm.order_zip01.value}-->-<!--{$arrForm.order_zip02.value}--><br />
            <!--{assign var=key value=$arrForm.order_pref.value}-->
            <!--{$arrPref[$key]}--><!--{$arrForm.order_addr01.value}--><!--{$arrForm.order_addr02.value}-->
        </td>
    </tr>
    <tr>
        <th>備考</th>
        <td><!--{$arrForm.message.value|h|nl2br}--></td>
    </tr>
</table>

<!--▼お届け先情報ここから-->
<h3>お届け先情報</h3>
<table class="form">
    <tr>
        <th>お名前</th>
        <td>
            <!--{assign var=key1 value="deliv_name01"}-->
            <!--{assign var=key2 value="deliv_name02"}-->
            <!--{$arrForm[$key1].value|h}-->
            <!--{$arrForm[$key2].value|h}-->
        </td>
    </tr>
    <tr>
        <th>お名前(カナ)</th>
        <td>
            <!--{assign var=key1 value="deliv_kana01"}-->
            <!--{assign var=key2 value="deliv_kana02"}-->
            <!--{$arrForm[$key1].value|h}-->
            <!--{$arrForm[$key2].value|h}-->
        </td>
    </tr>
    <tr>
        <th>郵便番号</th>
        <td>
            <!--{assign var=key1 value="deliv_zip01"}-->
            <!--{assign var=key2 value="deliv_zip02"}-->
            〒<!--{$arrForm[$key1].value|h}-->-<!--{$arrForm[$key2].value|h}-->
        </td>
    </tr>
    <tr>
        <th>TEL</th>
        <td>
            <!--{assign var=key1 value="deliv_tel01"}-->
            <!--{assign var=key2 value="deliv_tel02"}-->
            <!--{assign var=key3 value="deliv_tel03"}-->
            <!--{$arrForm[$key1].value|h}-->-<!--{$arrForm[$key2].value|h}-->-<!--{$arrForm[$key3].value|h}-->
        </td>
    </tr>
    <tr>
        <th>住所</th>
        <td>
            <!--{assign var=pref value=`$arrForm.deliv_pref.value`}-->
            <!--{$arrPref[$pref]}-->
            <!--{assign var=key value="deliv_addr01"}-->
            <!--{$arrForm[$key].value|h}-->
            <!--{assign var=key value="deliv_addr02"}-->
            <!--{$arrForm[$key].value|h}-->
        </td>
    </tr>
</table>
<!--▲お届け先情報ここまで-->

<h3>受注商品情報</h3>
<table class="list">
    <tr>
        <th class="id">商品コード</th>
        <th class="name">商品名/規格1/規格2</th>
        <th class="price">単価</th>
        <th class="qty">数量</th>
        <th class="price">小計</th>
    </tr>
    <!--{section name=cnt loop=$arrForm.quantity.value}-->
    <!--{assign var=key value="`$smarty.section.cnt.index`"}-->
    <tr>
        <td><!--{$arrForm.product_code.value[$key]|h}--></td>
        <td><!--{$arrForm.product_name.value[$key]|h}-->/<!--{$arrForm.classcategory_name1.value[$key]|default:"(なし)"|h}-->/<!--{$arrForm.classcategory_name2.value[$key]|default:"(なし)"|h}--></td>
        <td class="right"><!--{if $arrForm.price.value[$key] != 0}--><!--{$arrForm.price.value[$key]|number_format}-->円<!--{else}-->無料<!--{/if}--></td>
        <td class="center"><!--{$arrForm.quantity.value[$key]|h}--></td>
        <!--{assign var=price value=`$arrForm.price.value[$key]`}-->
        <!--{assign var=quantity value=`$arrForm.quantity.value[$key]`}-->
        <td class="right"><!--{if $price != 0}--><!--{$price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|sfMultiply:$quantity|number_format}-->円<!--{else}-->無料<!--{/if}--></td>
    </tr>
    <!--{/section}-->
    <tr>
        <th colspan="4" class="right">小計</th>
        <td align="right"><!--{$arrForm.subtotal.value|number_format}-->円</td>
    </tr>
    <tr>
        <th colspan="4" class="right">ポイント値引き</th>
        <td align="right"><!--{assign var=point_discount value="`$arrForm.use_point.value*$smarty.const.POINT_VALUE`"}--><!--{$point_discount|number_format}-->円</td>
    </tr>
    <!--{assign var=discount value="`$arrForm.discount.value`"}-->
    <!--{if $discount != "" && $discount > 0}-->
                         <tr>
        <th colspan="4" class="right">値引き</th>
        <td align="right"><!--{$discount|number_format}-->円</td>
    </tr>
    <!--{/if}-->
    <tr>
        <th colspan="4" class="right">送料</th>
        <td align="right"><!--{assign var=key value="deliv_fee"}--><!--{$arrForm[$key].value|number_format|h}--> 円</td>
    </tr>
    <tr>
        <th colspan="4" class="right">手数料</th>
        <td align="right"><!--{assign var=key value="charge"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span><!--{$arrForm[$key].value|number_format|h}--> 円</td>
    </tr>
    <tr>
        <th colspan="4" class="right">合計</th>
        <td align="right"><!--{$arrForm.total.value|number_format}--> 円</td>
    </tr>
    <tr>
        <th colspan="4" class="right">お支払い合計</th>
        <td align="right"><!--{$arrForm.payment_total.value|number_format}--> 円</td>
    </tr>
    <tr>
        <th colspan="4" class="right">使用ポイント</th>
        <td align="right"><!--{assign var=key value="use_point"}--><!--{if $arrForm[$key].value != ""}--><!--{$arrForm[$key].value|number_format}--><!--{else}-->0<!--{/if}--> pt</td>
    </tr>
    <!--{if $arrForm.birth_point.value > 0}-->
    <tr>
        <th colspan="4" class="right">お誕生日ポイント</th>
        <td align="right">
        <!--{$arrForm.birth_point.value|number_format}-->
         pt</td>
    </tr>
    <!--{/if}-->
    <tr>
        <th colspan="4" class="right">加算ポイント</th>
        <td align="right">
        <!--{$arrForm.add_point.value|default:0|number_format}-->
         pt</td>
    </tr>
    <tr>
        <!--{if $arrForm.customer_id.value > 0}-->
        <th colspan="4" class="right">現在ポイント</th>
        <td align="right">
        <!--{$arrForm.point.value|number_format}-->
         pt</td>
        <!--{else}-->
        <th colspan="4" class="right">現在ポイント</th><td align="center">(なし)</td>
        <!--{/if}-->
    </tr>
    <!--{*
    <tr>
        <th colspan="4" class="right">反映後ポイント (ポイントの変更は<a href="?" onclick="return fnEdit('<!--{$arrForm.customer_id.value}-->');">顧客編集</a>から手動にてお願い致します。)</th>
        <td align="right">
            <span class="attention"><!--{$arrErr.total_point}--></span>
            <!--{$arrForm.total_point.value|number_format}-->
             pt
        </td>
    </tr>
    *}-->
</table>

<table class="form">
    <tr>
        <th>お支払方法</th>
        <td>
            <!--{assign var=payment_id value="`$arrForm.payment_id.value`"}-->
            <!--{$arrPayment[$payment_id]|h}-->
        </td>
    </tr>
    <!--{if $arrForm.payment_info.value|@count > 0}-->
    <tr>
        <th><!--{$arrForm.payment_typ.valuee}-->情報</th>
        <td>
            <!--{foreach key=key item=item from=$arrForm.payment_info.value}-->
            <!--{if $key != "title"}--><!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value}--><br/><!--{/if}-->
            <!--{/foreach}-->
        </td>
    </tr>
    <!--{/if}-->
    <tr>
        <th>お届け時間</th>
        <td>
            <!--{assign var=deliv_time_id value="`$arrForm.deliv_time_id.value`"}-->
            <!--{$arrDelivTime[$deliv_time_id]|default:"指定なし"}-->
        </td>
    </tr>
    <tr>
        <th>お届け日</th>
        <td>
            <!--{assign var=key value="deliv_date"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
            <!--{$arrForm[$key].value|default:"指定なし"}-->
        </td>
    </tr>
    <tr>
        <th>メモ</th>
        <td>
            <!--{assign var=key value="note"}-->
            <!--{$arrForm[$key].value|h|nl2br}-->
        </td>
    </tr>
</table>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
