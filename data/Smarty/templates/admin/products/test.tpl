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
		</td>
		<td class="mainbg">
		<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">

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
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						
						
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
						
						
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--¢¥¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->						
												
						<!--¢§¾¦ÉÊ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{assign var=status value="`$arrProducts.0.status`"}-->
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
							<td rowspan="2" align="center"><!--{$arrProducts.0.product_id}--></td>
							<td rowspan="2" align="center">
							<!--{if $arrProducts.0.main_list_image != ""}-->
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts.0.main_list_image`"}-->
							<!--{else}-->
								<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
							<!--{/if}-->
							<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts.0.name|escape}-->" />
							</td>
							<td><!--{$arrProducts.0.product_code|escape|default:"-"}--></td>
							<td><!--{$arrProducts.0.name|escape}--></td>
							<td align="center">
							<!--{* ºß¸Ë *}-->
							<!--{if $arrProducts.0.stock_unlimited == '1'}-->
							ÌµÀ©¸Â
							<!--{else}-->
							<!--{$arrProducts.0.stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >ÊÔ½¸</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts.0.product_id}-->&admin=on" target="_blank">³ÎÇ§</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;" >µ¬³Ê</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts.0.category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts.0.product_id}-->); return false;">ºï½ü</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* ²Á³Ê *}-->
							<!--{if $arrProducts.0.price02 != ""}-->
							<!--{$arrProducts.0.price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* ¥«¥Æ¥´¥êÌ¾ *}-->
							<!--{assign var=key value=$arrProducts.0.category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* É½¼¨ *}-->
							<!--{assign var=key value=$arrProducts.0.status}-->
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
