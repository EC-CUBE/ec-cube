<!--��CONTENTS-->
<!--��MAIN ONTENTS-->
<div align="center">[emoji:69]<font color="#000080">����ɽ��</font></div>
<!--{if $tpl_login}-->
	<!--�Ҏ��ݎ��Ҏݎ�-->
	<!--{$tpl_name|escape}--> �ͤΎ����ߤν���Ύߎ��ݎĤώ�<span class="redst"><!--{$tpl_user_point|number_format|default:0}--> pt</span>���Ǥ���<br />
<!--{/if}-->
<!--{if $tpl_message != ""}-->
	<!--{$tpl_message}--><br>
<!--{/if}-->
<!--{if count($arrProductsClass) > 0}-->
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" utn>
	<input type="hidden" name="mode" value="confirm">
	<input type="hidden" name="cart_no" value="">
	<!--����ʸ���Ƥ�������-->
	<HR>
	<!--{section name=cnt loop=$arrProductsClass}-->
		<!--{* ����̾ *}--><!--{$arrProductsClass[cnt].name|escape}--><br>
		<!--{* ���� *}-->
		<!--{if $arrProductsClass[cnt].price02 != ""}-->
			\<!--{$arrProductsClass[cnt].price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
		<!--{else}-->
			\<!--{$arrProductsClass[cnt].price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
		<!--{/if}-->
		�� <!--{$arrProductsClass[cnt].quantity}-->��<br>
		<!--{* �ܺ� *}-->
		<!--{if $arrProductsClass[cnt].classcategory_name1 != ""}-->
			<!--{$arrProductsClass[cnt].class_name1}-->:<!--{$arrProductsClass[cnt].classcategory_name1}--><br>
		<!--{/if}-->
		<!--{if $arrProductsClass[cnt].classcategory_name2 != ""}-->
			<!--{$arrProductsClass[cnt].class_name2}-->:<!--{$arrProductsClass[cnt].classcategory_name2}--><br>
		<!--{/if}-->
		<br>
		<!--{* ��� *}-->
		����:<!--{$arrProductsClass[cnt].total_pretax|number_format}-->��<br>
		<div align="right"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProductsClass[cnt].product_id}-->">���ʹ����ܺ٤آ�</a></div>
		<HR>
	<!--{/section}-->
	���ʹ��:<!--{$tpl_total_pretax|number_format}-->��<br>
	���:<!--{$arrData.total-$arrData.deliv_fee|number_format}-->��<br>
	<!--{if $arrData.birth_point > 0}-->
		��������Ύߎ��ݎ�<br>
		<!--{$arrData.birth_point|number_format}-->pt<br>
	<!--{/if}-->
	����û��Ύߎ��ݎ�<br>
	<!--{$arrData.add_point|number_format}-->pt<br>
	<br>
	<input type="submit" value="��ʸ����" name="confirm">
</form>
<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="submit" value="����ʪ��³����" name="continue">
</form>
<!--{else}-->
	�����ߎ�������˾��ʤϤ������ޤ���<br>
<!--{/if}-->
<!--��CONTENTS-->
<!--��MAIN CONTENTS-->
<!--��CONTENTS-->

<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0">[emoji:134]TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<center>LOCKON CO.,LTD.</center>
<!-- ���եå��� �����ޤ� -->



