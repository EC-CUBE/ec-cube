<div align="center">会員情報入力 1/3</div>
<hr>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="set1">

	<font color="#FF0000">*は必須項目です。</font><br>
	<br>

	【メールアドレス】<font color="#FF0000">※</font><br>
	<font color="#FF0000"><!--{$arrErr.email}--></font>
<!--{if @$tpl_kara_mail_from}-->
  <!--{$tpl_kara_mail_from|escape}-->
<!--{else}-->
	<input type="text" name="email" value="<!--{$email|escape}-->" istyle="3">
<!--{/if}-->
  <br>

	【パスワード】<font color="#FF0000">※</font><br>
	（半角英数字6文字以上20文字以内）<br>
	<font color="#FF0000"><!--{$arrErr.password}--></font>
	<input type="text" name="password" value="<!--{$arrForm.password}-->" istyle="3"><br>

	【パスワード確認用の質問】<font color="#FF0000">※</font><br>
	<font color="#FF0000"><!--{$arrErr.reminder}--></font>
	<select name="reminder">
		<option value="" selected>選択してください</option>
		<!--{html_options options=$arrReminder selected=$reminder}-->
	</select><br>

	【質問の答え】<font color="#FF0000">※</font><br>
	<font color="#FF0000"><!--{$arrErr.reminder_answer}--></font>
	<input type="text" name="reminder_answer" value="<!--{$reminder_answer|escape}-->" istyle="1"><br>

	【お名前】<font color="#FF0000">※</font><br>
	<font color="#FF0000"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></font>
	姓（例：渋谷）<br>
	<input type="text" name="name01" value="<!--{$name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>

	名（例：花子）<br>
	<input type="text" name="name02" value="<!--{$name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>
	<font color="#FF0000"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></font>

	カナ/姓（例：シブヤ）<br>
	<input type="text" name="kana01" value="<!--{$kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

	カナ/名（例：ハナコ）<br>
	<input type="text" name="kana02" value="<!--{$kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

	<input type="submit" name="confirm" value="次へ">
</form>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->カゴを見る</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<center>LOCKON CO.,LTD.</center>
<!-- ▲フッター ここまで -->
