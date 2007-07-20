<center>コンビニ決済</center>

<hr>

<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="send">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">

下記から、お支払いするコンビニをご選択し、必要事項を入力してください。<br>
入力後、一番下の「次へ」ボタンをクリックしてください。<br>

<br>

コンビニの種類<br>
<font color="#ff0000"><!--{$arrErr.convenience}--></font>
<!--{foreach key=key item=item from=$arrConv}-->
<input type="radio" name="convenience" value="<!--{$key}-->" <!--{if $smarty.post.convenience == $key}-->checked<!--{/if}-->>
<!--{$item|escape}--><br>
<!--{/foreach}-->

姓(カナ)<br>
<font color="#ff0000"><!--{$arrErr.order_kana01}--><!--{$arrErr.order_kana02}--></font>
<input type="text" name="order_kana01" size="15" value="<!--{$arrForm.order_kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

名(カナ)<br>
<input type="text" name="order_kana02" size="15" value="<!--{$arrForm.order_kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

電話番号<br>
<font color="#ff0000"><!--{$arrErr.order_tel01}--><!--{$arrErr.order_tel02}--><!--{$arrErr.order_tel03}--></font>
<input type="text" name="order_tel01" size="6" value="<!--{$arrForm.order_tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">-<input type="text" name="order_tel02" size="6" value="<!--{$arrForm.order_tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">-<input type="text" name="order_tel03" size="6" value="<!--{$arrForm.order_tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4"><br>

<br>

<center><input type="submit" value="次へ"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<center>LOCKON CO.,LTD.</center>
<!-- ▲フッター ここまで -->
