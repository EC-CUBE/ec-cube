<SCRIPT language="JavaScript">
<!--
browser_type = 0;
if(navigator.userAgent.indexOf("MSIE") >= 0){
    browser_type = 1;
}
else if(navigator.userAgent.indexOf("Gecko/") >= 0){
    browser_type = 2;
}
//-->
</SCRIPT>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->ヘッダー･フッター編集</span></td>
										<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<!--▼ヘッダー編集　ここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" align="center"><strong>ヘッダー編集</strong></td>
									</tr>
									<!-- プレビューここから -->
									<tr class="fs12n">
										<td bgcolor="#ffffff" align="center">
											<br/>
		<script type="text/javascript">
			alert("aaa");
			if(navigator.userAgent.indexOf("MSIE") >= 0){
				<div style="zoom:0.8"><!--{include file="`$smarty.const.ROOT_DIR`html/user_data/include/preview/header.tpl"}--></div>
			} else {
				alert("aaa");
			    document.write('tttt' );
			}
		</SCRIPT>
											<br/>
										</td>
									</tr>
									<!-- プレビューここまで -->
									
									<form name="form_header" id="form_header" method="post" action="<!--{$smarty.server.PHP_SELF}-->" >
									<input type="hidden" name="mode" value="">
									<input type="hidden" name="division" value="header">
									<input type="hidden" name="header_row" value=<!--{$header_row}-->>
										<tr class="fs12n">
											<td bgcolor="#f2f1ec" align="center">
												<br/>
													<textarea name="header" cols=90 rows=<!--{$header_row}--> align="left" wrap=off style="width: 650px;"><!--{$header_data}--></textarea>
												<div align="right">
												<input type="button" value=<!--{if $header_row > 13}-->"小さくする"<!--{else}-->"大きくする"<!--{/if}--> onClick="ChangeSize(this, header, 50, 13, header_row)">
												</div>
												<br/>
											</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f2f1ec" align="center">
												<input type='button' value='登録' name='subm' onclick="fnFormModeSubmit('form_header','confirm','','');"  />
												<input type='button' value='プレビュー反映' name='preview' onclick="fnFormModeSubmit('form_header','preview','','');"  />
											</td>
										</tr>
									</form>
								</table>
								<!--▲ヘッダー編集　ここまで-->

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>

								<!--▼フッター編集　ここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" align="center" colspan="3"><strong>フッター編集</strong></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#ffffff" align="center">
											<br/>
												<div style="zoom:0.8"><!--{include file="`$smarty.const.ROOT_DIR`html/user_data/include/preview/footer.tpl"}--></div>
											<br/>
										</td>
									</tr>

									<form name="form_footer" id="form_footer" method="post" action="<!--{$smarty.server.PHP_SELF}-->" >
									<input type="hidden" name="mode" value="">
									<input type="hidden" name="division" value="footer">
									<input type="hidden" name="footer_row" value=<!--{$footer_row}-->>
										<tr class="fs12n">
											<td bgcolor="#f2f1ec" align="center">
												<br/>
												<textarea name="footer" cols=90 rows=<!--{$footer_row}--> align="left" wrap=off style="width: 650px;"><!--{$footer_data}--></textarea>
												<div align="right">
												<input type="button" value=<!--{if $footer_row > 13}-->"小さくする"<!--{else}-->"大きくする"<!--{/if}--> onClick="ChangeSize(this, footer, 50, 13, footer_row)">
												</div>
												<br/>
											</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f2f1ec" align="center">
												<input type='button' value='登録' name='subm' onclick="fnFormModeSubmit('form_footer','confirm','','');"  />
												<input type='button' value='プレビュー反映' name='preview' onclick="fnFormModeSubmit('form_footer','preview','','');"  />
											</td>
										</tr>
									</form>
								</table>
								<!--▲フッター編集　ここまで-->

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
</table>
<!--★★メインコンテンツ★★-->


<script type="text/javascript">
alert(1232);
	function ChangeSize(button, TextArea, Max, Min, row_tmp){
		if(TextArea.rows <= Min){
			TextArea.rows=Max; button.value="小さくする"; row_tmp.value=Max;
		}else{
			TextArea.rows =Min; button.value="大きくする"; row_tmp.value=Min;
		}
	}
</script>
