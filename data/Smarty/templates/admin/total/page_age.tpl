<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
	<tr bgcolor="#636469" align="center" class="fs12n">
		<td width="185"><span class="white">ǯ��</span></td>
		<td width="185"><span class="white">�������</span></td>				
		<td width="185"><span class="white">�������</span></td>				
		<td width="185"><span class="white">����ʿ��</span></td>
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
		<td align="center"><!--{*ǯ��*}--><!--{$arrResults[cnt].age_name}--></td>
		<td align="right"><!--{*�������*}--><!--{$arrResults[cnt].order_count}-->��</td>
		<td align="right"><!--{*�������*}--><!--{$arrResults[cnt].total|number_format}-->��</td>
		<td align="right"><!--{*����ʿ��*}--><!--{$arrResults[cnt].total_average|number_format}-->��</td>
	</tr>
	<!--{/section}-->
	
	<tr bgcolor="#636469" align="center" class="fs12n">
		<td width="185"><span class="white">ǯ��</span></td>
		<td width="185"><span class="white">�������</span></td>				
		<td width="185"><span class="white">�������</span></td>				
		<td width="185"><span class="white">����ʿ��</span></td>
	</tr>
</table>