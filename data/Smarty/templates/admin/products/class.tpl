<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="./class.php">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="class_id" value="<!--{$tpl_class_id}-->">
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
						
						<!--��Ͽ�ơ��֥뤳������-->
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
								<td bgcolor="#f2f1ec" width="160" class="fs12n">����̾<span class="red"> *</span></td>
								<td bgcolor="#ffffff" width="557" class="fs10n">
								<span class="red12"><!--{$arrErr.name}--></span>
								<input type="text" name="name" value="<!--{$arrForm.name|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="" size="30" class="box30"/>
								<span class="red"> �ʾ��<!--{$smarty.const.STEXT_LEN}-->ʸ����</span>
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
										<td>
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
												
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>
						
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr bgcolor="#f2f1ec" align="center" class="fs12n">
								<td width="322">����̾ (��Ͽ��)</td>
								<td width="100">ʬ����Ͽ</td>
								<td width="50">�Խ�</td>
								<td width="50">���</td>
								<td width="70">��ư</td>
							</tr>
							<!--{section name=cnt loop=$arrClass}-->
							<tr bgcolor="<!--{if $tpl_class_id != $arrClass[cnt].class_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->" class="fs12">
								<!--{assign var=class_id value=$arrClass[cnt].class_id}-->
								<td><!--{* ����̾ *}--><!--{$arrClass[cnt].name|escape}--> (<!--{$arrClassCatCount[$class_id]|default:0}-->)</td>
								<td align="center"><a href="<!--{$smarty.const.URL_DIR}-->" onclick="fnClassCatPage(<!--{$arrClass[cnt].class_id}-->); return false;">ʬ����Ͽ</a></td>
								<td align="center">
								<!--{if $tpl_class_id != $arrClass[cnt].class_id}-->
								<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('pre_edit', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;">�Խ�</a>
								<!--{else}-->
								�Խ���
								<!--{/if}-->
								</td>
								<td align="center">
								<!--{if $arrClassCatCount[$class_id] > 0}-->
								-
								<!--{else}-->
								<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('delete', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;">���</a>
								<!--{/if}-->
								</td>
								<td align="center">
								<!--{if $smarty.section.cnt.iteration != 1}-->
								<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('up', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;" />���</a>
								<!--{/if}-->
								<!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
								<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('down', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;" />����</a>
								<!--{/if}-->
								</td>
							</tr>
							<!--{/section}-->
						</table>
						<!--��Ͽ�ơ��֥뤳���ޤ�-->
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
