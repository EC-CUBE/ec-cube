<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->"  enctype="multipart/form-data">
<input type="hidden" name="mode" value="">
<input type="hidden" name="now_file" value="<!--{$tpl_now_dir}-->">
<input type="hidden" name="tree_select_file" value="">
<input type="hidden" name="tree_status" value="">
<input type="hidden" name="select_file" value="">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--��SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--��SUB NAVI-->
		</td>
		<td class="mainbg">
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--�ᥤ�󥨥ꥢ-->
				<tr>
					<td align="center">
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr valign="top">
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�ե��������</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
								<!--���ե���������ơ��֥뤳������-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n" bgcolor="#f2f1ec">
										<td>�ǥ��쥯�ȥ�</td>
										<td>
											<div id="now_dir">
											<img src="<!--{$smarty.const.URL_DIR}-->img/admin/contents/folder_open.gif" alt="�ե����">
											&nbsp;<!--{$tpl_now_file}-->
											</div>
										</td>
									</tr>
									<tr class="fs12n" bgcolor="#ffffff">
										<td>
											<div id="tree"></div>
										</td>
										<td><span class="red"><!--{$arrErr.select_file}--></span>
											<div id="file_view">
												<table border="0" cellspacing="0" cellpadding="2" summary=" ">
													<tr class="fs12n" bgcolor="#f2f1ec">
														<td>�ե�����̾</td>
														<td align="right">������</td>
														<td>��������</td>
													</tr>
													<!--{if !$tpl_is_top_dir}-->
													<tr class="fs12n" id="parent_dir" onclick="fnSetFormVal('form1', 'select_file', '<!--{$tpl_parent_dir|escape}-->');fnSelectFile('parent_dir', '#808080');" onDblClick="setTreeStatus('tree_status');fnDbClick(arrTree, '<!--{$tpl_parent_dir|escape}-->', true, '<!--{$tpl_now_dir|escape}-->', true)" style="" onMouseOver="fnChangeBgColor('parent_dir', '#808080');" onMouseOut="fnChangeBgColor('parent_dir', '');">
														<td>
															<img src="<!--{$smarty.const.URL_DIR}-->img/admin/contents/folder_parent.gif" alt="�ե����">&nbsp;..
														</td>
														<td align="right"></td>
														<td></td>
													</tr>										
													<!--{/if}-->
													<!--{section name=cnt loop=$arrFileList}-->
													<!--{assign var="id" value="select_file`$smarty.section.cnt.index`"}-->
													<tr class="fs12n" id="<!--{$id}-->" onclick="fnSetFormVal('form1', 'select_file', '<!--{$arrFileList[cnt].file_path|escape}-->');fnSelectFile('<!--{$id}-->', '#808080');" onDblClick="setTreeStatus('tree_status');fnDbClick(arrTree, '<!--{$arrFileList[cnt].file_path|escape}-->', <!--{if $arrFileList[cnt].is_dir|escape}-->true<!--{else}-->false<!--{/if}-->, '<!--{$tpl_now_dir|escape}-->', false)" style="" onMouseOver="fnChangeBgColor('<!--{$id}-->', '#808080');" onMouseOut="fnChangeBgColor('<!--{$id}-->', '');">
														<td>
															<!--{if $arrFileList[cnt].is_dir}-->
															<img src="<!--{$smarty.const.URL_DIR}-->img/admin/contents/folder_close.gif" alt="�ե����">
															<!--{else}-->
															<img src="<!--{$smarty.const.URL_DIR}-->img/admin/contents/file.gif">
															<!--{/if}-->
															<!--{$arrFileList[cnt].file_name|escape}-->
														</td>
														<td align="right"><!--{$arrFileList[cnt].file_size|number_format}--></td>
														<td><!--{$arrFileList[cnt].file_time|escape}--></td>
													</tr>
													<!--{/section}-->
												</table>
											</div>
											<table border="0" cellspacing="0" cellpadding="5" summary=" ">
												<tr>
													<td><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('view','',''); return false;" value="ɽ��"></td>
													<td><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('download','',''); return false;" value="���������"></td>
													<td><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('delete','',''); return false;" value="���"></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
								<table width="678" border="1" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td>���ߤΥǥ��쥯�ȥ�&nbsp;��&nbsp;<!--{$tpl_now_dir|sfTrimURL}-->/</td>
									</tr>
								</table>
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td bgcolor="#f2f1ec">�ե�����Υ��åץ���</td>
										<td bgcolor="#ffffff"><span class="red"><!--{$arrErr.upload_file}--></span><input type="file" name="upload_file" size="64" class="box54" <!--{if $arrErr.upload_file}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('upload','',''); return false;" value="���åץ���"></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec">�ե��������</td>
										<td bgcolor="#ffffff"><span class="red"><!--{$arrErr.create_file}--></span><input type="text" name="create_file" value="" style="width:336px;<!--{if $arrErr.create_file}--> background-color:<!--{$smarty.const.ERR_COLOR|escape}--><!--{/if}-->"><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('create','',''); return false;" value="����"></td>
									</tr>
									<thead>
								</table>
								<!--���ե���������ơ��֥뤳���ޤ�-->
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
				<!--�ᥤ�󥨥ꥢ-->
			</table>
		</td>
	</tr>
</form>
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->