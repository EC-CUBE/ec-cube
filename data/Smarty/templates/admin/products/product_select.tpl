<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();

function func_submit( id ){
    var fm = window.opener.document.form1;
    var no = escape('<!--{$smarty.get.no|h}-->');
    fm['recommend_id' + no].value = id;
    fm.select_recommend_no.value = no;
    fm.mode.value = 'recommend_select';
    fm.anchor_key.value = 'recommend_no' + no;
    fm.submit();
    window.close();
    return false;
}
//-->
</script>

<!--▼検索フォーム-->
<form name="form1" id="form1" method="post" action="#">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input name="mode" type="hidden" value="search" />
<input name="anchor_key" type="hidden" value="" />
<input name="search_pageno" type="hidden" value="" />
<table>
    <tr>
        <th><!--{t string="tpl_Category_01"}--></th>
        <td>
            <select name="search_category_id">
                <option value="" selected="selected"><!--{t string="tpl_Please make a selection_01"}--></option>
                <!--{html_options options=$arrCatList selected=$arrForm.search_category_id}-->
            </select>
        </td>
    </tr>
    <tr>
        <th><!--{t string="tpl_Product name_01"}--></th>
        <td><input type="text" name="search_name" value="<!--{$arrForm.search_name|h}-->" size="35" class="box35" /></td>
    </tr>
</table>
<div class="btn-area">
    <a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'search', '', ''); return false;" name="subm"><span class="btn-next"><!--{t string="tpl_Search_01"}--></span></a>
</div>

<!--▼検索結果表示-->
<!--{if $tpl_linemax}-->
    <p><!--{t string="tpl_T_ARG1 items were found._01" T_ARG1=$tpl_linemax}--></p>
    <!--{* ▼ページナビ *}-->
    <!--{$tpl_strnavi}-->
    <!--{* ▲ページナビ *}-->

    <!--{* ▼検索後表示部分 *}-->
    <table class="list">
        <tr>
            <th><!--{t string="tpl_Product image_01"}--></th>
            <th><!--{t string="tpl_Product code_01"}--></th>
            <th><!--{t string="tpl_Product name_01"}--></th>
            <th><!--{t string="tpl_Confirm_01"}--></th>
        </tr>
        <!--{section name=cnt loop=$arrProducts}-->
            <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
            <!--{assign var=status value="`$arrProducts[cnt].status`"}-->
            <tr style="background:<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->;">
                <td align="center">
                    <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrProducts[cnt].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$arrRecommend[$recommend_no].name|h}-->" />
                </td>
                <td>
                    <!--{$arrProducts[cnt].product_code_min|h}-->
                    <!--{if $arrProducts[cnt].product_code_min != $arrProducts[cnt].product_code_max}-->
                        <!--{t string="-"}--> <!--{$arrProducts[cnt].product_code_max|h}-->
                    <!--{/if}-->
                </td>
                <td><!--{$arrProducts[cnt].name|h}--></td>
                <td align="center"><a href="#" onclick="return func_submit(<!--{$arrProducts[cnt].product_id|h}-->)"><!--{t string="tpl_Confirm_01"}--></a></td>
            </tr>
            <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
            <!--{sectionelse}-->
            <tr>
                <td colspan="4"><!--{t string="tpl_There is no product registered_01"}--></td>
            </tr>
        <!--{/section}-->
    </table>
<!--{/if}-->
<!--{* ▲検索結果表示 *}-->

</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
