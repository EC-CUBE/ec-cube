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
<center>お支払方法・お届け時間等の指定</center>

<hr>

<form method="post" action="?">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
■お支払方法 <font color="#FF0000">*</font><br>
<!--{assign var=key value="payment_id"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<!--{section name=cnt loop=$arrPayment}-->
<input type="radio" name="<!--{$key}-->" value="<!--{$arrPayment[cnt].payment_id}-->" <!--{$arrPayment[cnt].payment_id|sfGetChecked:$arrForm[$key].value}-->>
<!--{$arrPayment[cnt].payment_method|h}-->
<br>
<!--{/section}-->
<br>

<!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
■お届け時間の指定<br>
<!--{foreach item=shippingItem name=shippingItem from=$shipping}-->
<!--{assign var=index value=$smarty.foreach.shippingItem.index}-->
<!--{if $isMultiple}-->
▼お届け先<!--{$smarty.foreach.shippingItem.iteration}--><br>
<!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01}--><!--{$shippingItem.shipping_addr02}--><br>
<!--{$shippingItem.shipping_name01}--><!--{$shippingItem.shipping_name02}--><br>
<!--{/if}-->
<!--★お届け日★-->
<!--{assign var=key value="deliv_date`$index`"}-->
<!--{if $arrErr[$key] != ""}--><font color="#FF0000"><!--{$arrErr[$key]}--></font><!--{/if}-->
お届け日：
<!--{if !$arrDelivDate}-->
ご指定頂けません。
<!--{else}-->
<select name="<!--{$key}-->">
<option value="">指定なし</option>
<!--{html_options options=$arrDelivDate selected=$arrForm[$key].value}-->
</select>
<!--{/if}-->
<br>
<!--★お届け時間★-->
<!--{assign var=key value="deliv_time_id`$index`"}-->
<!--{if $arrErr[$key] != ""}--><font color="#FF0000"><!--{$arrErr[$key]}--></font><!--{/if}-->
お届け時間：
<select name="<!--{$key}-->">
<option value="">指定なし</option>
<!--{html_options options=$arrDelivTime selected=$arrForm[$key].value}-->
</select>
<hr>
<!--{/foreach}-->
<!--{/if}-->
<br>

■その他お問い合わせ<br>
<!--{assign var=key value="message"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<textarea cols="20" rows="2" name="<!--{$key}-->"><!--{$arrForm[$key].value|h}--></textarea>
<br>
<br>

<!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
■ポイント使用の指定<br>
1ポイントを<!--{$smarty.const.POINT_VALUE}-->円として使用する事ができます。<br>
<br>
<!--{$objCustomer->getValue('name01')|h}--> <!--{$objCustomer->getValue('name02')|h}-->様の、現在の所持ポイントは「<!--{$tpl_user_point|number_format|default:0}-->Pt」です。<br>
<br>
今回ご購入合計金額： <!--{$arrData.subtotal|number_format}-->円<br>
(送料、手数料を含みません。)<br>
<br>
<input type="radio" name="point_check" value="1" <!--{$arrForm.point_check.value|sfGetChecked:1}-->>ポイントを使用する<br>
<!--{assign var=key value="use_point"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:$tpl_user_point}-->" maxlength="<!--{$arrForm[$key].length}-->" size="6">&nbsp;ポイントを使用する。<br>
<input type="radio" name="point_check" value="2" <!--{$arrForm.point_check.value|sfGetChecked:2}-->>ポイントを使用しない<br>
<br>
<!--{/if}-->

<center><input type="submit" value="次へ"></center>
</form>

<form action="<!--{$tpl_back_url|h}-->" method="get">
<center><input type="submit" name="return" value="戻る"></center>
</form>

<hr>

<a href="<!--{$smarty.const.MOBILE_CART_URL_PATH}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_TOP_URL_PATH}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
