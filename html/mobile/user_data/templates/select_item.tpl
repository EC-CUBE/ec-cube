<div align="center">数量指定</div>
<hr>

<!--{if $arrErr.classcategory_id2 != ""}-->
	<font color="#FF0000">※数量を入力して下さい｡</font><br>
<!--{/if}-->
<form method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
	<input type="text" name="quantity" size="3" value="<!--{$arrForm.quantity.value|default:1}-->" maxlength=<!--{$smarty.const.INT_LEN}--> istyle="4"><br>
	<input type="hidden" name="mode" value="cart">
	<input type="hidden" name="classcategory_id1" value="<!--{$arrForm.classcategory_id1.value}-->">
	<input type="hidden" name="classcategory_id2" value="<!--{$arrForm.classcategory_id2.value}-->">
	<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
	<input type="submit" name="submit" value="ｶｺﾞに入れる">
</form>

<!-- ▼フッター ここから -->
<hr>
<center>LOCKON CO.,LTD.</center>
<!-- ▲フッター ここまで -->
