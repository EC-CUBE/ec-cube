<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--��CONTENTS-->
<!--��MAIN ONTENTS-->
<div align="center"><font color="#000080">����ɽ��</font></div>
<!--{if $tpl_message != ""}-->
	<!--{$tpl_message}--><br>
<!--{/if}-->
<!--{if count($arrProductsClass) > 0}-->
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" utn>
	<input type="hidden" name="mode" value="confirm">
	<input type="hidden" name="cart_no" value="">
	<!--����ʸ���Ƥ�������-->
	<hr>
	<!--{section name=cnt loop=$arrProductsClass}-->
		<!--{* ����̾ *}--><!--{$arrProductsClass[cnt].name|escape}--><br>
		<!--{* ���� *}-->
		<!--{if $arrProductsClass[cnt].price02 != ""}-->
			\<!--{$arrProductsClass[cnt].price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
		<!--{else}-->
			\<!--{$arrProductsClass[cnt].price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
		<!--{/if}-->
		�� <!--{$arrProductsClass[cnt].quantity}--><br>
		<!--{* �ܺ� *}-->
		<!--{if $arrProductsClass[cnt].classcategory_name1 != ""}-->
			<!--{$arrProductsClass[cnt].class_name1}-->:<!--{$arrProductsClass[cnt].classcategory_name1}--><br>
		<!--{/if}-->
		<!--{if $arrProductsClass[cnt].classcategory_name2 != ""}-->
			<!--{$arrProductsClass[cnt].class_name2}-->:<!--{$arrProductsClass[cnt].classcategory_name2}--><br>
		<!--{/if}-->
		<br>
		<!--{* ���� *}-->
		����:<!--{$arrProductsClass[cnt].quantity}-->
		<a href="<!--{$smarty.server.PHP_SELF|escape}-->?mode=up&amp;cart_no=<!--{$arrProductsClass[cnt].cart_no}-->">+</a>
		<a href="<!--{$smarty.server.PHP_SELF|escape}-->?mode=down&amp;cart_no=<!--{$arrProductsClass[cnt].cart_no}-->">-</a>
		<a href="<!--{$smarty.server.PHP_SELF|escape}-->?mode=delete&amp;cart_no=<!--{$arrProductsClass[cnt].cart_no}-->">���</a><br>
		<!--{* ��� *}-->
		����:<!--{$arrProductsClass[cnt].total_pretax|number_format}-->��<br>
		<div align="right"><a href="<!--{$smarty.const.MOBILE_DETAIL_P_HTML}--><!--{$arrProductsClass[cnt].product_id}-->">���ʹ����ܺ٤آ�</a></div>
		<HR>
	<!--{/section}-->
	���ʹ��:<!--{$tpl_total_pretax|number_format}-->��<br>
	���:<!--{$arrData.total-$arrData.deliv_fee|number_format}-->��<br>
	<!--{if $arrData.birth_point > 0}-->
		��������Ύߎ��ݎ�<br>
		<!--{$arrData.birth_point|number_format}-->pt<br>
	<!--{/if}-->
	<br>
	<center><input type="submit" value="��ʸ����" name="confirm"></center>
</form>
<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<center><input type="submit" value="����ʪ��³����" name="continue"></center>
</form>
<!--{else}-->
	�����ߎ�������˾��ʤϤ������ޤ���<br>
<!--{/if}-->
<!--��CONTENTS-->
<!--��MAIN CONTENTS-->
<!--��CONTENTS-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
