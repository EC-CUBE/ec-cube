<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
	<tr bgcolor="#636469" align="center" class="fs12n">
		<td width="100"><span class="white">����</span></td>
		<td width="80"><span class="white">�������</span></td>
		<td width="80"><span class="white">����</span></td>				
		<td width="80"><span class="white">����</span></td>				
		<td width="80"><span class="white">����(���)</span></td>
		<td width="80"><span class="white">����<br />(����)</span></td>
		<td width="80"><span class="white">����(���)</span></td>
		<td width="80"><span class="white">����<br />(����)</span></td>
		<td width="80"><span class="white">�������</span></td>
		<td width="80"><span class="white">����ʿ��</span></td>
	</tr>
	
	<!--{section name=cnt loop=$arrResults}-->
	<!--{* ��ʬ��Ƚ�� *}-->
	<!--{if !$smarty.section.cnt.last}-->
		<!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
		<!--{if $type == 0}-->
			<!--{* ������ *}-->
			<!--{assign var=color value="#FFFFFF"}-->
		<!--{else}-->
			<!--{* ����� *}-->
			<!--{assign var=color value="#D1DEFE"}-->
		<!--{/if}-->
	<!--{else}-->
		<!--{* �ǽ��� *}-->
		<!--{assign var=color value="#FFD9EC"}-->
	<!--{/if}-->
	
	
	<tr bgcolor="<!--{$color}-->" class="fs12">
		<!--{assign var=wday value="`$arrResults[cnt].wday`"}-->
		
		<!--{if !$smarty.section.cnt.last}-->
			<td align="center"><!--{*����*}--><!--{$arrResults[cnt][$keyname]}--><!--{if $keyname == "key_day"}-->(<!--{$arrWDAY[$wday]}-->)<!--{/if}--><!--{$tpl_tail}--></td>
		<!--{else}-->
			<td align="center"><!--{*����*}-->���</td>
		<!--{/if}-->
		
		<td align="right"><!--{*�������*}--><!--{$arrResults[cnt].total_order}-->��</td>
		<td align="right"><!--{*����*}--><!--{$arrResults[cnt].men}--></td>
		<td align="right"><!--{*����*}--><!--{$arrResults[cnt].women}--></td>
		<td align="right"><!--{*����(���)*}--><!--{$arrResults[cnt].men_member}--></td>
		<td align="right"><!--{*����(����)*}--><!--{$arrResults[cnt].men_nonmember}--></td>
		<td align="right"><!--{*����(���)*}--><!--{$arrResults[cnt].women_member}--></td>
		<td align="right"><!--{*����(����)*}--><!--{$arrResults[cnt].women_nonmember}--></td>
		<td align="right"><!--{*�������*}--><!--{$arrResults[cnt].total|number_format}-->��</td>
		<td align="right"><!--{*����ʿ��*}--><!--{$arrResults[cnt].total_average|number_format}-->��</td>
	</tr>
	<!--{/section}-->
	
	<tr bgcolor="#636469" align="center" class="fs12n">
		<td width="100"><span class="white">����</span></td>
		<td width="80"><span class="white">�������</span></td>
		<td width="80"><span class="white">����</span></td>				
		<td width="80"><span class="white">����</span></td>				
		<td width="80"><span class="white">����(���)</span></td>
		<td width="80"><span class="white">����<br />(����)</span></td>
		<td width="80"><span class="white">����(���)</span></td>
		<td width="80"><span class="white">����<br />(����)</span></td>
		<td width="80"><span class="white">�������</span></td>
		<td width="80"><span class="white">����ʿ��</span></td>
	</tr>
</table>