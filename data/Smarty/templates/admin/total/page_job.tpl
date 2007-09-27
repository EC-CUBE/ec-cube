<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
	<tr bgcolor="#636469" align="center" class="fs12n">
		<td width="40"><span class="white">順位</span></td>
		<td width="175"><span class="white">職業</span></td>
		<td width="175"><span class="white">購入件数</span></td>				
		<td width="175"><span class="white">購入合計</span></td>				
		<td width="175"><span class="white">購入平均</span></td>
	</tr>
	
	<!--{section name=cnt loop=$arrResults}-->
	<!--{* 色分け判定 *}-->
	<!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
	<!--{if $type == 0}-->
		<!--{* 偶数行 *}-->
		<!--{assign var=color value="#FFFFFF"}-->
	<!--{else}-->
		<!--{* 奇数行 *}-->
		<!--{assign var=color value="#D1DEFE"}-->
	<!--{/if}-->

	<tr bgcolor="<!--{$color}-->" class="fs12">
		<td align="center"><!--{*順位*}--><!--{$smarty.section.cnt.iteration}--></td>
		<td align="center"><!--{*職業*}--><!--{$arrResults[cnt].job_name}--></td>
		<td align="right"><!--{*購入件数*}--><!--{$arrResults[cnt].order_count}-->件</td>
		<td align="right"><!--{*購入合計*}--><!--{$arrResults[cnt].total|number_format}-->円</td>
		<td align="right"><!--{*購入平均*}--><!--{$arrResults[cnt].total_average|number_format}-->円</td>
	</tr>
	<!--{/section}-->
	
	<tr bgcolor="#636469" align="center" class="fs12n">
		<td width="40"><span class="white">順位</span></td>
		<td width="175"><span class="white">職業</span></td>
		<td width="175"><span class="white">購入件数</span></td>				
		<td width="175"><span class="white">購入合計</span></td>				
		<td width="175"><span class="white">購入平均</span></td>
	</tr>
</table>