<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">変更確認</div>
<hr>
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="complete">
	<!--{foreach from=$list_data key=key item=item}-->
		<input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->">
	<!--{/foreach}-->
	下記の内容でご登録してもよろしいですか？<br>
	<br>
	【ﾒｰﾙｱﾄﾞﾚｽ】<br>
	<!--{$list_data.email|escape}--><br>
	<br>

	【ﾊﾟｽﾜｰﾄﾞ確認用質問】<br>
	<!--{$arrReminder[$list_data.reminder]|escape}--><br>
	<br>

	【質問の答え】<br>
	<!--{$list_data.reminder_answer|escape}--><br>
	<br>

	【個人情報】<br>
	<!--{$list_data.name01|escape}-->　<!--{$list_data.name02|escape}--><br>
	<!--{$list_data.kana01|escape}-->　<!--{$list_data.kana02|escape}--><br>
	<!--{if $list_data.sex eq 1}-->男性<!--{else}-->女性<!--{/if}--><br>
	<!--{if strlen($list_data.year) > 0 && strlen($list_data.month) > 0 && strlen($list_data.day) > 0}--><!--{$list_data.year|escape}-->年<!--{$list_data.month|escape}-->月<!--{$list_data.day|escape}-->日生まれ<!--{else}-->未登録<!--{/if}--><br>
	〒<!--{$list_data.zip01|escape}--> - <!--{$list_data.zip02|escape}--><br>
	<!--{$arrPref[$list_data.pref]|escape}--><!--{$list_data.addr01|escape}--><!--{$list_data.addr02|escape}--><br>
	<!--{$list_data.tel01|escape}-->-<!--{$list_data.tel02|escape}-->-<!--{$list_data.tel03|escape}--><br>
	<br>
	
	【ﾒｰﾙﾏｶﾞｼﾞﾝﾞ】<br>
	<!--{if $list_data.mailmaga_flg eq 2}-->希望する<!--{else}-->希望しない<!--{/if}--><br>
	<br>

	<input type="submit" name="submit" value="変更"><br>
	<input type="submit" name="return" value="戻る">
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
