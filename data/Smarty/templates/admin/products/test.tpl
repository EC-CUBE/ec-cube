<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="search_form" id="search_form" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="search">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
			<!-- ���֥ʥ� -->
		</td>
		<td class="mainbg">
		<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<!--�ᥤ�󥨥ꥢ-->
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�����������</span></td>
								<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>
						<!--�����������ơ��֥뤳������-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
								<td><img src="/img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
								<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
							</tr>
							<tr>
								<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
								<td bgcolor="#e9e7de" align="center">
								<table border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n">�������ɽ�����
											<!--{assign var=key value="search_page_max"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
											<!--{html_options options=$arrPageMax selected=$arrForm.search_page_max}-->
											</select> ��
										</td>
										<td><img src="/img/common/_.gif" width="10" height="1" alt=""></td>
										<td><input type="image" name="subm" onMouseover="chgImgImageSubmit('/img/contents/btn_search_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/contents/btn_search.jpg',this)" src="/img/contents/btn_search.jpg" width="123" height="24" alt="���ξ��Ǹ�������" border="0" onClick="submit();" ></td>
									</tr>
								</table>
								</td>
								<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
							</tr>
						</table>
						<!--�����������ơ��֥뤳���ޤ�-->
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
			<!--�ᥤ�󥨥ꥢ-->
		</table>
		</td>
	</tr>
</form>	
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete')}-->

<!--����������̰�������-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="product_id" value="">
<input type="hidden" name="category_id" value="">
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->		
	<tr><td colspan="2"><img src="/img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
	<tr bgcolor="cbcbcb">
		<td>
		<table border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="/img/contents/search_left.gif" width="19" height="22" alt=""></td>
				<td>
				<!--�������-->
				<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><img src="/img/contents/reselt_left_top.gif" width="22" height="5" alt=""></td>
						<td background="/img/contents/reselt_top_bg.gif"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="/img/contents/reselt_right_top.gif" width="22" height="5" alt=""></td>
					</tr>
					<tr>
						<td background="/img/contents/reselt_left_bg.gif"><img src="/img/contents/reselt_left_middle.gif" width="22" height="12" alt=""></td>
						<td bgcolor="#393a48" class="white10">������̰�����<span class="reselt"><!--������̿�--><!--{$tpl_linemax}-->��</span>&nbsp;���������ޤ�����</td>
						<td background="/img/contents/reselt_right_bg.gif"><img src="/img/common/_.gif" width="22" height="8" alt=""></td>
					</tr>
					<tr>
						<td><img src="/img/contents/reselt_left_bottom.gif" width="22" height="5" alt=""></td>
						<td background="/img/contents/reselt_bottom_bg.gif"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="/img/contents/reselt_right_bottom.gif" width="22" height="5" alt=""></td>
					</tr>
				</table>
				<!--�������-->
				<!--{if $smarty.const.ADMIN_MODE == '1'}-->
				<input type="button" name="subm" value="������̤򤹤٤ƺ��" onclick="fnModeSubmit('delete_all','','');" />
				<!--{/if}-->
				</td>
				<td><img src="/img/common/_.gif" width="8" height="1" alt=""></td>
				<td><a href="#" onmouseover="chgImg('/img/contents/btn_csv_on.jpg','btn_csv');" onmouseout="chgImg('/img/contents/btn_csv.jpg','btn_csv');"  onclick="fnModeSubmit('csv','','');" ><img src="/img/contents/btn_csv.jpg" width="99" height="22" alt="CSV DOWNLOAD" border="0" name="btn_csv" id="btn_csv"></a></td>
				<td><img src="/img/common/_.gif" width="8" height="1" alt=""></td>
				<td><a href="../contents/csv.php?tpl_subno_csv=product"><span class="fs12n"> >> CSV��������� </span></a></td>
			</tr>
		</table>
		</td>
		<td align="right">
			<!--{include file=$tpl_pager}-->
		</td>									
	</tr>
	<tr><td bgcolor="cbcbcb" colspan="2"><img src="/img/common/_.gif" width="1" height="5" alt=""></td></tr>
</table>

<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#f0f0f0" align="center">

		<!--{if count($arrProducts) > 0}-->		

			<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr><td height="12"></td></tr>
				<tr>
					<td bgcolor="#cccccc">
					<!--�������ɽ���ơ��֥�-->
					<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
						<tr bgcolor="#636469" align="center" class="fs10n">
							<td width="50" rowspan="2"><span class="white">����ID</span></td>
							<td width="90" rowspan="2"><span class="white">���ʲ���</span></td>
							<td width="90"><span class="white">���ʥ�����</span></td>
							<td width="350"><span class="white">����̾</span></td>
							<td width="60"><span class="white">�߸�</span></td>
							<td width="50" rowspan="2"><span class="white">�Խ�</span></td>
							<td width="50" rowspan="2"><span class="white">��ǧ</span></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td width="50" rowspan="2"><span class="white">����</span></td>
							<!--{/if}-->
							<td width="50" rowspan="2"><span class="white">���</span></td>
						</tr>
						<tr bgcolor="#636469" align="center" class="fs10n">
							<td width="90"><span class="white">����(��)</span></td>
							<td width="430"><span class="white">���ƥ���</span></td>
							<td width="60"><span class="white">����</span></td>
						</tr>
			
						<!--{section name=cnt loop=$arrProducts}-->
						<!--������<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts[cnt].status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts[cnt].product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts[cnt].main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts[cnt].main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts[cnt].name|escape}-->" />
							</td>
							<td><!--{$arrProducts[cnt].product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts[cnt].name|escape}--></td>
							<td align="center">
							<!--{* �߸� *}-->
							<!--{if $arrProducts[cnt].stock_unlimited == '1'}-->
							̵����
							<!--{else}-->
							<!--{$arrProducts[cnt].stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >�Խ�</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts[cnt].product_id}-->&admin=on" target="_blank">��ǧ</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >����</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts[cnt].category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;">���</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ���� *}-->
							<!--{if $arrProducts[cnt].price02 != ""}-->
							<!--{$arrProducts[cnt].price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ���ƥ���̾ *}-->
							<!--{assign var=key value=$arrProducts[cnt].category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* ɽ�� *}-->
							<!--{assign var=key value=$arrProducts[cnt].status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--������<!--{$smarty.section.cnt.iteration}-->-->
						<!--{/section}-->
						
					</table>
					<!--�������ɽ���ơ��֥�-->
					</td>
				</tr>
			</table>
	
		<!--{/if}-->

		</td>
	</tr>
</form>
</table>		
<!--����������̰�������-->		
<!--{/if}-->
