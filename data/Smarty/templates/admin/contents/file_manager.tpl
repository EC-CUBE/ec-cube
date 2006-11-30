<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->


<!--{foreach key=key item=item from=$arrErr}-->
	<!--{$key}-->：<!--{$item}-->
<!--{/foreach}-->
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
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
								<td bgcolor="">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->ファイル管理</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<!--▼ファイル管理テーブルここから-->
								<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->"  enctype="multipart/form-data">
								<input type="hidden" name="mode" value="">
								<input type="hidden" name="now_file" value="<!--{$tpl_now_dir}-->">
								<input type="hidden" name="tree_select_file" value="">
								<input type="hidden" name="tree_status" value="">
								<input type="hidden" name="select_file" value="">	
								
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n" bgcolor="#f2f1ec">
										<td>ディレクトリ</td>
										<td>
											<div id="now_dir">
											<img src="<!--{$smarty.const.URL_DIR}-->img/admin/contents/folder_open.gif" alt="フォルダ">
											&nbsp;<!--{$tpl_now_file}-->
											</div>
										</td>
									</tr>
									<tr class="fs12n" bgcolor="#ffffff">								
										<td>
											<div id="tree"></div>
										</td>
										<td>
											<div id="file_view">
												<table border="0" cellspacing="0" cellpadding="5" summary=" ">
													<tr class="fs12n" bgcolor="#f2f1ec">
														<td>ファイル名</td>
														<td align="right">サイズ</td>
														<td>更新日付</td>
													</tr>
													<!--{section name=cnt loop=$arrFileList}-->
													<!--{assign var="id" value="select_file`$smarty.section.cnt.index`"}-->
													<tr class="fs12n" id="<!--{$id}-->" onclick="fnSetFormVal('form1', 'select_file', '<!--{$arrFileList[cnt].file_path|escape}-->');fnSelectFile('<!--{$id}-->', '#808080');" style="" onMouseOver="fnChangeBgColor('<!--{$id}-->', '#808080');" onMouseOut="fnChangeBgColor('<!--{$id}-->', '');">
														<td>
															<!--{if $arrFileList[cnt].is_dir}-->
															<img src="<!--{$smarty.const.URL_DIR}-->img/admin/contents/folder_close.gif" alt="フォルダ">
															<!--{else}-->
															<img src="<!--{$smarty.const.URL_DIR}-->img/admin/contents/space.gif">
															<!--{/if}-->
															<!--{$arrFileList[cnt].file_name|escape}-->
														</td>
														<td align="right"><!--{$arrFileList[cnt].file_size|escape}--></td>
														<td><!--{$arrFileList[cnt].file_time|escape}--></td>
													</tr>
													<!--{/section}-->
												</table>
											</div>
											<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('view','',''); return false;" value="表示">
											<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('download','',''); return false;" value="ダウンロード">
											<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('delete','',''); return false;" value="削除">
										</td>
									</tr>
								</table>
								<table width="678" border="1" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td>現在のディレクトリ&nbsp;：&nbsp;<!--{$tpl_now_dir}--></td>
									</tr>
								</table>
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td bgcolor="#f2f1ec">ファイルのアップロード</td>
										<td bgcolor="#ffffff"><input type="file" name="upload_file" size="70" class="box60"><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('upload','',''); return false;" value="アップロード"></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec">フォルダ作成</td>
										<td bgcolor="#ffffff"><input type="text" name="create_file" value="" style="width:379px;"><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('create','',''); return false;" value="作成"></td>
									</tr>
									<thead>
								</table>
								</form>
								
								<!--▲ファイル管理テーブルここまで-->
																
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>
									
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
</table>
<!--★★メインコンテンツ★★-->