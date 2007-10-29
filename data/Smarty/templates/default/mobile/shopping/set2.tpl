<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
<div align="center">お届け先登録確認</div>
<hr>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="complete">

	【個人情報】<br>
	<!--{$list_data.name01|escape}-->　<!--{$list_data.name02|escape}--><br>
	<!--{$list_data.kana01|escape}-->　<!--{$list_data.kana02|escape}--><br>
	〒<!--{$list_data.zip01|escape}--> - <!--{$list_data.zip02|escape}--><br>
	<!--{$arrPref[$list_data.pref]|escape}--><!--{$list_data.addr01|escape}--><!--{$list_data.addr02|escape}--><br>
	<!--{$list_data.tel01|escape}-->-<!--{$list_data.tel02|escape}-->-<!--{$list_data.tel03|escape}--><br>

	<div align="center"><input type="submit" name="submit" value="次へ"></div>
	<div align="center"><input type="submit" name="return" value="戻る"></div>

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
