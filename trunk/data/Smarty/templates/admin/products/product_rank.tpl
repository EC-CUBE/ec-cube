<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" enctype="multipart/form-data">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="parent_category_id" value="<!--{$arrForm.parent_category_id}-->">
<input type="hidden" name="category_id" value="<!--{$arrForm.category_id}-->">
<input type="hidden" name="product_id" value="">
<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--��SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--��SUB NAVI-->
		</td>
		<td class="mainbg" >
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
						
							<!--����Ͽ�ơ��֥뤳������-->
							<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr>
									<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
								</tr>
								<tr>
									<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
									<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�����¤��ؤ�</span></td>
									<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
								</tr>
								<tr>
									<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
								</tr>
								<tr>
									<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
								</tr>
							</table>	
							
							<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
					
								<tr bgcolor="#ffffff">
									<!--�����̺�-->
									<td width="250" valign="top" class="fs12">
									<a href="<!--{$smarty.server.PHP_SELF|escape}-->">���ۡ���</a><br>
									<!--{section name=cnt loop=$arrTree}-->
										<!--{assign var=level value="`$arrTree[cnt].level`}-->
										
										<!--{* ��γ���ɽ���λ���div���Ĥ��� *}-->
										<!--{assign var=close_cnt value="`$before_level-$level+1`}-->
										<!--{if $close_cnt > 0}-->
											<!--{section name=n loop=$close_cnt}--></div><!--{/section}-->
										<!--{/if}-->
															
										<!--{* ���ڡ��������֤� *}-->
										<!--{section name=n loop=$level}-->����<!--{/section}-->
										
										<!--{* ���ƥ���̾ɽ�� *}-->
										<!--{assign var=disp_name value="`$arrTree[cnt].category_id`.`$arrTree[cnt].category_name`"}-->
										<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('tree', 'parent_category_id', <!--{$arrTree[cnt].category_id}-->); return false">
										<!--{if $arrForm.parent_category_id == $arrTree[cnt].category_id}-->
											<img src="<!--{$smarty.const.URL_DIR}-->misc/openf.gif" border="0">
										<!--{else}-->
											<img src="<!--{$smarty.const.URL_DIR}-->misc/closef.gif" border="0">
										<!--{/if}-->
										<!--{$disp_name|sfCutString:20|escape}-->(<!--{$arrTree[cnt].product_count|default:0}-->)</a>
									<br>					
										<!--{if $arrTree[cnt].display == true}-->
											<div id="f<!--{$arrTree[cnt].category_id}-->">
										<!--{else}-->
											<div id="f<!--{$arrTree[cnt].category_id}-->" style="display:none">
										<!--{/if}-->				
										<!--{assign var=before_level value="`$arrTree[cnt].level`}-->
									<!--{/section}-->		
									</td>
									<!--�����̺�-->
										
									<!--�����̱�-->				
									<!--{if count($arrProductsList) > 0}-->
									<td width="428" valign="top">
																	
									<table width="428" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr><td class="fs12n"><!--{$tpl_linemax}-->�郎�������ޤ�����	</td></tr>
										<tr class="fs12">
											<td align="center">
											<!--���ڡ����ʥ�-->
											<!--{$tpl_strnavi}-->
											<!--���ڡ����ʥ�-->
											</td>
										</tr>
										<tr><td height="10"></td></tr>
									</table>
													
									<table border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
										<tr bgcolor="#f0f0f0" align="center" class="fs12n">
											<td width="27">���</td>
											<td width="110">���ʥ�����</td>
											<td width="100">���ʲ���</td>
											<td width="130">����̾</td>
											<td width="120">��ư</td>
										</tr>
										<!--{section name=cnt loop=$arrProductsList}-->
										<tr bgcolor="#ffffff" align="left" class="fs12n">
											<!--{assign var=db_rank value="`$arrProductsList[cnt].rank`"}-->
											<!--{assign var=rank value="`$tpl_linemax-$db_rank+1`"}-->
											<td width="27" align="center"><!--{$rank}--></td>
											<td width="110"><!--{$arrProductsList[cnt].product_code|escape|default:"-"}--></td>
											<td width="100" align="center">
												<!--{* ���ʲ��� *}-->
												<!--{if $arrProductsList[cnt].main_list_image != ""}-->
													<!--{assign var=image_path value=`$smarty.const.IMAGE_SAVE_DIR`/`$arrProductsList[cnt].main_list_image`}-->
												<!--{else}-->
													<!--{assign var=image_path value=$smarty.const.NO_IMAGE_URL}-->
												<!--{/if}-->
												<img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$image_path|sfRmDupSlash}-->&width=65&height=65" alt="<!--{$arrProducts[cnt].name|escape}-->">
											</td>
											<td width="130" align="center">
												<!--{$arrProductsList[cnt].name|escape}-->
											</td>
											
											<td width="120" align="center">
											<!--{* ��ư *}-->
											<!--{if !(count($arrProductsList) == 1 && $rank == 1)}-->
											<input type="text" name="pos-<!--{$arrProductsList[cnt].product_id}-->" size="3" class="box3" />���ܤ�<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('move','product_id', '<!--{$arrProductsList[cnt].product_id}-->'); return false;">��ư</a><br />
											<!--{/if}-->
											<!--{if !($smarty.section.cnt.first && $tpl_disppage eq 1) }-->
											<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('up','product_id', '<!--{$arrProductsList[cnt].product_id}-->'); return false;">���</a>
											<!--{/if}-->
											<!--{if !($smarty.section.cnt.last && $tpl_disppage eq $tpl_pagemax) }-->
											<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('down','product_id', '<!--{$arrProductsList[cnt].product_id}-->'); return false;">����</a>
											<!--{/if}-->
											</td>
										</tr>
										<!--{/section}-->
									</table>
									
									<table width="428" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr><td height="5"></td></tr>
										<tr class="fs12">
											<td align="center">
											<!--���ڡ����ʥ�-->
											<!--{$tpl_strnavi}-->
											<!--���ڡ����ʥ�-->
											</td>
										</tr>
										<tr><td height="5"></td></tr>
									</table>
									</td>		
									<!--{else}-->
									<td width="428" valign="top" class="fs12n">���ƥ�������򤷤Ƥ���������</td>
									<!--{/if}-->
									<!--�����̱�-->
								</tr>
							</table>
							<!-- ����Ͽ�ơ��֥뤳���ޤ� -->

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
