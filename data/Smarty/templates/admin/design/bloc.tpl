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
		<td background="/img/contents/navi_bg.gif" height="402">
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
						<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr>
						<td background="/img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
						<td bgcolor="#cccccc">
						
						<!--登録テーブルここから-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="/img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->ブロック編集</span></td>
								<td background="/img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>

						<!--▼ブロック編集　ここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center"><strong>ブロック編集</strong></td>
							</tr>
							<!--{if $arrBlocData.tpl_path != '' and $preview == on}-->
							<tr class="fs12n">
								<td bgcolor="#ffffff" align="center">
									<br/>
										<!--{include file=$arrBlocData.tpl_path}-->
									<br/>
								</td>
							</tr>
							<!--{/if}-->
							
							<form name="form_bloc" id="form_bloc" method="post" action="<!--{$smarty.server.PHP_SELF}-->" >
							<input type="hidden" name="mode" value="">
							<input type="hidden" name="bloc_id" value="<!--{$bloc_id}-->">

							<tr class="fs12n">
								<td bgcolor="#ffffff" align="left">
									<!--{ if $arrErr.bloc_name != "" }--> <div align="center"> <span class="red12"><!--{$arrErr.bloc_name}--></span></div> <!--{/if}-->
									ブロック名：<input type="text" name="bloc_name" value="<!--{$arrBlocData.bloc_name|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.bloc_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" size="60" class="box60" /><span class="red"> （上限<!--{$smarty.const.STEXT_LEN}-->文字）</span>
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#ffffff" align="left">
									<!--{ if $arrErr.filename != "" }--> <div align="center"> <span class="red12"><!--{$arrErr.filename}--></span></div> <!--{/if}-->
									ファイル名：<input type="text" name="filename" value="<!--{$arrBlocData.filename|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.filename != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />.tpl<span class="red"> （上限<!--{$smarty.const.STEXT_LEN}-->文字）</span>
								</td>
							</tr>
						
							<tr class="fs12n">
								<td bgcolor="#ffffff" align="center">
									<br/>
									<div>
									<textarea name="bloc_html" cols=75 rows=<!--{$text_row}--> align="left" wrap=off style="width: 650px;"><!--{$arrBlocData.tpl_data}--></textarea>
									<input type="hidden" name="html_area_row" value="<!--{$text_row}-->">
									</div>
									<div align="right">
									<input type="button" value=<!--{if $text_row > 13}-->"小さくする"<!--{else}-->"大きくする"<!--{/if}--> onClick="ChangeSize(this, bloc_html, 50, 13, html_area_row)">
									</div>
									<br/>
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center">
									<input type='button' value='登録' name='subm' onclick="fnFormModeSubmit('form_bloc','confirm','','');"  />
									<input type='button' value='プレビュー' name='preview' onclick="fnFormModeSubmit('form_bloc','preview','','');"  />
								</td>
							</tr>
						</table>
						<!--▲ブロック編集　ここまで-->

						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>

						<!--▼ブロック一覧　ここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=2 ><strong>編集可能ブロック</strong></td>
							</tr>
							
							<!--{foreach key=key item=item from=$arrBlocList}-->
							<tr class="fs12n" height=20>
								<td align="center" width=600 bgcolor="<!--{if $item.bloc_id == $bloc_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<a href="<!--{$smarty.server.PHP_SELF}-->?bloc_id=<!--{$item.bloc_id}-->" ><!--{$item.bloc_name}--></a>
								</td>
								<td  align="center" width=140 bgcolor="<!--{if $item.bloc_id == $bloc_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<!--{if $item.del_flg == 0}-->
									<input type="button" value="削除" name="del<!--{$item.bloc_id}-->" onclick="fnFormModeSubmit('form_bloc','delete','bloc_id',this.name.substr(3));"  />
									<input type="hidden" value="<!--{$item.bloc_id}-->" name="del_id<!--{$item.bloc_id}-->">
									<!--{/if}-->
								</td>
							</tr>
							<!--{/foreach}-->
					
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=2>
								<input type='button' value='新規ブロック作成' name='subm' onclick="location.href='http://<!--{$smarty.server.HTTP_HOST}--><!--{$smarty.server.PHP_SELF}-->'">
								</td>
							</tr>
						</table>
						<!--▲ブロック一覧　ここまで-->

						</td>
						<td background="/img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
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

