				<table cellspacing="0" cellpadding="0" summary=" " id="category">
					<tr>
						<td bgcolor="#cc0000" height="3" colspan="3"></td>
					</tr>
					<tr>
						<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
						<td>
						<table width="168" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td><img src="../img/left/category_title.jpg" width="168" height="32" alt="¥«¥Æ¥´¥ê" /></td>
							</tr>
						</table>
						</td>
						<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
					</tr>
				</table>
				<table width="170" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
						<td bgcolor="#ecf5ff"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
						<td bgcolor="#ecf5ff">
						<table width="150" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td height="10"><img src="../img/_.gif" width="20" height="1" alt="" /></td>
								<td><img src="../img/_.gif" width="130" height="1" alt="" /></td>
							</tr>
							<!--{section name=cnt loop=$arrCategory}-->
							<!--{* ³¬ÁØ2 *}-->
							<!--{if $arrCategory[cnt].level == 2}-->
								<!--{if $smarty.section.cnt.index != 0}-->
								<tr>
									<td colspan="2" height="15"><img src="../img/left/category_line.gif" width="150" height="1" alt="" /></td>
								</tr>
								<!--{/if}-->
								<tr>
									<!--{if $arrCategory[cnt].category_id == $tpl_category_id || $arrCategory[cnt].category_id == $tpl_parent_category_id}-->
									<td><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}--><!--{$smarty.const.LIST_C_HTML}--><!--{$arrCategory[cnt].category_id}-->.html" class="link01"><img src="/img/left/category_icon_down.gif" width="14" height="11" alt="" /></a></td>
									<!--{else}-->
									<td><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}--><!--{$smarty.const.LIST_C_HTML}--><!--{$arrCategory[cnt].category_id}-->.html" class="link01"><img src="/img/left/category_icon.gif" width="14" height="11" alt="" /></a></td>
									<!--{/if}-->									
									<!--{if $arrCategory[cnt].category_id != $tpl_category_id}-->
									<td class="fs12st"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}--><!--{$smarty.const.LIST_C_HTML}--><!--{$arrCategory[cnt].category_id}-->.html" class="link01"><!--{$arrCategory[cnt].category_name|escape}-->(<!--{$arrCategory[cnt].product_count|default:0}-->)</a></td>
									<!--{else}-->
									<td class="red12st"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}--><!--{$smarty.const.LIST_C_HTML}--><!--{$arrCategory[cnt].category_id}-->.html" class="pan"><!--{$arrCategory[cnt].category_name|escape}-->(<!--{$arrCategory[cnt].product_count|default:0}-->)</a></td>
									<!--{/if}-->
								</tr>
							<!--{* ³¬ÁØ3 *}-->
							<!--{else}-->
								<tr>
									<td></td>
									<!--{if $arrCategory[cnt].category_id != $tpl_category_id}-->
									<td class="fs12"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}--><!--{$smarty.const.LIST_C_HTML}--><!--{$arrCategory[cnt].category_id}-->.html" class="link01"><!--{$arrCategory[cnt].category_name|escape}-->(<!--{$arrCategory[cnt].product_count|default:0}-->)</a></td>
									<!--{else}-->
									<td class="red12st"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}--><!--{$smarty.const.LIST_C_HTML}--><!--{$arrCategory[cnt].category_id}-->.html" class="pan"><!--{$arrCategory[cnt].category_name|escape}-->(<!--{$arrCategory[cnt].product_count|default:0}-->)</a></td>
									<!--{/if}-->
								</tr>
							<!--{/if}-->							
							<!--{/section}-->
						</table>
						</td>
						<td bgcolor="#ecf5ff"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
						<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
					</tr>
					<tr>
						<td colspan="5"><img src="../img/left/category_bottom.gif" width="170" height="10" alt="" /></td>
					</tr>
					<tr><td height="15"></td></tr>
				</table>