<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" >
<input type="hidden" name="mode" value="complete">
<!--{foreach key=key item=item from=$arrTrackback}-->
<!--{if $key ne "mode"}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/if}-->
<!--{/foreach}-->
<!--{foreach key=key item=item from=$arrSearchHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->
	<tr valign="top">
		<td background="<!--{$TPL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg" >
		<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<!--メインエリア-->
			<tr>
				<td align="center">
				<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">

					<tr><td height="14"></td></tr>
					<tr>
						<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr>
						<td background="<!--{$TPL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
						<td bgcolor="#cccccc">
						
							<!--▼登録テーブルここから-->
							<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr>
									<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
								</tr>
								<tr>
									<td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
									<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->トラックバック設定</span></td>
									<td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
								</tr>
								<tr>
									<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
								</tr>
								<tr>
									<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
								</tr>
							</table>


							<!--▼編集テーブルここから-->
							<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" " bgcolor="#cccccc">
								<tr class="fs12n">
									<td bgcolor="#f2f1ec" width="160">商品名</td>
									<td bgcolor="#ffffff" width="483"><!--{$arrTrackback.name|escape}--></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec" width="160">ブログ名</td>
									<td bgcolor="#ffffff" width="483"><!--{$arrTrackback.blog_name|escape}--></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec" width="160">ブログ記事タイトル</td>
									<td bgcolor="#ffffff" width="483"><!--{$arrTrackback.title|escape}--></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec" width="160">ブログ記事内容</td>
									<td bgcolor="#ffffff" width="483"><!--{$arrTrackback.excerpt|escape}--></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec" width="160">ブログURL</td>
									<td bgcolor="#ffffff" width="483"><!--{$arrTrackback.url|escape}--></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec">投稿日</td>
									<td bgcolor="#ffffff"><!--{$arrTrackback.create_date|sfDispDBDate}--></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec">状態</td>
									<td bgcolor="#ffffff">
									<!--{assign var=key value="status"}-->
									<span class="red12"><!--{$arrErr.status}--></span>
									<select name="<!--{$key}-->" style="<!--{$arrErr.status|sfGetErrorColor}-->" >
									<option value="">選択してください</option>
									<!--{html_options options=$arrTrackBackStatus selected=$arrTrackback[$key]}-->
									</select>
									</td>
								</tr>
							</table>
							<!--▲編集テーブルここまで-->
							<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr>
									<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
									<td><img src="<!--{$TPL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
									<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
								</tr>
								<tr>
									<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									<td bgcolor="#e9e7de" align="center">
									<table border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td>
												<input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_search_back_on.jpg',this);" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_search_back.jpg',this);" src="<!--{$TPL_DIR}-->img/contents/btn_search_back.jpg" width="123" height="24" alt="検索画面に戻る" border="0" name="back" onclick="document.form1.action='./trackback.php'; fnModeSubmit('search','','');" ></a>
												<input type="image" onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$TPL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" onclick="fnModeSubmit('complete','','');" />
											</td>
										</tr>
									</table>
									</td>
									<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
								</tr>
								<tr>
									<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
								</tr>
							</table>
							<!-- ▲登録テーブルここまで -->

						</td>
						<td background="<!--{$TPL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
					</tr>
					<tr>
						<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
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