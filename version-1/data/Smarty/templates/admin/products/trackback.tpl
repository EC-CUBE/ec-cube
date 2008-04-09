<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="search_form" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" >
<input type="hidden" name="mode" value="search">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
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
						<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
						<td bgcolor="#cccccc">
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->検索条件設定</span></td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>

						<!--検索条件設定テーブルここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">ブログ名</td>
								<td bgcolor="#ffffff" width="248"><input type="text" name="search_blog_name" value="<!--{$arrForm.search_blog_name|escape}-->" size="30" class="box30" /></td>
								<td bgcolor="#f2f1ec" width="110">ブログ記事<br />タイトル</td>
								<td bgcolor="#ffffff" width="249"><input type="text" name="search_blog_title" value="<!--{$arrForm.search_blog_title|escape}-->" size="30" class="box30" /></td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">URL</td>
								<td bgcolor="#ffffff" width="248"><input type="text" name="search_blog_url" value="<!--{$arrForm.search_blog_url|escape}-->" size="30" class="box30" /></td>
								<td bgcolor="#f2f1ec" width="110">状態</td>
								<td bgcolor="#ffffff" width="249">
								<select name="search_status" style="<!--{$arrErr.search_status|sfGetErrorColor}-->">
								<option value="">----</option>
								<!--{html_options options=$arrTrackBackStatus selected=$arrForm.search_status}-->
								</select>
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">商品名</td>
								<td bgcolor="#ffffff" width="248"><input type="text" name="search_name" value="<!--{$arrForm.search_name|escape}-->" size="30" class="box30" /></td>
								<td bgcolor="#f2f1ec" width="110">商品コード</td>
								<td bgcolor="#ffffff" width="249"><input type="text" name="search_product_code" value="<!--{$arrForm.search_product_code|escape}-->" size="30" class="box30" /></td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">投稿日</td>
								<td bgcolor="#ffffff" width="607" colspan="3">
								<span class="red"><!--{$arrErr.search_startyear}--></span>
								<span class="red"><!--{$arrErr.search_endyear}--></span>		
								<select name="search_startyear" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
								<option value="">----</option>
								<!--{html_options options=$arrStartYear selected=$arrForm.search_startyear}-->
								</select>年
								<select name="search_startmonth" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
								<option value="">--</option>
								<!--{html_options options=$arrStartMonth selected=$arrForm.search_startmonth}-->
								</select>月
								<select name="search_startday" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
								<option value="">--</option>
								<!--{html_options options=$arrStartDay selected=$arrForm.search_startday}-->
								</select>日〜
								<select name="search_endyear" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
								<option value="">----</option>
								<!--{html_options options=$arrEndYear selected=$arrForm.search_endyear}-->
								</select>年
								<select name="search_endmonth" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
								<option value="">--</option>
								<!--{html_options options=$arrEndMonth selected=$arrForm.search_endmonth}-->
								</select>月
								<select name="search_endday" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
								<option value="">--</option>
								<!--{html_options options=$arrEndDay selected=$arrForm.search_endday}-->
								</select>日
								</td>
							</tr>
						</table>

						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
								<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
								<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
							</tr>
							<tr>
								<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
								<td bgcolor="#e9e7de" align="center">
								<table border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n">検索結果表示件数
											<!--{assign var=key value="search_page_max"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
											<!--{html_options options=$arrPageMax selected=$arrForm.search_page_max}-->
											</select> 件
										</td>
										<td><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="10" height="1" alt=""></td>
										<td><input type="image" name="subm" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_search_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_search.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_search.jpg" width="123" height="24" alt="この条件で検索する" border="0" ></td>
									</tr>
								</table>
								</td>
								<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
							</tr>
						</table>
						<!--検索条件設定テーブルここまで-->
						</td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
					</tr>
					<tr>
						<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr><td height="30"></td></tr>
				</table>
				</td>
			</tr>
			<!--メインエリア-->
		</table>
		</td>
	</tr>
</form>	
</table>
<!--★★メインコンテンツ★★-->

<!--{if $smarty.post.mode == 'search'}-->

	<!--★★検索結果一覧★★-->
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="trackback_id" value="">
	<input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->">
	<!--{foreach key=key item=item from=$arrHidden}-->
	<!--{if $key ne "search_pageno"}-->
	<input type="hidden" name="<!--{$key}-->" value="<!--{$item}-->" >
	<!--{/if}-->
	<!--{/foreach}-->
		<tr><td colspan="2"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
		<tr bgcolor="cbcbcb">
			<td>
			<table border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr>
					<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/search_left.gif" width="19" height="22" alt=""></td>
					<td>
					<!--検索結果-->
					<table border="0" cellspacing="0" cellpadding="0" summary=" ">
						<tr>
							<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_left_top.gif" width="22" height="5" alt=""></td>
							<td background="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_top_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
							<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_right_top.gif" width="22" height="5" alt=""></td>
						</tr>
						<tr>
							<td background="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_left_middle.gif" width="22" height="12" alt=""></td>
							<td bgcolor="#393a48" class="white10">検索結果一覧　<span class="reselt"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。</td>
							<td background="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="22" height="8" alt=""></td>
						</tr>
						<tr>
							<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_left_bottom.gif" width="22" height="5" alt=""></td>
							<td background="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_bottom_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
							<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_right_bottom.gif" width="22" height="5" alt=""></td>
						</tr>
					</table>
					<!--検索結果-->
					<!--{if $smarty.const.ADMIN_MODE == '1'}-->
					<input type="button" name="subm" value="検索結果をすべて削除" onclick="fnModeSubmit('delete_all','','');" />
					<!--{/if}-->
					</td>
					<td><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="8" height="1" alt=""></td>
					<td><a href="#" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/contents/btn_csv_on.jpg','btn_csv');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/contents/btn_csv.jpg','btn_csv');"  onclick="fnModeSubmit('csv','','');" ><img src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_csv.jpg" width="99" height="22" alt="CSV DOWNLOAD" border="0" name="btn_csv" id="btn_csv"></a></td>
				</tr>
			</table>
			</td>
			<td align="right">
				<!--{include file=$tpl_pager}-->
			</td>									
		</tr>
		<tr><td bgcolor="cbcbcb" colspan="2"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td></tr>
	</table>
	
	<!--{ if $arrTrackback > 0 & $tpl_linemax > 0 }-->
		<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td bgcolor="#f0f0f0" align="center">
				<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="12"></td></tr>
					<tr>
						<td bgcolor="#cccccc">
						
						<!--検索結果表示テーブル-->
						<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr bgcolor="#636469" align="center" class="fs12">
								<td width="130"><span class="white">投稿日</span></td>
								<td width="162"><span class="white">商品名</span></td>
								<td width="150"><span class="white">ブログ名</span></td>
								<td width="140"><span class="white">ブログ記事タイトル</span></td>
								<td width="80"><span class="white">状態</span></td>
								<td width="50"><span class="white">編集</span></td>
								<td width="50"><span class="white">削除</span></td>
							</tr>
		
							<!--{section name=cnt loop=$arrTrackback}-->
							<tr bgcolor="#ffffff" class="fs12">
								<td align="center"><!--{$arrTrackback[cnt].create_date|sfDispDBDate}--></td>
								<td><!--{$arrTrackback[cnt].name|escape}--></td>
								<td><a href="<!--{$arrTrackback[cnt].url|escape}-->"><!--{$arrTrackback[cnt].blog_name|escape}--></a></td>
								<td><!--{$arrTrackback[cnt].title|escape}--></td>
								<td align="center"><!--{if $arrTrackback[cnt].status eq 1}-->表示<!--{elseif $arrTrackback[cnt].status eq 2}-->非表示<!--{elseif $arrTrackback[cnt].status eq 3}-->スパム<!--{/if}--></td>
								<td align="center"><a href="#" onclick="fnChangeAction('./trackback_edit.php'); fnModeSubmit('','trackback_id','<!--{$arrTrackback[cnt].trackback_id}-->');">編集</a></td>
								<td align="center"><a href="#" onclick="fnModeSubmit('delete','trackback_id','<!--{$arrTrackback[cnt].trackback_id}-->'); return false;">削除</a></td>
							</tr>
							<!--{/section}-->
						</table>
						<!--検索結果表示テーブル-->
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</form>
		</table>
	<!--{ /if }-->
<!--{ /if }-->
<!--★★検索結果一覧★★-->		
