<!--▼商品カテゴリーここから-->
<table width="166" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td colspan="3"><img src="/img/side/title_cat.jpg" width="166" height="35" alt="商品カテゴリー"></td>
	</tr>
	<tr>
		<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>

		<td align="center" bgcolor="#fff1e3">
			<table width="146" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr>
					<td height="10"><img src="/img/common/_.gif" width="15" height="1" alt=""></td>
					<td><img src="/img/common/_.gif" width="131" height="1" alt=""></td>
				</tr>
				
				<!--{section name=cnt loop=$arrTree}-->
				<!--{assign var=level value="`$arrTree[cnt].level`}-->
				
				<!--{* 区切り線表示 *}-->				
				<!--{if $level == 1 && !$smarty.section.cnt.first}-->
				<tr><td colspan="2" height="15"><img src="/img/side/line_146.gif" width="146" height="1" alt=""></td></tr>
				<!--{/if}-->
				<!--{* カテゴリ名表示 *}-->
				<!--{assign var=disp_name value="`$arrTree[cnt].category_name`"}-->
				<!--{if $arrTree[cnt].display == 1}-->
				<tr>
					<td colspan="2" class="fs12">
						<!--{if $tpl_category_id == $arrTree[cnt].category_id || $root_parent_id == $arrTree[cnt].category_id}-->
							<!--{section name=n loop=`$level-1`}-->&nbsp;&nbsp;<!--{/section}--><!--{if $level == 1}--><img src="/img/common/arrow_red.gif" width="11" height="14" alt=""><!--{/if}-->
						<!--{else}-->
							<!--{section name=n loop=`$level-1`}-->&nbsp;&nbsp;<!--{/section}--><!--{if $level == 1}--><img src="/img/common/arrow_blue.gif" width="11" height="14" alt=""><!--{/if}-->
						<!--{/if}-->
						<!--{if $tpl_category_id == $arrTree[cnt].category_id }-->
							<a href="/products/list.php?category_id=<!--{$arrTree[cnt].category_id}-->"><span class="redst"><!--{$disp_name|sfCutString:20|escape}-->(<!--{$arrTree[cnt].product_count|default:0}-->)</span></a>						
						<!--{else}-->							
							<a href="/products/list.php?category_id=<!--{$arrTree[cnt].category_id}-->"><!--{$disp_name|sfCutString:20|escape}-->(<!--{$arrTree[cnt].product_count|default:0}-->)</a>
						<!--{/if}-->
					</td>
				</tr>
				<!--{/if}-->
				<!--{/section}-->
			</table>
		</td>
		<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
	</tr>
	<tr>
		<td colspan="3"><img src="/img/side/flame_bottom02.gif" width="166" height="15" alt=""></td>
	</tr>
	<tr><td height="10"></td></tr>
</table>
<!--▲商品カテゴリーここまで-->
