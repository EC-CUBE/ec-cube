<!--{*
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="/img/contents/main_left.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->メンバー管理</span></td>
										<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
								<tr bgcolor="#ffffff" class="fs12n"><td>
										<table width="650" border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr bgcolor="#ffffff" class="fs12n">
												<td align="center">
												<!--▼ページ送り-->
												<!--{$tpl_strnavi}-->
												<!--▲ページ送り-->
												</td>
											</tr>
											<tr><td height="10"></td></tr>
										</table>
								
										<!--▼メンバー一覧ここから-->
										<table width="650" bgcolor="#cccccc" border="0" cellspacing="1" cellpadding="5" summary=" ">
											<tr bgcolor="#f2f1ec" align="center" class="fs12n">
												<td width="65">権限</td>
												<td width="155">名前</td>
												<td width="155">所属</td>
												<td width="30">稼動</td>
												<td width="60">非稼動</td>
												<td width="50">編集</td>
												<td width="50">削除</td>
												<td width="80">移動</td>
											</tr>
											<!--{section name=data loop=$list_data}--><!--▼メンバー<!--{$smarty.section.data.iteration}-->-->
											<tr bgcolor="#ffffff" class="fs12">
												<!--{assign var="auth" value=$list_data[data].authority}--><td width="65" align="center"><!--{$arrAUTHORITY[$auth]|escape}--></td>
												<td width=""><!--{$list_data[data].name|escape}--></td>
												<td width=""><!--{$list_data[data].department|escape}--></td>
												<td width="" align="center"><!--{if $list_data[data].work eq 1}--><input type="radio" name="radio<!--{$smarty.section.data.iteration}-->" value="稼動" onclick="fnChangeRadio(this.name, 1, <!--{$list_data[data].member_id}-->, <!--{$tpl_disppage}-->);" checked /><!--{else}--><input type="radio" name="radio<!--{$smarty.section.data.iteration}-->" value="稼動" onclick="fnChangeRadio(this.name, 1, <!--{$list_data[data].member_id}-->, <!--{$tpl_disppage}-->);"/><!--{/if}--></td>
												<td width="" align="center"><!--{if $list_data[data].work eq 0}--><input type="radio" name="radio<!--{$smarty.section.data.iteration}-->" value="非稼動"  onclick="fnChangeRadio(this.name, 0, <!--{$list_data[data].member_id}-->, <!--{$tpl_disppage}-->);" checked /><!--{else}--><input type="radio" name="radio<!--{$smarty.section.data.iteration}-->" value="非稼動" onclick="fnChangeRadio(this.name, 0, <!--{$list_data[data].member_id}-->, <!--{$tpl_disppage}-->);" <!--{if $workmax <= 1 }-->disabled<!--{/if}-->  /><!--{/if}--></td>
												<td width="" align="center"><a href="./" onClick="win01('./input.php?id=<!--{$list_data[data].member_id}-->&pageno=<!--{$tpl_disppage}-->','member_edit','500','420'); return false;">編集</a></td>
												<td width="" align="center"><!--{if $workmax > 1 }--><a href="./" onClick="fnDeleteMember(<!--{$list_data[data].member_id}-->,<!--{$tpl_disppage}-->); return false;">削除</a><!--{else}-->-<!--{/if}--></td>
												<td width="" align="center">
												<!--{$tpl_nomove}-->
												<!--{if !($smarty.section.data.first && $tpl_disppage eq 1) }--><a href="./rank.php?id=<!--{$list_data[data].member_id}-->&move=up&pageno=<!--{$tpl_disppage}-->">上へ</a><!--{/if}-->
												<!--{if !($smarty.section.data.last && $tpl_disppage eq $tpl_pagemax) }--><a href="./rank.php?id=<!--{$list_data[data].member_id}-->&move=down&pageno=<!--{$tpl_disppage}-->">下へ</a><!--{/if}-->
												</td>
											</tr>
											<!--▲メンバー<!--{$smarty.section.data.iteration}-->-->
											<!--{/section}-->
										</table>
										<table width="650" border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr><td height="10"></td></tr>
											<tr bgcolor="#ffffff" class="fs12n">
												<td align="center">
												<!--▼ページ送り-->
												<!--{$tpl_strnavi}-->
												<!--▲ページ送り-->
								
												</td>
											</tr>
										</table>
									</td></tr>

								</table>


								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="/img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td><input type="button" name="new" value="メンバー新規登録" onclick="win01('./input.php','input','500','420');" /></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								</td>
								<td background="/img/contents/main_right.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--メインエリア-->
			</table>
			<!--▲登録テーブルここまで-->
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->
