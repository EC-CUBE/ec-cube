<!--¡ú¡ú¥á¥¤¥ó¥³¥ó¥Æ¥ó¥Ä¡ú¡ú-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="search_form" id="search_form" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="search">
<!--{foreach key=key item=item from=$arrHidden}-->
<!--{if $key == 'campaign_id' || $key == 'search_mode'}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/if}-->
<!--{/foreach}-->
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
			<!-- ¥µ¥Ö¥Ê¥Ó -->
			<!--{include file=$tpl_subnavi}-->
		</td>
		<td class="mainbg">
		<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<!--¥á¥¤¥ó¥¨¥ê¥¢-->
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--¥³¥ó¥Æ¥ó¥Ä¥¿¥¤¥È¥ë-->¸¡º÷¾ò·ïÀßÄê</span></td>
								<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>
						<!--¸¡º÷¾ò·ïÀßÄê¥Æ¡¼¥Ö¥ë¤³¤³¤«¤é-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12">
								<td bgcolor="#f2f1ec" width="110">¾¦ÉÊID</td>
								<td bgcolor="#ffffff" width="194"><input type="text" name="search_product_id" value="<!--{$arrForm.search_product_id|escape}-->" size="30" class="box30" /></td>
								<td bgcolor="#f2f1ec" width="110">µ¬³ÊID</td>
								<td bgcolor="#ffffff" width="195"><input type="text" name="search_product_class_id" value="<!--{$arrForm.search_product_class_id|escape}-->" size="30" class="box30" /></td>
							</tr>
							<tr class="fs12">
								<td bgcolor="#f2f1ec" width="110">¾¦ÉÊ¥³¡¼¥É</td>
								<td bgcolor="#ffffff" width="194"><input type="text" name="search_product_code" value="<!--{$arrForm.search_product_code|escape}-->" size="30" class="box30" /></td>
								<td bgcolor="#f2f1ec" width="110">¾¦ÉÊÌ¾</td>
								<td bgcolor="#ffffff" width="195"><input type="text" name="search_name" value="<!--{$arrForm.search_name|escape}-->" size="30" class="box30" /></td>
							</tr>
							<tr class="fs12">
								<td bgcolor="#f2f1ec" width="110">¥«¥Æ¥´¥ê</td>
								<td bgcolor="#ffffff" width="194">
									<select name="search_category_id" style="<!--{if $arrErr.search_category_id != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->">
									<option value="">ÁªÂò¤·¤Æ¤¯¤À¤µ¤¤</option>
									<!--{html_options options=$arrCatList selected=$arrForm.search_category_id}-->
									</select>
								</td>
								<td bgcolor="#f2f1ec" width="110">¼ïÊÌ</td>
								<td bgcolor="#ffffff" width="195">
									<!--{html_checkboxes name="search_status" options=$arrDISP selected=$arrForm.search_status}-->
								</td>
							</tr class="fs12">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">ÅÐÏ¿¡¦¹¹¿·Æü</td>
								<td bgcolor="#ffffff" width="499" colspan=3>
									<span class="red"><!--{$arrErr.search_startyear}--></span>
									<span class="red"><!--{$arrErr.search_endyear}--></span>		
									<select name="search_startyear" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
									<option value="">----</option>
									<!--{html_options options=$arrStartYear selected=$arrForm.search_startyear}-->
									</select>Ç¯
									<select name="search_startmonth" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrStartMonth selected=$arrForm.search_startmonth}-->
									</select>·î
									<select name="search_startday" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrStartDay selected=$arrForm.search_startday}-->
									</select>Æü¡Á
									<select name="search_endyear" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
									<option value="">----</option>
									<!--{html_options options=$arrEndYear selected=$arrForm.search_endyear}-->
									</select>Ç¯
									<select name="search_endmonth" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrEndMonth selected=$arrForm.search_endmonth}-->
									</select>·î
									<select name="search_endday" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrEndDay selected=$arrForm.search_endday}-->
									</select>Æü
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">¥¹¥Æ¡¼¥¿¥¹</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
								<!--{html_checkboxes name="search_product_flag" options=$arrSTATUS selected=$arrForm.search_product_flag}-->
								</td>
							</tr>
						</table>
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
										<td class="fs12n">¸¡º÷·ë²ÌÉ½¼¨·ï¿ô
											<!--{assign var=key value="search_page_max"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
											<!--{html_options options=$arrPageMax selected=$arrForm.search_page_max}-->
											</select> ·ï
										</td>
										<td><img src="/img/common/_.gif" width="10" height="1" alt=""></td>
										<td><input type="image" name="subm" onMouseover="chgImgImageSubmit('/img/contents/btn_search_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/contents/btn_search.jpg',this)" src="/img/contents/btn_search.jpg" width="123" height="24" alt="¤³¤Î¾ò·ï¤Ç¸¡º÷¤¹¤ë" border="0" onClick="submit();" ></td>
									</tr>
								</table>
								</td>
								<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
							</tr>
						</table>
						<!--¸¡º÷¾ò·ïÀßÄê¥Æ¡¼¥Ö¥ë¤³¤³¤Þ¤Ç-->
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
			<!--¥á¥¤¥ó¥¨¥ê¥¢-->
		</table>
		</td>
	</tr>
</form>	
</table>
<!--¡ú¡ú¥á¥¤¥ó¥³¥ó¥Æ¥ó¥Ä¡ú¡ú-->

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete')}-->

<!--¡ú¡ú¸¡º÷·ë²Ì°ìÍ÷¡ú¡ú-->
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
				<!--¸¡º÷·ë²Ì-->
				<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><img src="/img/contents/reselt_left_top.gif" width="22" height="5" alt=""></td>
						<td background="/img/contents/reselt_top_bg.gif"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="/img/contents/reselt_right_top.gif" width="22" height="5" alt=""></td>
					</tr>
					<tr>
						<td background="/img/contents/reselt_left_bg.gif"><img src="/img/contents/reselt_left_middle.gif" width="22" height="12" alt=""></td>
						<td bgcolor="#393a48" class="white10">¸¡º÷·ë²Ì°ìÍ÷¡¡<span class="reselt"><!--¸¡º÷·ë²Ì¿ô--><!--{$tpl_linemax}-->·ï</span>&nbsp;¤¬³ºÅö¤·¤Þ¤·¤¿¡£</td>
						<td background="/img/contents/reselt_right_bg.gif"><img src="/img/common/_.gif" width="22" height="8" alt=""></td>
					</tr>
					<tr>
						<td><img src="/img/contents/reselt_left_bottom.gif" width="22" height="5" alt=""></td>
						<td background="/img/contents/reselt_bottom_bg.gif"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="/img/contents/reselt_right_bottom.gif" width="22" height="5" alt=""></td>
					</tr>
				</table>
				<!--¸¡º÷·ë²Ì-->
				<!--{if $smarty.const.ADMIN_MODE == '1'}-->
				<input type="button" name="subm" value="¸¡º÷·ë²Ì¤ò¤¹¤Ù¤Æºï½ü" onclick="fnModeSubmit('delete_all','','');" />
				<!--{/if}-->
				</td>
				<td><img src="/img/common/_.gif" width="8" height="1" alt=""></td>
				<td><a href="#" onmouseover="chgImg('/img/contents/btn_csv_on.jpg','btn_csv');" onmouseout="chgImg('/img/contents/btn_csv.jpg','btn_csv');"  onclick="fnModeSubmit('csv','','');" ><img src="/img/contents/btn_csv.jpg" width="99" height="22" alt="CSV DOWNLOAD" border="0" name="btn_csv" id="btn_csv"></a></td>
				<td><img src="/img/common/_.gif" width="8" height="1" alt=""></td>
				<td><a href="../contents/csv.php?tpl_subno_csv=product"><span class="fs12n"> >> CSV½ÐÎÏÀßÄê¤Ø </span></a></td>
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
					<!--¸¡º÷·ë²ÌÉ½¼¨¥Æ¡¼¥Ö¥ë-->
					<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
						<tr bgcolor="#636469" align="center" class="fs10n">
							<td width="50" rowspan="2"><span class="white">¾¦ÉÊID</span></td>
							<td width="90" rowspan="2"><span class="white">¾¦ÉÊ²èÁü</span></td>
							<td width="90"><span class="white">¾¦ÉÊ¥³¡¼¥É</span></td>
							<td width="350"><span class="white">¾¦ÉÊÌ¾</span></td>
							<td width="60"><span class="white">ºß¸Ë</span></td>
							<td width="50" rowspan="2"><span class="white">ÊÔ½¸</span></td>
							<td width="50" rowspan="2"><span class="white">³ÎÇ§</span></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td width="50" rowspan="2"><span class="white">µ¬³Ê</span></td>
							<!--{/if}-->
							<td width="50" rowspan="2"><span class="white">ºï½ü</span></td>
						</tr>
						<tr bgcolor="#636469" align="center" class="fs10n">
							<td width="90"><span class="white">²Á³Ê(±ß)</span></td>
							<td width="430"><span class="white">¥«¥Æ¥´¥ê</span></td>
							<td width="60"><span class="white">¼ïÊÌ</span></td>
						</tr>
			
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						
						
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
						
						
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0..product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0..main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0..main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0..name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0..product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0..name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0..stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0..stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0..product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0..category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0..product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0..price02 != ""}-->
							<!--{$arrProducts.0..price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0..category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0..status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
					</table>
					<!--¸¡º÷·ë²ÌÉ½¼¨¥Æ¡¼¥Ö¥ë-->
					</td>
				</tr>
			</table>
	
		<!--{/if}-->

		</td>
	</tr>
</form>
</table>		
<!--¡ú¡ú¸¡º÷·ë²Ì°ìÍ÷¡ú¡ú-->		
<!--{/if}-->
