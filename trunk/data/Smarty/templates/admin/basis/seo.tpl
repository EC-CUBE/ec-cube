<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.post.PHP_SELF|escape}-->" onSubmit="return window.confirm('��Ͽ���Ƥ⵹�����Ǥ���');">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="page_id" value="">
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
								
								<!--{if count($arrPageData) > 0 }-->
									<!--{foreach name=page key=key item=item from=$arrPageData}-->
									<input type="hidden" name="disp_flg<!--{$item.page_id}-->" value="<!--{$disp_flg[$item.page_id]}-->">
									<!-- <!--{$item.page_name}--> �������� -->
									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="638" class="fs14n"><span class="white" style="float:left"><!--����ƥ�ĥ����ȥ�--><!--{$item.page_name}--> <!--{$item.url}--></span><a href="#" id="switch<!--{$item.page_id}-->" style="float:right " onClick="fnDispChange('disp<!--{$item.page_id}-->', 'switch<!--{$item.page_id}-->', 'disp_flg<!--{$item.page_id}-->');"><!--{if $disp_flg[$item.page_id] == ""}--><FONT Color="#FFFF99"> >> ��ɽ��</FONT><!--{else}--><FONT Color="#FFFF99"> << ɽ��</FONT><!--{/if}--></a></td>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
										</tr>
									</table>
									
									<div id="disp<!--{$item.page_id}-->" style="display:<!--{$disp_flg[$item.page_id]}-->">
										<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
											<tr>
												<td bgcolor="#f2f1ec" width="160" class="fs12n">�᥿����:Author</td>
												<td bgcolor="#ffffff" width="557" class="fs10n">
												<span class="red12"><!--{$arrErr[$item.page_id].author}--></span>
												<input type="text" name="meta[<!--{$item.page_id}-->][author]" value="<!--{$item.author|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style='<!--{if $arrErr[$item.page_id].author != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->' /><span class="red"> �ʾ��<!--{$smarty.const.STEXT_LEN}-->ʸ����</span></td>
											</tr>
											<tr>
												<td bgcolor="#f2f1ec" width="160" class="fs12n">�᥿����:Description</td>
												<td bgcolor="#ffffff" width="557" class="fs10n">
												<span class="red12"><!--{$arrErr[$item.page_id].description}--></span>
												<input type="text" name="meta[<!--{$item.page_id}-->][description]" value="<!--{$item.description|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style='<!--{if $arrErr[$item.page_id].description != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->' /><span class="red"> �ʾ��<!--{$smarty.const.STEXT_LEN}-->ʸ����</span></td>
											</tr>
											<tr>
												<td bgcolor="#f2f1ec" width="160" class="fs12n">�᥿����:Keywords</td>
												<td bgcolor="#ffffff" width="557" class="fs10n">
												<span class="red12"><!--{$arrErr[$item.page_id].keyword}--></span>
												<input type="text" name="meta[<!--{$item.page_id}-->][keyword]" value="<!--{$item.keyword|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style='<!--{if $arrErr[$item.page_id].keyword != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->' /><span class="red"> �ʾ��<!--{$smarty.const.STEXT_LEN}-->ʸ����</span></td>
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
														<td><input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="�������Ƥ���Ͽ����" border="0" name="subm" onclick="document.form1.page_id.value = <!--{$item.page_id}-->;"></td>
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
									</div>
									<!-- <!--{$item.page_name}--> �����ޤ� -->
									<!--{/foreach}-->
								<!--{else}-->
									<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr>
											<td bgcolor="#ffffff" width="" height="300" class="fs12n" align="center">
												ɽ������ǡ���������ޤ���
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
