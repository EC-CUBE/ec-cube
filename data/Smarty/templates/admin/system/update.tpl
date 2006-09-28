<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="module_id" value="">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->アップデート機能一覧</span></td>
										<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
								
								<!--{* 一覧が存在する場合のみ表示する *}-->
								<!--{if count($arrUpdate) > 0 }-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="80" align="center">機能名</td>
										<td width="240">機能説明</td>
										<td width="40">現在Ver</td>
										<td width="40">最新Ver</td>										
										<td width="80" align="center">リリース日</td>
										<td width="50">install</td>
										<td width="50">uninstall</td>									
									</tr>
									<!--{section name=cnt loop=$arrUpdate}-->
									<tr bgcolor="#ffffff" class="fs12">
										<td ><!--{$arrUpdate[cnt].module_name}--></td>
										<td ><!--{$arrUpdate[cnt].module_explain}--></td>			
										<td align="center"><!--{$arrUpdate[cnt].now_version|default:'-'}--></td>
										<td align="center"><!--{$arrUpdate[cnt].latest_version}--></td>
										<td align="center"><!--{$arrUpdate[cnt].release_date|sfDispDBDate:false}--></td>
										<td align="center">
										<!--{if $arrUpdate[cnt].now_version != $arrUpdate[cnt].latest_version}-->
											<a href="#" onclick="fnModeSubmit('install','module_id','<!--{$arrUpdate[cnt].module_id}-->');">install</a></td>
										<!--{else}-->
											-
										<!--{/if}-->										
										<td align="center">
										<!--{if $arrUpdate[cnt].now_version != ""}-->
											<a href="#" onclick="fnModeSubmit('uninstall','module_id','<!--{$arrUpdate[cnt].module_id}-->');">uninstall</a>
										<!--{else}-->
											-
										<!--{/if}-->										
										</td>	
									</tr>
									<!--{/section}-->
								</table>
								<!--{/if}-->							
							
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
												<td><input type="submit" name="subm" value="最新のアップデート情報を取得する"/></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								
								<!--{if $update_mess != ""}-->
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">								
									<tr>
										<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
													
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr bgcolor="#ffffff" class="fs12">
										<td>
											▼実行結果<br>
											<!--{$update_mess}-->
										</td>
									</tr>								
								</table>
								<!--{/if}-->
								
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
