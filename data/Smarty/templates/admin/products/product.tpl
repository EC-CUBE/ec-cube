<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script type="text/javascript">
// URL��ɽ����ɽ���ڤ��ؤ�
function lfDispSwitch(id){
	var obj = document.getElementById(id);
	if (obj.style.display == 'none') {
		obj.style.display = '';
	} else {
		obj.style.display = 'none';		
	}
}
</script>
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
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
						<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" enctype="multipart/form-data">
						<!--{foreach key=key item=item from=$arrSearchHidden}-->
							<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
						<!--{/foreach}-->
						<input type="hidden" name="mode" value="edit">
						<input type="hidden" name="image_key" value="">
						<input type="hidden" name="product_id" value="<!--{$arrForm.product_id}-->" >
						<input type="hidden" name="copy_product_id" value="<!--{$arrForm.copy_product_id}-->" >
						<input type="hidden" name="anchor_key" value="">
						<!--{foreach key=key item=item from=$arrHidden}-->
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
										<td bgcolor="#f2f1ec" width="160" class="fs12n">����ID</td>
										<td bgcolor="#ffffff" width="557" class="fs10n"><!--{$arrForm.product_id}--></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">����̾<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><!--{$arrErr.name}--></span>
										<input type="text" name="name" value="<!--{$arrForm.name|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" size="60" class="box60" /><span class="red"> �ʾ��<!--{$smarty.const.STEXT_LEN}-->ʸ����</span>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">���ʥ��ƥ���<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><!--{$arrErr.category_id}--></span>
										<select name="category_id" style="<!--{if $arrErr.category_id != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" onchange="">
										<option value="">���򤷤Ƥ�������</option>
										<!--{html_options values=$arrCatVal output=$arrCatOut selected=$arrForm.category_id}-->
										</select></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�����������<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs12n"><input type="radio" name="status" value="1" <!--{if $arrForm.status == "1"}-->checked<!--{/if}-->/>������<input type="radio" name="status" value="2" <!--{if $arrForm.status == "2"}-->checked<!--{/if}--> />�����</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">���ʥ��ơ�����</td>
										<td bgcolor="#ffffff" width="557">
										<!--{html_checkboxes name="product_flag" options=$arrSTATUS selected=$arrForm.product_flag}-->
										</td>
									</tr>
									
									<!--{if $tpl_nonclass == true}-->
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">���ʥ�����<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><!--{$arrErr.product_code}--></span>
										<input type="text" name="product_code" value="<!--{$arrForm.product_code|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.product_code != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" size="60" class="box60" /><span class="red"> �ʾ��<!--{$smarty.const.STEXT_LEN}-->ʸ����</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160"><!--{$smarty.const.NORMAL_PRICE_TITLE}--></td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><!--{$arrErr.price01}--></span>
										<input type="text" name="price01" value="<!--{$arrForm.price01|escape}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" style="<!--{if $arrErr.price01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/>��<span class="red10"> ��Ⱦ�ѿ��������ϡ�</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160"><!--{$smarty.const.SALE_PRICE_TITLE}--><span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><!--{$arrErr.price02}--></span>
										<input type="text" name="price02" value="<!--{$arrForm.price02|escape}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" style="<!--{if $arrErr.price02 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/>��<span class="red10"> ��Ⱦ�ѿ��������ϡ�</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">�߸˿�<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><!--{$arrErr.stock}--></span>
										<input type="text" name="stock" value="<!--{$arrForm.stock|escape}-->" size="6" class="box6" maxlength="<!--{$smarty.const.AMOUNT_LEN}-->" style="<!--{if $arrErr.stock != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/>��
										<input type="checkbox" name="stock_unlimited" value="1" <!--{if $arrForm.stock_unlimited == "1"}-->checked<!--{/if}--> onclick="fnCheckStockLimit('<!--{$smarty.const.DISABLED_RGB}-->');"/>̵����</td>
										</td>
									</tr>
									<!--{/if}-->
									
									<!--{* �����θ��̻���ϼ�����ȯ���ɲ�ͽ��
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">��������</td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><!--{$arrErr.deliv_fee}--></span>
										<input type="text" name="deliv_fee" value="<!--{$arrForm.deliv_fee|escape}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" style="<!--{if $arrErr.deliv_fee != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/>��<span class="red10"> ��Ⱦ�ѿ��������ϡ�</span></td>
										</td>
									</tr>
									*}-->
									
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">�ݥ������ͿΨ<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><!--{$arrErr.point_rate}--></span>
										<input type="text" name="point_rate" value="<!--{$arrForm.point_rate|escape|default:$arrInfo.point_rate}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PERCENTAGE_LEN}-->" style="<!--{if $arrErr.point_rate != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/>��<span class="red10"> ��Ⱦ�ѿ��������ϡ�</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">ȯ�����ܰ�</td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><!--{$arrErr.deliv_date_id}--></span>
										<select name="deliv_date_id" style="<!--{$arrErr.deliv_date_id|sfGetErrorColor}-->">
										<option value="">���򤷤Ƥ�������</option>
										<!--{html_options options=$arrDELIVERYDATE selected=$arrForm.deliv_date_id}-->
										</select>
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">��������<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><!--{$arrErr.sale_limit}--></span>
										<input type="text" name="sale_limit" value="<!--{$arrForm.sale_limit|escape}-->" size="6" class="box6" maxlength="<!--{$smarty.const.AMOUNT_LEN}-->" style="<!--{if $arrErr.sale_limit != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/>��
										<input type="checkbox" name="sale_unlimited" value="1" <!--{if $arrForm.sale_unlimited == "1"}-->checked<!--{/if}--> onclick="fnCheckSaleLimit('<!--{$smarty.const.DISABLED_RGB}-->');"/>̵����</td>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�᡼����URL</td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><!--{$arrErr.comment1}--></span>
										<input type="text" name="comment1" value="<!--{$arrForm.comment1|escape}-->" maxlength="<!--{$smarty.const.URL_LEN}-->" size="60" class="box60" style="<!--{$arrErr.comment1|sfGetErrorColor}-->" /><span class="red"> �ʾ��<!--{$smarty.const.URL_LEN}-->ʸ����</span></td>
									</tr>
									<!--{*
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">��ʬ</td>
										<td bgcolor="#ffffff" width="557" class="fs10n"><textarea name="comment2" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.STEXT_LEN}-->"><!--{$arrForm.comment2|escape}--></textarea><span class="red"> �ʾ��<!--{$smarty.const.LTEXT_LEN}-->ʸ����</span></td>
									</tr>
									*}-->
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�������<br />��ʣ���ξ��ϡ������( , )���ڤ�����Ϥ��Ʋ�����</td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><!--{$arrErr.comment3}--></span>
										<textarea name="comment3" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.comment3|sfGetErrorColor}-->"><!--{$arrForm.comment3|escape}--></textarea><br /><span class="red"> �ʾ��<!--{$smarty.const.LLTEXT_LEN}-->ʸ����</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">����-�ᥤ�󥳥���<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><!--{$arrErr.main_list_comment}--></span>
										<textarea name="main_list_comment" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{if $arrErr.main_list_comment != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.main_list_comment|escape}--></textarea><br /><span class="red"> �ʾ��<!--{$smarty.const.MTEXT_LEN}-->ʸ����</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-�ᥤ�󥳥���<span class="red">(��������)*</span></td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><!--{$arrErr.main_comment}--></span>
										<textarea name="main_comment" value="<!--{$arrForm.main_comment|escape}-->" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.main_comment != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"  cols="60" rows="8" class="area60"><!--{$arrForm.main_comment|escape}--></textarea><br /><span class="red"> �ʾ��<!--{$smarty.const.LLTEXT_LEN}-->ʸ����</span></td>
									</tr>
									<tr>
										<!--{assign var=key value="main_list_image"}-->
										<td bgcolor="#f2f1ec" width="160" class="fs12n">����-�ᥤ�����<span class="red"> *</span><br />[<!--{$smarty.const.SMALL_IMAGE_WIDTH}-->��<!--{$smarty.const.SMALL_IMAGE_HEIGHT}-->]</td>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<a name="<!--{$key}-->"></a>
										<a name="main_image"></a>
										<a name="main_large_image"></a>
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<!--{if $arrFile[$key].filepath != ""}-->
										<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" />��<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[�����μ��ä�]</a><br>
										<!--{/if}-->
										<input type="file" name="main_list_image" size="50" class="box50" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
										<input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->')" value="���åץ���">
										</td>
									</tr>
									<tr>
										<!--{assign var=key value="main_image"}-->
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-�ᥤ�����<span class="red"> *</span><br />[<!--{$smarty.const.NORMAL_IMAGE_WIDTH}-->��<!--{$smarty.const.NORMAL_IMAGE_HEIGHT}-->]</td>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<!--{if $arrFile[$key].filepath != ""}-->
										<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" />��<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[�����μ��ä�]</a><br>
										<!--{/if}-->
										<input type="file" name="main_image" size="50" class="box50" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
										<input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->')" value="���åץ���">
										</td>
									</tr>
									<tr>
										<!--{assign var=key value="main_large_image"}-->
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-�ᥤ��������<br />[<!--{$smarty.const.LARGE_IMAGE_WIDTH}-->��<!--{$smarty.const.LARGE_IMAGE_HEIGHT}-->]</td>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<!--{if $arrFile[$key].filepath != ""}-->
										<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" />��<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[�����μ��ä�]</a><br>
										<!--{/if}-->
										<input type="file" name="<!--{$key}-->" size="50" class="box50" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
										<input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->')" value="���åץ���">
										</td>
									</tr>
									
									<!--{if $tpl_movilink_flg}-->
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" colspan="2">����ӥ�Ϣ���Ѣ�</td>
									</tr>									
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">������;��ʥ�����<span class="red12"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<!--{assign var=key value="movilink_code1"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="20" class="box20" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">����̾(�եꥬ��)<span class="red12"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<!--{assign var=key value="movilink_kana"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="40" class="box40" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">���ʲ���<span class="red12"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<!--{assign var=key value="movilink_price"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/>��<span class="red10"> ��Ⱦ�ѿ��������ϡ�</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">������Ψ<span class="red12"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<!--{assign var=key value="movilink_net_percent"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/>��<span class="red10"> ��Ⱦ�ѿ��������ϡ�</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">�����󽷳�<span class="red12"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<!--{assign var=key value="movilink_net_fix"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/>��<span class="red10"> ��Ⱦ�ѿ��������ϡ�</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">���𥿥�����<span class="red12"> *</span></td>
										<td bgcolor="#ffffff" width="557"> 
										<!--{assign var=key value="movilink_draft_text1"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="20" class="box20" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/><br>
										<!--{assign var=key value="movilink_draft_text2"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="20" class="box20" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->"/><span class="red10"> ��8ʸ����2�Ԥ����ϡ�</span></td>
									</tr>
									<!--{/if}-->
									
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr height="36" align="center">
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#f2f1ec" width="676"><input type="button" name="btn" onclick="lfDispSwitch('sub_detail');" value="���־���ɽ��/��ɽ��"></td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr height="1" align="center">
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" height="1" alt=""></td>
									</tr>
								</table>
								
								<!--{if $sub_find == true}-->								
								<div id="sub_detail" style="">
								<!--{else}-->
								<div id="sub_detail" style="display:none">
								<!--{/if}-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
									<!--������<!--{$smarty.section.cnt.iteration}-->-->
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-���֥����ȥ��<!--{$smarty.section.cnt.iteration}-->��</td>
										<!--{assign var=key value="sub_title`$smarty.section.cnt.iteration`"}-->
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="sub_title<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrForm[$key]|escape}-->" size="60" class="box60" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"/><span class="red10"> �ʾ��<!--{$smarty.const.STEXT_LEN}-->ʸ����</span>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-���֥����ȡ�<!--{$smarty.section.cnt.iteration}-->��<span class="red">(��������)</span></td>
										<!--{assign var=key value="sub_comment`$smarty.section.cnt.iteration`"}-->
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<textarea name="sub_comment<!--{$smarty.section.cnt.iteration}-->" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key]|escape}--></textarea><br /><span class="red10"> �ʾ��<!--{$smarty.const.LLTEXT_LEN}-->ʸ����</span></td>
									</tr>
									<tr>
										<!--{assign var=key value="sub_image`$smarty.section.cnt.iteration`"}-->
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-���ֲ�����<!--{$smarty.section.cnt.iteration}-->��<br />[<!--{$smarty.const.NORMAL_SUBIMAGE_WIDTH}-->��<!--{$smarty.const.NORMAL_SUBIMAGE_HEIGHT}-->]</td>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<a name="<!--{$key}-->"></a>
										<!--{assign var=largekey value="sub_large_image`$smarty.section.cnt.iteration`"}-->
										<a name="<!--{$largekey}-->"></a>
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<!--{if $arrFile[$key].filepath != ""}-->
										<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" />��<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[�����μ��ä�]</a><br>
										<!--{/if}-->
										<input type="file" name="<!--{$key}-->" size="50" class="box50" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"/>
										<input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->')" value="���åץ���">
										</td>
									</tr>
									<tr>
										<!--{assign var=key value="sub_large_image`$smarty.section.cnt.iteration`"}-->
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�ܺ�-���ֳ��������<!--{$smarty.section.cnt.iteration}-->��<br />[<!--{$smarty.const.LARGE_SUBIMAGE_WIDTH}-->��<!--{$smarty.const.LARGE_SUBIMAGE_HEIGHT}-->]</td>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<!--{if $arrFile[$key].filepath != ""}-->
										<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" />��<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[�����μ��ä�]</a><br>
										<!--{/if}-->
										<input type="file" name="<!--{$key}-->" size="50" class="box50" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"/>
										<input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->')" value="���åץ���">
										</td>
									</tr>
									<!--������<!--{$smarty.section.cnt.iteration}-->-->
									<!--{/section}-->
								</table>
								</div>
								
								
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr height="36" align="center">
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#f2f1ec" width="676"><input type="button" name="btn" onclick="lfDispSwitch('recommend_select');" value="��Ϣ����ɽ��/��ɽ��"></td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr height="1" align="center">
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" height="1" alt=""></td>
									</tr>
								</table>
								
								<!--{if count($arrRecommend) > 0}-->
								<div id="recommend_select" style="">
								<!--{else}-->
								<div id="recommend_select" style="display:none">
								<!--{/if}-->								
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<!--{if $smarty.const.OPTION_RECOMMEND == 1}-->			
									<!--����Ϣ����-->
									<!--{section name=cnt loop=$smarty.const.RECOMMEND_PRODUCT_MAX}-->			
									<!--{assign var=recommend_no value="`$smarty.section.cnt.iteration`"}-->
									<tr>
										<!--{assign var=key value="recommend_id`$smarty.section.cnt.iteration`"}-->
										<!--{assign var=anckey value="recommend_no`$smarty.section.cnt.iteration`"}-->
										<td bgcolor="#f2f1ec" width="160" class="fs12n">��Ϣ����(<!--{$smarty.section.cnt.iteration}-->)<br>
										<!--{if $arrRecommend[$recommend_no].main_list_image != ""}-->
											<!--{assign var=image_path value="`$arrRecommend[$recommend_no].main_list_image`"}-->
										<!--{else}-->
											<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_DIR`"}-->
										<!--{/if}-->
										<img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$image_path|sfRmDupSlash}-->&width=65&height=65" alt="<!--{$arrRecommend[$recommend_no].name|escape}-->">
										</td>
										<td bgcolor="#ffffff" width="557" class="fs12">
										<a name="<!--{$anckey}-->"></a>
										<input type="hidden" name="<!--{$key}-->" value="<!--{$arrRecommend[$recommend_no].product_id|escape}-->">
										<input type="button" name="change" value="�ѹ�" onclick="win03('./product_select.php?no=<!--{$smarty.section.cnt.iteration}-->', 'search', '500', '500'); " >
										<!--{assign var=key value="recommend_delete`$smarty.section.cnt.iteration`"}-->
										<input type="checkbox" name="<!--{$key}-->" value="1">���<br>
										���ʥ�����:<!--{$arrRecommend[$recommend_no].product_code_min}--><br>
										����̾:<!--{$arrRecommend[$recommend_no].name|escape}--><br>
										<!--{assign var=key value="recommend_comment`$smarty.section.cnt.iteration`"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<textarea name="<!--{$key}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrRecommend[$recommend_no].comment|escape}--></textarea><br /><span class="red10"> �ʾ��<!--{$smarty.const.LTEXT_LEN}-->ʸ����</span></td>
										</td>
									</tr>
									<!--{/section}-->
									<!--����Ϣ����-->
									<!--{/if}-->
								</table>
								</div>
								
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
												<!--{if count($arrSearchHidden) > 0}-->
												<!--��������̤����-->
													<a href="#" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/contents/btn_search_back_on.jpg','back');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/contents/btn_search_back.jpg','back');" onClick="fnChangeAction('<!--{$smarty.const.URL_SEARCH_TOP}-->'); fnModeSubmit('search','',''); return false;"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_search_back.jpg" width="123" height="24" alt="�������̤����" border="0" name="back"></a>
												<!--��������̤����-->
												<!--{/if}-->
												<input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_confirm_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_confirm.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_confirm.jpg" width="123" height="24" alt="��ǧ�ڡ�����" border="0" name="subm" >
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