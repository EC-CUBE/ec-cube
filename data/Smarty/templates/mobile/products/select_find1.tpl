<div align="center"><!--{$tpl_class_name1}--></div>
<hr>

<!--{if $arrErr.classcategory_id1 != ""}-->
	<font color="#FF0000">※<!--{$tpl_class_name1}-->を入力して下さい｡</font><br>
<!--{/if}-->
<form method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
	<select name="classcategory_id1">
		<option value="">選択してください</option>
		<!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
	</select><br>
	<input type="hidden" name="mode" value="select2">
	<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
	<input type="submit" name="submit" value="次へ">
</form>

<!-- ▼フッター ここから -->
<hr>
<center>LOCKON CO.,LTD.</center>
<!-- ▲フッター ここまで -->
