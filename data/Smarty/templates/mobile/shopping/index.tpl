<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--��CONTENTS-->
<!--��MAIN ONTENTS-->
<div align="center"><font color="#000080">����ʸ��³��</font></div><br>
<hr>
<!--{if !$tpl_valid_phone_id}-->
<!--�������Ͽ�����ѤߤǤʤ�������-->
�����Ƥ���ʸ����<br>
(��������Ͽ)<br>
<form name="member_form" id="member_form" method="post" action="<!--{$smarty.const.MOBILE_URL_DIR}-->entry/kiyaku.php">
	<div align="center"><input type="submit" value="������Ͽ"></div><br>
</form>
<!--���ޤ������Ͽ����Ƥ��ʤ�������-->
<!--{/if}-->

<!--�������Ͽ�����ѤߤΤ�����-->
<form name="member_form" id="member_form" method="post" action="./deliv.php">
	<input type="hidden" name="mode" value="login">
<!--{if !$tpl_valid_phone_id}-->
	�������ˤ���ʸ���줿��<br>
	(��Х�������PC�Ǥ���Ͽ�Ѥ�)<br>
	���᡼�륢�ɥ쥹<br>
	<!--{assign var=key value="login_email"}-->
	<font color="#FF0000"><!--{$arrErr[$key]}--></font>
	<input type="text" name="<!--{$key}-->" value="<!--{$tpl_login_email|escape}-->" 
		maxlength="<!--{$arrForm[$key].length}-->" size="40" istyle="3"><br>
<!--{else}-->
<input type="hidden" name="login_email" value="dummy">
<!--{/if}-->
	���ѥ����<br>
	<!--{assign var=key value="login_pass"}--><font color="#FF0000"><!--{$arrErr[$key]}--></font>
	<input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" size="40" istyle="3"><br>
	<center><input type="submit" value="����" name="log"></center><br>
	<a href="<!--{$smarty.const.MOBILE_URL_DIR}-->forgot/index.php">�ѥ���ɤ�˺������Ϥ�����</a><br>
	
</form>
<!--�������Ͽ�����ѤΤ�����-->
<!--�������Ͽ����ʤ�������-->
<form name="nonmember_form" id="nonmember_form" method="post" action="<!--{$smarty.const.MOBILE_URL_DIR}-->nonmember/index.php">
	<input type="hidden" name="mode" value="nonmember">
	<input type="hidden" name="mode2" value="set1">
	<center><input type="submit" value="��Ͽ�����˹���" name="nonmember"></center>
</form>
<!--�������Ͽ����ʤ�������-->
<!--��MAIN ONTENTS-->
<!--��CONTENTS-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
