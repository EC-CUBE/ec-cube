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
<div align="center">会員情報入力 3/3</div>
<hr>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="confirm">

	<font color="#FF0000">*は必須項目です。</font><br>
	<br>

	【都道府県】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.pref}--></font>
	<select name="pref">
		<option value="">都道府県を選択</option>
		<!--{html_options options=$arrPref selected=$pref}-->
	</select><br>

	【住所1】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.addr01}--></font>
	<input type="text" name="addr01" value="<!--{$addr01|escape}-->" istyle="1"><br>

	【住所2】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.addr02}--></font>
	<input type="text" name="addr02" value="<!--{$addr02|escape}-->" istyle="1"><br>

	【電話番号】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></font>
	<!--{assign var="size" value="`$smarty.const.TEL_ITEM_LEN+2`"}-->
	<input type="text" size="<!--{$size}-->" name="tel01" value="<!--{$tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">
	&nbsp;-&nbsp;
	<input type="text" size="<!--{$size}-->" name="tel02" value="<!--{$tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">
	&nbsp;-&nbsp;
	<input type="text" size="<!--{$size}-->" name="tel03" value="<!--{$tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4"><br>

	【メールマガジン】<br>
	お得な情報を希望されますか？<br>
	配信希望<input type="checkbox" name="mailmaga_flg" value="on" <!--{if $mailmaga_flg eq 'on' || $mailmaga_flg == 2}-->checked<!--{/if}--> /><br>
	（希望されない場合はチェックをはずしてください）<br>
	<br>

	<input type="submit" name="submit" value="次へ"><br>
	<input type="submit" name="return" value="戻る">

	<!--{foreach from=$list_data key=key item=item}-->
		<input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->">
	<!--{/foreach}-->
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
