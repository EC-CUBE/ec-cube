<!--{*
/*
 * Copyright ¢í 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
	<tr bgcolor="#636469" align="center" class="fs12n">
		<td width="185"><span class="white">Ç¯Îð</span></td>
		<td width="185"><span class="white">¹ØÆþ·ï¿ô</span></td>				
		<td width="185"><span class="white">¹ØÆþ¹ç·×</span></td>				
		<td width="185"><span class="white">¹ØÆþÊ¿¶Ñ</span></td>
	</tr>
	
	<!--{section name=cnt loop=$arrResults}-->
	<!--{* ¿§Ê¬¤±È½Äê *}-->
	<!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
	<!--{if $type == 0}-->
		<!--{* ¶ö¿ô¹Ô *}-->
		<!--{assign var=color value="#FFFFFF"}-->
	<!--{else}-->
		<!--{* ´ñ¿ô¹Ô *}-->
		<!--{assign var=color value="#D1DEFE"}-->
	<!--{/if}-->

	<tr bgcolor="<!--{$color}-->" class="fs12">
		<td align="center"><!--{*Ç¯Îð*}--><!--{$arrResults[cnt].age_name}--></td>
		<td align="right"><!--{*¹ØÆþ·ï¿ô*}--><!--{$arrResults[cnt].order_count}-->·ï</td>
		<td align="right"><!--{*¹ØÆþ¹ç·×*}--><!--{$arrResults[cnt].total|number_format}-->±ß</td>
		<td align="right"><!--{*¹ØÆþÊ¿¶Ñ*}--><!--{$arrResults[cnt].total_average|number_format}-->±ß</td>
	</tr>
	<!--{/section}-->
	
	<tr bgcolor="#636469" align="center" class="fs12n">
		<td width="185"><span class="white">Ç¯Îð</span></td>
		<td width="185"><span class="white">¹ØÆþ·ï¿ô</span></td>				
		<td width="185"><span class="white">¹ØÆþ¹ç·×</span></td>				
		<td width="185"><span class="white">¹ØÆþÊ¿¶Ñ</span></td>
	</tr>
</table>