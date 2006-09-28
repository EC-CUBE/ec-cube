<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="200">
			<!-- サブナビ -->
			<!--{include file=$tpl_subnavi}-->
		</td>
		<td class="mainbg">
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル--><!--{$tpl_subtitle}--></span></td>
								<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>
						<!--検索条件設定テーブルここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<form name="search_form1" id="search_form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
							<input type="hidden" name="mode" value="search">
							<input type="hidden" name="form" value="1">
							<input type="hidden" name="page" value="<!--{$arrForm.page.value}-->">
							<input type="hidden" name="type" value="<!--{$smarty.post.type}-->">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">月度集計</td>
								<td bgcolor="#ffffff" width="607" colspan="3">
									<span class="red"><!--{$arrErr.search_startyear_m}--></span>
									<span class="red"><!--{$arrErr.search_endyear_m}--></span>		
									<select name="search_startyear_m"  style="<!--{$arrErr.search_startyear_m|sfGetErrorColor}-->">
									<!--{html_options options=$arrYear selected=$arrForm.search_startyear_m.value}-->
									</select>年
									<select name="search_startmonth_m" style="<!--{$arrErr.search_startyear_m|sfGetErrorColor}-->">
									<!--{html_options options=$arrMonth selected=$arrForm.search_startmonth_m.value}-->
									</select>月度 （<!--{if $smarty.const.CLOSE_DAY == 31}-->末<!--{else}--><!--{$smarty.const.CLOSE_DAY}--><!--{/if}-->日締め）
									　<input type="submit" name="subm" value="月度で集計する" />
								</td>
							</tr>
							</form>
							<form name="search_form2" id="search_form2" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
							<input type="hidden" name="mode" value="search">
							<input type="hidden" name="form" value="2">
							<input type="hidden" name="page" value="<!--{$arrForm.page.value}-->">
							<input type="hidden" name="type" value="<!--{$smarty.post.type}-->">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">期間集計</td>
								<td bgcolor="#ffffff" width="607" colspan="3">
									<span class="red"><!--{$arrErr.search_startyear}--></span>
									<span class="red"><!--{$arrErr.search_endyear}--></span>		
									<select name="search_startyear"  style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
									<option value="">----</option>
									<!--{html_options options=$arrYear selected=$arrForm.search_startyear.value}-->
									</select>年
									<select name="search_startmonth" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrMonth selected=$arrForm.search_startmonth.value}-->
									</select>月
									<select name="search_startday" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrDay selected=$arrForm.search_startday.value}-->
									</select>日〜
									<select name="search_endyear" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
									<option value="">----</option>
									<!--{html_options options=$arrYear selected=$arrForm.search_endyear.value}-->
									</select>年
									<select name="search_endmonth" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrMonth selected=$arrForm.search_endmonth.value}-->
									</select>月
									<select name="search_endday" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrDay selected=$arrForm.search_endday.value}-->
									</select>日
									　<input type="submit" name="subm" value="期間で集計する" />
								</td>
							</tr>
							</form>

						</table>
						<!--検索条件設定テーブルここまで-->
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
		</td>
	</tr>
</table>
<!--★★メインコンテンツ★★-->


<!--{if count($arrResults) > 0}-->
	<!--★★検索結果一覧★★-->
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="type" value="<!--{$arrForm.type.value}-->">
	<input type="hidden" name="page" value="<!--{$arrForm.page.value}-->">
	<!--{foreach key=key item=item from=$arrHidden}-->
	<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
	<!--{/foreach}-->	
		<tr><td colspan="2"><img src="/img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
	</table>
	
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<tr>
			<td bgcolor="#f0f0f0" align="center">
	
				<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td bgcolor="#f0f0f0">
						<!--検索結果表示テーブル-->
						<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr><td align="center">
								<!--{* タイトル表示 *}-->
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td><hr noshade size="1" color="#f0f0f0" /></td></tr>
									<tr><td height="5"></td></tr>
									<tr>
										<!--{include file=$tpl_graphsubtitle}-->
									</tr>
								</table>
						
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="5"></td></tr>
									<tr>
										<td align = center>
										<input type="button" name="subm" value="検索結果をCSVダウンロード" onclick="fnModeSubmit('csv','','');" />
										<!--{* PDF機能は次期開発で追加予定
										 <input type="button" name="subm" value="検索結果をPDFダウンロード" onclick="fnModeSubmit('pdf','','');" />
										*}-->
										</td>
									</tr>
								</table>

								<!--{* グラフ表示 *}-->		
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="15"></td></tr>
									<tr>
										<td align="center"><img src="<!--{$tpl_image}-->" alt="グラフ"></td>
									</tr>
									<tr><td height="15"></td></tr>
								</table>
								<!--{* グラフ表示 *}-->		
								<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc">
										<!--▼検索結果テーブルここから-->
										<!--{include file=$tpl_page_type}-->
										<!--▲検索結果テーブルここまで-->
										</td>
									</tr>
								</table>
								<!--▲MAIN CONTENTS-->
								</td>
							</tr>
						</table>
						<!--検索結果表示テーブル-->
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</form>
	</table>
<!--▲検索結果表示エリアここまで-->
<!--{else}-->
	<!--{if $smarty.post.mode == 'search'}-->
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="type" value="<!--{$arrForm.type.value}-->">
	<input type="hidden" name="page" value="<!--{$arrForm.page.value}-->">
	<!--{foreach key=key item=item from=$arrHidden}-->
	<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
	<!--{/foreach}-->	
		<tr><td colspan="2"><img src="/img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
	</table>	
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<tr>
			<td bgcolor="#f0f0f0" align="center">
				<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td bgcolor="#f0f0f0">
						<!--検索結果表示テーブル-->
						<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr><td align="center">	
								<!--{* タイトル表示 *}-->
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td><hr noshade size="1" color="#f0f0f0" /></td></tr>
									<tr><td height="5"></td></tr>
									<tr>
											<!--{include file=$tpl_graphsubtitle}-->
									</tr>
								</table>
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="10"></td></tr>
									<tr class="fs12"><td align="center" height="200">該当するデータはありません。</td></tr>
								</table>
								</td>
							</tr>
						</table>
						<!--検索結果表示テーブル-->
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</form>
	</table>
	<!--{/if}-->
<!--{/if}-->

<!--★★検索結果一覧★★-->		
