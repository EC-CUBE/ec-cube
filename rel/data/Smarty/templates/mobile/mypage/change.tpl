<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">登録内容変更 1/3</div>
<hr>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="set1">

	<font color="#FF0000">*は必須項目です。</font><br>
	<br>

	【メールアドレス】<font color="#FF0000">※</font><br>
	<font color="#FF0000"><!--{$arrErr.email}--></font>
	<input type="text" name="email" value="<!--{$arrForm.email|escape}-->" istyle="3">
	<br>

	【パスワード】<font color="#FF0000">※</font><br>
	（半角英数字<!--{$smarty.const.PASSWORD_LEN1}-->文字以上<!--{$smarty.const.PASSWORD_LEN2}-->文字以内）<br>
	<font color="#FF0000"><!--{$arrErr.password}--></font>
	<!--{assign var="size" value="`$smarty.const.PASSWORD_LEN2+2`"}-->
	<input type="password" name="password" value="<!--{$arrForm.password}-->" istyle="4" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" size="<!--{$size}-->"><br>

	【パスワード確認用の質問】<font color="#FF0000">※</font><br>
	<font color="#FF0000"><!--{$arrErr.reminder}--></font>
	<select name="reminder">
		<option value="">選択してください</option>
		<!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
	</select><br>

	【質問の答え】<font color="#FF0000">※</font><br>
	<font color="#FF0000"><!--{$arrErr.reminder_answer}--></font>
	<input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer|escape}-->" istyle="1"><br>

	【お名前】<font color="#FF0000">※</font><br>
	<font color="#FF0000"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></font>
	姓（例：渋谷）<br>
	<input type="text" name="name01" value="<!--{$arrForm.name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>

	名（例：花子）<br>
	<input type="text" name="name02" value="<!--{$arrForm.name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>
	<font color="#FF0000"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></font>

	カナ/姓（例：シブヤ）<br>
	<input type="text" name="kana01" value="<!--{$arrForm.kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

	カナ/名（例：ハナコ）<br>
	<input type="text" name="kana02" value="<!--{$arrForm.kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

	<input type="submit" name="confirm" value="次へ">

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
