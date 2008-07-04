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
■ご利用いただけるカードの種類<br>
<img src="<!--{$smarty.const.IMAGE_SAVE_URL}--><!--{$tpl_payment_image}-->"><br><br>
<!--{/if}-->

■支払回数<br>
<!--{assign var=key1 value="payment_class"}-->
<font color="#ff0000"><!--{$arrErr[$key1]}--></font>
<select name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" >
<!--{html_options options=$arrPaymentClass selected=$arrForm[$key1].value}-->
</select>
<br><br>

<!--{if $cnt_card >= 1}-->
■登録カード<br>
<!--{assign var=key1 value="CardSeq"}-->
<font color="#ff0000"><!--{$arrErr[$key1]}--></font>
<input type="checkbox" name="stock" value="1" <!--{if $smarty.post.stock == 1}-->checked<!--{/if}-->>登録カードを利用する<br>
<font size="2">登録カードをご利用の方は、カード情報の入力は不要です。<br><font color="#ff6600">入力されても適用されませんので、ご注意ください。</font></font>
<table border>
  <tr>
    <td>選択</td>
    <td>カード番号</td>
    <td>有効期限</td>
    <td>カード名義</td>
  </tr>
  <!--{foreach name=cardloop from=$arrCardInfo item=card}-->
  <tr>
    <td><input type="radio" name="<!--{$key1}-->" value="<!--{$card[$key1]}-->" <!--{if $smarty.post.$key1 == $card[$key1]}-->checked<!--{/if}-->></td>
    <td><!--{$card.CardNo}--></td>
    <td><!--{$card.Expire|substr:0:2}-->月/<!--{$card.Expire|substr:2:4}-->年</td>
    <td><!--{$card.HolderName}--></td>
  </tr>
  <!--{/foreach}-->
</table>
<input type="submit" name="deletecard" value="選択カードの削除">
<br><br>
<!--{/if}-->

■カード番号<br>
<font size="2"><font color="#ff6600">ご本人名義のカードをご使用ください。</font><br>
半角入力（例：1234-5678-9012-3456）</font><br>
<!--{assign var=key1 value="card_no01"}-->
<!--{assign var=key2 value="card_no02"}-->
<!--{assign var=key3 value="card_no03"}-->
<!--{assign var=key4 value="card_no04"}-->
<font color="#ff0000"><!--{$arrErr[$key1]}--></font>
<font color="#ff0000"><!--{$arrErr[$key2]}--></font>
<font color="#ff0000"><!--{$arrErr[$key3]}--></font>
<font color="#ff0000"><!--{$arrErr[$key4]}--></font>
<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" size="6" istyle="4">-
<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" size="6" istyle="4">-
<input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" size="6" istyle="4">-
<input type="text" name="<!--{$key4}-->" value="<!--{$arrForm[$key4].value|escape}-->" maxlength="<!--{$arrForm[$key4].length}-->" size="6" istyle="4">
<br><br>

■有効期限<br>
<!--{assign var=key1 value="card_month"}-->
<!--{assign var=key2 value="card_year"}-->
<font color="#ff0000"><!--{$arrErr[$key1]}--></font>
<font color="#ff0000"><!--{$arrErr[$key2]}--></font>
<select name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->">
<option value="">--</option>
<!--{html_options options=$arrMonth selected=$arrForm[$key1].value}-->
</select>月/
<select name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->">
<option value="">--</option>
<!--{html_options options=$arrYear selected=$arrForm[$key2].value}-->
</select>年
<br><br>

■カード名義（ローマ字）</td>
<font size="2">半角入力（例：TARO YAMADA）</font><br>
<!--{assign var=key2 value="card_name01"}-->
<!--{assign var=key1 value="card_name02"}-->								
<font color="#ff0000"><!--{$arrErr[$key1]}--></font>
<font color="#ff0000"><!--{$arrErr[$key2]}--></font>
名<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" istyle="3" size="15"><br>
姓<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" istyle="3" size="15">
<br><br>

<!--{if $stock_flg == 1}-->
■カード登録<br>
<input type="checkbox" name="stock_new" value="1" <!--{if $smarty.post.stock_new == 1}-->checked<!--{/if}-->>登録する<br>
<font size="2">カード情報を登録しておくと、次回以降の購入時にカード情報入力が省略でき、大変便利です。<br>最大5件まで登録できます。</font>
<br><br>
<!--{/if}-->

<br>

以上の内容で間違いなければ、下記「次へ」ボタンをクリックしてください。<br>
<font size="2" color="#ff6600">※画面が切り替るまで少々時間がかかる場合がございますが、そのままお待ちください。</font><br>
<center><input type="submit" value="次へ"></center>
</form>
<form action="./load_payment_module.php" method="post">
<input type="hidden" name="mode" value="return">
<center><input type="submit" value="戻る"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<center>LOCKON CO.,LTD.</center>
<!-- ▲フッター ここまで -->
