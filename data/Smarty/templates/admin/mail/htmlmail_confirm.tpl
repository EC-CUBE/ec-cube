<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="complete">
<!--$arrForm-->
<!--{foreach key=key item=item from=$arrForm}-->
	<!--{if $key != "mode"}-->
	<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
	<!--{/if}-->
<!--{/foreach}-->

<!--$arrHidden-->
<!--{foreach key=key item=item from=$arrHidden}-->
	<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->HTML�᡼�����</span></td>
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
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">Subject<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547" class="fs12n"><!--{$arrForm.subject|escape}--></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�᡼��ô���̿�<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547">
										<!--{assign var=key value="charge_image"}-->
										<!--{if $arrFile[$key].filepath != ""}-->
										<img src="<!--{$arrFile[$key].filepath}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" />
										<!--{/if}-->
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�إå����ƥ�����<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547" class="fs12n"><!--{$arrForm.header|escape|nl2br}--></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�ᥤ���ʥ����ȥ�<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547" class="fs12n"><!--{$arrForm.main_title|escape}--></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�ᥤ���ʥ�����<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547" class="fs12n"><!--{$arrForm.main_comment|escape}--></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�ᥤ��������<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547">
										<img src="<!--{$smarty.const.IMAGE_SAVE_URL|escape}--><!--{$arrFileName[0].main_image}-->" alt="�ᥤ���ʲ���"><br />
										<input type="text" name="name_main_product" value="<!--{$arrFileName[0].name|escape}-->" disabled="disabled"  size="65" class="box65" style="background:#FFF;border-style:solid;border-color:#FFF;"/>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">���־��ʷ������ȥ�<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547" class="fs12n"><!--{$arrForm.sub_title|escape}--></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">���־��ʷ�������<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547" class="fs12n"><!--{$arrForm.sub_comment|escape|nl2br}--></td>
									</tr>
									<!--{foreach key=key item=item from=$arrSub.delete}-->
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">���ʲ�����<!--{$key}-->��</td>
										<td bgcolor="#ffffff" width="547">
											<!--{if $arrFileName[$key].main_image != "" && $item != 'on'}-->
											<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrFileName[$key].main_image`"}-->
											<img src="<!--{$image_path}-->" width="<!--{$smarty.const.SMALL_IMAGE_WIDTH}-->" height="<!--{$smarty.const.SMALL_IMAGE_HEIGHT}-->" alt="���ʲ���<!--{$smarty.section.cnt.iteration}-->" /><br />
											<input type="text" name="name_sub_product" value="<!--{$arrFileName[$key].name|escape}-->" disabled="disabled"  size="65" class="box65" style="background:#FFF;border-style:solid;border-color:#FFF;"/>
											<!--{else}-->̤��Ͽ<!--{/if}-->
										</td>
									</tr>
									<!--{/foreach}-->
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
												<td>
													<input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_back_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_back.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_back.jpg" width="123" height="24" alt="���Υڡ��������" border="0" name="return" onclick="fnModeSubmit('return', '', ''); return false;" >
													<input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="�������Ƥ���Ͽ����" border="0" name="subm" >
												</td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
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
			<!--����Ͽ�ơ��֥뤳���ޤ�-->
		</td>
	</tr>
</form>
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->
