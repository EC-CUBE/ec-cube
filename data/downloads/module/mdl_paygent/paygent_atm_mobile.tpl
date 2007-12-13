<center><!--{$tpl_payment_method}--></center>

<hr>

<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="next">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">

<!--{if $tpl_error != ""}-->
<font color="#ff0000"><!--{$tpl_error}--></font><br><br>
<!--{/if}-->

下記に必要事項を入力してください。<br><br>

<!--{if $tpl_payment_image != ""}-->
ご利用いただける金融機関の種類<br>
<img src="<!--{$smarty.const.IMAGE_SAVE_URL}--><!--{$tpl_payment_image}-->"><br><br>
<!--{/if}-->

利用者<br>
<font size="2">※ 特殊な漢字は使用できない場合がございます。</font><br>
<!--{assign var=key1 value="customer_family_name"}-->
<!--{assign var=key2 value="customer_name"}-->
<font color="#ff0000"><!--{$arrErr[$key1]}--></font>
<font color="#ff0000"><!--{$arrErr[$key2]}--></font>
姓<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" istyle="2" size="15"><br>
名<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" istyle="2" size="15">
<br><br>

利用者(カナ)<br>
<font size="2">※カナに濁点（゛）や半濁点（゜）がある場合、該当記号のみ除外されます。予めご了承ください。</font><br>
<!--{assign var=key1 value="customer_family_name_kana"}-->
<!--{assign var=key2 value="customer_name_kana"}-->
<font color="#ff0000"><!--{$arrErr[$key1]}--></font>
<font color="#ff0000"><!--{$arrErr[$key2]}--></font>
セイ<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" istyle="2" size="15"><br>
メイ<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" istyle="2" size="15">
<br><br>

<br>

以上の内容で間違いなければ、下記「次へ」ボタンをクリックしてください。<br>
<font size="2" color="#ff6600">※画面が切り替るまで少々時間がかかる場合がございますが、そのままお待ちください。</font><br>
<center><input type="submit" value="次へ">
</form>
<form action="./load_payment_module.php" method="post">
<input type="hidden" name="mode" value="return">
<input type="submit" value="戻る"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<center>LOCKON CO.,LTD.</center>
<!-- ▲フッター ここまで -->
