<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="list_name" value="">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->バックアップ作成</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
								
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr align="center" class="fs12n">
										<td bgcolor="#f2f1ec" width="130">バックアップ名<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="548" align=left>
											<span class="red12"><!--{$arrErr.bkup_name}--></span>
											<input type="text" name="bkup_name" value="<!--{$arrForm.bkup_name|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.bkup_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}--> ime-mode: disabled;" /><span class="red"> （上限<!--{$smarty.const.STEXT_LEN}-->文字）</span>
										</td>
									</tr>
									<tr align="center" class="fs12n">
										<td bgcolor="#f2f1ec" width="130">バックアップメモ</td>
										<td bgcolor="#ffffff" width="548" align=left>
											<span class="red12"><!--{$arrErr.bkup_memo}--></span>
											<textarea name="bkup_memo" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" cols="60" rows="5" class="area60" style="<!--{if $arrErr.bkup_memo != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" ><!--{$arrForm.bkup_memo|escape}--></textarea>
											<span class="red"> （上限<!--{$smarty.const.MTEXT_LEN}-->文字）</span>
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
												<td><input type="button" name="cre_bkup" value="バックアップデータを作成する" onClick="document.body.style.cursor = 'wait'; form1.mode.value='bkup'; submit();" /></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>
								
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->バックアップ一覧</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
								
								<!--{* 一覧が存在する場合のみ表示する *}-->
								<!--{if count($arrBkupList) > 0 }-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="150" align="center">バックアップ名</td>
										<td width="338">バックアップメモ</td>
										<td width="170">作成日</td>
										<td width="50">リストア</td>
										<td width="90">ダウンロード</td>
										<td width="50" align="center">削除</td>
									</tr>
									<!--{section name=cnt loop=$arrBkupList}-->
									<tr bgcolor="#ffffff" class="fs12">
										<td ><!--{$arrBkupList[cnt].bkup_name}--></td>
										<td ><!--{$arrBkupList[cnt].bkup_memo}--></td>			
										<td align="center"><!--{$arrBkupList[cnt].create_date|sfCutString:19:true:false}--></td>
										<td align="center"><a href="#" onclick="document.body.style.cursor = 'wait'; fnModeSubmit('restore','list_name','<!--{$arrBkupList[cnt].bkup_name}-->');">restore</a></td>
										<td align="center"><a href="#" onclick="fnModeSubmit('download','list_name','<!--{$arrBkupList[cnt].bkup_name}-->');">download</a></td>
										<td align="center">
											<a href="#" onclick="fnModeSubmit('delete','list_name','<!--{$arrBkupList[cnt].bkup_name}-->');">delete</a>
										</td>	
									</tr>
									<!--{/section}-->
								</table>
								<!--{/if}-->

								
								<!--{if $restore_msg != ""}-->
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">								
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
													
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr bgcolor="#ffffff" class="fs12">
										<td>
											▼実行結果<br>
											<!--{if $restore_err == false}--><input type="button" name="restore_config" value="テーブル構成を無視してリストアする" onClick="document.body.style.cursor = 'wait'; form1.mode.value='restore_config'; form1.list_name.value='<!--{$restore_name}-->'; submit();" /><br><!--{/if}-->
											<!--{$restore_msg}-->
										</td>
									</tr>								
								</table>
								<!--{/if}-->
								
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