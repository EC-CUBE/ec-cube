<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="module_id" value="">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--��SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--��SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--����Ͽ�ơ��֥뤳������-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--�ᥤ�󥨥ꥢ-->
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�⥸�塼�뵡ǽ����</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
								
								<!--{* ������¸�ߤ�����Τ�ɽ������ *}-->
								<!--{if count($arrUpdate) > 0 }-->
								<table width="678" border="0" cellspacing="1" cellpadding="4" summary=" ">
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="50">��</td>
										<td width="160">��ǽ̾</td>
										<td width="290">����</td>
										<td width="45">����</td>
										<td width="45">�ǿ�</td>
										<td width="70">��꡼����</td>
										<td width="45">����</td>
										<td width="45">�¹�</td>
									</tr>
									<!--{section name=cnt loop=$arrUpdate}-->
									<tr bgcolor="#ffffff" class="fs12">
										<!--{if $arrUpdate[cnt].url}-->
										<td align="center">
										<a href="<!--{$arrUpdate[cnt].url}-->" target="_blank"><img src="resize_image.php?image=<!--{$arrUpdate[cnt].icon}-->&width=50&height=50"></a>
										</td>
										<!--{else}-->
										<td align="center">
										<img src="resize_image.php?image=<!--{$arrUpdate[cnt].icon}-->&width=50&height=50">
										</td>
										<!--{/if}-->
										<td ><!--{$arrUpdate[cnt].module_name}--></td>
										<td ><!--{$arrUpdate[cnt].module_explain}-->(<!--{$arrUpdate[cnt].eccube_version}-->�ʹߤ��б�)</td>
										<td align="center"><!--{$arrUpdate[cnt].now_version|default:"-"}--></td>
										<td align="center"><!--{$arrUpdate[cnt].latest_version}--></td>		
										<td align="center"><!--{$arrUpdate[cnt].release_date|sfDispDBDate:false}--></td>
										<td align="center">
										<!--{if $arrUpdate[cnt].now_version == "" || $arrUpdate[cnt].now_version < $arrUpdate[cnt].latest_version}-->
											<!--{if $arrUpdate[cnt].eccube_version <= $smarty.const.ECCUBE_VERSION}-->
											<span class="icon_edit"><a href="#" onclick="fnModeSubmit('install','module_id','<!--{$arrUpdate[cnt].module_id}-->');">Ŭ��</a></span>
											<!--{else}-->
											-
											<!--{/if}-->
										<!--{else}-->
											<span class="icon_delete"><a href="#" onclick="fnModeSubmit('uninstall','module_id','<!--{$arrUpdate[cnt].module_id}-->');">���</a></span>
										<!--{/if}-->									
										</td>
										<td align="center">
										<!--{if $arrUpdate[cnt].now_version != ""}-->
											<span class="icon_confirm"><a href="#" onclick="win01('<!--{$smarty.const.URL_DIR}-->load_module.php?module_id=<!--{$arrUpdate[cnt].module_id}-->','module<!--{$arrUpdate[cnt].module_id}-->','<!--{$arrUpdate[cnt].module_x}-->','<!--{$arrUpdate[cnt].module_y}-->'); return false;">�¹�</a></span>
										<!--{else}-->
											-
										<!--{/if}-->
										</td>
									</tr>
									<!--{/section}-->
								</table>
								<!--{else}-->
								<table width="678" border="0" cellspacing="1" cellpadding="4" summary=" ">
									<tr bgcolor="#ffffff" align="center" class="fs12n">
										<td>���ߡ��⥸�塼�����Ϥ������ޤ���</td>
									</tr>
								</table>
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">	
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
								<!--{/if}-->							
							
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
												<td><input type="submit" name="subm" value="�ǿ��Υ��åץǡ��Ⱦ�����������"/></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								
								<!--{if $update_mess != ""}-->
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">								
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
													
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr bgcolor="#ffffff" class="fs12">
										<td>
											���¹Է��<br>
											<!--{$update_mess}-->
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
				<!--�ᥤ�󥨥ꥢ-->
			</table>
			<!--����Ͽ�ơ��֥뤳���ޤ�-->
		</td>
	</tr>
</form>
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->
