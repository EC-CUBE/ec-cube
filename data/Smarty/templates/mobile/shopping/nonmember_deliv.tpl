<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>���������</center>

<hr>

<!--{if $arrErr.deli != ""}-->
<font color="#ff0000"><!--{$arrErr.deli}--></font>
<!--{/if}-->

<!--��CONTENTS-->
<!--{section name=cnt loop=$arrAddr}-->
<form method="post" action="<!--{$smarty.const.MOBILE_URL_DIR}-->shopping/payment.php">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="deli" value="<!--{$smarty.section.cnt.iteration}-->">
<!--{if $smarty.section.cnt.first}-->
<input type="text" name="mode" value="nonmember">
<input type="text" name="mode2" value="customer_addr">
<input type="hidden" name="other_deliv_id" value="">
<input type="hidden" name="zip01" value="<!--{$arrAddr[0].zip01}-->">
<input type="hidden" name="zip02" value="<!--{$arrAddr[0].zip02}-->">
<input type="hidden" name="pref" value="<!--{$arrAddr[0].pref}-->">
<input type="hidden" name="addr01" value="<!--{$arrAddr[0].addr01}-->">
<input type="hidden" name="addr02" value="<!--{$arrAddr[cnt].addr02}-->">
<!--{else}-->
<input type="hidden" name="mode" value="other_addr">
<input type="text" name="other_deliv_id" value="<!--{$arrAddr[cnt].other_deliv_id}-->">
<!--{/if}-->
��������<!--{$smarty.section.cnt.iteration}--><br>
��<!--{$arrAddr[cnt].zip01}-->-<!--{$arrAddr[cnt].zip02}--><br>
<!--{assign var=key value=$arrAddr[cnt].pref}--><!--{$arrPref[$key]}--><!--{$arrAddr[cnt].addr01|escape}--><br>
<!--{if $arrAddr[cnt].addr02 != ""}-->
<!--{$arrAddr[cnt].addr02|escape}--><br>
<!--{/if}-->
<center><input type="submit" value="����������"></center>
</form>
<!--{/section}-->

<br>

������¾�Τ��Ϥ�������<br>
<form method="get" action="deliv_addr.php">
<center><input type="submit" value="������Ͽ"></center>
</form>
<!--��CONTENTS-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<center>LOCKON CO.,LTD.</center>
<!-- ���եå��� �����ޤ� -->
