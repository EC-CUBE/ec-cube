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
<input type="hidden" name="mode" value="">
<input type="hidden" name="campaign_id" value="<!--{$arrForm.campaign_id}-->">
<input type="hidden" name="status" value="<!--{$arrForm.status}-->">
<input type="hidden" name="header_row" value="">
<input type="hidden" name="contents_row" value="">
<input type="hidden" name="footer_row" value="">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル--><!--{$tpl_campaign_title}--></span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<!--▼ヘッダー編集　ここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" align="center"><strong>ヘッダー編集</strong></td>
									</tr>
									
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" align="center">
											<br/><textarea name="header" cols=90 rows=<!--{$header_row}--> align="left" wrap=off style="width: 650px;"><!--{$header_data|smarty:nodefaults}--></textarea>
											<div align="right">
											<input type="button" value=<!--{if $header_row > 13}-->"小さくする"<!--{else}-->"大きくする"<!--{/if}--> onClick="ChangeSize(this, header, 50, 13, header_row)">
											</div><br/>
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
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>

								<!--▲ヘッダー編集　ここまで-->

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>

								<!--▼コンテンツ編集　ここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" align="center"><strong>コンテンツ編集</strong></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" align="center" colspan="2"><br/>
											<textarea name="contents" cols=90 rows=<!--{$contents_row}--> align="left" wrap=off style="width: 650px;"><!--{$contents_data|smarty:nodefaults}--></textarea>
											<div align="right">
											<input type="button" value="商品設定" onclick="win03('./campaign_create_tag.php?campaign_id=<!--{$arrForm.campaign_id}-->', 'search', '550', '500');">
											<input type="button" value=<!--{if $contents_row > 13}-->"小さくする"<!--{else}-->"大きくする"<!--{/if}--> onClick="ChangeSize(this, contents, 50, 13, contents_row)">
											</div><br/>
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
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>

								<!--▲コンテンツ編集　ここまで-->

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>

								<!--▼フッター編集　ここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" align="center" colspan="3"><strong>フッター編集</strong></td>
									</tr>

									<tr class="fs12n">
										<td bgcolor="#f2f1ec" align="center"><br/>
											<textarea name="footer" cols=90 rows=<!--{$footer_row}--> align="left" wrap=off style="width: 650px;"><!--{$footer_data|smarty:nodefaults}--></textarea>
											<div align="right">
											<input type="button" value=<!--{if $footer_row > 13}-->"小さくする"<!--{else}-->"大きくする"<!--{/if}--> onClick="ChangeSize(this, footer, 50, 13, footer_row)">
											</div><br/>
										</td>
									</tr>
								</table>
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td>
													<input type="button" onclick="fnFormModeSubmit('form1', 'return', '', ''); return false;" value="登録ページへ戻る">											
													<input type="button" onclick="fnFormModeSubmit('form1', 'regist', '', ''); return false;" value="保存">
													<input type="button" onclick="fnFormModeSubmit('form1', 'preview', '', ''); return false;" value="プレビュー">
												</td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>								
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>								
								<!--▲フッター編集　ここまで-->
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
			<!--▲登録テーブルここまで-->
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->

<script type="text/javascript">
	/* テキストエリアの大きさを変更する */
	function ChangeSize(button, TextArea, Max, Min, row_tmp){
		if(TextArea.rows <= Min){
			TextArea.rows=Max; button.value="小さくする"; row_tmp.value=Max;
		}else{
			TextArea.rows =Min; button.value="大きくする"; row_tmp.value=Min;
		}
	}
	
	/* ブラウザの種類をセットする */
	function lfnSetBrowser(form, item){
		browser_type = 0;
		if(navigator.userAgent.indexOf("MSIE") >= 0){
		    browser_type = 1;
		}
		else if(navigator.userAgent.indexOf("Gecko/") >= 0){
		    browser_type = 2;
		}
		
		document[form][item].value=browser_type;
	}

</script>
