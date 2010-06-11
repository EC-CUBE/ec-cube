<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
<script type="text/javascript">
function doPreview(){
	document.form_edit.mode.value="preview"
	document.form_edit.target = "_blank";
	document.form_edit.submit();
}

function fnTargetSelf(){
	document.form_edit.target = "_self";
}

</script>

<SCRIPT language="JavaScript">
<!--
browser_type = 0;
if(navigator.userAgent.indexOf("MSIE") >= 0){
    browser_type = 1;
}
else if(navigator.userAgent.indexOf("Mozilla") >= 0){
    browser_type = 2;
}
//-->
</SCRIPT>


<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form_edit" id="form_edit" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" >
<input type="hidden" name="mode" value="">
<input type="hidden" name="page_id" value="<!--{$page_id}-->">
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
						
						<!--登録テーブルここから-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->ページ詳細設定</span></td>
								<td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>

						<!--▼編集画面　ここから-->
						<!--{if $arrErr.page_id_err != ""}-->
						<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr>
								<td bgcolor="#ffffff" align="center" class="fs12st red"><!--{$arrErr.page_id_err}--></td>
							</tr>
						</table>
						<!--{/if}-->
						
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#ffffff" align="left" colspan=2>
									<!--{ if $arrErr.page_name != "" }--><span class="red12"><!--{$arrErr.page_name}--></span><!--{/if}-->
									<!--{if $arrPageData.edit_flg == 2}-->
										名称：<!--{$arrPageData.page_name|escape}--><input type="hidden" name="page_name" value="<!--{$arrPageData.page_name|escape}-->" />
									<!--{else}-->
										名称：<input type="text" name="page_name" value="<!--{$arrPageData.page_name|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.page_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" size="60" class="box60" /><span class="red"> （上限<!--{$smarty.const.STEXT_LEN}-->文字）</span>
									<!--{/if}-->
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#ffffff" align="left" colspan=2>
									<!--{ if $arrErr.url != "" }--><span class="red12"><!--{$arrErr.url}--></span><!--{/if}-->
									URL：<!--{if $arrPageData.edit_flg == 2}-->
											<!--{$smarty.const.SITE_URL|escape}--><!--{$arrPageData.url|escape}-->
											<input type="hidden" name="url" value="<!--{$arrPageData.filename|escape}-->" />
										<!--{else}-->
											<!--{$user_URL|escape}--><input type="text" name="url" value="<!--{$arrPageData.directory|escape}--><!--{$arrPageData.filename|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.url != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}--> ime-mode: disabled;" size="40" class="box40" />.php<span class="red"> （上限<!--{$smarty.const.STEXT_LEN}-->文字）</span>
										<!--{/if}-->
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#ffffff" align="left">
									<label for="header_chk"><input type="checkbox" name="header_chk" id="header_chk" <!--{$arrPageData.header_chk}-->>共通のヘッダーを使用する</label>
								</td>
								<td bgcolor="#ffffff" align="left">
									<label for="footer_chk"><input type="checkbox" name="footer_chk" id="footer_chk" <!--{$arrPageData.footer_chk}-->>共通のフッターを使用する</label>
								</td>
							</tr>

							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=2>
									<br/>
									<div>
									<textarea name="tpl_data" cols=90 rows=<!--{$text_row}--> align="left" wrap=off style="width: 650px; "><!--{$arrPageData.tpl_data|escape|smarty:nodefaults}--></textarea>
									<input type="hidden" name="html_area_row" value="<!--{$text_row}-->">
									</div>
									<div align="right">
										<input type="button" value="大きくする" onClick="ChangeSize(this, tpl_data, 50, 13, html_area_row)">
									</div>
									<br/>
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=2>
									<input type='button' value='登録' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form_edit','confirm','','');"  />
									<input type='button' value='プレビュー' name='preview' onclick="doPreview(); "  />
								</td>
							</tr>
						</table>
						<!--▲編集画面　ここまで-->
						
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>
						
						<!--▼一覧　ここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=3 ><strong>編集可能画面一覧</strong></td>
							</tr>
							
							<!--{foreach key=key item=item from=$arrPageList}-->
							<tr class="fs12n" height=20>
								<td align="center" width=600 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<a href="<!--{$smarty.server.PHP_SELF|escape}-->?page_id=<!--{$item.page_id}-->" ><!--{$item.page_name}--></a>
								</td>
								<td align="center" width=78 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<input type="button" value="レイアウト" name="layout<!--{$item.page_id}-->" onclick="location.href='./index.php?page_id=<!--{$item.page_id}-->';"  />
									<input type="hidden" value="<!--{$item.page_id}-->" name="del_id<!--{$item.page_id}-->">
								</td>
								<td align="center" width=78 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<!--{if $item.edit_flg == 1}-->
									<input type="button" value="削除" name="del<!--{$item.page_id}-->" onclick="fnTargetSelf(); fnFormModeSubmit('form_edit','delete','page_id',this.name.substr(3));"  />
									<input type="hidden" value="<!--{$item.page_id}-->" name="del_id<!--{$item.page_id}-->">
									<!--{/if}-->
								</td>
							</tr>
							<!--{/foreach}-->
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=3>
								<input type='button' value='新規ページ作成' name='subm' onclick="location.href='http://<!--{$smarty.server.HTTP_HOST}--><!--{$smarty.server.PHP_SELF|escape}-->'">
								</td>
							</tr>
						</table>
						<!--▲一覧　ここまで-->

						<!--登録テーブルここまで-->
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


