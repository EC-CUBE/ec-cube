<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
	<tr bgcolor="#636469" align="center" class="fs12n">
		<td width="40"><span class="white">���</span></td>
		<td width="80"><span class="white">�����ֹ�</span></td>
		<td width="300"><span class="white">����̾</span></td>				
		<td width="80"><span class="white">�������</span></td>
		<td width="80"><span class="white">����</span></td>
		<td width="80"><span class="white">ñ��</span></td>
		<td width="80"><span class="white">���</span></td>
	</tr>
	
	<!--{section name=cnt loop=$arrResults}-->
	<!--{* ��ʬ��Ƚ�� *}-->
	<!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
	<!--{if $type == 0}-->
		<!--{* ������ *}-->
		<!--{assign var=color value="#FFFFFF"}-->
	<!--{else}-->
		<!--{* ����� *}-->
		<!--{assign var=color value="#D1DEFE"}-->
	<!--{/if}-->

	<tr bgcolor="<!--{$color}-->" class="fs12">
		<td align="center"><!--{*���*}--><!--{$smarty.section.cnt.iteration}--></td>
		<td align="right"><!--{*�����ֹ�*}--><!--{$arrResults[cnt].product_code|escape}--></td>
		<td align="left"><!--{*����̾*}--><!--{$arrResults[cnt].name|sfCutString:40|escape}--></td>
		<td align="right"><!--{*�������*}--><!--{$arrResults[cnt].order_count}-->��</td>
		<td align="right"><!--{*����*}--><!--{$arrResults[cnt].products_count}--></td>
		<td align="right"><!--{*ñ��*}--><!--{$arrResults[cnt].price|number_format}-->��</td>
		<td align="right"><!--{*���*}--><!--{$arrResults[cnt].total|number_format}-->��</td>
	</tr>
	<!--{/section}-->
	
	<tr bgcolor="#636469" align="center" class="fs12n">
		<td width="40"><span class="white">���</span></td>
		<td width="80"><span class="white">�����ֹ�</span></td>
		<td width="300"><span class="white">����̾</span></td>				
		<td width="80"><span class="white">�������</span></td>				
		<td width="80"><span class="white">����</span></td>
		<td width="80"><span class="white">ñ��</span></td>
		<td width="80"><span class="white">���</span></td>
	</tr>
</table>