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
<div align="center">登録内容変更 2/3</div>
<hr>
<form name="form1" method="post" action="?">
	<input type="hidden" name="mode" value="set2">

	<font color="#FF0000">*は必須項目です。</font><br>
	<br>

	【性別】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.sex}--></font>
	<input type="radio" name="sex" value="1" <!--{if $arrForm.sex eq "1"}-->checked<!--{/if}--> />男性&nbsp;
	<input type="radio" name="sex" value="2" <!--{if $arrForm.sex eq "2"}-->checked<!--{/if}--> />女性<br>

	【生年月日】<br>
	<font color="#FF0000"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></font>
	<input type="text" name="year" value="<!--{$arrForm.year|h}-->" size="4" maxlength="4" istyle="4">年<br>
	<select name="month">
		<!--{html_options options=$arrMonth selected=$arrForm.month}-->
	</select>月<br>
	<select name="day">
		<!--{html_options options=$arrDay selected=$arrForm.day}-->
	</select>日<br>

	<!--{assign var=key1 value="zip01"}-->
	<!--{assign var=key2 value="zip02"}-->
	【郵便番号】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font>
	<!--{assign var="size1" value="`$smarty.const.ZIP01_LEN+2`"}-->
	<!--{assign var="size2" value="`$smarty.const.ZIP02_LEN+2`"}-->
	<input size="<!--{$size1}-->" type="text" name="zip01" value="<!--{if $arrForm.zip01 == ""}--><!--{$arrForm.zip01|h}--><!--{else}--><!--{$arrForm.zip01|h}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" istyle="4">
	&nbsp;-&nbsp;
	<input size="<!--{$size2}-->" type="text" name="zip02" value="<!--{if $arrForm.zip02 == ""}--><!--{$arrForm.zip02|h}--><!--{else}--><!--{$arrForm.zip02|h}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" istyle="4"><br>

	<input type="submit" name="confirm" value="次へ"><br>
	<input type="submit" name="return" value="戻る">

	<!--{foreach from=$list_data key=key item=item}-->
		<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->">
	<!--{/foreach}-->
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_CART_URL_PATH}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_TOP_URL_PATH}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
