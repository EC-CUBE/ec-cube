<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="center" valign="top" height="400">
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<!--��SUB NAVI-->
				<td class="fs12n"><!--{include file=$tpl_subnavi}--></td>
				<!--��SUB NAVI-->
			</tr><tr><td height="25"></td></tr>
		</table>
		
		<!--��MAIN CONTENTS-->
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td class="fs14n"><strong>���������</strong></td>
			</tr>
			<tr height="15">
				<td></td>
			</tr>
		</table>
		
		<form name="search_form" id="search_form" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
		<input type="hidden" name="mode" value="search">
		
		<!--�������ơ��֥뤳������-->
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">���ʥ�����</td>
				<td bgcolor="#ffffff" width="248"><input type="text" name="search_product_code" value="<!--{$arrForm.search_product_code|escape}-->" size="30" class="box30" /></td>
				<td bgcolor="#f0f0f0" width="110">����̾</td>
				<td bgcolor="#ffffff" width="249"><input type="text" name="search_name" value="<!--{$arrForm.search_name|escape}-->" size="30" class="box30" /></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">���ƥ���</td>
				<td bgcolor="#ffffff" width="607" colspan="7">
				<select name="search_category_id" style="<!--{if $arrErr.search_category_id != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->">
				<option value="">���򤷤Ƥ�������</option>
				<!--{html_options options=$arrCatList selected=$arrForm.search_category_id}-->
				</select></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">��Ͽ��������</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
				<span class="red"><!--{$arrErr.search_startyear}--></span>
				<span class="red"><!--{$arrErr.search_endyear}--></span>		
				<select name="search_startyear" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
				<option value="">----</option>
				<!--{html_options options=$arrStartYear selected=$arrForm.search_startyear}-->
				</select>ǯ
				<select name="search_startmonth" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrStartMonth selected=$arrForm.search_startmonth}-->
				</select>��
				<select name="search_startday" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrStartDay selected=$arrForm.search_startday}-->
				</select>����
				<select name="search_endyear" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
				<option value="">----</option>
				<!--{html_options options=$arrEndYear selected=$arrForm.search_endyear}-->
				</select>ǯ
				<select name="search_endmonth" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrEndMonth selected=$arrForm.search_endmonth}-->
				</select>��
				<select name="search_endday" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrEndDay selected=$arrForm.search_endday}-->
				</select>��
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">���ơ�����</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
				<!--{html_checkboxes name="search_product_flag" options=$arrSTATUS selected=$arrForm.search_product_flag}-->
				</td>
				</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">����</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
				<!--{html_checkboxes name="search_status" options=$arrDISP selected=$arrForm.search_status}-->
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">�������ɽ����</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
				<!--{assign var=key value="search_page_max"}-->
				<span class="red12"><!--{$arrErr[$key]}--></span>
				<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
				<!--{html_options options=$arrPageMax selected=$arrForm.search_page_max}-->
				</select> ��</td>
			</tr>
		</table>
		<!--�������ơ��֥뤳���ޤ�-->
		
		<br />
		<input type="submit" name="subm" value="�������ƤǸ�������" />
		</form>
		
		<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="product_id" value="">
		<input type="hidden" name="category_id" value="">
		<!--{foreach key=key item=item from=$arrHidden}-->
		<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
		<!--{/foreach}-->

		<!--{if count($arrProducts) > 0}-->
		
		<!--���������ɽ�����ꥢ��������-->
		<hr noshade size="1" color="#cccccc" />		
		<input type="button" name="subm" value="������̤�CSV���������" onclick="fnModeSubmit('csv','','');" />
		
		<!--{if $smarty.const.ADMIN_MODE == '1'}-->
		<input type="button" name="subm" value="������̤򤹤٤ƺ��" onclick="fnModeSubmit('delete_all','','');" />
		<!--{/if}-->
		
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr class="fs12"><td align="left"><!--{$tpl_linemax}-->�郎�������ޤ�����	</td></tr>
			<tr class="fs12">
				<td align="center">
				<!--���ڡ����ʥ�-->
				<!--{$tpl_strnavi}-->
				<!--���ڡ����ʥ�-->
				</td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
				
		<!--��������̥ơ��֥뤳������-->
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr bgcolor="#f0f0f0" align="center" class="fs12n">
				<td width="50" rowspan="2">����ID</td>
				<td width="90" rowspan="2">���ʲ���</td>
				<td width="90">���ʥ�����</td>
				<td width="400">����̾</td>
				<td width="60">�߸�</td>
				<td width="30">�Խ�</td>
				<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
				<td width="30" rowspan="2">����</td>
				<!--{/if}-->
				<td width="30" rowspan="2">���</td>
			</tr>
			<tr bgcolor="#f0f0f0" align="center" class="fs12n">
				<td width="90">����(��)</td>
				<td width="400">���ƥ���</td>
				<td width="60">����</td>
				<td width="30">��ǧ</td>
			</tr>
						
			<!--{section name=cnt loop=$arrProducts}-->
			<!--������<!--{$smarty.section.cnt.iteration}-->-->
			<!--{assign var=status value="`$arrProducts[cnt].status`"}-->
			<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs12">
				<td width="50" rowspan="2" align="center"><!--{$arrProducts[cnt].product_id}--></td>
				<td width="90" align="center" rowspan="2">
				<!--{if $arrProducts[cnt].main_list_image != ""}-->
					<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts[cnt].main_list_image`"}-->
				<!--{else}-->
					<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
				<!--{/if}-->
				<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts[cnt].name|escape}-->" />
				</td>
				<td width="90" ><!--{$arrProducts[cnt].product_code|escape|default:"-"}--></td>
				<td width="310"><!--{$arrProducts[cnt].name|escape}--></td>
				<td width="60" align="center">
				<!--{* �߸� *}-->
				<!--{if $arrProducts[cnt].stock_unlimited == '1'}-->
				̵����
				<!--{else}-->
				<!--{$arrProducts[cnt].stock|escape|default:"-"}-->
				<!--{/if}-->
				</td>
				<td width="30" align="center"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >�Խ�</a></td>
				<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
				<td width="30" align="center" rowspan="2"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >����</a></td>
				<!--{/if}-->
				<td width="30" align="center" rowspan="2"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts[cnt].category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;">���</a></td>
			</tr>
			<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs12n">
				<td width="90" align="right">
				<!--{* ���� *}-->
				<!--{if $arrProducts[cnt].price02 != ""}-->
				<!--{$arrProducts[cnt].price02}-->
				<!--{else}-->
				<!--{$arrProducts[cnt].price01}-->
				<!--{/if}-->
				</td>
				<td width="370">
				<!--{* ���ƥ���̾ *}-->
				<!--{assign var=key value=$arrProducts[cnt].category_id}-->
				<!--{$arrCatList[$key]|sfTrim}-->
				</td>
				<!--{* ɽ�� *}-->
				<!--{assign var=key value=$arrProducts[cnt].status}-->
				<td width="60" align="center"><!--{$arrDISP[$key]}--></td>
				<td width="30" align="center"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts[cnt].product_id}-->&admin=on" target="_blank">��ǧ</a></td>
			</tr>
			<!--������<!--{$smarty.section.cnt.iteration}-->-->
			<!--{/section}-->
						
		</table>
		<!--��������̥ơ��֥뤳���ޤ�-->
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="5"></td></tr>
			<tr><td height="5"></td></tr>
			<tr class="fs12">
				<td align="center">
				<!--���ڡ����ʥ�-->
				<!--{$tpl_strnavi}-->
				<!--���ڡ����ʥ�-->
				</td>
			</tr>
		</table>
		</form>
		<!--���������ɽ�����ꥢ�����ޤ�-->
		<!--��MAIN CONTENTS-->
		<!--{else}-->
			<!--{if $smarty.post.mode == 'search' || $smarty.post.mode == 'delete_all'}-->
			<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr class="fs12"><td align="center">��������ǡ����Ϥ���ޤ���</td></tr>
			</table>
			<!--{/if}-->
		<!--{/if}-->
		
		</td>
	</tr>

</table>
<!--��CONTENTS-->