<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--��SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--��SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--��CONTENTS-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--�ᥤ�󥨥ꥢ-->
				<tr>
					<td align="center">
					<!--��MAIN CONTENTS-->
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<!--����Ͽ�ơ��֥뤳������-->
						<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" enctype="multipart/form-data">
						<!--{foreach key=key item=item from=$arrForm}-->
						<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
						<!--{/foreach}-->
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
									<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->������Ͽ</span></td>
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
									<td bgcolor="#f2f1ec" width="160" class="fs12n">����̾</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.name|escape}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">���ʥ��ƥ���</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{assign var=key value=$arrForm.category_id}-->
									<!--{$arrCatList[$key]|strip|sfTrim}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">�����������</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrDISP[$arrForm.status]}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">���ʥ��ơ�����</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{section name=cnt loop=$arrForm.product_flag|count_characters}-->
										<!--{if $arrForm.product_flag[cnt] == "1"}--><!--{assign var=key value="`$smarty.section.cnt.iteration`"}--><img src="<!--{$arrSTATUS_IMAGE[$key]}-->"><!--{/if}-->
									<!--{/section}-->
									</td>
								</tr>
								
								<!--{if $tpl_nonclass == true}-->
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">���ʥ�����</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.product_code|escape}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">���ͻԾ����</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.price01|escape}-->
									��</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">���ʲ���</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.price02|escape}-->
									��</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">�߸˿�</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{if $arrForm.stock_unlimited == 1}-->
									̵����
									<!--{else}-->
									<!--{$arrForm.stock|escape}-->
									��<!--{/if}-->
									</td>
								</tr>
								<!--{/if}-->
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">�ݥ������ͿΨ</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.point_rate|escape}-->
									��</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">ȯ�����ܰ�</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrDELIVERYDATE[$arrForm.deliv_date_id]|escape}-->
									</td>
								</tr>			
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">��������</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{if $arrForm.sale_unlimited == 1}-->
									̵����
									<!--{else}-->
									<!--{$arrForm.sale_limit|escape}-->
									��<!--{/if}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">�᡼����URL</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.comment1|escape|sfPutBR:$smarty.const.LINE_LIMIT_SIZE}-->
									</td>
								</tr>
								<!--{*
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">��ʬ</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.comment2|escape}-->
									</td>
								</tr>
								*}-->
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">�������</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.comment3|escape}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">����-�ᥤ�󥳥���</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.main_list_comment|escape|nl2br}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-�ᥤ�󥳥���</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.main_comment|nl2br}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">����-�ᥤ�����</td>
									<td bgcolor="#ffffff" width="557">
									<!--{assign var=key value="main_list_image"}-->
									<!--{if $arrFile[$key].filepath != ""}-->
									<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" /><br />
									<!--{/if}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-�ᥤ�����</td>
									<td bgcolor="#ffffff" width="557">
									<!--{assign var=key value="main_image"}-->
									<!--{if $arrFile[$key].filepath != ""}-->
									<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" /><br />
									<!--{/if}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-�ᥤ��������</td>
									<td bgcolor="#ffffff" width="557">
									<!--{assign var=key value="main_large_image"}-->
									<!--{if $arrFile[$key].filepath != ""}-->
									<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" /><br />
									<!--{/if}-->
									</td>
								</tr>
								<!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
								<!--������<!--{$smarty.section.cnt.iteration}-->-->
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-���֥����ȥ��<!--{$smarty.section.cnt.iteration}-->��</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{assign var=key value="sub_title`$smarty.section.cnt.iteration`"}-->
									<!--{$arrForm[$key]|escape}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-���֥����ȡ�<!--{$smarty.section.cnt.iteration}-->��</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{assign var=key value="sub_comment`$smarty.section.cnt.iteration`"}-->
									<!--{$arrForm[$key]|nl2br}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-���ֲ�����<!--{$smarty.section.cnt.iteration}-->��</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{assign var=key value="sub_image`$smarty.section.cnt.iteration`"}-->
									<!--{if $arrFile[$key].filepath != ""}-->
									<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" /><br />
									<!--{/if}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-���ֳ��������<!--{$smarty.section.cnt.iteration}-->��</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{assign var=key value="sub_large_image`$smarty.section.cnt.iteration`"}-->
									<!--{if $arrFile[$key].filepath != ""}-->
									<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" /><br />
									<!--{/if}-->
									</td>
								</tr>
								<!--������<!--{$smarty.section.cnt.iteration}-->-->
								<!--{/section}-->
								
								<!--{if $smarty.const.OPTION_RECOMMEND == 1}-->	
								<!--����Ϣ����-->
								<!--{section name=cnt loop=$smarty.const.RECOMMEND_PRODUCT_MAX}-->			
								<!--{assign var=recommend_no value="`$smarty.section.cnt.iteration`"}-->
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">��Ϣ����(<!--{$smarty.section.cnt.iteration}-->)<br>
									<!--{if $arrRecommend[$recommend_no].main_list_image != ""}-->
										<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_DIR`/`$arrRecommend[$recommend_no].main_list_image`"}-->
									<!--{else}-->
										<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_DIR`"}-->
									<!--{/if}-->
									<img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$image_path|sfRmDupSlash}-->&width=65&height=65" alt="<!--{$arrRecommend[$recommend_no].name|escape}-->">
									</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{if $arrRecommend[$recommend_no].name != ""}-->
									���ʥ�����:<!--{$arrRecommend[$recommend_no].product_code_min}--><br>
									����̾:<!--{$arrRecommend[$recommend_no].name|escape}--><br>
									������:<br>
									<!--{$arrRecommend[$recommend_no].comment|escape}-->
									<!--{/if}-->
									</td>
								</tr>
								<!--{/section}-->
								<!--����Ϣ����-->
								<!--{/if}-->
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
												<a href="#" onMouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/contents/btn_back_on.jpg','back')" onMouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/contents/btn_back.jpg','back');" onclick="fnModeSubmit('confirm_return','',''); return false;"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_back.jpg" width="123" height="24" alt="���Υڡ��������" border="0" name="back"></a>
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
						
					<!--����Ͽ�ơ��֥뤳���ޤ�-->
					</form>
					<!--��MAIN CONTENTS-->
					</td>
				</tr>
			</table>
			<!--��CONTENTS-->
		</td>
	</tr>
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->
