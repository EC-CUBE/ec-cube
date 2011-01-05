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
<div align="center">変更確認</div>
<hr>
<form name="form1" id="form1" method="post" action="?">
	<input type="hidden" name="mode" value="complete">
	<!--{foreach from=$list_data key=key item=item}-->
		<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->">
	<!--{/foreach}-->
	下記の内容でご登録してもよろしいですか？<br>
	<br>
	【ﾒｰﾙｱﾄﾞﾚｽ】<br>
	<!--{$list_data.email|h}--><br>
	<br>

	【携帯ﾒｰﾙｱﾄﾞﾚｽ】<br>
	<!--{$list_data.email_mobile|default:"未登録"|h}--><br>
	<br>

	【ﾊﾟｽﾜｰﾄﾞ確認用質問】<br>
	<!--{$arrReminder[$list_data.reminder]|h}--><br>
	<br>

	【質問の答え】<br>
	<!--{$list_data.reminder_answer|h}--><br>
	<br>

	【個人情報】<br>
	<!--{$list_data.name01|h}-->　<!--{$list_data.name02|h}--><br>
	<!--{$list_data.kana01|h}-->　<!--{$list_data.kana02|h}--><br>
	<!--{if $list_data.sex eq 1}-->男性<!--{else}-->女性<!--{/if}--><br>
	<!--{if strlen($list_data.year) > 0 && strlen($list_data.month) > 0 && strlen($list_data.day) > 0}--><!--{$list_data.year|h}-->年<!--{$list_data.month|h}-->月<!--{$list_data.day|h}-->日生まれ<!--{else}-->生年月日 未登録<!--{/if}--><br>
	〒<!--{$list_data.zip01|h}--> - <!--{$list_data.zip02|h}--><br>
	<!--{$arrPref[$list_data.pref]|h}--><!--{$list_data.addr01|h}--><!--{$list_data.addr02|h}--><br>
	<!--{$list_data.tel01|h}-->-<!--{$list_data.tel02|h}-->-<!--{$list_data.tel03|h}--><br>
	<br>
	
	【ﾒｰﾙﾏｶﾞｼﾞﾝﾞ】<br>
	<!--{if $list_data.mailmaga_flg eq 2}-->希望する<!--{else}-->希望しない<!--{/if}--><br>
	<br>

	<input type="submit" name="submit" value="変更"><br>
	<input type="submit" name="return" value="戻る">
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_CART_URL_PATH}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_TOP_URL_PATH}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
