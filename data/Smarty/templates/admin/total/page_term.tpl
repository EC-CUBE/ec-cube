<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
	<tr bgcolor="#636469" align="center" class="fs10n">
		<td width="100">期間</td>
		<td width="80"><span class="white">購入件数</span></td>
		<td width="80"><span class="white">男性</span></td>				
		<td width="80"><span class="white">女性</span></td>				
		<td width="80"><span class="white">男性(会員)</span></td>
		<td width="80"><span class="white">男性<br />(非会員)</span></td>
		<td width="80"><span class="white">女性(会員)</span></td>
		<td width="80"><span class="white">女性<br />(非会員)</span></td>
		<td width="80"><span class="white">購入合計</span></td>
		<td width="80"><span class="white">購入平均</span></td>
	</tr>
	
	<!--{section name=cnt loop=$arrResults}-->
	<!--{* 色分け判定 *}-->
	<!--{if !$smarty.section.cnt.last}-->
		<!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
		<!--{if $type == 0}-->
			<!--{* 偶数行 *}-->
			<!--{assign var=color value="#FFFFFF"}-->
		<!--{else}-->
			<!--{* 奇数行 *}-->
			<!--{assign var=color value="#D1DEFE"}-->
		<!--{/if}-->
	<!--{else}-->
		<!--{* 最終行 *}-->
		<!--{assign var=color value="#FFD9EC"}-->
	<!--{/if}-->
	
	
	<tr bgcolor="<!--{$color}-->" class="fs10">
		<!--{assign var=wday value="`$arrResults[cnt].wday`"}-->
		
		<!--{if !$smarty.section.cnt.last}-->
			<td align="center"><!--{*期間*}--><!--{$arrResults[cnt][$keyname]}--><!--{if $keyname == "key_day"}-->(<!--{$arrWDAY[$wday]}-->)<!--{/if}--><!--{$tpl_tail}--></td>
		<!--{else}-->
			<td align="center"><!--{*期間*}-->合計</td>
		<!--{/if}-->
		
		<td align="right"><!--{*購入件数*}--><!--{$arrResults[cnt].total_order}-->件</td>
		<td align="right"><!--{*男性*}--><!--{$arrResults[cnt].men}--></td>
		<td align="right"><!--{*女性*}--><!--{$arrResults[cnt].women}--></td>
		<td align="right"><!--{*男性(会員)*}--><!--{$arrResults[cnt].men_member}--></td>
		<td align="right"><!--{*男性(非会員)*}--><!--{$arrResults[cnt].men_nonmember}--></td>
		<td align="right"><!--{*女性(会員)*}--><!--{$arrResults[cnt].women_member}--></td>
		<td align="right"><!--{*女性(非会員)*}--><!--{$arrResults[cnt].women_nonmember}--></td>
		<td align="right"><!--{*購入合計*}--><!--{$arrResults[cnt].total|number_format}-->円</td>
		<td align="right"><!--{*購入平均*}--><!--{$arrResults[cnt].total_average|number_format}-->円</td>
	</tr>
	<!--{/section}-->
	
	<tr bgcolor="#636469" align="center" class="fs10n">
		<td width="100">期間</td>
		<td width="80"><span class="white">購入件数</span></td>
		<td width="80"><span class="white">男性</span></td>				
		<td width="80"><span class="white">女性</span></td>				
		<td width="80"><span class="white">男性(会員)</span></td>
		<td width="80"><span class="white">男性<br />(非会員)</span></td>
		<td width="80"><span class="white">女性(会員)</span></td>
		<td width="80"><span class="white">女性<br />(非会員)</span></td>
		<td width="80"><span class="white">購入合計</span></td>
		<td width="80"><span class="white">購入平均</span></td>
	</tr>
</table>