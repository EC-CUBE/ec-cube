<div align="center">������</div>
<hr>

<!--��CONTENTS-->
<form name="login_mypage" id="login_mypage" method="post" action="./index.php">
	<input type="hidden" name="mode" value="login" >
<!--{if !$tpl_valid_phone_id}-->
	���᡼�륢�ɥ쥹<br>
	<!--{assign var=key value="login_email"}-->
	<font color="#FF0000"><!--{$arrErr[$key]}--></font>
	<input type="text" name="<!--{$key}-->" value="<!--{$login_email|escape}-->" 
		maxlength="<!--{$arrForm[$key].length}-->" size="40" istyle="3"><br>
<!--{else}-->
	<input type="hidden" name="login_email" value="dummy">
<!--{/if}-->
	���ѥ����<br>
	<!--{assign var=key value="login_pass"}--><font color="#FF0000"><!--{$arrErr[$key]}--></font>
	<input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" size="40" istyle="3"><br>
	<center><input type="submit" value="����" name="log"></center><br>
	<a href="<!--{$smarty.const.URL_DIR}-->forgot/index.php">�ѥ���ɤ�˺������Ϥ�����</a><br>
</form>
<!--��CONTENTS-->

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
